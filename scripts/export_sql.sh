#!/bin/sh

_dbprefix=cms_
_this=`basename $0`

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
  if [ $_x = 0 -a $_x1 = 1 ]; then
    _tables="$_t $_tables"
  fi
done

# Dump all the tables
mysqldump ${_opts} --skip-quote-names --compact --no-create-info ${_dbname} ${_tables} | sed -e "s/^INSERT INTO ${_dbprefix}/INSERT INTO {DB_PREFIX}/g" 
