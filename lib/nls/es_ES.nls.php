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

#US English
#Created by: Ted Kulp <tedkulp@users.sf.net>
#Maintained by: Ted Kulp <tedkulp@users.sf.net>
#This is the default language

#Native language name
$nls['language']['es_ES'] = 'Espa&ntilde;ol';
$nls['englishlang']['es_ES'] = 'Spanish';

#Possible aliases for language
$nls['alias']['es'] = 'es_ES';
$nls['alias']['espa&ntilde;ol'] = 'es_ES' ;
$nls['alias']['eng'] = 'es_ES' ;
$nls['alias']['es_AR'] = 'es_ES' ;
$nls['alias']['es_PE'] = 'es_ES' ;
$nls['alias']['es_MX'] = 'es_ES' ;
$nls['alias']['es_US.ISO8859-1'] = 'es_ES' ;

#Possible locale for language
$nls['locale']['es_ES'] = 'es_ES,es_ES.utf8,es_ES.utf-8,es_ES.UTF-8,es_ES@euro,spanish,Spanish_Spain.1252';

#Encoding of the language
$nls['encoding']['es_ES'] = 'UTF-8';

#Location of the file(s)
$nls['file']['es_ES'] = array(dirname(__FILE__).'/es_ES/admin.inc.php');

#Language setting for HTML area
# Only change this when translations exist in HTMLarea and plugin dirs
# (please send language files to HTMLarea development)

$nls['htmlarea']['es_ES'] = 'es';
?>
