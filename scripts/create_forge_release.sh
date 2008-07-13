#!/bin/sh -x

_owd=`pwd`
_this=`basename $0`
_gztype=5020
_othertype=9999
_txttype=8100
_processor=8000

_project=portal
_stable_package=1
_unstable_package=26
_srcdir=/tmp/cmsmadesimple-1.4-beta1

#
# Read config file
#
if [ -r /etc/$_this ]; then
  . /etc/$_this
fi
if [ -r /usr/local/etc/$_this ]; then
  . /usr/local/etc/$_this
fi
if [ -r ~/.$this ]; then
  . /etc/$_this
fi


#
# Process command line argument
#

#
# Interactive portion
#

#
# Checks
#
if [ ${_username:-notset} = notset -o {$_password:-notset} = notset ]; then
  echo "FATAL: username and/or password not set
  exit
fi
if [ !-d $_srcdir ]; then
  echo "FATAL: source directory doesn't exist"
  exit
fi

# Get the version number
_version=`basename $_srcdir | cut -d- -f2-`
if [ $_version == '' ]; then
  echo "FATAL: could not get version number"
  exit
fi

# override package number
_package=607

#
# Begin Processing
#

# login
gforge login --username=${_username} --password=${_password} --project=${_project} >/dev/null

# add a release to the designated package
_id=`gforge frs addrelease --package=${_package} --name=${_version} | tail -3 | head -1 | cut -d" " -f2`

_now=`date +%Y-%m-%d`
cd $_srcdir
for file in * ; do
  echo "INFO: Uploading $file"
  _isgz=`echo $file | grep -ce 'gz$'`
  _isdat=`echo $file | grep -ce 'dat$'`

  if [ ${_isgz} = 1 ]; then
    gforge frs addfile --package=${_package} --release=${_id} --file=$file --type=${_gztype} --date=${_now} >/dev/null
  elif [ $_isdat = 1 ]; then
    gforge frs addfile --package=${_package} --release=${_id} --file=$file --type=${_txttype} --date=${_now} >/dev/null
  else
    gforge frs addfile --package=${_package} --release=${_id} --file=$file --type=${_othertype} --date=${_now} >/dev/null
  fi
done

# logout
gforge logout >/dev/null
echo "INFO: Done"