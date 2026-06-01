<?php
declare(strict_types=1);

// One-command conversion smoke demo for package verification.
//
// Usage:
//   samples/compdfphp samples/direct_convert_demo.php
//
// Environment overrides:
//   COMPDFKIT_DEMO_LICENSE   override the packaged license XML path.
//   COMPDFKIT_DEMO_DEVICE_ID optional device ID.
//   COMPDFKIT_DEMO_APP_ID    optional app ID.
//   COMPDFKIT_RESOURCE       path passed to LibraryManager::initialize().

require __DIR__ . '/autoload.php';

use ComPDFKit\Conversion\Conversion;
use ComPDFKit\Conversion\ConvertOption;
use ComPDFKit\Conversion\ErrorCode;
use ComPDFKit\Conversion\LibraryManager;
use ComPDFKit\Conversion\OcrLanguage;
use ComPDFKit\Conversion\PageLayoutMode;

const LICENSE_FILE_NAME = 'license.xml';

$inputDir = __DIR__ . '/input_files';
$outputDir = __DIR__ . '/output_files';
$resourcePath = getenv('COMPDFKIT_RESOURCE') ?: dirname(__DIR__);
$modelPath = $resourcePath . '/resource/models/documentai.model';

$license = getenv('COMPDFKIT_DEMO_LICENSE') ?: __DIR__ . '/' . LICENSE_FILE_NAME;
$deviceId = getenv('COMPDFKIT_DEMO_DEVICE_ID') ?: '';
$appId = getenv('COMPDFKIT_DEMO_APP_ID') ?: '';

foreach (['word.pdf', 'excel.pdf', 'powerpoint.pdf'] as $inputName) {
    $inputPath = $inputDir . '/' . $inputName;
    if (!is_file($inputPath)) {
        fwrite(STDERR, "Input PDF not found: {$inputPath}" . PHP_EOL);
        exit(1);
    }
}

if (!is_dir($outputDir) && !mkdir($outputDir, 0777, true) && !is_dir($outputDir)) {
    fwrite(STDERR, "Failed to create output directory: {$outputDir}" . PHP_EOL);
    exit(1);
}

foreach (glob($outputDir . '/*') ?: [] as $oldPath) {
    if (!removePath($oldPath)) {
        fwrite(STDERR, "Failed to remove old output path: {$oldPath}" . PHP_EOL);
        exit(1);
    }
}

$licenseCode = LibraryManager::licenseVerify($license, $deviceId, $appId);
if (!ErrorCode::isSuccess($licenseCode)) {
    fwrite(STDERR, 'License verify failed: ' . ErrorCode::describe($licenseCode) . " ({$licenseCode})" . PHP_EOL);
    exit(2);
}

LibraryManager::initialize($resourcePath);
LibraryManager::setLogger(false, true);

