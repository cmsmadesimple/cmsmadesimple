#!/bin/sh

mkdir tmp/templates_c
touch tmp/templates_c/index.html
mkdir tmp/templates
touch tmp/templates/index.html
mkdir tmp/configs
touch tmp/configs/index.html
mkdir tmp/cache
touch tmp/cache/index.html

#echo "<!-- This is a dummy file for the purposes of making sure this directory is created properly.  You're welcome to remove it at any time. -->" > uploads/index.html
#echo "<!-- This is a dummy file for the purposes of making sure this directory is created properly.  You're welcome to remove it at any time. -->" > uploads/images/index.html

touch tmp/cache/SITEDOWN
chmod 777 tmp/cache/SITEDOWN

chmod 777 tmp/templates_c
chmod 777 tmp/templates
chmod 777 tmp/configs
chmod 777 tmp/cache
