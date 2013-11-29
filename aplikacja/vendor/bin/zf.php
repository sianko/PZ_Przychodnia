#!/usr/bin/env sh
SRC_DIR="`pwd`"
cd "`dirname "$0"`"
cd "../zftool"
BIN_TARGET="`pwd`/zf.php"
cd "$SRC_DIR"
"$BIN_TARGET" "$@"
