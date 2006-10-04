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

#Catalan
#Created by: Daniel Vives < daniel.vives [at] catalonia [dot] net >
#Maintained by: Daniel Vives < daniel.vives [at] catalonia [dot] net >

#Native language name
$nls['language']['ca_ES'] = 'Catal&agrave;';
$nls['englishlang']['ca_ES'] = 'Catalan';

#Possible aliases for language
$nls['alias']['ca'] = 'ca_ES';
$nls['alias']['cat'] = 'ca_ES' ;
$nls['alias']['catal&agrave;'] = 'ca_ES' ;
$nls['alias']['ca_ES.ISO8859-1'] = 'ca_ES' ;

#Encoding of the language
$nls['encoding']['ca_ES'] = 'UTF-8';

#Location of the file(s)
$nls['file']['ca_ES'] = array(dirname(__FILE__).'/ca_ES/admin.inc.php');

#Language setting for HTML area
# Only change this when translations exist in HTMLarea and plugin dirs
# (please send language files to HTMLarea development)

$nls['htmlarea']['ca_ES'] = 'en';
?>
