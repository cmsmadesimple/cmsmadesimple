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

#Slovenian
#Created by: Ted Kulp <tedkulp@users.sf.net>
#Slovenian maintained by: Goran Ilic <uniqu3e [AT] gmail [DOT] com>

#Native language name
$nls['language']['sl_SI'] = 'Slovensko';
$nls['englishlang']['sl_SI'] = 'Slovenian';

#Possible aliases for language
$nls['alias']['sl'] = 'sl_SI';
$nls['alias']['slovenian'] = 'sl_SI' ;
$nls['alias']['slo'] = 'sl_SI' ;
$nls['alias']['sl_SI'] = 'sl_SI' ;
$nls['alias']['sl_SI.WINDOWS-1250'] = 'sl_SI' ;
$nls['alias']['sl_SI.ISO8859-2'] = 'sl_SI' ;

#Encoding of the language
$nls['encoding']['sl_SI'] = 'UTF-8';

#Location of the file(s)
$nls['file']['sl_SI'] = array(dirname(__FILE__).'/sl_SI/admin.inc.php');

#Language setting for HTML area
# Only change this when translations exist in HTMLarea and plugin dirs
# (please send language files to HTMLarea development)

$nls['htmlarea']['sl_SI'] = 'sl';
?>
