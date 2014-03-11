#!/bin/sh

if [ ! -r version.php -o ! -r config.php ]; then
  echo "FATAL: Please run this script from the CMSMS install root directory";
  exit 1
fi

if [ ! -d tmp/cache ]; then
  echo "FATAL: Please run this script from the CMSMS install root directory";
  exit 1
fi

rm -rf tmp && mkdir -p tmp/cache && touch tmp/cache/index.html && mkdir -p tmp/templates_c && touch tmp/cache/index.html

echo "DONE";

