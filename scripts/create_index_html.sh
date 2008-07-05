#!/bin/sh

_txt='`<!-- dummy index.html -->';

if [ ! -r $1/index.php ]; then 
  echo $_txt > $1/index.html
fi
