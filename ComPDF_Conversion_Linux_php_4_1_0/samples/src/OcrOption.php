<?php
declare(strict_types=1);

namespace ComPDFKit\Conversion;

final class OcrOption
{
    public const INVALID_CHARACTER                = 0;
    public const SCAN_PAGE                        = 1;
    public const INVALID_CHARACTER_AND_SCAN_PAGE  = 2;
    public const ALL                              = 3;
}