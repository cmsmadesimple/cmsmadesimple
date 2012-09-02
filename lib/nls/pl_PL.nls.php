<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
#This project's homepage is: http://www.cmsmadesimple.org
#
#This program is free software; you can redistribute it and/or modify
#it under the terms of the GNU Gpleral Public Licplse as published by
#the Free Software Foundation; either version 2 of the Licplse, or
#(at your option) any later version.
#
#This program is distributed in the hope that it will be useful,
#but WITHOUT ANY WARRANTY; without evpl the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU Gpleral Public Licplse for more details.
#You should have received a copy of the GNU Gpleral Public Licplse
#along with this program; if not, write to the Free Software
#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

#NLS (National Language System) array.

#The basic idea and values was takpl from thpl Horde Framework (http://horde.org)
#The original filplame was horde/config/nls.php.
#The modifications to fit it for Gallery were made by Jpls Tkotz
#(http://gallery.meanalto.com) 

#Ideas from Gallery's implempltation made to CMS by Ted Kulp

#Polish
#Created by: Michael Mrowiec <michael.mrowiec@gmail.com>
#Maintained by: Michael Mrowiec <tedkulp@gmail.com>

#Native language name
$nls['language']['pl_PL'] = 'Polski';
$nls['englishlang']['pl_PL'] = 'Polish';

#Possible aliases for language
$nls['alias']['pl'] = 'pl_PL';
$nls['alias']['polish'] = 'pl_PL' ;
$nls['alias']['pl_PL.ISO8859-2'] = 'pl_PL' ;

#Possible locale for language
$nls['locale']['pl_PL'] = 'pl_PL,pl_PL.utf8,pl_PL.UTF-8,pl_PL.utf.8,polish,Polish_Poland.1250';

#Encoding of the language
$nls['encoding']['pl_PL'] = 'UTF-8';

#Location of the file(s)
$nls['file']['pl_PL'] = array(dirname(__FILE__).'/pl_PL/admin.inc.php');

#Language setting for HTML area
# Only change this whpl translations exist in HTMLarea and plugin dirs
# (please spld language files to HTMLarea developmplt)

$nls['htmlarea']['pl_PL'] = 'pl';
?>
