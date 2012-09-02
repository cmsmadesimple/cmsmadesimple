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
$nls['language']['fr_FR'] = 'Fran&#231;ais';
$nls['englishlang']['fr_FR'] = 'French';

#Possible aliases for language
$nls['alias']['fr'] = 'fr_FR';
$nls['alias']['french'] = 'fr_FR' ;
$nls['alias']['fra'] = 'fr_FR' ;
$nls['alias']['fr_BE'] = 'fr_FR' ;
$nls['alias']['fr_CA'] = 'fr_FR' ;
$nls['alias']['fr_LU'] = 'fr_FR' ;
$nls['alias']['fr_CH'] = 'fr_FR' ;
$nls['alias']['fr_FR.ISO8859-1'] = 'fr_FR' ;

#Possible locale for language
$nls['locale']['fr_FR'] = 'fr_FR,fr_FR.utf8,fr_FR.utf-8,fr_FR.UTF-8,fr_FR@euro,french,French_France.1252';

#Encoding of the language
$nls['encoding']['fr_FR'] = 'UTF-8';

#Location of the file(s)
$nls['file']['fr_FR'] = array(dirname(__FILE__).'/fr_FR/admin.inc.php');

#Language setting for HTML area
$nls['htmlarea']['fr_FR'] = 'fr';

?>
