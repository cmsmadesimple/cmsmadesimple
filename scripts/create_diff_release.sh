#!/bin/sh

#
# Setup
#
_owd=`pwd`
_this=`basename $0`
_workdir=/tmp/$_this.$$
_owd=`pwd`
_clean=1
# basedir    (if set, specify the base directory to put generated releases).

# adjust the path
_t=`pwd`/scripts
if [ -d $_t/create_lang_packs.sh ]; then
  PATH="$_t:$PATH"
  export PATH
fi
if [ -x ./create_lang_packs.sh ]; then
  PATH="$_owd:$PATH"
  export PATH
fi
unset _t

# Check for config file(s)
# search /etc/create_cms_release.sh first
# then /usr/local/etc/create_cms_release.sh
# then ~/.create_cms_release.sh
_ext='create_cms_release.sh'
if [ -r /etc/$_ext ]; then
. /etc/$_ext
fi
if [ -r /usr/local/etc/$_ext ]; then
. /usr/local/etc/$_ext
fi
if [ -r ~/.$_ext ]; then
. ~/.$_ext
fi

#
# Process command line arguments
#
while [ $# -gt 0 ]; do
  case $1 in
    '--basedir' )
      basedir=$2
      shift 2
      continue
      ;;
    '--noclean' )
      _clean=0
      shift
      continue
      ;; 
  esac
done

#
# Post Setup
#
if [ ${basedir:-notset} = notset ]; then
  basedir='/tmp'
fi

#
# Look for versions
#
cd $basedir;
_newest=`ls -1dt cmsmadesimple* | head -1`
_all=`ls -1dt cmsmadesimple*`


#
# build a menu
#
_from=''
_to=$_newest;
_done=0
while [ $_done = 0 ]; do
  echo "Please Select the version(s) to create a diff against"
  echo "====================================================="
  _i=1;
  for _d in $_all ; do
    _x=''
    _xx=''
    if [ $_d = $_newest ]; then
      _x='(newest)'
    fi
    if [ $_d = $_to ]; then
      _xx='(to)'
    fi
    if [ ${_from:-notset} != notset ]; then
      if [ $_from = $_d ]; then
        _xx='(from)'
       fi
    fi

    printf '%3d : %s %s %s\n' $_i $_d $_x $_xx
    _i=`expr $_i + 1`
  done
  echo "---"
  echo -n "Select From and to Version (syntax f=## t=##), q to quit, or d for done ";
  read ans
  if [ $ans = 'q' -o $ans = 'quit' -o $ans = 'Q' -o $ans = 'QUIT' ]; then
    echo "Exiting..."
    exit
  fi
  if [ $ans = 'd' -o $ans = 'done' -o $ans = 'D' -o $ans = 'DONE' ]; then
    _done=1
  fi
  
  if [ $_done = 0 ]; then
    # parse the answer
    _t1=`echo $ans | cut -d' ' -f1 | cut -d= -f1`
    _t2=`echo $ans | cut -d' ' -f1 | cut -d= -f2`
    _t3=`echo $ans | cut -d' ' -f2 | cut -d= -f1`
    _t4=`echo $ans | cut -d' ' -f2 | cut -d= -f2`
    if [ $_t1 = 'f' ]; then
      _from=`ls -1dt cmsmadesimple* | head -$_t2 | tail -1`
    elif [ $_t1 = 't' ]; then
      _to=`ls -1dt cmsmadesimple* | head -$_t2 | tail -1`
    fi

    if [ $_t3 = 'f' ]; then
      _from=`ls -1dt cmsmadesimple* | head -$_t4 | tail -1`
    elif [ $_t3 = 't' ]; then
      _to=`ls -1dt cmsmadesimple* | head -$_t4 | tail -1`
    fi
  fi
done

echo
echo "Create diff from $_from to $_to"
_fromver=`echo $_from | cut -d- -f2-`
_tover=`echo $_to | cut -d- -f2-`

#1.  Expand the base version of $_from
echo "Processing base version";
mkdir $_workdir
_frombase=$basedir/$_from/${_from}-base.tar.gz 
cd $_workdir
mkdir from_base
cd from_base
tar zxf $_frombase

#2.  Expand the base version of $_to
_tobase=$basedir/$_to/${_to}-base.tar.gz 
cd $_workdir
mkdir to_base
cd to_base
tar zxf $_tobase