try {
    $option = new ConvertOption();
    $option->containImage = true;
    $option->containAnnotation = true;
    $option->autoCreateFolder = true;

    echo 'Resource: ' . $resourcePath . PHP_EOL;
    echo 'DocumentAI model: ' . (is_file($modelPath) ? $modelPath : 'not found') . PHP_EOL;
    echo 'SDK version: ' . LibraryManager::getVersion() . PHP_EOL;

    runConversion('pdf to word', function () use ($inputDir, $outputDir, $option): int {
        return Conversion::pdfToWord($inputDir . '/word.pdf', '', $outputDir . '/word.docx', $option);
    }, $outputDir . '/word.docx');

    runConversion('pdf to excel', function () use ($inputDir, $outputDir, $option): int {
        return Conversion::pdfToExcel($inputDir . '/excel.pdf', '', $outputDir . '/excel.xlsx', $option);
    }, $outputDir . '/excel.xlsx');

    runConversion('pdf to ppt', function () use ($inputDir, $outputDir, $option): int {
        return Conversion::pdfToPpt($inputDir . '/powerpoint.pdf', '', $outputDir . '/powerpoint.pptx', $option);
    }, $outputDir . '/powerpoint.pptx');

    $option->excelCsvFormat = true;
    runConversion('pdf to csv', function () use ($inputDir, $outputDir, $option): int {
        return Conversion::pdfToExcel($inputDir . '/excel.pdf', '', $outputDir, $option);
    }, $outputDir, true);
    $option->excelCsvFormat = false;

    $option->pageLayoutMode = PageLayoutMode::BOX;
    runConversion('pdf to html', function () use ($inputDir, $outputDir, $option): int {
        return Conversion::pdfToHtml($inputDir . '/word.pdf', '', $outputDir . '/html.html', $option);
    }, $outputDir . '/html.html');

    runConversion('pdf to rtf', function () use ($inputDir, $outputDir, $option): int {
        return Conversion::pdfToRtf($inputDir . '/word.pdf', '', $outputDir . '/rtf.rtf', $option);
    }, $outputDir . '/rtf.rtf');

    runConversion('pdf to image', function () use ($inputDir, $outputDir, $option): int {
        return Conversion::pdfToImage($inputDir . '/word.pdf', '', $outputDir, $option);
    }, $outputDir, true);

    runConversion('pdf to txt', function () use ($inputDir, $outputDir, $option): int {
        return Conversion::pdfToTxt($inputDir . '/word.pdf', '', $outputDir . '/txt.txt', $option);
    }, $outputDir . '/txt.txt');

    runConversion('pdf to json', function () use ($inputDir, $outputDir, $option): int {
        return Conversion::pdfToJson($inputDir . '/word.pdf', '', $outputDir . '/json.json', $option);
    }, $outputDir, true);

    runConversion('pdf to markdown', function () use ($inputDir, $outputDir, $option): int {
        return Conversion::pdfToMarkdown($inputDir . '/word.pdf', '', $outputDir . '/markdown.md', $option);
    }, $outputDir, true);

    $option->enableOcr = true;
    $option->transparentText = true;
    $option->languages = [OcrLanguage::ENGLISH];
    runConversion('pdf to searchable pdf', function () use ($inputDir, $outputDir, $option): int {
        return Conversion::pdfToSearchablePdf($inputDir . '/word.pdf', '', $outputDir . '/pdf.pdf', $option);
    }, $outputDir . '/pdf.pdf');

    runConversion('pdf to ofd', function () use ($inputDir, $outputDir, $option): int {
        return Conversion::pdfToOfd($inputDir . '/word.pdf', '', $outputDir . '/pdf.ofd', $option);
    }, $outputDir . '/pdf.ofd');

    echo 'OK: all conversion smoke tests succeeded' . PHP_EOL;
} catch (\Throwable $e) {
    fwrite(STDERR, 'Conversion failed: ' . $e->getMessage() . PHP_EOL);
    exit(3);
} finally {
    LibraryManager::release();
}

/**
 * @param callable():int $convert
 */
function runConversion(string $name, callable $convert, string $expectedPath, bool $expectDirectoryOutput = false): void
{
    $before = snapshot($expectedPath);
    $code = $convert();
    echo $name . ': ' . $code . PHP_EOL;
    if (!ErrorCode::isSuccess($code)) {
        throw new RuntimeException($name . ' failed: ' . ErrorCode::describe($code) . " ({$code})");
    }

    if ($expectDirectoryOutput) {
        $created = array_diff(snapshot($expectedPath), $before);
        if (!$created) {
            throw new RuntimeException($name . ' finished but no output file was created in ' . $expectedPath);
        }
        return;
    }

    clearstatcache(true, $expectedPath);
    $size = is_file($expectedPath) ? filesize($expectedPath) : 0;
    if ($size === false || $size <= 0) {
        throw new RuntimeException($name . ' finished but output is missing or empty: ' . $expectedPath);
    }
}

/**
 * @return string[]
 */
function snapshot(string $path): array
{
    if (!is_dir($path)) {
        return [];
    }
    $files = [];
    foreach (glob($path . '/*') ?: [] as $file) {
        if (is_file($file)) {
            $files[] = $file;
        }
    }
    sort($files);
    return $files;
}

function removePath(string $path): bool
{
    if (is_file($path) || is_link($path)) {
        return unlink($path);
    }
    if (!is_dir($path)) {
        return true;
    }
    foreach (glob($path . '/*') ?: [] as $child) {
        if (!removePath($child)) {
            return false;
        }
    }
    return rmdir($path);
}