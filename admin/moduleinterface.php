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

$CMS_ADMIN_PAGE=1;

require_once("../include.php");

check_login($config);

$module = "";
if (isset($_GET["module"])) $module = $_GET["module"];
else if (isset($_POST["module"])) $module = $_POST["module"];

include_once("header.php");

if (count($cmsmodules) > 0) {

?>

	<h3><?=$module?> Interface</h3>

<?

	if (isset($cmsmodules[$module])) {
		@ob_start();
		$obj = $cmsmodules[$module]['Instance'];
		$obj->executeadmin($modulecmsobj,"module_".$module."_");
		$content = @ob_get_contents();
		@ob_end_clean();
		echo $content;
	} else {
		redirect("index.php");
	}

}

include_once("footer.php");

?>
