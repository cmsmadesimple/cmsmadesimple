#!/bin/sh

all_dir=$(find . -type d -iname ??_??)
for po_file in $all_dir ; do
	msgfmt -f -o $po_file/LC_MESSAGES/cmsmadesimple.mo $po_file/LC_MESSAGES/cmsmadesimple.po 
done
