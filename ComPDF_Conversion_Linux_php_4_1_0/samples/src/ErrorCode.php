<?php
declare(strict_types=1);

namespace ComPDFKit\Conversion;

/**
 * CSDKErrorCode mirror. Keep in sync with samples/src/Internal/cdef/*.h.
 */
final class ErrorCode
{
    public const SUCCESS = 0;
    public const CANCEL = 1;
    public const FILE_ERROR = 2;
    public const PDF_PASSWORD = 3;
    public const PDF_PAGE = 4;
    public const PDF_FORMAT = 5;
    public const PDF_SECURITY = 6;
    public const OUT_OF_MEMORY = 7;
    public const IO_ERROR = 8;
    public const COMPRESS = 9;
    public const LICENSE_INVALID = 20;
    public const LICENSE_EXPIRE = 21;
    public const LICENSE_UNSUPPORTED_PLATFORM = 22;
    public const LICENSE_UNSUPPORTED_ID = 23;
    public const LICENSE_UNSUPPORTED_DEVICE = 24;
    public const LICENSE_PERMISSION_DENY = 25;
    public const LICENSE_UNINITIALIZED = 26;
    public const LICENSE_ILLEGAL_ACCESS = 27;
    public const LICENSE_FILE_READ_FAILED = 28;
    public const LICENSE_OCR_PERMISSION_DENY = 29;
    public const NO_TABLE = 40;
    public const OCR_FAILURE = 41;
    public const CONVERTING = 60;
    public const INVALID_ARG = 80;
    public const INVALID_HANDLE = 81;
    public const MODEL_INVALID_FORMAT = 82;
    public const MODEL_FUNCTION_UNSUPPORTED = 83;
    public const MODEL_FORMAT_UNSUPPORTED = 84;
    public const MODEL_SDK_MISMATCH = 85;
    public const IMAGE_DATA_EMPTY = 86;
    public const IMAGE_WH_ERROR = 87;
    public const IMAGE_UNSUPPORTED_FORMAT = 88;
    public const IMAGE_INVALID = 89;
    public const EXPIRE = 90;
    public const MISSING_ARG = 91;
    public const LICENSE_UNSUPPORTED_API = 92;
    public const LICENSE_MISMATCH = 93;
    public const INVALID_TABLE = 94;
    public const UNKNOWN = 100;

    private const NAMES = [
        0 => 'Success',
        1 => 'Cancel',
        2 => 'File error',
        3 => 'PDF password error',
        4 => 'PDF page error',
        5 => 'PDF format error',
        6 => 'PDF security error',
        7 => 'Out of memory',
        8 => 'I/O error',
        9 => 'Compression error',
        20 => 'License invalid',
        21 => 'License expired',
        22 => 'License unsupported platform',
        23 => 'License unsupported ID',
        24 => 'License unsupported device',
        25 => 'License permission denied',
        26 => 'License uninitialized',
        27 => 'License illegal access',
        28 => 'License file read failed',
        29 => 'License OCR permission denied',
        40 => 'No table',
        41 => 'OCR failure',
        60 => 'Converting error',
        80 => 'Invalid argument',
        81 => 'Invalid handle',
        82 => 'Model invalid format',
        83 => 'Model function unsupported',
        84 => 'Model format unsupported',
        85 => 'Model/SDK version mismatch',
        86 => 'Image data empty',
        87 => 'Image width/height error',
        88 => 'Image unsupported format',
        89 => 'Image invalid',
        90 => 'Expired',
        91 => 'Missing argument',
        92 => 'License unsupported API',
        93 => 'License mismatch',
        94 => 'Invalid table',
        100 => 'Unknown error',
    ];

    private function __construct() {}

    public static function describe(int $code): string
    {
        return self::NAMES[$code] ?? sprintf('Error %d', $code);
    }

    public static function isSuccess(int $code): bool
    {
        return $code === self::SUCCESS;
    }
}
