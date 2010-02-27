#!/bin/sh

echo "drop database cms; create database cms;" | mysql -u cms_user -pcms_pass cms
rm config.php
touch config.php
chmod 777 config.php
rm -fr tmp/templates_c/*
rm -fr tmp/cache/*
