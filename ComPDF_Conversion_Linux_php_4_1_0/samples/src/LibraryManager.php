<?php
declare(strict_types=1);

namespace ComPDFKit\Conversion;

use FFI;
use FFI\CData;
use ComPDFKit\Conversion\Exception\CompdfException;
use ComPDFKit\Conversion\Internal\FFIFactory;

/**
 * Common SDK lifecycle: license verification, initialization, logging,
 * version info, page-count queries, and shutdown.
 *
 * Equivalent to ComPDFKit::common::LibraryManager in C++ and the
 * `compdfkit_go_*` helpers in the Go binding.
 */
final class LibraryManager
{
    private static bool $initialized = false;

    private function __construct() {}

    public static function licenseVerify(string $license, string $deviceId = '', string $appId = ''): int
    {
        $ffi = FFIFactory::ffi();
        // CPDF_LicenseVerify always takes char* on every platform.
        return (int) $ffi->CPDF_LicenseVerify($license, $deviceId, $appId);
    }

    public static function initialize(string $resourcePath): void
    {
        $ffi = FFIFactory::ffi();

        if (self::isWindows()) {
            $wpath = self::toWString($resourcePath);
            $ffi->CPDF_Initialize($wpath);
        } else {
            $ffi->CPDF_Initialize($resourcePath);
        }

        self::$initialized = true;
    }

    public static function release(): void
    {
        $ffi = FFIFactory::ffi();
        $ffi->CPDF_Release();
        self::$initialized = false;
    }

    public static function setLogger(bool $enableInfo, bool $enableWarning): void
    {
        FFIFactory::ffi()->CPDF_SetLogger($enableInfo, $enableWarning);
    }

    public static function getPageCount(string $filePath, string $password = ''): int
    {
        $ffi = FFIFactory::ffi();
        if (self::isWindows()) {
            return (int) $ffi->CPDF_GetPageCount(self::toWString($filePath), self::toWString($password));
        }
        return (int) $ffi->CPDF_GetPageCount($filePath, $password);
    }

    public static function getRemainingPageQuota(): int
    {
        return (int) FFIFactory::ffi()->CPDF_GetRemainingPageQuota();
    }

    /**
     * Load the DocumentAI model used by OCR, Layout Analysis and Table
     * Recognition. Must be called after {@see initialize()} and before any
     * conversion that relies on AI features.
     *
     * @param string $modelPath Absolute path to the `documentai.model` file.
     * @param int    $gpuId     GPU device index; pass -1 to disable GPU.
     */
    public static function setDocumentAIModel(string $modelPath, int $gpuId = -1): int
    {
        $ffi = FFIFactory::ffi();
        if (self::isWindows()) {
            return (int) $ffi->CPDF_SetDocumentAIModel(self::toWString($modelPath), $gpuId);
        }
        return (int) $ffi->CPDF_SetDocumentAIModel($modelPath, $gpuId);
    }

    /**
     * Configure how many concurrent Layout / Table model instances the SDK
     * keeps in memory. Increase the counts to improve throughput when many
     * conversions run in parallel.
     */
    public static function setDocumentAIModelCount(int $layoutModelCount, int $tableModelCount): void
    {
        FFIFactory::ffi()->CPDF_SetDocumentAIModelCount($layoutModelCount, $tableModelCount);
    }

    /**
     * Release the DocumentAI model (and its GPU resources) without shutting
     * down the rest of the SDK. After this call AI features become unavailable
     * until {@see setDocumentAIModel()} is invoked again.
     */
    public static function releaseDocumentAIModel(): void
    {
        FFIFactory::ffi()->CPDF_ReleaseDocumentAIModel();
    }

    public static function getVersion(): string
    {
        $ffi = FFIFactory::ffi();
        $buf = $ffi->new('char[64]');
        $ffi->CPDF_GetVersion($buf);
        return FFI::string($buf);
    }

    public static function isInitialized(): bool
    {
        return self::$initialized;
    }

    /**
     * Encode a UTF-8 PHP string as a NUL-terminated UTF-16LE buffer for the
     * Windows wchar_t* APIs and return it as a CData uint16_t array.
     */
    public static function toWString(string $utf8): CData
    {
        $utf16 = mb_convert_encoding($utf8, 'UTF-16LE', 'UTF-8');
        if ($utf16 === false) {
            throw new CompdfException('Failed to convert string to UTF-16LE');
        }
        $bytes   = strlen($utf16);
        $count   = ($bytes / 2) + 1; // +1 for NUL terminator
        $buffer  = FFI::new("uint16_t[$count]", false);
        if ($bytes > 0) {
            FFI::memcpy($buffer, $utf16, $bytes);
        }
        return $buffer;
    }

    public static function isWindows(): bool
    {
        return strtolower(PHP_OS_FAMILY) === 'windows';
    }
}
