<?php
declare(strict_types=1);

namespace ComPDFKit\Conversion\Exception;

class CompdfException extends \RuntimeException
{
    /** @var int CSDKErrorCode value returned by the native SDK. */
    private int $sdkCode;

    public function __construct(string $message, int $sdkCode = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
        $this->sdkCode = $sdkCode;
    }

    public function getErrorCode(): int
    {
        return $this->sdkCode;
    }
}
