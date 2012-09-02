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

#RU Russian
#Created by: Alexey Tyagunov <webmaster@superhonda.ru>
#Maintained by: Alexey Tyagunov <webmaster@superhonda.ru>

#Native language name
#NOTE: Enocde me with HTML escape chars like &#231; or &ntilde; so I work on every page
$nls['language']['ru_RU'] = 'Русский';
$nls['englishlang']['ru_RU'] = 'Russian';

#Possible aliases for language
$nls['alias']['ru'] = 'ru_RU';
$nls['alias']['russian'] = 'ru_RU' ;
$nls['alias']['rus'] = 'ru_RU' ;

#Possible locale for language
$nls['locale']['ru_RU'] = 'ru_RU,ru_RU.cp1251,ru_RU.CP1251,ru_RU.CP866,ru_RU.koi8r,ru_RU.utf8,ru_RU.iso88595,russian,Russian_Russia.1251';

#Encoding of the language
$nls['encoding']['ru_RU'] = "utf-8";

#Location of the file(s)
$nls['file']['ru_RU'] = array(dirname(__FILE__).'/ru_RU/admin.inc.php');

#Language setting for HTML area
# Only change this when translations exist in HTMLarea and plugin dirs
# (please send language files to HTMLarea development)

$nls['htmlarea']['ru_RU'] = 'ru';
?>
