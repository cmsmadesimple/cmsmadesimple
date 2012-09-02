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

#US English
#Created by: Ted Kulp <tedkulp@users.sf.net>
#Maintained by: Ted Kulp <tedkulp@users.sf.net>
#This is the default language

#Native language name
$nls['language']['zh_TW'] = '&#32321;&#39636;&#20013;&#25991;';
$nls['englishlang']['zh_TW'] = 'Traditional Chinese';

#Possible aliases for language
$nls['alias']['chinese'] = 'zh_TW' ;
$nls['alias']['zh_TW.Big5'] = 'zh_TW' ;

#Possible locale for language
$nls['locale']['zh_TW'] = 'zh_TW,zh_TW.euctw,zh_TW.utf8,zh_TW.big5,zh_TW.Big5,zh_TW.utf8,zh_TW.UTF-8,chinese-traditional,Chinese_Taiwan.950';

#Encoding of the language
$nls['encoding']['zh_TW'] = 'UTF-8';

#Location of the file(s)
$nls['file']['zh_TW'] = array(dirname(__FILE__).'/zh_TW/admin.inc.php');

$nls['htmlarea']['zh_TW'] = 'en';
?>
