#!/bin/bash

type -P phpunit &>/dev/null || { echo "PHPUnit is required, but is not installed. Aborting." >&2; exit 1; }

SCRIPTPATH="$(dirname $(cd "${0%/*}" 2>/dev/null; echo "$PWD"/"${0##*/}"))"

export APPLICATION_ENV=testing
$SCRIPTPATH/doctrine-cli.php build-all-reload
cd $SCRIPTPATH/../tests && phpunit && cd $SCRIPTPATH
export APPLICATION_ENV=