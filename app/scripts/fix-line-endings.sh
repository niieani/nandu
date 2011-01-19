#!/bin/bash

find ../application/ -name "*.php" -exec dos2unix {} \;
find ../library/doctrine/ -name "*.php" -exec dos2unix {} \;
find ../library/vendor/ -name "*.php" -exec dos2unix {} \;
find ../library/void/ -name "*.php" -exec dos2unix {} \;
find ../tests/ -name "*.php" -exec dos2unix {} \;

dos2unix ../public/.htaccess