#3.  Create the diff
cd $_workdir
_changedfiles=''
_newfiles=''
_delfiles=''
diff -q -r from_base to_base > $_workdir/diffout_base.tmp 2>/dev/null
while read line ; do
  _c=`echo $line | grep -c '^Files'`
  _n=`echo $line | grep -c '^Only in to_base'`
  _d=`echo $line | grep -c '^Only in from_base'`
  _f=`echo $line | cut -d' ' -f4 | cut -d/ -f2-`

  _ci=`echo $line | grep -c 'install/'`

  if [ $_c = 1 -a $_ci = 0 ]; then
    _changedfiles="$_f $_changedfiles"
  elif [ $_n = 1 -a $_ci = 0 ]; then
    _newfiles="$_f $_newfiles"
  elif [ $_d = 1 ]; then
    _delfiles="$_f $_delfiles"
  fi
done < $_workdir/diffout_base.tmp

mkdir $_workdir/base_diff
cd $_workdir/to_base
for _f in $_changedfiles ; do
  tar cf - $_f | (cd $_workdir/base_diff && tar xf - )
done
for _f in $_newfiles ; do
  tar cf - $_f | (cd $_workdir/base_diff && tar xf - )
done
for _f in $_delfiles ; do
  _dn=`dirname $_f`
  _fn=`basename $_f`
  mkdir -p $_workdir/base_diff/$_dn
  touch $_workdir/base_diff/$_dn/$_fn
done

#4 tar it up
cd $_workdir/base_diff
tar zcf ${basedir}/${_to}/cmsmadesimple-base-diff-${_fromver}-${_tover}.tar.gz .

#5.  Expand the full version of $_from
echo Processing full version
mkdir $_workdir >/dev/null 2>/dev/null
_fromfull=$basedir/$_from/${_from}-full.tar.gz 
cd $_workdir
mkdir from_full
cd from_full
tar zxf $_fromfull

#6.  Expand the full version of $_to
_tofull=$basedir/$_to/${_to}-full.tar.gz 
cd $_workdir
mkdir to_full
cd to_full
tar zxf $_tofull

#7.  Create the diff
cd $_workdir
_changedfiles=''
_newfiles=''
_delfiles=''
diff -q -r from_full to_full > $_workdir/diffout_full.tmp 2>/dev/null
while read line ; do
  _c=`echo $line | grep -c '^Files'`
  _n=`echo $line | grep -c '^Only in to_full'`
  _d=`echo $line | grep -c '^Only in from_full'`
  _fn=`echo $line | cut -d' ' -f4 | cut -d/ -f2-`

  _ci=`echo $line | grep -c 'install/'`

  if [ $_c = 1 -a $_ci = 0 ]; then
    _changedfiles="$_fn $_changedfiles"
  elif [ $_n = 1 -a $_ci = 0 ]; then
    _p=`echo $line | cut -d' ' -f3 | cut -d: -f1 | cut -d/ -f2-`
    _fn=$_p/$_fn
    _newfiles="$_fn $_newfiles"
  elif [ $_d = 1 ]; then
    _p=`echo $line | cut -d' ' -f3 | cut -d: -f1 | cut -d/ -f2-`
    _fn=$_p/$_fn
    _delfiles="$_fn $_delfiles"
  fi
done < $_workdir/diffout_full.tmp

mkdir $_workdir/full_diff
cd $_workdir/to_full
for _f in $_changedfiles ; do
  tar cf - $_f | (cd $_workdir/full_diff && tar xf - )
done
for _f in $_newfiles ; do
  tar cf - $_f | (cd $_workdir/full_diff && tar xf - )
done
for _f in $_delfiles ; do
  _dn=`dirname $_f`
  _fn=`basename $_f`
  mkdir -p $_workdir/full_diff/$_dn >/dev/null
  touch $_workdir/full_diff/$_dn/$_fn
done

#8 tar it up
cd $_workdir/full_diff
tar zcf ${basedir}/${_to}/cmsmadesimple-full-diff-${_fromver}-${_tover}.tar.gz .

if [ $_clean = 1 ]; then
  echo "Cleaning up"
  cd $_owd
  rm -rf $_workdir
fi


echo "Done"
