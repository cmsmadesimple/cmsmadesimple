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

$module = "";
if (isset($_GET["module"])) $module = $_GET["module"];

$plugin = "";
if (isset($_GET["plugin"])) $plugin = $_GET["plugin"];

$action = "";
if (isset($_GET["action"])) $action = $_GET["action"];

$userid = get_userid();
$access = check_permission($userid, "Modify Modules");

$smarty = new Smarty_CMS($gCms->config);

include_once("header.php");

if ($access)
{
	if ($action == "install")
	{
		#run install on it (if there is one)
		if (isset($gCms->modules[$module]['install_function'])) {
			call_user_func_array($gCms->modules[$module]['install_function'], array($gCms));
		}

		#now insert a record
		$query = "INSERT INTO ".cms_db_prefix()."modules (module_name, version, status, active) VALUES (".$db->qstr($module).",".$db->qstr($gCms->modules[$module]['Version']).",'Installed',1)";
		$db->Execute($query);
		redirect("plugins.php");
	}

	if ($action == 'upgrade')
	{
		if (isset($gCms->modules[$module]['upgrade_function'])) {
			call_user_func_array($gCms->modules[$module]['upgrade_function'], array($gCms, $_GET['oldversion'], $_GET['newversion']));
		}
		$query = "UPDATE ".cms_db_prefix()."modules SET version = ? WHERE module_name = ?";
		$db->Execute($query,array($_GET['newversion'],$module));
		redirect("plugins.php");
	}

	if ($action == "uninstall")
	{
		#run uninstall on it (if there is one)
		if (isset($gCms->modules[$module]['uninstall_function']))
		{
			call_user_func_array($gCms->modules[$module]['uninstall_function'], array($gCms));
		}

		#now delete the record
		$query = "DELETE FROM ".cms_db_prefix()."modules WHERE module_name = ".$db->qstr($module);
		$db->Execute($query);
		redirect("plugins.php");
	}

	if ($action == "settrue") {
		$query = "UPDATE ".cms_db_prefix()."modules SET active = 1 WHERE module_name = ".$db->qstr($module);
		$db->Execute($query);
		redirect("plugins.php");
	}

	if ($action == "setfalse") {
		$query = "UPDATE ".cms_db_prefix()."modules SET active = 0 WHERE module_name = ".$db->qstr($module);
		$db->Execute($query);
		redirect("plugins.php");
	}
}

include_once("header.php");

