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
#Hungarian maintained by: Kozman Bálint <qzy [AT] ultramarin [DOT] hu>

#Native language name
$nls['language']['hr_HR'] = 'Hrvatski';
$nls['englishlang']['hr_HR'] = 'Croatian';

#Possible aliases for language
$nls['alias']['hr'] = 'hr_HR';
$nls['alias']['croatian'] = 'hr_HR' ;
$nls['alias']['hrvatski'] = 'hr_HR' ;
$nls['alias']['hr_HR'] = 'hr_HR' ;
$nls['alias']['hr_HR.WINDOWS-1250'] = 'hr_HR' ;
$nls['alias']['hr_HR.ISO8859-2'] = 'hr_HR' ;

#Encoding of the language
$nls['encoding']['hr_HR'] = 'UTF-8';

#Location of the file(s)
$nls['file']['hr_HR'] = array(dirname(__FILE__).'/hr_HR/admin.inc.php');

#Language setting for HTML area
# Only change this when translations exist in HTMLarea and plugin dirs
# (please send language files to HTMLarea development)

$nls['htmlarea']['hr_HR'] = 'hr';
?>
