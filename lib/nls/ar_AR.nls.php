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

#Arabic
#Created by: JohnnyTheOne(J. Eshak)<info@johnnytheone.com>
#Maintained by: Basim Lahdo <info@gazire.com>
#This is the default language

#Native language name
#NOTE: Enocde me with HTML escape chars like &#231; or &ntilde; so I work on every page
$nls['language']['ar_AR'] = 'العربية';
$nls['englishlang']['ar_AR'] = 'Arabic';

#Possible aliases for language
$nls['alias']['ar'] = 'ar_AR';
$nls['alias']['arabic'] = 'ar_AR';
$nls['alias']['arb'] = 'ar_AR';
$nls['alias']['ar_SY'] = 'ar_AR';
$nls['alias']['ar_SA'] = 'ar_AR';
$nls['alias']['ar_AR.ISO8859-6'] = 'ar_AR';
$nls['alias']['ar_AR.windows-1256'] = 'ar_AR';

#Possible locale for language
$nls['locale']['ar_AR'] = 'ar_AR,ar_AR.utf8,ar_AR.utf-8,ar_AR.UTF-8,ar_AE.utf-8,ar_AE.UTF-8,arabic,Arabic_Saudi Arabia.1256';

#Encoding of the language
$nls['encoding']['ar_AR'] = 'UTF-8';

#Direction of the language
$nls['direction']['ar_AR'] = 'rtl';

#Location of the file(s)
$nls['file']['ar_AR'] = array(dirname(__FILE__).'/ar_AR/admin.inc.php');

#Language setting for HTML area
# Only change this when translations exist in HTMLarea and plugin dirs
# (please send language files to HTMLarea development)
$nls['htmlarea']['ar_AR'] = 'ar';
?>
