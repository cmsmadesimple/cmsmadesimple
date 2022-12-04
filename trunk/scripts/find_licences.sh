#!/bin/bash
# script to find license-related strings in CMSMS *.module.php source files
# scans the first 30 lines of relevant files

#this is where the things are executed from, must be parent of twigs
#and by default the parent of this script's folder
SHAREDROOT='..'
#dirs to scan
TWIGS="lib admin assets uploads tests phar_installer"

#PATTERN=
#SHORTPATTERN="\\([(]C[)] 20\\)\\($PATTERN\\)\\( CMS Made Simple Foundation\\)"
#LONGPATTERN="\\([(]C[)] 20\\)\\($PATTERN\\)-20\\($PATTERN\\)\\( CMS Made Simple Foundation\\)"
#echo "short = $SHORTPATTERN"
#echo "long = $LONGPATTERN"

cd $SHAREDROOT
echo "execute from $SHAREDROOT"

# update the array usages
for dir in $TWIGS; do
  LEAVES=$(find -L $dir -type f -name \*.module.php -not -wholename \*svn\* -not -wholename \*git\* -exec echo -n "{} " \;)
  for LEAF in $LEAVES; do
# produce a 5-field description like -rw-rw-r-- 1   9108 2007 ACL-description.txt
# of which we use only the year
    LONGTEXT=$(ls -Gg --time-style=+%04Y $LEAF)
    MODYEAR=$(echo $LONGTEXT | gawk '{ print $4 }')
#    if [ "$MODYEAR" -lt "$THISYEAR" ]; then
#      echo "$LEAF is old"
#    else
    if [ "$MODYEAR" -le "$THISYEAR" ]; then
        echo "$LEAF has been updated"
      fi
    fi
  done
done
