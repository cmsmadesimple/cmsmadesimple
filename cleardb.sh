#!/bin/sh

echo "drop database cms3; create database cms3;" | mysql -u root cms3
rm config.php
touch config.php
chmod 777 config.php
rm -fr tmp/templates_c/*
rm -fr tmp/cache/*
