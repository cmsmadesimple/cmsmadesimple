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
#Created by: Jani Mikkonen <jani@mikkonen.org>
#Maintained by: Jani Mikkonen <jani@mikkonen.org>

#Native language name
$cms_nls['language']['fi_FI'] = 'Suomi';
$cms_nls['englishlang']['fi_FI'] = 'Finnish';

#Possible aliases for language
$cms_nls['alias']['fi'] = 'fi_FI';
$cms_nls['alias']['finnish'] = 'fi_FI' ;
$cms_nls['alias']['fin'] = 'fi_FI' ;
$cms_nls['alias']['fi_FI.ISO8859-1'] = 'fi_FI' ;
$cms_nls['alias']['fi_FI.ISO8859-15'] = 'fi_FI' ;

#Encoding of the language
$cms_nls['encoding']['fi_FI'] = 'UTF-8';

#Location of the file(s)
$cms_nls['file']['fi_FI'] = array(dirname(__FILE__).'/fi_FI/admin.inc.php');

#Language setting for HTML area
# Only change this when translations exist in HTMLarea and plugin dirs
# (please send language files to HTMLarea development)

$cms_nls['htmlarea']['fi_FI'] = 'en';

?>
