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
$nls['language']['kz_KZ'] = 'Kazakh';
$nls['englishlang']['kz_KZ'] = 'Kazakh';

#Possible aliases for language
$nls['alias']['kz'] = 'kz_KZ';
$nls['alias']['kazak'] = 'kz_KZ' ;
$nls['alias']['kazakh'] = 'kz_KZ' ;

#Possible locale for language
$nls['locale']['kz_KZ'] = 'kk_KZ,kk_KZ.utf-8,kk_KZ.UTF-8,kk_KZ.PT154,kazakh';

#Encoding of the language
$nls['encoding']['kz_KZ'] = 'UTF-8';

#Location of the file(s)
$nls['file']['kz_KZ'] = array(dirname(__FILE__).'/kz_KZ/admin.inc.php');

#Language setting for HTML area
$nls['htmlarea']['kz_KZ'] = 'en';

?>