if ($action == "showmoduleabout")
{
	if (isset($gCms->modules[$module]['about_function'])) {
		@ob_start();
		call_user_func_array($gCms->modules[$module]['about_function'], array($gCms));
		$content = @ob_get_contents();
		@ob_end_clean();
		echo "<div class=\"moduleabout\">";
		echo "<h2>".lang('moduleabout', array($module))."</h2>";
		echo $content;
		?>
		<FORM ACTION="plugins.php" METHOD="get">
		<P><INPUT TYPE="submit" VALUE="<?php echo lang('backtoplugins')?>" CLASS="button" onMouseOver="this.className='buttonHover'" onMouseOut="this.className='button'"></P>
		</FORM>
		<?php
		echo "</div>";
	}
}
else if ($action == "showmodulehelp")
{
	if (isset($gCms->modules[$module]['help_function'])) {
		@ob_start();
		call_user_func_array($gCms->modules[$module]['help_function'], array($gCms));
		$content = @ob_get_contents();
		@ob_end_clean();
		echo "<div class=\"moduleabout\">";
		echo "<h2>".lang('modulehelp', array($module))."</h2>";
		echo $content;
		?>
		<FORM ACTION="plugins.php" METHOD="get">
		<P><INPUT TYPE="submit" VALUE="<?php echo lang('backtoplugins')?>" CLASS="button" onMouseOver="this.className='buttonHover'" onMouseOut="this.className='button'"></P>
		</FORM>
		<?php
		echo "</div>";
	}
}
else if ($action == "showpluginhelp")
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
		<FORM ACTION="plugins.php" METHOD="get">
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
		<FORM ACTION="plugins.php" METHOD="get">
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
else
{

	if ($action != "" && !$access) {
		echo "<p class=\"error\">".lang('needpermissionto', array('Modify Modules'))."</p>";
	}

	if (count($gCms->modules) > 0) {

		$query = "SELECT * from ".cms_db_prefix()."modules";
		$result = $db->Execute($query);
		while ($row = $result->FetchRow()) {
			$dbm[$row['module_name']]['Status'] = $row['status'];
			$dbm[$row['module_name']]['Version'] = $row['version'];
			$dbm[$row['module_name']]['Active'] = $row['active'];
		}

?>


	<H3><?php echo lang('modules')?></H3>

	<TABLE CELLSPACING="0" CLASS="admintable">
		<TR>
			<TD><?php echo lang('name')?></TD>
			<TD WIDTH="10%"><?php echo lang('version')?></TD>
			<TD WIDTH="10%"><?php echo lang('status')?></TD>
			<TD WIDTH="10%"><?php echo lang('active')?></TD>
			<TD WIDTH="10%"><?php echo lang('action')?></TD>
			<TD WIDTH="10%"><?php echo lang('help')?></TD>
			<TD WIDTH="10%"><?php echo lang('about')?></TD>
		</TR>

<?php

		$curclass = "row1";
		// construct true/false button images
		$image_true ="<img src=\"../images/cms/true.gif\" alt=\"".lang('true')."\" title=\"".lang('true')."\" border=\"0\">";
		$image_false ="<img src=\"../images/cms/false.gif\" alt=\"".lang('false')."\" title=\"".lang('false')."\" border=\"0\">";
		
		foreach($gCms->modules as $key=>$value)
		{
			echo "<tr class=\"$curclass\">\n";
			echo "<td>$key</td>\n";
			if (!isset($dbm[$key])) #Not installed, lets put up the install button
			{
				echo "<td>".$gCms->modules[$key]['Version']."</td>";
				echo "<td>".lang('notinstalled')."</td>";
				echo "<td>&nbsp;</td>";
				echo "<td><a href=\"plugins.php?action=install&amp;module=".$key."\">".lang('install')."</a></td>";
			}
			else if (version_compare($gCms->modules[$key]['Version'], $dbm[$key]['Version']) == 1) #Check for an upgrade
			{
				echo "<td>".$gCms->modules[$key]['Version']."</td>";
				echo "<td>".lang('needupgrade')."</td>";
				echo "<td>".($dbm[$key]['Active']==="1"?"<a href='plugins.php?action=setfalse&amp;module=".$key."'>".$image_true."</a>":"<a href='plugins.php?action=settrue&amp;module=".$key."'>".$image_false."</a>")."</td>";
				echo "<td><a href=\"plugins.php?action=upgrade&amp;module=".$key."&amp;oldversion=".$dbm[$key]['Version']."&amp;newversion=".$gCms->modules[$key]['Version']."\" onclick=\"return confirm('".lang('upgradeconfirm')."');\">".lang('upgrade')."</a></td>";
			}
			else #Must be installed
			{
				echo "<td>".$dbm[$key]['Version']."</td>";
				echo "<td>".$dbm[$key]['Status']."</td>";
				echo "<td>".($dbm[$key]['Active']==="1"?"<a href='plugins.php?action=setfalse&amp;module=".$key."'>".$image_true."</a>":"<a href='plugins.php?action=settrue&amp;module=".$key."'>".$image_false."</a>")."</td>";
				echo "<td><a href=\"plugins.php?action=uninstall&amp;module=".$key."\" onclick=\"return confirm('".lang('uninstallconfirm')."');\">".lang('uninstall')."</a></td>";
			}
			if (isset($gCms->modules[$key]['help_function']))
			{
				echo "<td><a href=\"plugins.php?action=showmodulehelp&amp;module=".$key."\">".lang('help')."</a></td>";
			}
			else
			{
				echo "<td>&nbsp;</td>";
			}
			if (isset($gCms->modules[$key]['about_function']))
			{
				echo "<td><a href=\"plugins.php?action=showmoduleabout&amp;module=".$key."\">".lang('about')."</a></td>";
			}
			else
			{
				echo "<td>&nbsp;</td>";
			}
		
			echo "</tr>\n";

			($curclass=="row1"?$curclass="row2":$curclass="row1");
		}

	?>

</TABLE>

	<H3><?php echo lang('tags')?></H3>

	<TABLE CELLSPACING="0" CLASS="admintable">
		<TR>
			<TD><?php echo lang('name')?></TD>
			<TD WIDTH="16">&nbsp;</TD>
			<TD WIDTH="16">&nbsp;</TD>
			<TD WIDTH="8%"><?php echo lang('help')?></TD>
			<TD WIDTH="8%"><?php echo lang('about')?></TD>
		</TR>

<?php

		$curclass = "row1";

		foreach($gCms->cmsplugins as $oneplugin)
		{
			echo "<tr class=\"$curclass\">\n";
			if (array_key_exists($oneplugin, $gCms->userplugins))
			{
				echo "<td>$oneplugin (".lang('user').")</td>\n";
			}
			else
			{
				echo "<td>$oneplugin</td>\n";
			}
			if (array_key_exists($oneplugin, $gCms->userplugins))
			{
				echo "<td><a href=\"edituserplugin.php?userplugin_id=".$gCms->userplugins[$oneplugin]."\"><img src=\"../images/cms/edit.gif\" width=\"16\" height=\"16\" border=\"0\" title=\"".lang('edit')."\" alt=\"".lang('edit')."\"></a></td>\n";
			}
			else
			{
				echo "<td>&nbsp;</td>";
			}
			if (array_key_exists($oneplugin, $gCms->userplugins))
			{
				echo "<td><a href=\"deleteuserplugin.php?userplugin_id=".$gCms->userplugins[$oneplugin]."\" onclick=\"return confirm('".lang('deleteconfirm')."');\"><img src=\"../images/cms/delete.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"".lang('delete')."\" title=\"".lang('delete')."\"></a></td>\n";
			}
			else
			{
				echo "<td>&nbsp;</td>";
			}
			if (function_exists('smarty_cms_help_function_'.$oneplugin))
			{
				echo "<td><a href=\"plugins.php?action=showpluginhelp&amp;plugin=".$oneplugin."\">".lang('help')."</a></td>";
			}
			else
			{
				echo "<td>&nbsp;</td>";
			}
			if (function_exists('smarty_cms_about_function_'.$oneplugin))
			{
				echo "<td><a href=\"plugins.php?action=showpluginabout&amp;plugin=".$oneplugin."\">".lang('about')."</a></td>";
			}
			else
			{
				echo "<td>&nbsp;</td>";
			}
		
			echo "</tr>\n";

			($curclass=="row1"?$curclass="row2":$curclass="row1");
		}

	?>

</TABLE>

<DIV CLASS="button"><A HREF="adduserplugin.php"><?php echo lang('addusertag')?></A></DIV>

<BR />

	<?php

	}

}

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
