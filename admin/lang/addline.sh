#!/bin/sh

#Shell script to add a new line to each existing translation file
#
#Example: addline.sh 'messagename' 'new message text'

if [ $# -ne 2 ] ; then
	echo "usage: addline.sh 'messagename' 'new message text'"
	exit 1
fi

#First check en_US to make sure it doesn't exist
grep "lang\['admin'\]\['$1'\]" en_US/admin.inc.php > /dev/null
if [ $? -eq 0 ] ; then
	echo "lang['admin']['$1'] already exists"
	exit 1
fi

for file in `find . -name "admin.inc.php"`
do
	echo $file " "
	grep -v '?>' ${file} > ${file}.new
	echo "\$lang['admin']['"$1"'] = '"$2"'; //needs translation" >> ${file}.new
	echo "?>" >> ${file}.new
	rm $file
	mv ${file}.new $file
done

echo "Message Added"
