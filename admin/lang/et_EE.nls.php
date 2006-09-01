<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
#This project's homepage is: http://cmsmadesimple.sf.net
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

#Finnish

#Native language name
$nls['language']['et_EE'] = 'Eesti';
$nls['englishlang']['et_EE'] = 'Estonian';

#Possible aliases for language
$nls['alias']['et'] = 'et_EE';
$nls['alias']['estonian'] = 'et_EE' ;
$nls['alias']['eti'] = 'et_EE' ;
$nls['alias']['et_EE.ISO8859-1'] = 'et_EE' ;
$nls['alias']['et_EE.ISO8859-15'] = 'et_EE' ;
$nls['alias']['et_EE.UTF-8'] = 'et_EE' ;

#Encoding of the language
$nls['encoding']['et_EE'] = "UTF-8";

#Location of the file(s)
$nls['file']['et_EE'] = array(dirname(__FILE__).'/et_EE/admin.inc.php');

#Language setting for HTML area
# Only change this when translations exist in HTMLarea and plugin dirs
# (please send language files to HTMLarea development)

$nls['htmlarea']['et_EE'] = 'en';

?>
