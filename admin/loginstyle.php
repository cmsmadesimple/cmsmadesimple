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
#
#$Id: login.php 4251 2007-11-15 21:34:40Z calguy1000 $

$CMS_ADMIN_PAGE=1;

require_once("../include.php");
require_once("../lib/classes/class.user.inc.php");

$themeObject = cms_utils::get_theme_object();
$theme = $themeObject->themeName;

header("Content-type: text/css; charset=" . get_encoding());
if (file_exists(dirname(__FILE__)."/themes/$theme/css/style.css"))
  {
    echo file_get_contents(dirname(__FILE__)."/themes/$theme/css/style.css");
  }
else
  {
    echo file_get_contents(dirname(__FILE__)."/themes/default/css/style.css");
  }
?>
