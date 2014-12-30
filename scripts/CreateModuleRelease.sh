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
_excludes='*~ #*# .#* .svn CVS *.bak .git* *.tmp .cms_ignore *.swp _internal phpdoc.xml'
_tmpdir="/tmp/$_this.$$"
_yes=0
_svn=1
_tag=1
_doc=1
_salt=''
_checksums=1

usage()
{
  echo "USAGE $_this [options]"
  echo "options:";
  echo "  -t|--notag                 : do not create a tag for this release";
  echo "  -D|--nodoc                 : do not attempt to run phpdocs";
  echo "  -c|--configfile <filename> : source this file for config information"
  echo "  -d|--destdir <directory>   : place output file in directory"
  echo "  -n|--name  <name>          : use name for module name (use caution)"
  echo "  -v|--version <version>     : use version for version tag"
  echo "  -e|--exclude <pattern>     : exclude files matching this pattern"
  echo "                               from the resulting archive"
  echo "  -q|--quiet                 : assume non interactive mode"
  echo "  -s|--svn                   : skip the svn update step"
  echo "  -m|--checksums             : skip the checksum generation step."
  echo "  -h|-help|--help            : this text"
  echo
  echo "NOTE: This utility expects the module or the desired export directory"
  echo "      to be your current working directory. It also looks for a file"
  echo "      entitled <name>.module.php in the current working directory"
  echo "      If this file does not exist, the script will not proceed"
  echo
  echo "NOTE: This function will look in the home directory for a file called .CreateRelease.rc"
  echo "      which can contain default values for the various argument of this script."
  echo "      This file is a standard bash script that is 'included' by this script."
  echo "      variables that can be adjusted are:"
  echo "        _doc = turn on or off phpdoc generation."
  echo "        _tag = turn on or off svn tag generation."
  echo "        _destdir = The destination directory."
  echo "        _excludes = Standard exlude patterns."
  echo "        _yes = if value is 1, turn on quiet mode."
  echo "        _svn = if value is 0 turn off svn update option."
  echo "        _checksums = if value is 0 do not attempt to create checksums."
  echo "NOTE:  Standard excludes are: *~ #*# .#* .svn CVS *.bak .git* *.tmp .cms_ignore *.swp _internal phpdoc.xml"
  echo
  echo "  The special file .cms_ignore in any directory to exclude additional files in that directory and files below it (one pattern per line)"
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

    -D|--nodoc)
      _doc=0
      shift
      continue
      ;;

    -t|--notag)
      _tag=0
      shift
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

    -m|--checksums)
      _checksums=0
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

_vc_type=''
if [ -d .svn ]; then
  _vc_type='svn'
elif [ -d .git ]; then
  if [ -d .git/svn ]; then
    _vc_type='gitsvn'
  fi
fi

# do a phpdoc generation
if [ $_doc = 1 -a -r phpdoc.xml ]; then
    _t=`which phpdoc`;
    if [ -z $_t ]; then
      echo "WARNING: phpdoc.xml file found, but could not find phpdoc executable.";
    else
	_o="/tmp/phpdoc.$_this.$$";
	_tpl="${_name} ${_version}"
	echo "INFO: generating phpdocs. Output from this command will be stored in $_o";
	phpdoc --defaultpackagename $_name --title "${_tpl}" > $_o;
    fi
else
    _excludes="$_excludes apidoc apidocs"
fi

# do an svn update
if [ $_svn = 1 -a ${_vc_type:-bad} != 'bad' ]; then
  if [ $_vc_type = 'gitsvn' ]; then
    echo "INFO: Performing git-svn rebase"
    git svn rebase
  else
    echo "INFO: Performing svn update"
    svn update >/dev/null 2>&1
  fi
fi

# expand the excludes by the contents of the .cms_ignore file(s)
_x=`find . -name '.cms_ignore' 2>/dev/null`;
for x in $_x ; do
 _f=`basename $x`
 _d=`dirname $x`
 _d=`basename $_d`
 _fi=`cat $x`
 if [ $_d != '.' ]; then
   _d="${_d}/"
 else
   _d="${_name}/"
 fi
 for x2 in $_fi ; do
   _excludes="$_excludes ${_d}${x2}";
 done
done

# create dummy index.html files in each directory
#_dirs=`find . -type d | grep -v \.svn`
#for i in $_dirs ; do
#  if [ ! -f $i/index.html ]; then
#    echo '<!-- dummy -->' > ${i}/index.html
#  fi
#done


# do an svn tag command
if [ $_tag = 1 -a ${_vc_type:-bad} != 'bad' ]; then
   _newtag=''
   if [ ${_vc_type:-bad} = 'svn' ]; then
     _repo=`svn info | grep 'Root\:' | cut -d' ' -f3-`
     _newtag=${_repo}/tags/v${_version}
   else
     _repo=`git svn info | grep 'Root\:' | cut -d' ' -f3-`
     _newtag="v${_version}"
   fi
   echo -n "Do you want to create a tag at: ${_newtag} (Y/n)?"
   read _ans
   if [ ${_ans:-ns} = ns ]; then
     _ans=y
   fi
   if [ "$_ans" = 'y' -o "$_ans" = 'Y' -o "$_ans" = 'YES' -o "$_ans" = 'yes' ]; then
     _msg="${_name} version ${_version}"
     if [ ${_vc_type:-bad} = 'svn' ]; then
       echo "Performiong svn tag";
       svn copy -m "${_msg}" . ${_newtag}
     else
       echo "Performiong git svn branch";
       git svn branch -t -m "${_msg}" ${_newtag} .
     fi
   fi
fi

# make a temporary file of all the stuff we don't want in the archive
mkdir $_tmpdir
for i in $_excludes ; do
  echo $i >> ${_tmpdir}/excludes.dat
done

_destname=${_destdir}/${_name}-${_version}.tar.gz
_tmpdest=${_tmpdir}/files.tgz
cd ..
tar zcvXf ${_tmpdir}/excludes.dat ${_tmpdest} ${_name}
mkdir $_tmpdir/tmp
cd ${_tmpdir}/tmp
tar zxf ${_tmpdir}/files.tgz

if [ $_checksums = 1 ]; then
    # create the archive checksums
    # _c.dat contains checksum values for each individual file
    # _d.dat contains checksum values for the _c.dat file.
  cd ${_name}
  find . -name '*php' | grep -v lang > ${_tmpdir}/file.lst
  while read _line ; do
    _md5=`md5sum $_line | cut -d" " -f1`
    _fn=`echo $_line | cut -d/ -f2-`
    _t=`echo "${_md5}::${_fn}::${_version}" | md5sum | cut -d" " -f1`
    _t2=`echo "${_t}:${_salt}" | md5sum -b | cut -d" " -f1`
    echo "${_t}::${_t2}" >> _c.dat
  done < ${_tmpdir}/file.lst
  _t3=`md5sum -b _c.dat | cut -d" " -f1`
  echo "${_salt}::${_t3}" | md5sum -b > _d.dat
  cd ..
fi

# create the archive.
tar zcf ${_destname} ${_name}

# and cleanup
#rm -rf $_tmpdir 2>/dev/null
