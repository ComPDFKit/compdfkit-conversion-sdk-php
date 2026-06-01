<?php
declare(strict_types=1);

// Quick sanity check: print the SDK version. Useful to confirm the native
// library and FFI cdef load correctly before doing any conversion work.
//
// Usage:
//   COMPDFKIT_LIB_DIR=/abs/path/to/libs php samples/version.php

require __DIR__ . '/autoload.php';

use ComPDFKit\Conversion\LibraryManager;

echo 'SDK version: ' . LibraryManager::getVersion() . PHP_EOL;
echo 'Remaining quota: ' . LibraryManager::getRemainingPageQuota() . PHP_EOL;
