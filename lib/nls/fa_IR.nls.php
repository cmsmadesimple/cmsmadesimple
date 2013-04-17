<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
#This project's homepage is: http://www.cmsmadesimple.org
#
#This program is free software; you can redistribute it and/or modify
#it under the terms of the GNU General Public License as published by
#the Free Software Foundation; either version 2 of the License, or
#(at your option) any later version.
#
#This program is distributed in the hope that it will be useful,
#but WITHOUT ANY WARRANTY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
#You should have received a copy of the GNU General Public License
#along with this program; if not, write to the Free Software
#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

#NLS (National Language System) array.

#The basic idea and values was taken from then Horde Framework (http://horde.org)
#The original filename was horde/config/nls.php.
#The modifications to fit it for Gallery were made by Jens Tkotz
#(http://gallery.meanalto.com) 

#Ideas from Gallery's implementation made to CMS by Ted Kulp

#Native language name
$nls['language']['fa_IR'] = 'فارسی';
$nls['englishlang']['fa_IR'] = 'Farsi';

#Possible aliases for language
$nls['alias']['fa'] = 'fa_IR';
$nls['alias']['farsi'] = 'fa_IR';
$nls['alias']['fa_IR.UTF-8'] = 'fa_IR';

#Possible locale for language
$nls['locale']['fa_IR'] = 'fa_IR,fa_IR.utf-8,fa_IR.utf-8,fa_IR.UTF-8,farsi,Farsi_Iran.1256';

#Encoding of the language
$nls['encoding']['fa_IR'] = 'UTF-8';

#Direction of the language
$nls['direction']['fa_IR'] = 'rtl';

#Location of the file(s)
$nls['file']['fa_IR'] = array(dirname(__FILE__).'/fa_IR/admin.inc.php');

#Language setting for HTML area
# Only change this when translations exist in HTMLarea and plugin dirs
# (please send language files to HTMLarea development)

$nls['htmlarea']['fa_IR'] = 'fa';
?>
