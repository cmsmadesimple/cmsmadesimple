#!/bin/sh

echo ------------------------------------
echo Running Cleanup Script
echo ------------------------------------

rm -fr CHECKLIST
rm -fr config.php
rm -fr config.php.sample
rm -fr autogen.sh
rm -fr mpd.sql
rm -fr mysql.sql
rm -fr images/*.svg
rm -fr smarty/cms/cache/*
rm -fr smarty/cms/templates_c/*
find -depth -type d -name .svn -exec rm -fr {} \;
rm -fr release-cleanup.sh

echo ------------------------------------
echo Done!
echo ------------------------------------
