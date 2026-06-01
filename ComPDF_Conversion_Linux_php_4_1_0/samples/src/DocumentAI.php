<?php
declare(strict_types=1);

namespace ComPDFKit\Conversion;

use FFI;
use FFI\CData;
use ComPDFKit\Conversion\Exception\CompdfException;
use ComPDFKit\Conversion\Internal\FFIFactory;

/**
 * Thin wrapper over the document-AI C entrypoints. Each method returns plain
 * PHP arrays/values and takes care of releasing the underlying native buffers
 * via the corresponding CPDF_*Release function.
 *
 * Mirrors the document_ai module of the Python SDK.
 */
final class DocumentAI
{
    private function __construct() {}

    /**
     * Run OCR on a PDF file. Returns an array of records:
     *   [['text' => string, 'confidence' => float, 'font_size' => float,
     *     'bbox' => [l,t,r,b], 'color' => [r,g,b]], ...]
     *
     * @param int[] $languages OCR language ints (see {@see OcrLanguage}).
     * @return array<int,array<string,mixed>>
     */
    public static function ocr(string $filePath, array $languages = [OcrLanguage::AUTO]): array
    {
        $ffi = FFIFactory::ffi();

        $count   = count($languages);
        $langArr = $ffi->new("int[$count]", false);
        for ($i = 0; $i < $count; $i++) {
            $langArr[$i] = (int) $languages[$i];
        }

        $detOut    = $ffi->new('da_ocr_det_t*', false);
        $recOut    = $ffi->new('da_ocr_rec_t*', false);
        $countOut  = $ffi->new('int', false);

        $pathArg = self::pathArg($filePath);

        try {
            $rc = $ffi->CPDF_Ocr(
                $pathArg,
                FFI::cast('int*', FFI::addr($langArr[0])),
                $count,
                FFI::addr($detOut),
                FFI::addr($recOut),
                FFI::addr($countOut)
            );
            self::check($rc, 'CPDF_Ocr');

            $records = [];
            $n = (int) $countOut->cdata;
            for ($i = 0; $i < $n; $i++) {
                $rec = $recOut[$i];
                $records[] = [
                    'text'       => FFI::string(FFI::addr($rec->text[0])),
                    'confidence' => (float) $rec->confidence,
                    'font_size'  => (float) $rec->font_size,
                    'type'       => (int) $rec->type,
                    'color'      => [(int) $rec->text_color_r, (int) $rec->text_color_g, (int) $rec->text_color_b],
                    'word_count' => (int) $rec->word_count,
                ];
            }

            $ffi->CPDF_OcrRelease($detOut, $recOut);
            return $records;
        } finally {
            FFI::free($langArr);
            FFI::free($detOut);
            FFI::free($recOut);
            FFI::free($countOut);
        }
    }

    /**
     * Detect page-layout regions. Returns [['object' => string, 'rect' => [l,t,r,b],
     * 'confidence' => float], ...].
     *
     * @return array<int,array<string,mixed>>
     */
    public static function layoutAnalysis(string $filePath): array
    {
        return self::detectionCall('CPDF_LayoutAnalysis', 'CPDF_LayoutAnalysisRelease', $filePath);
    }

    /**
     * Detect stamps in a PDF. Same shape as layoutAnalysis().
     *
     * @return array<int,array<string,mixed>>
     */
    public static function stampDetection(string $filePath): array
    {
        return self::detectionCall('CPDF_StampDetection', 'CPDF_StampDetectionRelease', $filePath);
    }

    public static function magicColor(string $filePath, string $outputPath): void
    {
        $ffi = FFIFactory::ffi();
        $rc = $ffi->CPDF_MagicColor(self::pathArg($filePath), self::pathArg($outputPath));
        self::check($rc, 'CPDF_MagicColor');
    }

    private static function detectionCall(string $sym, string $releaseSym, string $filePath): array
    {
        $ffi = FFIFactory::ffi();

        $out      = $ffi->new('da_detection_t*', false);
        $countOut = $ffi->new('int', false);

        try {
            $rc = $ffi->{$sym}(self::pathArg($filePath), FFI::addr($out), FFI::addr($countOut));
            self::check($rc, $sym);

            $results = [];
            $n = (int) $countOut->cdata;
            for ($i = 0; $i < $n; $i++) {
                $d = $out[$i];
                $results[] = [
                    'object'     => FFI::string(FFI::addr($d->object[0])),
                    'rect'       => [(int) $d->position->left, (int) $d->position->top,
                                     (int) $d->position->right, (int) $d->position->bottom],
                    'confidence' => (float) $d->confidence,
                ];
            }

            $ffi->{$releaseSym}($out);
            return $results;
        } finally {
            FFI::free($out);
            FFI::free($countOut);
        }
    }

    /**
     * @return string|CData char* on Linux/macOS, uint16_t* on Windows.
     */
    private static function pathArg(string $path)
    {
        return LibraryManager::isWindows() ? LibraryManager::toWString($path) : $path;
    }

    private static function check(int $rc, string $where): void
    {
        if (!ErrorCode::isSuccess($rc)) {
            throw new CompdfException("$where failed: " . ErrorCode::describe($rc), $rc);
        }
    }
}
