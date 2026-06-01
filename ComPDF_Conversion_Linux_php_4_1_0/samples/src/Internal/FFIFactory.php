<?php
declare(strict_types=1);

namespace ComPDFKit\Conversion\Internal;

use FFI;
use FFI\CData;
use ComPDFKit\Conversion\Exception\CompdfException;

/**
 * Resolves the platform-specific FFI handle and the native library directory.
 *
 * The handle is loaded once per process and cached as a singleton. Native
 * libraries are looked up in this order:
 *
 *   1. The COMPDFKIT_LIB_DIR environment variable (absolute path).
 *   2. <package-root>/lib/<linux|windows|mac/{arch}>.
 */
final class FFIFactory
{
    private static ?FFI $ffi = null;
    private static ?string $libDir = null;

    private function __construct() {}

    public static function ffi(): FFI
    {
        if (self::$ffi !== null) {
            return self::$ffi;
        }

        $libDir = self::libDir();
        [$cdefFile, $libFile] = self::platformFiles($libDir);

        // Make sure dependent libraries (libDocumentAI.so, libonnxruntime.so,
        // libopencv_world.so, etc.) sitting next to the main library are
        // discoverable by the dynamic linker for this process.
        self::registerSearchPath($libDir);

        $cdef = file_get_contents($cdefFile);
        if ($cdef === false) {
            throw new CompdfException("Failed to read FFI cdef: $cdefFile");
        }

        // Strip the FFI_LIB directive so we can pass an absolute path instead
        // of relying on LD_LIBRARY_PATH at first load.
        $cdef = preg_replace('/^\s*#define\s+FFI_LIB\b.*$/m', '', $cdef) ?? $cdef;

        try {
            self::$ffi = FFI::cdef($cdef, $libFile);
        } catch (\Throwable $e) {
            throw new CompdfException(
                "Failed to load ComPDFKit native library at $libFile: " . $e->getMessage(),
                0,
                $e
            );
        }

        return self::$ffi;
    }

    public static function libDir(): string
    {
        if (self::$libDir !== null) {
            return self::$libDir;
        }

        $env = getenv('COMPDFKIT_LIB_DIR');
        if (is_string($env) && $env !== '') {
            if (!is_dir($env)) {
                throw new CompdfException("COMPDFKIT_LIB_DIR is set but not a directory: $env");
            }
            return self::$libDir = $env;
        }

        $root = dirname(__DIR__, 3); // .../php/
        $os   = strtolower(PHP_OS_FAMILY);

        switch ($os) {
            case 'linux':
                $dir = $root . '/lib/linux';
                break;
            case 'windows':
                $dir = $root . '/lib/windows/' . self::windowsArch();
                if (!is_dir($dir)) {
                    $dir = $root . '/lib/windows';
                }
                break;
            case 'darwin':
                $arch = self::macArch();
                $dir  = $root . '/lib/mac/' . $arch;
                break;
            default:
                throw new CompdfException("Unsupported OS: $os");
        }

        if (!is_dir($dir)) {
            throw new CompdfException(
                "Native library directory not found: $dir\n" .
                "Set COMPDFKIT_LIB_DIR or make sure the package lib directory exists."
            );
        }

        return self::$libDir = $dir;
    }

    /**
     * @return array{0:string,1:string} [cdef path, native library path]
     */
    private static function platformFiles(string $libDir): array
    {
        $cdefDir = __DIR__ . '/cdef';
        switch (strtolower(PHP_OS_FAMILY)) {
            case 'linux':
                return [$cdefDir . '/compdfkit_linux.h',   $libDir . '/libcompdf_php_shim.so'];
            case 'windows':
                return [$cdefDir . '/compdfkit_windows.h', $libDir . '/cpdfconversionsdk.dll'];
            case 'darwin':
                return [$cdefDir . '/compdfkit_macos.h',   $libDir . '/libcpdfconversionsdk.dylib'];
        }
        throw new CompdfException('Unsupported OS family: ' . PHP_OS_FAMILY);
    }

    private static function macArch(): string
    {
        $machine = strtolower((string) php_uname('m'));
        if ($machine === 'arm64' || $machine === 'aarch64') {
            return 'arm64';
        }
        if ($machine === 'x86_64' || $machine === 'amd64' || $machine === 'i386') {
            return 'x64';
        }
        throw new CompdfException("Unsupported macOS architecture: $machine");
    }

    private static function windowsArch(): string
    {
        $machine = strtolower((string) php_uname('m'));
        if ($machine === 'amd64' || $machine === 'x86_64') {
            return 'x64';
        }
        if ($machine === 'x86' || $machine === 'i386' || $machine === 'i686') {
            return 'x86';
        }
        return 'x64';
    }

    private static function registerSearchPath(string $libDir): void
    {
        switch (strtolower(PHP_OS_FAMILY)) {
            case 'linux':
                self::prependEnv('LD_LIBRARY_PATH', $libDir);
                break;
            case 'darwin':
                self::prependEnv('DYLD_LIBRARY_PATH', $libDir);
                self::prependEnv('DYLD_FALLBACK_LIBRARY_PATH', $libDir);
                break;
            case 'windows':
                self::prependEnv('PATH', $libDir);
                break;
        }
    }

    private static function prependEnv(string $name, string $value): void
    {
        $existing = (string) getenv($name);
        $sep      = strtolower(PHP_OS_FAMILY) === 'windows' ? ';' : ':';
        $new      = $existing === '' ? $value : ($value . $sep . $existing);
        putenv($name . '=' . $new);
    }

    /**
     * Allocates a NUL-terminated C string copy. Caller is responsible for
     * keeping the returned CData alive while the C side reads it.
     */
    public static function cstring(?string $s): ?CData
    {
        if ($s === null) {
            return null;
        }
        $len = strlen($s) + 1;
        $buf = FFI::new("char[$len]", false);
        FFI::memcpy($buf, $s, strlen($s));
        // Trailing NUL is zero-initialised by FFI::new when persistent=false.
        return $buf;
    }

    /**
     * Frees memory previously allocated with cstring().
     */
    public static function freeCData(?CData $data): void
    {
        if ($data !== null) {
            FFI::free($data);
        }
    }
}
