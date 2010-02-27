#!/bin/sh
# bourne shell script to create a release archive of a cmsms module
# within a cmsms install

# finds the name
_this=`basename $0`
_configfile=${HOME}/.CreateRelease.rc
_pwd=`pwd`
_name=`basename $_pwd`
_destdir=${HOME}
_version=0
_excludes="*~ #*# .svn CVS *.bak"
_tmpfile="/tmp/$_this.$$"
_yes=0
_svn=1

usage()
{
  echo "USAGE $_this [options]"
  echo "options:";
  echo "  -c|--configfile <filename> : source this file for config information"
  echo "  -d|--destdir <directory>   : place output file in directory"
  echo "  -n|--name  <name>          : use name for module name (use caution)"
  echo "  -v|--version <version>     : use version for version tag"
  echo "  -v|--exclude <pattern>     : exclude files matching this pattern"
  echo "                               from the resulting archive"
  echo "  -q|--quiet                 : assume non interactive mode"
  echo "  -s|--svn                   : skip the svn update step"
  echo "  -h|-help|--help            : this text"
  echo 
  echo "NOTE: This utility expects the module or the desired export directory"
  echo "      to be your current working directory. It also looks for a file"
  echo "      entitled <name>.module.php in the current working directory"
  echo "      If this file does not exist, the script will not proceed"
  echo
}

# read in any default config file if it exists
if [ -r ${_configfile} ]; then
 . ${_configfile}
fi

# process command line arguments
while [ $# -gt 0 ]; do
  case $1 in
    -c|--configfile)
      . $2
      shift 2
      continue
      ;;

    -d|--destdir)
      _destdir=$2
      shift 2
      continue
      ;;

    -n|--name)
      _name=$2
      shift 
      continue
      ;;

    -v|--version)
      _version=$2
      shift 2
      continue
      ;;

    -e|--exclude)
      _excludes="$_excludes $2"
      shift 2
      continue
      ;;
 
    -q|--quiet)
      _yes=1
      shift
      continue
      ;;

    -s|--svn)
      _svn=0
      shift
      continue
      ;;

    -h|--help|-help)
      usage;
      exit 0;
      ;;
  
    *)
      echo "FATAL: unrecognized option $1"
      usage
      exit 1
  esac
done

# validate command line arguments
if [ ! -d $_destdir -o ! -w $_destdir ]; then
  echo "FATAL: $_destdir does not exist or is not writable"
  exit 1
fi

# find the filename
_fn=${_name}.module.php
if [ ! -r ${_fn} ]; then
  echo "FATAL: Could not find ${_fn}"
  exit 1
fi

# find the version
# thanks to _SjG_ the perl regexp expert
_version2=`cat ${_fn} | perl -0777 -p -e 's/(.*?)function\s+GetVersion\(\)\s*\{\s*return\s*([^;]+)(.*)/$2/s' | cut -d\' -f2 | cut -d\" -f2`
if [ ${_version2:-notset} = notset ]; then
  echo "WARNING: could not auto-detect the version from the module.php file"
fi

#| perl -0777 -p -e 's/(.*?)function\s+GetVersion\(\)\s*\{\s*return\s*([^;]+)(.*)/$2/s' | cut -d\' -f2 | cut -d\" -f2
# asks for the veersion
while [ $_version = 0 ]; do
  echo -n "Please enter a version string like x.xx.x ($_version2): "
  read _v
  if [ ${_v:-notset} = notset ]; then
    _version=$_version2;
  else
    _version=$_v
  fi
done

# Ask for confirmation
if [ $_yes = 0 ]; then
  echo "Do you want to create a file named ${_name}-${_version}.tar.gz"
  echo "  in ${_destdir} using the contents of "
  echo -n "  $_pwd  (y/n)?"
  read _ans
  if [ ${_ans:-ns} = ns ]; then
    _ans=n
  fi
  if [ "$_ans" = 'y' -o "$_ans" = 'Y' -o "$_ans" = 'YES' -o "$_ans" = 'yes' ]; then
    _yes=1
  fi   
fi
if [ $_yes = 0 ]; then
  exit 0;
fi

# do an svn update
if [ $_svn = 1 ]; then
  echo "Performing svn update"
  svn update >/dev/null 2>&1
fi

# create dummy index.html files in each directory
#_dirs=`find . -type d | grep -v \.svn`
#for i in $_dirs ; do
#  if [ ! -f $i/index.php ]; then
#    echo '<!-- dummy -->' > ${i}/index.html
#  fi
#done

# make a temporary file of all the stuff we don't want in the archive
for i in $_excludes ; do
  echo $i >> $_tmpfile
done

# create the archive
_destname=${_destdir}/${_name}-${_version}.tar.gz
cd ..
tar zcvXf ${_tmpfile} ${_destname} ${_name}

# and cleanup
rm -f $_tmpfile 2>/dev/null
