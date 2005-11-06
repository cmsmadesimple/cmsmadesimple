#!/bin/sh

for file in `find . -name "admin.inc.php"`
do
	echo ${file}
	cat ${file} | grep "\$lang" | wc -l
done
