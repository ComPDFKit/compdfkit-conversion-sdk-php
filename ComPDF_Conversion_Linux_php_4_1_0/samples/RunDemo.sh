#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PKG_ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"
OUTPUT_DIR="$SCRIPT_DIR/output_files"

require_file() {
    if [[ ! -f "$1" ]]; then
        echo "missing required file: $1" >&2
        exit 1
    fi
}

require_command() {
    if ! command -v "$1" >/dev/null 2>&1; then
        echo "missing required command: $1" >&2
        exit 1
    fi
}

require_command php
require_file "$SCRIPT_DIR/compdfphp"
require_file "$SCRIPT_DIR/license.xml"
require_file "$PKG_ROOT/resource/models/documentai.model"
require_file "$SCRIPT_DIR/input_files/word.pdf"
require_file "$SCRIPT_DIR/input_files/excel.pdf"
require_file "$SCRIPT_DIR/input_files/powerpoint.pdf"

rm -rf "$OUTPUT_DIR"
mkdir -p "$OUTPUT_DIR"

cd "$PKG_ROOT"
"$SCRIPT_DIR/compdfphp" "$SCRIPT_DIR/direct_convert_demo.php"

printf '\nOutput file count: '
find "$OUTPUT_DIR" -type f | wc -l
printf 'Output directory: %s\n' "$OUTPUT_DIR"
