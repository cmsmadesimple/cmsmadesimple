#!/bin/sh

# Setup
_this=`basename $0`
_svn=http://svn.cmsmadesimple.org/svn/cmsmadesimple/branches/1.2.x
_workdir=/tmp/$_this.$$
_owd=`pwd`
# nohtaccess (if set, disable htaccess generation)
# noperms    (if set, disable permissions adjusting)
# noclean    (if set, don't cleanup)

# Check for config file
if [ -r ~/.$_this ]; then
. ~/.$_this
fi

# Process command line arguments


echo "Export CMS Source"
echo "=================="
echo "SVN URL: $_svn"
echo "Is this correct? (yes/no)";
read ans
case $ans in
 y|Y|yes|YES|Yes)
   true;
   ;;

 *)
   if [ ${noclean:-notset} != notset ];
     echo "Cleaning up"
     cd $_owd
     rm -rf $_workdir
   fi
   echo "Exiting..."
   exit;
   ;;
esac

# Export the directory
echo
echo "Exporting: $_svn";
svn export $_svn $_workdir >/dev/null
 
# Get the version
cd $_workdir
_version=`grep '^\$CMS_VERSION' version.php | grep -v _NAME | cut -d\" -f2`
_destdir=/tmp/cmsmadesimple-$_version

echo "Create CMS Release"
echo "=================="
echo "VERSION: $_version"
echo "DESTDIR: $_destdir"
echo "SVN URL: $_svn"
echo
echo "Is this correct? (yes/no)";
read ans
case $ans in
 y|Y|yes|YES|Yes)
   true;
   ;;

 *)
   if [ ${noclean:-notset} != notset ];
     echo "Cleaning up"
     cd $_owd
     rm -rf $_workdir
   fi
   echo "Exiting..."
   exit;
   ;;
esac

# Clean up permissions
echo
if [ ${noperms:-notset} != notset ];
  echo "Cleaning Permissions"
  find . -type f -exec chmod 644 {} \;
  find . -type d -exec chmod 755 {} \;
fi

# Create the full package
echo "Creating full package"
mkdir $_destdir
tar zcf $_destdir/cmsmadesimple-$_version-full.tar.gz .

# run the create_lang_packs script
echo "Creating language packs"
sh ./scripts/create_lang_packs.sh -s ${_workdir} -d $_destdir >/dev/null

# Create the lite package
# it is created after the langpacks are created, because the langpack
# generation removes files from the working directory.
echo "Creating lite package"
tar zcf $_destdir/cmsmadesimple-$_version-lite.tar.gz .

# cleanup
if [ ${noclean:-notset} != notset ];
  echo "Cleaning up"
  cd $_owd
  rm -rf $_workdir
fi

echo "Done: All release files should be in $_destdir";