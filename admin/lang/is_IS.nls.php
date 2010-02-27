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

#Icelandic
#Created by: Rikhardur Brynjolfsson <rikhardur @nospam@ gmail.com>
#Maintained by: Rikhardur Brynjolfsson <rikhardur @nospam@ gmail.com>

#Native language name
$nls['language']['is_IS'] = 'Íslenska';
$nls['englishlang']['is_IS'] = 'Icelandic';

#Possible aliases for language
$nls['alias']['is'] = 'is_IS';
$nls['alias']['icelandic'] = 'is_IS' ;
$nls['alias']['ice'] = 'is_IS' ;
$nls['alias']['isl'] = 'is_IS' ;
$nls['alias']['is_IS'] = 'is_IS' ;
$nls['alias']['is_IS.ISO8859-1'] = 'is_IS' ;
$nls['alias']['is_IS.ISO8859-15'] = 'is_IS' ;

#Encoding of the language
$nls['encoding']['is_IS'] = 'UTF-8';

#Location of the file(s)
$nls['file']['is_IS'] = array(dirname(__FILE__).'/is_IS/admin.inc.php');

#Language setting for HTML area
# Only change this when translations exist in HTMLarea and plugin dirs
# (please send language files to HTMLarea development)

$nls['htmlarea']['is_IS'] = 'en';

?>
