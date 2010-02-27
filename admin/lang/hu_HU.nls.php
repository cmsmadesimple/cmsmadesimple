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
$nls['language']['hu_HU'] = 'Magyar';
$nls['englishlang']['hu_HU'] = 'Hungarian';

#Possible aliases for language
$nls['alias']['hu'] = 'hu_HU';
$nls['alias']['hungarian'] = 'hu_HU' ;
$nls['alias']['magyar'] = 'hu_HU' ;
$nls['alias']['hu_HU'] = 'hu_HU' ;
$nls['alias']['hu_HU.WINDOWS-1250'] = 'hu_HU' ;
$nls['alias']['hu_HU.ISO8859-2'] = 'hu_HU' ;

#Encoding of the language
$nls['encoding']['hu_HU'] = 'UTF-8';

#Location of the file(s)
$nls['file']['hu_HU'] = array(dirname(__FILE__).'/hu_HU/admin.inc.php');

#Language setting for HTML area
# Only change this when translations exist in HTMLarea and plugin dirs
# (please send language files to HTMLarea development)

$nls['htmlarea']['hu_HU'] = 'hu';
?>
