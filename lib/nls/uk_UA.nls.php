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

#Ukrainian

#Native language name
$nls['language']['uk_UA'] = 'Українська';
$nls['englishlang']['uk_UA'] = 'Ukrainian';

#Possible aliases for language
$nls['alias']['uk'] = 'uk_UA';
$nls['alias']['ua'] = 'uk_UA';
$nls['alias']['ukrainian'] = 'uk_UA';
$nls['alias']['ukr'] = 'uk_UA';

#Possible locale for language
$nls['locale']['uk_UA'] = 'uk_UA,uk_UA.cp1251,uk_UA.CP1251,uk_UA.CP866,uk_UA.koi8r,uk_UA.koi8ru,uk_UA.koi8u,uk_UA.utf8,uk_UA.utf.8,uk_ua.UTF-8,uk_UA.iso88595,ukrainian,ukrainian_Ukraine.1251';

#Encoding of the language
$nls['encoding']['uk_UA'] = 'UTF-8';

#Location of the file(s)
$nls['file']['uk_UA'] = array(dirname(__FILE__).'/uk_UA/admin.inc.php');

#Language setting for HTML area
# Only change this when translations exist in HTMLarea and plugin dirs
# (please send language files to HTMLarea development)

$nls['htmlarea']['uk_UA'] = 'uk';

?>