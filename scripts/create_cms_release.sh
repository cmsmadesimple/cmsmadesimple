#!/bin/sh

_this=`basename $0`
_svn=http://svn.cmsmadesimple.org/svn/cmsmadesimple/branches/1.2.x
_workdir=/tmp/$_this.$$
_owd=`pwd`

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
   echo "Cleaning up"
   cd $_owd
   rm -rf $_workdir
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
   echo "Cleaning up"
   cd $_owd
   rm -rf $_workdir
   echo "Exiting..."
   exit;
   ;;
esac

# Create the full package
echo
echo "Creating full package"
mkdir $_destdir
tar zcf $_destdir/cmsmadesimple-$_version-full.tar.gz .

# run the create_lang_packs script
echo "Creating language packs"
./scripts/create_lang_packs.sh -s ${_workdir} -d $_destdir >/dev/null

# Create the lite package
echo "Creating lite package"
tar zcf $_destdir/cmsmadesimple-$_version-lite.tar.gz .

# cleanup
echo "Cleaning up"
cd $_owd
rm -rf $_workdir

echo "Done: All release files should be in $_destdir";