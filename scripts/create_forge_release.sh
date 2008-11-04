#!/bin/sh

_owd=`pwd`
_this=`basename $0`
_gztype=5020
_othertype=9999
_txttype=8100
_processor=8000

_project=cmsmadesimple
_stable_package=1
_unstable_package=26
_tr_stable_package=618
_tr_unstable_package=26
_basedir=/tmp

#
# Read config file
#
if [ -r /etc/$_this ]; then
  . /etc/$_this
fi
if [ -r /usr/local/etc/$_this ]; then
  . /usr/local/etc/$_this
fi
if [ -r ~/.$_this ]; then
  . ~/.$_this
fi


#
# Process command line argument
#

#
# Find the source dir
#
cd $_basedir
_dirs=`ls -dt cmsmadesimple* | head -10`
_suspect=`ls -dt cmsmadesimple* | head -1`
clear
_done=0
while [ $_done = 0 ]; do
  echo "Possible Releases:";
  for _d in $_dirs ; do
    echo "   $_d"
  done
  echo -n "Please choose a release: ($_suspect)? ";
  read ans
  if [ ${ans:-notset} = notset ]; then
    _done=1
    _srcdir=$_basedir/$_suspect
  else
    if [ -d $_basedir/$ans ]; then
      _done=1
      _srcdir=$basedir/$ans
    fi
  fi
done

#
# Checks
#
if [ ${_username:-notset} = notset -o {$_password:-notset} = notset ]; then
  echo "FATAL: username and/or password not set"
  exit
fi
if [ ! -d $_srcdir ]; then
  echo "FATAL: source directory $_srcdir doesn't exist"
  exit
fi
_version=`basename $_srcdir | cut -d- -f2-`
if [ ${_version:-notset} = notset ]; then
  echo "FATAL: could not get version number"
  exit
fi

#
# Interactive portion
#
echo
echo "Create forge release for $_srcdir";
_done=0
while [ $_done = 0 ]; do
  echo -n "Is this a (S)table or (U)nstable Release? "
  read ans;
  if [ $ans = 's' -o $ans = 'S' ]; then
    _package=$_stable_package
    _tr_package=$_tr_stable_package;
    _done=1
  elif [ $ans = 'u' -o $ans = 'U' ]; then
    _package=$_unstable_package
    _tr_package=$_tr_unstable_package;
    _done=1
  else
    echo
  fi
done

# Double check the version number
_done=0
while [ $_done = 0 ]; do
  echo -n "Please enter version number ($_version): "
  read ans
  if [ ${ans:-notset} = notset ]; then
    _done=1
  else
    _version=$ans
    _done=1
  fi
done


#
# Begin Processing
#

# login
gforge login --username=${_username} --password=${_password} --project=${_project} >/dev/null

# add a release to the designated package
_id=`gforge frs addrelease --package=${_package} --name=${_version} | tail -3 | head -1 | cut -d" " -f2`
if [ $_tr_package ne $_package ]; then
  _tr_id=`gforge frs addrelease --package=${_tr_package} --name=${_version} | tail -3 | head -1 | cut -d" " -f2`
fi

_now=`date +%Y-%m-%d`
cd $_srcdir
for file in * ; do
  echo "INFO: Uploading $file"
  _isgz=`echo $file | grep -ce 'gz$'`
  _isdat=`echo $file | grep -ce 'dat$'`
  _islang=`echo $file | grep -c 'langpack'`

  if [ $_islang = 1 ]; then
    gforge frs addfile --package=${_tr_package} --release={$_tr_id} --file=$file --type=${_gztype} --date=${_now} >/dev/null
  else
    if [ ${_isgz} = 1 ]; then
      gforge frs addfile --package=${_package} --release=${_id} --file=$file --type=${_gztype} --date=${_now} >/dev/null
    elif [ $_isdat = 1 ]; then
      gforge frs addfile --package=${_package} --release=${_id} --file=$file --type=${_txttype} --date=${_now} >/dev/null
    else
      gforge frs addfile --package=${_package} --release=${_id} --file=$file --type=${_othertype} --date=${_now} >/dev/null
    fi
  fi
done

# logout
gforge logout >/dev/null
echo "INFO: Done"
