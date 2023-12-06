<?php
#Module MicroTiny upgrade script
#(c) 2004 CMS Made Simple Foundation Inc <foundation@cmsmadesimple.org>
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
#

if( version_compare($oldversion,'1.1') < 0 ) {
  $this->CreatePermission('MicroTiny View HTML Source','MicroTiny View HTML Source');
}

if( version_compare($oldversion,'2.0') < 0 ) {
  $this->RemovePreference();
  $this->DeleteTemplate();
  include_once(__DIR__.'/method.install.php');
}
/*
NOTE: when upgrading TinyMCE, ensure that all its related translation
files (*.js) that also correspond to supported CMSMS translations (nls
files exist, even if not currently installed) are isted in the translations
lookup file,
 __DIR__/lib/langs.manifest
in each case, without a trailing '.js'
*/
#
# EOF
#
?>
