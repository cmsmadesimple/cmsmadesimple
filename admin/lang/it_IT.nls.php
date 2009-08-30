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

#US English
#Created by: Ted Kulp <tedkulp@users.sf.net>
#Maintained by: Ted Kulp <tedkulp@users.sf.net>
#This is the default language

#Native language name
$cms_nls['language']['it_IT'] = 'Italiano';
$cms_nls['englishlang']['it_IT'] = 'Italian';

#Possible aliases for language
$cms_nls['alias']['it'] = 'it_IT';
$cms_nls['alias']['italiano'] = 'it_IT' ;
$cms_nls['alias']['ita'] = 'it_IT' ;
$cms_nls['alias']['italian'] = 'it_IT' ;
$cms_nls['alias']['it_IT.ISO8859-1'] = 'it_IT' ;
$cms_nls['alias']['it_IT.ISO8859-15'] = 'it_IT' ;

#Encoding of the language
$cms_nls['encoding']['it_IT'] = 'UTF-8';

#Location of the file(s)
$cms_nls['file']['it_IT'] = array(dirname(__FILE__).'/it_IT/admin.inc.php');

#Language setting for HTML area
# Only change this when translations exist in HTMLarea and plugin dirs
# (please send language files to HTMLarea development)

$cms_nls['htmlarea']['it_IT'] = 'it';
?>
