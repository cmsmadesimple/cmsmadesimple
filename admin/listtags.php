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
#
#$Id$

$CMS_ADMIN_PAGE=1;
$CMS_TOP_MENU=6;

require_once("../include.php");

check_login();

$plugin = "";
if (isset($_GET["plugin"])) $plugin = $_GET["plugin"];

$action = "";
if (isset($_GET["action"])) $action = $_GET["action"];

$userid = get_userid();
$access = check_permission($userid, "Modify Modules");

$smarty = new Smarty_CMS($gCms->config);

include_once("header.php");

if ($action == "showpluginhelp")
{
	if (function_exists('smarty_cms_help_function_'.$plugin))
	{
		@ob_start();
		call_user_func_array('smarty_cms_help_function_'.$plugin, array());
		$content = @ob_get_contents();
		@ob_end_clean();
		echo "<div class=\"moduleabout\">";
		echo "<h2>".lang('pluginhelp', array($plugin))."</h2>";
		echo $content;
		?>
		<FORM ACTION="listtags.php" METHOD="get">
		<P><INPUT TYPE="submit" VALUE="<?php echo lang('backtoplugins')?>" CLASS="button" onMouseOver="this.className='buttonHover'" onMouseOut="this.className='button'"></P>
		</FORM>
		<?php
		echo "</div>";
	}
	else
	{
		?>
		<P>No help text available for this plugin.</P>
		<?php
	}
}
else if ($action == "showpluginabout")
{
	if (function_exists('smarty_cms_about_function_'.$plugin))
	{
		@ob_start();
		call_user_func_array('smarty_cms_about_function_'.$plugin, array());
		$content = @ob_get_contents();
		@ob_end_clean();
		echo "<div class=\"moduleabout\">";
		echo "<h2>".lang('pluginabout', array($plugin))."</h2>";
		echo $content;
		?>
		<FORM ACTION="listtags.php" METHOD="get">
		<P><INPUT TYPE="submit" VALUE="<?php echo lang('backtoplugins')?>" CLASS="button" onMouseOver="this.className='buttonHover'" onMouseOut="this.className='button'"></P>
		</FORM>
		<?php
		echo "</div>";
	}
	else
	{
		?>
		<P>No about text available for this tag.</P>
		<?php
	}
}

?>

	<h3><?php echo lang('tags')?></h3>

	<table cellspacing="0" class="admintable">
		<tr>
			<td><?php echo lang('name')?></td>
			<td width="8%"><?php echo lang('help')?></td>
			<td width="8%"><?php echo lang('about')?></td>
		</tr>

<?php

		$curclass = "row1";

		foreach($gCms->cmsplugins as $oneplugin)
		{
			if (!array_key_exists($oneplugin, $gCms->userplugins))
			{
				echo "<tr class=\"$curclass\">\n";

				echo "<td>$oneplugin</td>\n";

				if (function_exists('smarty_cms_help_function_'.$oneplugin))
				{
					echo "<td><a href=\"listtags.php?action=showpluginhelp&amp;plugin=".$oneplugin."\">".lang('help')."</a></td>";
				}
				else
				{
					echo "<td>&nbsp;</td>";
				}
				if (function_exists('smarty_cms_about_function_'.$oneplugin))
				{
					echo "<td><a href=\"listtags.php?action=showpluginabout&amp;plugin=".$oneplugin."\">".lang('about')."</a></td>";
				}
				else
				{
					echo "<td>&nbsp;</td>";
				}
			
				echo "</tr>\n";

				($curclass=="row1"?$curclass="row2":$curclass="row1");
			}
		}

	?>

</table>

	<?php

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
