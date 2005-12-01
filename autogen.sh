#!/bin/sh

mkdir tmp/templates_c
touch tmp/templates_c/index.html
mkdir tmp/templates
touch tmp/templates/index.html
mkdir tmp/configs
touch tmp/configs/index.html
mkdir tmp/cache
touch tmp/cache/index.html

touch uploads/index.html
touch uploads/images/index.html

touch tmp/cache/SITEDOWN
chmod 777 tmp/cache/SITEDOWN

chmod 777 tmp/templates_c
chmod 777 tmp/templates
chmod 777 tmp/configs
chmod 777 tmp/cache
