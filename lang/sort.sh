#!/bin/sh

# Shell script to sort all entries so they're easier to read

for file in `find . -name "admin.inc.php"`
do
	echo $file " "
	echo "<?php" > ${file}.new
	cat $file | grep -v '$lang' | grep -v '?>' | grep -v "<?" >> ${file}.new
	cat $file | grep '$lang' | sort | uniq >> ${file}.new
	echo "?>" >> ${file}.new
	rm $file
	mv ${file}.new $file
done
