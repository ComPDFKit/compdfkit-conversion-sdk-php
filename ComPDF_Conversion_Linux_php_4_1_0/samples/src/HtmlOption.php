<?php
declare(strict_types=1);

namespace ComPDFKit\Conversion;

final class HtmlOption
{
    public const SINGLE_PAGE                = 0;
    public const SINGLE_PAGE_WITH_BOOKMARK  = 1;
    public const MULTI_PAGE                 = 2;
    public const MULTI_PAGE_WITH_BOOKMARK   = 3;
}