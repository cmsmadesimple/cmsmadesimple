#!/bin/sh

all_dir=$(find . -type d -iname ??_??)
for po_file in $all_dir ; do
	msgfmt $po_file/LC_MESSAGES/cmsmadesimple.po -o $po_file/LC_MESSAGES/cmsmadesimple.mo
done
