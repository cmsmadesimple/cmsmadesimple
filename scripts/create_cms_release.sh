#!/bin/sh

#
# Functions
#
create_checksum_file()
{
    find . -type f -exec md5sum -b {} \; >$1 2>/dev/null
}

cleanup()
{
    if [ ${noclean:-notset} = notset ]; then
	echo "Cleaning up"
	cd $_owd
	rm -rf $_workdir
    fi
}

#
# Setup
#
_this=`basename $0`
_svn=http://svn.cmsmadesimple.org/svn/cmsmadesimple/branches/1.6.x
_workdir=/tmp/$_this.$$
_owd=`pwd`
_rmfiles='CHECKLIST scripts build config.php autogen.sh mpd.sql mysql.sql makedoc.sh cleardb.sh generatedump.php images/cms/*.svg svn-propset* find-mime admin/lang/*sh admin/lang/*.bat admin/lang/*pl admin/editconfig.php lib/adodb lib/preview.functions.php plugins/cache release-cleanup.sh blank.htm .gitignore';
_cmsurl='http://svn.cmsmadesimple.org/svn/cmsmadesimple';
# basedir    (if set, specify the base directory to put generated releases).
# nohtaccess (if set, disable htaccess generation)
# noremove   (if set, disable removal of files that shouldn't be shipped with the distribution)
# noindex    (if set, disable index.html creation)
# noperms    (if set, disable permissions adjusting)
# noask      (if set, disable confirmation check)
# noclean    (if set, don't perform cleanup of temporary files)
# notag      (if set, don't create a tag for this release)

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
if [ -r /etc/$_this ]; then
. /etc/$_this
fi
if [ -r /usr/local/etc/$_this ]; then
. /usr/local/etc/$_this
fi
if [ -r ~/.$_this ]; then
. ~/.$_this
fi
if [ -r ~/.${_this}.stat ]; then
_svn=`cat ~/.${_this}.stat`
fi

#
# Process command line arguments
#
while [ $# -gt 0 ]; do
  case $1 in
    '--notag' )
      notag=1
      shift 1
      continue
      ;;
    '--noclean' )
      noclean=1
      shift 1
      continue
      ;;
    '--noindex' )
      noindex=1
      shift 1
      continue
      ;;
    '--noremove' )
      noremove=1
      shift 1
      continue
      ;;
    '--nohtaccess' )
      noremove=1
      shift 1
      continue
      ;;
    '--noperms' )
      noperms=1
      shift 1
      continue
      ;;
    '--noask' )
      noask=1
      shift 1
      continue
      ;;
    '--basedir' )
      basedir=$2
      shift 2
      continue
      ;;
  esac
done

#
# Ask for the root url to export
#
if [ ${noask:-notset} = notset ]; then
  _done=0
  clear
  while [ $_done = 0 ]; do
    echo "Export CMS Source"
    echo "=================="
    echo -n "Enter SVN URL ($_svn): "
    read ans
    if [ ${ans:-notset} = notset ]; then
      _done=1
    else
      _svn=$ans
      _done=1
    fi
  done
fi

# Export the directory
echo
echo "Exporting: $_svn";
svn export $_svn $_workdir >/dev/null
 
if [ ${basedir:-notset} = notset ]; then
  basedir='/tmp'
fi

# Get the version
cd $_workdir
_version=`grep '^\$CMS_VERSION' version.php | grep -v _NAME | cut -d\" -f2`
_destdir=${basedir}/cmsmadesimple-$_version

if [ ${noask:-notset} = notset ]; then
  echo "Create CMS Release"
  echo "=================="
  echo "VERSION: $_version"
  echo "DESTDIR: $_destdir"
  echo "SVN URL: $_svn"
  echo -n "CREATE htaccess files? ";
  if [ ${nohtaccess:-notset} = notset ]; then echo 'YES'; else echo "NO"; fi;
  echo -n "REMOVE system utility files? ";
  if [ ${noremove:-notset} = notset ]; then echo 'YES'; else echo "NO"; fi;
  echo -n "CREATE index.html files? ";
  if [ ${noindex:-notset} = notset ]; then echo 'YES'; else echo "NO"; fi;
  echo -n "DO POST Processing cleanup? ";
  if [ ${noclean:-notset} = notset ]; then echo 'YES'; else echo "NO"; fi;
  echo -n "CREATE CMS TAG ${_cmsurl}/tags/version-$_version? ";
  if [ ${notag:-notset} = notset ]; then echo 'YES'; else echo "NO"; fi;

  echo
  echo -n "Is this correct? (yes/no) ";
  read ans
  case $ans in
    y|Y|yes|YES|Yes)
      true;
      ;;

    *)
      if [ ${noclean:-notset} != notset ]; then
        echo "Cleaning up"
        cd $_owd
        rm -rf $_workdir
      fi
      echo "Exiting..."
      cleanup;
      exit;
      ;;
  esac
  echo
fi

# Clean up files that are not distributed
# or don't need to be in the release
if [ ${noremove:-notset} = notset ]; then
  echo "Clean un-necessary files"
  for _f in $_rmfiles ; do
    rm -rf $_f >/dev/null 2>&1
  done
fi

# Create CMS tag
if [ ${notag:-notset} = notset ]; then
  echo "Create CMS Tag cmsmadesimple/tags/version-$_version"
  _desturl=${_cmsurl}/tags/version-${_version}
  svn import -m "--Release: $_version --" $_desturl --no-auto-props >/dev/null
fi

# Create necessary files
mkdir -p tmp/cache tmp/templates_c tmp/configs
touch tmp/cache/SITEDOWN
if [ ${noindex:-notset} = notset ]; then
  echo "Create index.html files"
  find * -type d -exec create_index_html.sh {} \;
fi

# Clean up permissions
if [ ${noperms:-notset} = notset ]; then
  echo "Cleaning Permissions"
  find . -type f -exec chmod 644 {} \;
  find . -type d -exec chmod 755 {} \;
fi

# Create the full package checksum file
echo "Creating checksum file"
mkdir $_destdir
create_checksum_file $_destdir/cmsmadesimple-$_version-full-checksum.dat

# Create the full package
echo "Creating full package"
tar zcf $_destdir/cmsmadesimple-$_version-full.tar.gz .

# run the create_lang_packs script
echo "Creating language packs"
create_lang_packs.sh -s ${_workdir} -d $_destdir >/dev/null

# Create the base (english) package checksum file
echo "Creating checksum file again"
create_checksum_file $_destdir/cmsmadesimple-$_version-english-checksum.dat

# Create the base package
# it is created after the langpacks are created, because the langpack
# generation removes files from the working directory.
echo "Creating base (english only) package"
tar zcf $_destdir/cmsmadesimple-$_version-english.tar.gz .

# Create a final checksum data file
cd $_destdir
md5sum -b * >cmsmadesimple-$_version-checksums.dat 2>/dev/null

# cleanup
cleanup

echo $_svn > ~/.${_this}.stat
echo "Done: All release files should be in $_destdir";
