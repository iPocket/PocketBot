#!/bin/bash
DIR="$(cd -P "$( dirname "${BASH_SOURCE[0]}" )" && pwd)"
cd "$DIR"

exec php "./start.php" -config --enable-ansi $@