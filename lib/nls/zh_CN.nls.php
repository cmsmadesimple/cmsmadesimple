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
$nls['language']['zh_CN'] = '&#31616;&#20307;&#20013;&#25991;';
$nls['englishlang']['zh_CN'] = 'Simplified Chinese';

#Possible aliases for language
$nls['alias']['zh_CN.EUC'] = 'zh_CN' ;
$nls['alias']['chinese_gb2312'] = 'zh_CN' ;

#Possible locale for language
$nls['locale']['zh_CN'] = 'zh_CN,zh_CN.utf8,zh_CN.UTF-8,zh_CN.eucCN,zh_CN.gbk,zh_CN.gb18030,zh_CN.gbk,chinese,chinese-simplified,Chinese_China.936';

#Encoding of the language
$nls['encoding']['zh_CN'] = 'UTF-8';

#Location of the file(s)
$nls['file']['zh_CN'] = array(dirname(__FILE__).'/zh_CN/admin.inc.php');

$nls['htmlarea']['zh_CN'] = 'en';
?>
