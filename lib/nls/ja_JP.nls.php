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

#Japanese
#Translation: Nuspace Pty Ltd <http://www.nuspace.net/>

#Native language name
$nls['language']['ja_JP'] = '日本語';
$nls['englishlang']['ja_JP'] = 'Japanese';

#Possible aliases for language
$nls['alias']['ja'] = 'ja_JP';
$nls['alias']['japanese'] = 'ja_JP' ;
$nls['alias']['ja_JP.EUC-JP'] = 'ja_JP' ;
$nls['alias']['ja_JP.Shift_JIS'] = 'ja_JP' ;
$nls['alias']['ja_JP.UTF-8'] = 'ja_JP' ;

#Possible locale for language
$nls['locale']['ja_JP'] = 'ja_JP,ja_JP.utf8,ja_JP.utf-8,ja_JP.UTF-8,ja_JP.SJIS,ja_JP.eucjp,japanese,Japanese_Japan.932';

#Encoding of the language
$nls['encoding']['ja_JP'] = 'UTF-8';

#Location of the file(s)
$nls['file']['ja_JP'] = array(dirname(__FILE__).'/ja_JP/admin.inc.php');

#Language setting for HTML area
# Only change this when translations exist in HTMLarea and plugin dirs
# (please send language files to HTMLarea development)

$nls['htmlarea']['ja_JP'] = 'ja';
?>
