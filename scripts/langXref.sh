#!/bin/sh
# a boring little script to cross reference a .module.php file with a lang file
# to find out which lang['entries'] are missing and are extra.
# usage: langXref.sh lang_file php_file ...


_lang=$1
shift
_lang_entries=`grep -e '^\\$lang' $_lang | cut -d\' -f2 | cut -d\" -f2 | sort | uniq`
_code_entries=`cat $* | grep Lang | cut -d\' -f2 | cut -d\" -f2 | sort | uniq`

# find all the entries in code but not in lang file
echo "Missing entries in lang file"
echo "----------------------------"
for c in $_code_entries ; do
  _x=`echo $_lang_entries | grep -c $c`
  if [ $_x = 0 ]; then
    echo $c
  fi
done

# find all the entries in lang file but not in code
echo
echo "Extra entries in lang file"
echo "--------------------------"
for l in $_lang_entries ; do
  _x=`echo $_code_entries | grep -c $l`
  if [ $_x = 0 ]; then
    echo $l
  fi
done 
