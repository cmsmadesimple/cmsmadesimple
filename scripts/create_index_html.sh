#!/bin/sh

if [ ! -r $1/index.php ]; then 
  touch $1/index.html
fi
