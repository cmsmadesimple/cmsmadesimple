#!/bin/sh

echo ------------------------------------
echo Running Cleanup Script
echo ------------------------------------

sh autogen.sh

rm -fr build
rm -fr config.php
rm -fr autogen.sh
rm -fr makedoc.sh
rm -fr images/cms/*.svg
rm -fr admin/editconfig.php
rm -fr lib/adodb
rm -fr lib/preview.functions.php
rm -fr test
rm -fr command.php
rm -fr lib/pear/php_shell
find . -depth -type d -name .svn -exec rm -fr {} \;
find . -type d -exec chmod 775 {} \;
find . -type f -name "*.php" chmod -x {} \;
rm -fr release-cleanup.sh

echo ------------------------------------
echo Done!
echo ------------------------------------
