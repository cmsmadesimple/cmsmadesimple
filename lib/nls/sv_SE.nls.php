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

#Swedish (svenska)
#Created by: Tobias Amnell <tobias.amnell@home.se> with additional translation by Daniel Westergren <daniel@wproductions.se>

#Native language name
$nls['language']['sv_SE'] = 'Svenska';
$nls['englishlang']['sv_SE'] = 'Swedish';

#Possible aliases for language
$nls['alias']['sv'] = 'sv_SE';
$nls['alias']['svenska'] = 'sv_SE' ;
$nls['alias']['sve'] = 'sv_SE' ;
$nls['alias']['sv_SE'] = 'sv_SE' ;
$nls['alias']['sv_SE.ISO8859-1'] = 'sv_SE' ;
$nls['alias']['sv_SE.ISO8859-15'] = 'sv_SE' ;

#Possible locale for language
$nls['locale']['sv_SE'] = 'sv_SE,sv_SE.utf8,sv_SE.UTF-8,sv_SE.utf-8,sv_SE.iso885915,swedish,Swedish_Sweden.1252';

#Encoding of the language
$nls['encoding']['sv_SE'] = 'UTF-8';

#Location of the file(s)
$nls['file']['sv_SE'] = array(dirname(__FILE__).'/sv_SE/admin.inc.php');

#Language setting for HTML area
# Only change this when translations exist in HTMLarea and plugin dirs
# (please send language files to HTMLarea development)

$nls['htmlarea']['sv_SE'] = 'en';
?>
