<?php
declare(strict_types=1);

namespace ComPDFKit\Conversion;

use FFI;
use FFI\CData;

/**
 * High-level holder for the optional native callbacks accepted by every
 * `CPDF_StartPDFTo*` function. Equivalent to the C `CConvertCallback` struct
 * (see `common/com_config_macros.h` in the SDK source tree).
 *
 * Assign any subset of callables; unset slots are reported to the SDK as
 * `NULL`, restoring the built-in behaviour for that hook.
 *
 * Signatures (all PHP callables):
 * - $onProgress    : function (int $currentPage, int $totalPage): void
 * - $onCancel      : function (): bool                    // true cancels the job
 * - $onOcr         : function (string $imagePath): bool   // true delegates OCR to caller
 * - $onOcrResult   : function (): string                  // JSON for the last image
 * - $onLayout      : function (string $imagePath): bool
 * - $onLayoutResult: function (): string
 * - $onTable       : function (string $imagePath): bool
 * - $onTableResult : function (): string
 *
 * All callbacks are invoked synchronously from the same OS thread that called
 * the conversion function — PHP FFI does not support cross-thread callbacks.
 */
final class ConvertCallback
{
    /** @var callable|null */ public $onProgress = null;
    /** @var callable|null */ public $onCancel = null;
    /** @var callable|null */ public $onOcr = null;
    /** @var callable|null */ public $onOcrResult = null;
    /** @var callable|null */ public $onLayout = null;
    /** @var callable|null */ public $onLayoutResult = null;
    /** @var callable|null */ public $onTable = null;
    /** @var callable|null */ public $onTableResult = null;

    /** @var array<int,mixed> Trampolines kept alive for the SDK call. */
    private array $_keepAlive = [];

    /** @var array<int,CData> char[] buffers returned by get_*_result hooks. */
    private array $_resultBuffers = [];

    /**
     * Build a native `CConvertCallback` cdata. The PHP caller MUST keep both
     * the returned cdata and this ConvertCallback instance reachable until
     * the conversion call returns; otherwise the trampolines may be GC'd
     * mid-call and the SDK will crash.
     */
    public function buildCData(FFI $ffi): CData
    {
        // Reset state for a fresh native call.
        $this->_keepAlive = [];
        $this->_resultBuffers = [];

        $cb = $ffi->new('CConvertCallback');
        $cb->handle = null;
        $cb->cancel = null;
        $cb->progress = null;
        $cb->ocr = null;
        $cb->layout = null;
        $cb->table = null;
        $cb->get_ocr_result = null;
        $cb->get_layout_result = null;
        $cb->get_table_result = null;

        if ($this->onProgress !== null) {
            $fn = $this->onProgress;
            $t = function (int $current, int $total) use ($fn): void {
                ($fn)($current, $total);
            };
            $cb->progress = $t;
            $this->_keepAlive[] = $t;
        }

        if ($this->onCancel !== null) {
            $fn = $this->onCancel;
            $t = function () use ($fn): bool {
                return (bool) ($fn)();
            };
            $cb->cancel = $t;
            $this->_keepAlive[] = $t;
        }

        $this->bindTrigger($cb, 'ocr', $this->onOcr);
        $this->bindTrigger($cb, 'layout', $this->onLayout);
        $this->bindTrigger($cb, 'table', $this->onTable);

        $this->bindResult($cb, 'get_ocr_result', $this->onOcrResult);
        $this->bindResult($cb, 'get_layout_result', $this->onLayoutResult);
        $this->bindResult($cb, 'get_table_result', $this->onTableResult);

        return $cb;
    }

    private function bindTrigger(CData $cb, string $field, $callable): void
    {
        if ($callable === null) {
            return;
        }
        $t = function ($imagePath) use ($callable): bool {
            $path = $imagePath === null ? '' : FFI::string($imagePath);
            return (bool) ($callable)($path);
        };
        $cb->{$field} = $t;
        $this->_keepAlive[] = $t;
    }

    private function bindResult(CData $cb, string $field, $callable): void
    {
        if ($callable === null) {
            return;
        }
        $buffers = &$this->_resultBuffers;
        $t = function () use ($callable, &$buffers): CData {
            $json = (string) ($callable)();
            $len = strlen($json);
            $buf = FFI::new('char[' . ($len + 1) . ']', false);
            if ($len > 0) {
                FFI::memcpy($buf, $json, $len);
            }
            $buf[$len] = "\0";
            $buffers[] = $buf; // keep alive for the SDK to read
            return FFI::cast('const char*', FFI::addr($buf[0]));
        };
        $cb->{$field} = $t;
        $this->_keepAlive[] = $t;
    }
}
