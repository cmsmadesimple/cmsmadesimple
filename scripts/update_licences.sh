#!/bin/bash
# script to update copyright-year strings in CMSMS .php source files
# scans the first 6 lines of relevant files

#this is where the things are executed from, must be parent of twigs
#and by default the parent of this script's folder
SHAREDROOT='..'
#dirs to scan
TWIGS="lib admin assets uploads tests phar_installer"
#TWIGS="admin"
#TWIGS="tests"

THISYEAR=$(date +%04Y)
KEYYEAR=$(date +%-y)
LASTYEAR=$((${KEYYEAR}-1))
if [ "$LASTYEAR" -lt "10" ]; then
 PATTERN=0[0-$LASTYEAR]
else
 LASTTEN=$((${LASTYEAR}/10))
 if [ "$LASTTEN" -gt "1" ]; then
  LASTTEN="[1-$LASTTEN]"
 fi
 LASTONE=$((${LASTYEAR}%10))
 if [ "$LASTONE" -gt "0" ]; then
  LASTONE="[0-$LASTONE]"
 fi
 PATTERN="0[0-9]\\|$LASTTEN$LASTONE"
fi

SHORTPATTERN="\\([(]C[)] 20\\)\\($PATTERN\\)\\( CMS Made Simple Foundation\\)"
LONGPATTERN="\\([(]C[)] 20\\)\\($PATTERN\\)-20\\($PATTERN\\)\\( CMS Made Simple Foundation\\)"
#echo "short = $SHORTPATTERN"
#echo "long = $LONGPATTERN"

cd $SHAREDROOT
echo "execute from $SHAREDROOT"

# update the array usages
for dir in $TWIGS; do
  LEAVES=$(find -L $dir -type f -name \*.php -not -wholename \*svn\* -not -wholename \*git\* -exec echo -n "{} " \;)
  for LEAF in $LEAVES; do
# produce a 5-field description like -rw-rw-r-- 1   9108 2007 ACL-description.txt
# of which we use only the year
    LONGTEXT=$(ls -Gg --time-style=+%04Y $LEAF)
    MODYEAR=$(echo $LONGTEXT | gawk '{ print $4 }')
#    if [ "$MODYEAR" -lt "$THISYEAR" ]; then
#      echo "$LEAF is old"
#    else
    if [ "$MODYEAR" -le "$THISYEAR" ]; then
#      echo "$LEAF needs to be checked"
#      rm -f  $LEAF-newyear >/dev/null
      SEDARG1="1,6s~$SHORTPATTERN~\1\2-$THISYEAR\3~"
      SEDARG2="1,6s~$LONGPATTERN~\1\2-$THISYEAR\4~"
#      echo $SEDARG1 $SEDARG2
      sed -e "$SEDARG1" -e "$SEDARG2" $LEAF > $LEAF-newyear
      OLDCS=$(md5sum $LEAF | gawk '{ print $1 }')
      NEWCS=$(md5sum $LEAF-newyear | gawk '{ print $1 }')
      if [ "$OLDCS" = "$NEWCS" ] ; then
        echo "stet $LEAF"
        rm -f  $LEAF-newyear >/dev/null
     else
#        mv -f $LEAF $LEAF-old >/dev/null
        mv -f $LEAF-newyear $LEAF >/dev/null
        echo "$LEAF has been updated"
      fi
    fi
  done
done
