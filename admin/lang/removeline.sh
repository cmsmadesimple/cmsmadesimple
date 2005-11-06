#!/bin/sh

#Shell script to remove a line to each existing translation file
#
#Example: removeline.sh 'messagename'

if [ $# -ne 1 ] ; then
	echo "usage: removeline.sh 'messagename'"
	exit 1
fi

#First check en_US to make sure it exists
grep "lang\['admin'\]\['$1'\]" en_US/admin.inc.php > /dev/null
if [ $? -ne 0 ] ; then
	echo "lang['admin']['$1'] doesn't exist"
	exit 1
fi

for file in `find . -name "admin.inc.php"`
do
	echo $file " "
	grep -v "lang\['admin'\]\['$1'\]" ${file} > ${file}.new
	rm $file
	mv ${file}.new $file
done

echo "Message Removed"
