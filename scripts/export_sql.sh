#!/bin/sh

_dbprefix=cms_
_this=`basename $0`
_excludes="adminlog additional_htmlblob_users"

if [ $# -le 0 ]; then
  echo "Usage: ${_this} dbname [username] [password] [dbprefix]"
  exit 1
fi
_dbname=$1
if [ $# = 2 ]; then
 _uname=$2
fi
if [ $# = 3 ]; then
 _pw=$3
fi

_opts=''
if [ ${_uname:-notset} != 'notset' ]; then
  _opts="-u $_uname "
fi
if [ ${_pw:-notset} != 'notset' ]; then
  _opts="$_opts -p${_pw}"
fi
 
#1.  Get the list of tables
_tmp=`echo show tables | mysql ${_opts} ${_dbname}`
_tables=''
for _t in $_tmp ; do
  _x=`echo $_t | grep -ce '_seq$'`
  _x1=`echo $_t | grep -ce "^${_dbprefix}"`

  _x2=0
  for _e in $_excludes ; do
    if [ "${_dbprefix}${_e}" = $_t ]; then
     _x2=1
    fi
  done

  if [ $_x2 = 0 -a $_x = 0 -a $_x1 = 1 ]; then
    _tables="$_t $_tables"
  fi
done

# Dump all the tables
_tmp=1
mysqldump ${_opts} --compatible=mysql40 --skip-opt --skip-quote-names --compact --no-create-info ${_dbname} ${_tables} | sed -e "s/^INSERT INTO ${_dbprefix}/INSERT INTO {DB_PREFIX}/g" > /tmp/$_this.$$.$_tmp

# post processing
cat /tmp/$_this.$$.$_tmp | grep -v "siteprefs VALUES ('sitename"

# cleanup
rm /tmp/$_this.$$.$_tmp