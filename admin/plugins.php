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

check_login();

$plugin = "";
if (isset($_GET["plugin"])) $plugin = $_GET["plugin"];

$action = "";
if (isset($_GET["action"])) $action = $_GET["action"];

$smarty = new Smarty_CMS($gCms->config);

include_once("header.php");

if ($action == "showabout")
{
	if (function_exists('smarty_cms_help_function_'.$plugin))
	{
		@ob_start();
		call_user_func_array('smarty_cms_help_function_'.$plugin, array());
		$content = @ob_get_contents();
		@ob_end_clean();
		echo "<div class=\"moduleabout\">";
		echo "<h2>About the $plugin plugin</h2>";
		echo $content;
		?>
		<form action="plugins.php" method="get">
		<p><input type="submit" value="Back to Plugin List" /></p>
		</form>
		<?
		echo "</div>";
	}
}
else
{

?>

	<h3><?=$gettext->gettext("Plugin List")?></h3>

	<table cellspacing="0" class="admintable">
		<tr>
			<td><?=$gettext->gettext("Module Name")?></td>
			<td width="10%"><?=$gettext->gettext("About")?></td>
		</tr>

<?

		$curclass = "row1";

		foreach($gCms->cmsplugins as $oneplugin) {

			echo "<tr class=\"$curclass\">\n";
			echo "<td>$oneplugin</td>\n";
			if (function_exists('smarty_cms_help_function_'.$oneplugin))
			{
				echo "<td><a href=\"plugins.php?action=showabout&plugin=".$oneplugin."\">".$gettext->gettext("About")."</a></td>";
			}
			else
			{
				echo "<td>&nbsp;</td>";
			}
		
			echo "</tr>\n";

			($curclass=="row1"?$curclass="row2":$curclass="row1");
		}

	?>

</table>

</div>

	<?

}

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
