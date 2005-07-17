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

#Swedish (svenska)
#Created by: Tobias Amnell <tobias.amnell@home.se>

#Native language name
$nls['language']['se_SE'] = 'Svenska';
$nls['englishlang']['se_SE'] = 'Swedish';

#Possible aliases for language
$nls['alias']['se'] = 'se_SE';
$nls['alias']['svenska'] = 'se_SE' ;
$nls['alias']['sve'] = 'se_SE' ;
$nls['alias']['se_SE'] = 'se_SE' ;
$nls['alias']['se_SE.ISO8859-1'] = 'se_SE' ;
$nls['alias']['se_SE.ISO8859-15'] = 'se_SE' ;

#Encoding of the language
$nls['encoding']['se_SE'] = "ISO-8859-15";

#Location of the file(s)
$nls['file']['se_SE'] = array();
array_push($nls['file']['se_SE'], dirname(__FILE__)."/se_SE/admin.inc.php");

#Language setting for HTML area
# Only change this when translations exist in HTMLarea and plugin dirs
# (please send language files to HTMLarea development)

$nls['htmlarea']['se_SE'] = "en";
?>
