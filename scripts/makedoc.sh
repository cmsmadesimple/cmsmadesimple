#!/bin/sh

# NOTES
#  - this script requires phpdoc (phpDocumentor)
#     recommend install via pear (see the phpDocumentor website: http://www.phpdoc.org/docs/latest/getting-started/installing.html)
#  - this script assumes the cmsms phpDocumentor template (may need te copied into the phpdoc shared templates location
#     i.e: /usr/share/php/phpDocumentor/data/templates
#     this template is stored in svn upder scripts/phpDocumentor Template
#  - the phpDocumentor template relies on php's xslt extensions (in ubuntu based systems: sudo-apt-get install php5-xslt)
#  - this script must be executed from the CMSMS root (i.e: ./scripts/makedoc.sh)
#  - API documentation is output in the APIDOC subdirectory.

_config='scripts/makedoc.xml';
_exe=`which phpdoc`
if [ -z $_exe ]; then
  echo "FATAL: phpdoc must be installed";
  exit 1
fi
if [ ! -r config.php -o ! -r version.php ]; then
  echo "FATAL: This script must be executed from the CMSMS root path";
  exit 1
fi
if [ ! -r ${_config} ]; then
  echo "FATAL: phpdoc configuration file not found";
  exit 1
fi

trap cleanup INT

cleanup() {
  rm phpdoc.dist.xml
}

_recompile=0
while [ $# -gt 0 ]; do
  if [ $1 = '-r' ]; then 
    _recompile=1; 
    shift; 
  fi
done

if [ $_recompile ]; then
  rm -rf /tmp/phpdoc-twig-cache 2>/dev/null
fi

# temporarily copy the makedoc.xml file to the root directory
cp ${_config} phpdoc.dist.xml

# execute phpdoc
phpdoc | tee tmp/phpdoc.log

# cleanup
cleanup;
