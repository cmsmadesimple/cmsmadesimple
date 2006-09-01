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
#(http://gallery.menalto.com) 

#Ideas from Gallery's implementation made to CMS by Ted Kulp

#Indonesian (Bahasa Indonesia)
#Created by: Prihantoosa Supraja  < pht854 [at]  yahoo [dot] com >

#Native language name
$nls['language']['id_ID'] = 'Bahasa Indonesia';
$nls['englishlang']['id_ID'] = 'Indonesian';

#Possible aliases for language
$nls['alias']['id'] = 'id_ID';
$nls['alias']['ind'] = 'id_ID' ;
$nls['alias']['id_ID'] = 'id_ID' ;
$nls['alias']['id_ID.ISO8859-15'] = 'id_ID' ;

#Encoding of the language
$nls['encoding']['id_ID'] = 'UTF-8';

#Location of the file(s)
$nls['file']['id_ID'] = array(dirname(__FILE__).'/id_ID/admin.inc.php');

#Language setting for HTML area
# Only change this when translations exist in HTMLarea and plugin dirs
# (please send language files to HTMLarea development)

$nls['htmlarea']['id_ID'] = 'en';
?>
