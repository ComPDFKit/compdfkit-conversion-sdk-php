<?php
declare(strict_types=1);

if (PHP_VERSION_ID < 70400) {
    throw new RuntimeException('ComPDFKit Conversion PHP SDK requires PHP 7.4 or later.');
}

if (!extension_loaded('ffi')) {
    throw new RuntimeException('ComPDFKit Conversion PHP SDK requires the PHP FFI extension.');
}

spl_autoload_register(static function (string $class): void {
    $prefix = 'ComPDFKit\\Conversion\\';
    $prefixLength = strlen($prefix);

    if (strncmp($class, $prefix, $prefixLength) !== 0) {
        return;
    }

    $relativeClass = substr($class, $prefixLength);
    $file = __DIR__ . '/src/' . str_replace('\\', '/', $relativeClass) . '.php';

    if (is_file($file)) {
        require $file;
    }
});

return true;
