#!/bin/bash
# script to delete dummy index.htm[l] files in source folders

#this is where the things are executed from, must be parent of TWIGS
#and by default the parent of this script's folder
SHAREDROOT='..'
#dirs to scan
TWIGS="admin assets doc lib modules phar_installer tmp uploads"

cd $SHAREDROOT
BASE=$(pwd)
echo "execute from $BASE"

for dir in $TWIGS; do
  LEAVES=$(find -L $dir -type f -name index.htm* -size -30c -exec echo -n "{} " \;)
  for LEAF in $LEAVES; do
    echo remove $BASE/$LEAF
    unlink $BASE/$LEAF
  done
done
