<?php
declare(strict_types=1);

// Demonstrates the new ConvertCallback (progress + cancel) and the
// DocumentAI model lifecycle helpers exposed in PHP SDK v4.1.0+.
//
// Usage:
//   samples/compdfphp samples/callback_demo.php

require __DIR__ . '/autoload.php';

use ComPDFKit\Conversion\Conversion;
use ComPDFKit\Conversion\ConvertCallback;
use ComPDFKit\Conversion\ConvertOption;
use ComPDFKit\Conversion\ErrorCode;
use ComPDFKit\Conversion\LibraryManager;

$resource = dirname(__DIR__);
$input    = __DIR__ . '/input_files/word.pdf';
$outputA  = __DIR__ . '/output_files/callback_progress.docx';
$outputB  = __DIR__ . '/output_files/callback_cancelled.docx';

@mkdir(dirname($outputA), 0777, true);

$rc = LibraryManager::licenseVerify(__DIR__ . '/license.xml');
if (!ErrorCode::isSuccess($rc)) {
    fwrite(STDERR, 'License verify failed: ' . ErrorCode::describe($rc) . PHP_EOL);
    exit(1);
}
LibraryManager::initialize($resource);
LibraryManager::setLogger(false, true);

// Document-AI lifecycle is now exposed as well.
$modelPath = $resource . '/resource/models/documentai.model';
$aiRc = LibraryManager::setDocumentAIModel($modelPath, -1);
echo 'setDocumentAIModel: ' . ErrorCode::describe($aiRc) . PHP_EOL;
LibraryManager::setDocumentAIModelCount(1, 1);

try {
    // --- 1. Progress reporting ----------------------------------------------
    $option = new ConvertOption();
    $option->autoCreateFolder = true;

    $progressCb = new ConvertCallback();
    $progressCb->onProgress = static function (int $current, int $total): void {
        echo "[progress] {$current}/{$total}" . PHP_EOL;
    };

    $code = Conversion::pdfToWord($input, '', $outputA, $option, $progressCb);
    echo 'progress run rc=' . $code . ' (' . ErrorCode::describe($code) . ')' . PHP_EOL;

    // --- 2. Cancellation -----------------------------------------------------
    $cancelCb = new ConvertCallback();
    $seen = 0;
    $cancelCb->onProgress = static function (int $current, int $total) use (&$seen): void {
        $seen = $current;
        echo "[cancel-progress] {$current}/{$total}" . PHP_EOL;
    };
    $cancelCb->onCancel = static function () use (&$seen): bool {
        // Ask the SDK to stop as soon as we've seen the first page.
        return $seen >= 1;
    };

    $code = Conversion::pdfToWord($input, '', $outputB, $option, $cancelCb);
    echo 'cancel run rc=' . $code . ' (' . ErrorCode::describe($code) . ')' . PHP_EOL;
} finally {
    LibraryManager::releaseDocumentAIModel();
    LibraryManager::release();
}
