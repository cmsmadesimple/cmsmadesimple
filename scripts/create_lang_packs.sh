#!/bin/sh
# Create language packs for CMS Made Simple distribution
#

#
# functions
#
build_file_list()
{
  cd $_startdir
  thelang=$1
  slang=$2
  _x="*$thelang*";
  _files_1=`find . -name $_x 2>/dev/null | grep -v .svn`
  _files_2=''
  _files_3=''
  if [ $slang != 'en' ]; then
    _x="$slang.js";
    _files_2=`find . -name $_x 2>/dev/null | grep -v .svn`
    _x="${slang}_dlg.js"
    _files_3=`find . -name $_x 2>/dev/null | grep -v .svn`
  fi
  _files1="$_files_1 $_files_2 $_files_3"

  # preprocess the files
  _files=''
  for onefile in $_files1 ; do
    if [ `echo $onefile | grep -c admin/lang` = 1 ]; then
      _files="$onefile $_files" 
    elif [ `echo $onefile | grep -c install/lang` = 1 ]; then
      _files="$onefile $_files"
    elif [ `echo $onefile | grep -c lib/nls` = 1 ]; then
      _files="$onefile $_files"
    else
      for coremod in $_coremodules ; do
        if [ `echo $onefile | grep -c $coremod` = 1 ]; then
          _files="$onefile $_files"
        fi
      done
    fi
  done

  eval $3=\"$_files\"
  return 1;
}

create_file_archive()
{
  _destfile=$1
  eval list=\$$2

  mkdir $_workdir
  cd $_startdir
  for file in $list ; do
    dn=`dirname $file`
    mkdir -p $_workdir/$dn
    cp -rp $file $_workdir/$dn
  done

  cd $_workdir
  tar zcf $_destfile .
  cd $_startdir
  rm -rf $_workdir 2>/dev/null
}

delete_files_in_list()
{
  eval list=\$$1
  cd $_startdir
  for file in $list ; do
    rm -rf $file 2>/dev/null
  done
}

#
# Setup
#
_this=`basename $0`
_startdir=`pwd`
_owd=`pwd`
_workdir=/tmp/$_this.$$
_destdir=/tmp
_langs=''
_coremodules='Search CMSPrinting News MicroTiny ModuleManager ThemeManager MenuManager FileManager CMSMailer'

# Process command line arguments
while [ $# -gt 1 ]; do
  case $1 in
    "-s")
    _startdir=$2
    shift 2
    ;;
  
    "-d")
    _destdir=$2
    shift 2
  esac  
done

#
# Initialization
#
cd $_startdir

# get the CMS version
_version=`grep '^\$CMS_VERSION' version.php | grep -v _NAME | cut -d\" -f2`

# find the list of languages (except english)
cd admin/lang/ext
_langs=`ls -1 | grep -v index`

#
# Process each language
#
for onelang in $_langs ; do
  shortlang=`echo $onelang | cut -d_ -f1`
  if [ $onelang != 'en_US' ]; then
    echo "Processing $onelang / $shortlang"

    # build the file list
    file_list=''
    build_file_list $onelang $shortlang file_list
 
    # create the file archive
    _destfile=$_destdir/cmsmadesimple-$_version-langpack-$onelang.tar.gz
    create_file_archive $_destfile file_list
  
    # remove the files
    delete_files_in_list file_list

    echo 
    echo
  fi
done

#
# cleanup
#
rm -rf $_workdir  2>/dev/null
cd $_owd
