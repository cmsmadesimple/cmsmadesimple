#!/bin/bash
# script to relocate lang-related scripts

#this is where the things are executed from, must be parent of TWIGS
#and by default the parent of this script's folder
SHAREDROOT='..'
#dirs to scan
TWIGS="admin/lang"

cd $SHAREDROOT
BASE=$(pwd)
echo "execute from $BASE"
for dir in $TWIGS; do
  LEAVES=$(find -L $BASE/$dir -maxdepth 1 -type f -not -iname \*.php -not -name \*index.htm\* -exec echo -n "{} " \;)
  for LEAF in $LEAVES; do
   FN=$(basename $LEAF)
   echo move $LEAF to $BASE/scripts/lang/$FN
   unlink $BASE/scripts/lang/$FN 2>/dev/null
   mv $LEAF $BASE/scripts/lang/$FN
   chmod 0751 $BASE/scripts/lang/$FN
  done
done
