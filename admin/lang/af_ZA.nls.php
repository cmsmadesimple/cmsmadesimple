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

#Afrikaans
#Created by: Theuns Cloete < theunis [at] kompas [dot] co [dot] za >
#Maintained by: Theuns Cloete < theunis [at] kompas [dot] co [dot] za >


#Native language name
$nls['language']['af_ZA'] = 'Afrikaans';
$nls['englishlang']['af_ZA'] = 'Africaans';

#Possible aliases for language
$nls['alias']['af'] = 'af_ZA';
$nls['alias']['afr'] = 'af_ZA';
$nls['alias']['afrikaans'] = 'af_ZA';
$nls['alias']['af_ZA.ISO8859-1'] = 'af_ZA' ;

#Encoding of the language
$nls['encoding']['af_ZA'] = 'UTF-8';

#Location of the file(s)
$nls['file']['af_ZA'] = array(dirname(__FILE__).'/af_ZA/admin.inc.php');

#Language setting for HTML area
# Only change this when translations exist in HTMLarea and plugin dirs
# (please send language files to HTMLarea development)
$nls['htmlarea']['af_ZA'] = 'en';



?>
