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
$LOAD_ALL_MODULES=1;
$CMS_TOP_MENU='extensions';
$CMS_ADMIN_TITLE='plugins';

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
		if (isset($gCms->modules[$module]))
		{
			$modinstance = $gCms->modules[$module]['object'];
			$result = $modinstance->Install();

			#now insert a record
			if (!isset($result) || $result === FALSE)
			{
				$query = "INSERT INTO ".cms_db_prefix()."modules (module_name, version, status, active) VALUES (".$db->qstr($module).",".$db->qstr($modinstance->GetVersion()).",'installed',1)";
				$db->Execute($query);
				
				#and insert any dependancies
				if (count($modinstance->GetDependencies()) > 0) #Check for any deps
				{
					#Now check to see if we can satisfy any deps
					foreach ($modinstance->GetDependencies() as $onedepkey=>$onedepvalue)
					{
						$query = "INSERT INTO ".cms_db_prefix()."module_deps (parent_module, child_module, minimum_version, create_date, modified_date) VALUES (?,?,?,?,?)";
						$db->Execute($query, array($onedepkey, $module, $onedepvalue, $db->DBTimeStamp(time()), $db->DBTimeStamp(time())));
					}
				}

				#and show the installpost if necessary...
				if ($modinstance->InstallPostMessage() != FALSE)
				{
					@ob_start();
					echo $modinstance->InstallPostMessage();
					$content = @ob_get_contents();
					@ob_end_clean();
					echo "<div class=\"moduleabout\">";
					echo "<h2>".lang('moduleinstallmessage', array($module))."</h2>";
					echo $content;
					?>
					<FORM ACTION="plugins.php" METHOD="get">
					<P><INPUT TYPE="submit" VALUE="<?php echo lang('backtoplugins')?>" CLASS="button" onMouseOver="this.className='buttonHover'" onMouseOut="this.className='button'"></P>
					</FORM>
					<?php
					echo "</div>";
					include_once("footer.php");
					exit;
					
				}

			}
			else
			{
				//TODO: Echo error
			}
		}
		
		redirect("plugins.php");
	}

	if ($action == 'upgrade')
	{
		if (isset($gCms->modules[$module]))
		{
			$modinstance = $gCms->modules[$module]['object'];
			$result = $modinstance->Upgrade($_GET['oldversion'], $_GET['newversion']);

			#now insert a record

			if (!isset($result) || $result === FALSE)
			{
				$query = "UPDATE ".cms_db_prefix()."modules SET version = ? WHERE module_name = ?";
				$db->Execute($query,array($_GET['newversion'],$module));
			}
			else
			{
				//TODO: Echo error
			}
		}

		redirect("plugins.php");
	}

	if ($action == "uninstall")
	{
		if (isset($gCms->modules[$module]))
		{
			$modinstance = $gCms->modules[$module]['object'];
			$result = $modinstance->Uninstall();

			#now insert a record
			if (!isset($result) || $result === FALSE)
			{
				#now delete the record
				$query = "DELETE FROM ".cms_db_prefix()."modules WHERE module_name = ?";
				$db->Execute($query, array($module));
				
				#delete any dependencies
				$query = "DELETE FROM ".cms_db_prefix()."module_deps WHERE child_module = ?";
				$db->Execute($query, array($module));

				#and show the uninstallpost if necessary...
				if ($modinstance->UninstallPostMessage() != FALSE)
				{
					@ob_start();
					echo $modinstance->UninstallPostMessage();
					$content = @ob_get_contents();
					@ob_end_clean();
					echo "<div class=\"moduleabout\">";
					echo "<h2>".lang('moduleuninstallmessage', array($module))."</h2>";
					echo $content;
					?>
					<FORM ACTION="plugins.php" METHOD="get">
					<P><INPUT TYPE="submit" VALUE="<?php echo lang('backtoplugins')?>" CLASS="button" onMouseOver="this.className='buttonHover'" onMouseOut="this.className='button'"></P>
					</FORM>
					<?php
					echo "</div>";
					include_once("footer.php");
					exit;
				}
			}
			else
			{
				//TODO: Echo error
			}
		}
		
		redirect("plugins.php");
	}

	if ($action == "settrue")
	{
		$query = "UPDATE ".cms_db_prefix()."modules SET active = 1 WHERE module_name = ?";
		$db->Execute($query, array($module));
		redirect("plugins.php");
	}

	if ($action == "setfalse")
	{
		$query = "UPDATE ".cms_db_prefix()."modules SET active = 0 WHERE module_name = ?";
		$db->Execute($query, array($module));
		redirect("plugins.php");
	}
}

include_once("header.php");

if ($action == "showmoduleabout")
{
	if (isset($gCms->modules[$module]['object']))
	{
		echo "<div class=\"moduleabout\">";
		echo "<h2>".lang('moduleabout', array($module))."</h2>";
		if ($gCms->modules[$module]['object']->GetAuthor() != '')
		{
			echo "<br />".lang('author').": " . $gCms->modules[$module]['object']->GetAuthor();
			if ($gCms->modules[$module]['object']->GetAuthorEmail() != '')
			{
				echo ' &lt;' . $gCms->modules[$module]['object']->GetAuthorEmail() . '&gt;';
			}
			echo "<br />";
		}
		echo "<br />".lang('version').": " .$gCms->modules[$module]['object']->GetVersion() . "<br />";

		if ($gCms->modules[$module]['object']->GetChangeLog() != '')
		{
			echo "<br />".lang('changehistory').":<br />";
			echo $gCms->modules[$module]['object']->GetChangeLog() . '<br />';
		}

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
	if (isset($gCms->modules[$module]['object']))
	{
		echo "<div class=\"moduleabout\">";
		echo "<h2>".lang('modulehelp', array($module))."</h2>";
		@ob_start();
		echo $gCms->modules[$module]['object']->GetHelp();
		$content = @ob_get_contents();
		@ob_end_clean();
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
else if ($action == 'missingdeps')
{
	echo "<div class=\"moduleabout\">";
	echo '<h2>'.lang('depsformodule', array($module)).'</h2>';
	echo '<table cellspacing="0" class="admintable">';
	echo '<tr><td>'.lang('name').'</td><td>'.lang('minimumversion').'</td><td>'.lang('installed').'</td></tr>';

	if (isset($gCms->modules[$module]))
	{
		$modinstance = $gCms->modules[$module]['object'];
		if (count($modinstance->GetDependencies()) > 0) #Check for any deps
		{
			$curclass = 'row1';
			#Now check to see if we can satisfy any deps
			debug_buffer($modinstance->GetDependencies(), 'deps in module');
			foreach ($modinstance->GetDependencies() as $onedepkey=>$onedepvalue)
			{
				echo '<tr class="'.$curclass.'"><td>'.$onedepkey.'</td><td>'.$onedepvalue.'</td><td>';

				$havedep = false;

				if (isset($gCms->modules[$onedepkey]) && 
					$gCms->modules[$onedepkey]['installed'] == true &&
					$gCms->modules[$onedepkey]['active'] == true &&
					version_compare($gCms->modules[$onedepkey]['Version'], $onedepvalue) > -1)
				{
					$havedep = true;
				}

				echo lang(($havedep?'true':'false'));
				echo '</td></tr>';
				($curclass=="row1"?$curclass="row2":$curclass="row1");
			}
		}
	}

	echo '</table>';

	?>
	<FORM ACTION="plugins.php" METHOD="get">
	<P><INPUT TYPE="submit" VALUE="<?php echo lang('backtoplugins')?>" CLASS="button" onMouseOver="this.className='buttonHover'" onMouseOut="this.className='button'"></P>
	</FORM>
	<?php
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


	<h3><?php echo lang('modules')?></h3>

		<?php

		if (isset($_SESSION['modules_messages']) && count($_SESSION['modules_messages']) > 0)
		{
			echo '<ul class="messages">';
			foreach ($_SESSION['modules_messages'] as $onemessage)
			{
				echo "<li>" . $onemessage . "</li>";
			}
			echo "</ul>";
			unset($_SESSION['modules_messages']);
		}

		?>

	<table cellspacing="0" class="admintable">
		<tr>
			<td><?php echo lang('name')?></td>
			<td width="10%"><?php echo lang('version')?></td>
			<td width="10%"><?php echo lang('status')?></td>
			<td width="10%"><?php echo lang('active')?></td>
			<td width="10%"><?php echo lang('action')?></td>
			<td width="10%"><?php echo lang('help')?></td>
			<td width="10%"><?php echo lang('about')?></td>
		</tr>

<?php

		$curclass = "row1";
		// construct true/false button images
        $image_true = $themeObject->DisplayImage('true.gif', lang('true'));
        $image_false = $themeObject->DisplayImage('false.gif', lang('false'));

		foreach($gCms->modules as $key=>$value)
		{
			$modinstance = $value['object'];

			echo "<tr class=\"$curclass\">\n";
			echo "<td>$key</td>\n";
            if (!isset($dbm[$key])) #Not installed, lets put up the install button
            {
                $brokendeps = 0;

				$dependencies = $modinstance->GetDependencies();

				if (count($dependencies) > 0) #Check for any deps
				{
					#Now check to see if we can satisfy any deps
					foreach ($dependencies as $onedepkey=>$onedepvalue)
					{
						if (!isset($gCms->modules[$onedepkey]) ||
							$gCms->modules[$onedepkey]['installed'] != true ||
							$gCms->modules[$onedepkey]['active'] != true ||
							version_compare($gCms->modules[$onedepkey]['object']->GetVersion(), $onedepvalue) < 0)
						{
							$brokendeps++;
						}
					}
				}

                echo "<td>".$modinstance->GetVersion()."</td>";
				echo "<td>".lang('notinstalled')."</td>";

                if ($brokendeps > 0)
                {
                	echo "<td>&nbsp;</td>";
                	echo '<td><a href="plugins.php?action=missingdeps&amp;module='.$key.'">'.lang('missingdependency').'</a></td>';
                }
                else
                {
                	echo "<td>&nbsp;</td>";
                    echo "<td><a href=\"plugins.php?action=install&amp;module=".$key."\">".lang('install')."</a></td>";
                }
            }
			else if (version_compare($modinstance->GetVersion(), $dbm[$key]['Version']) == 1) #Check for an upgrade
			{
				echo "<td>".$dbm[$key]['Version']."</td>";
				echo "<td>".lang('needupgrade')."</td>";
				echo "<td>".($dbm[$key]['Active']==="1"?"<a href='plugins.php?action=setfalse&amp;module=".$key."'>".$image_true."</a>":"<a href='plugins.php?action=settrue&amp;module=".$key."'>".$image_false."</a>")."</td>";
				echo "<td><a href=\"plugins.php?action=upgrade&amp;module=".$key."&amp;oldversion=".$dbm[$key]['Version']."&amp;newversion=".$modinstance->GetVersion()."\" onclick=\"return confirm('".lang('upgradeconfirm')."');\">".lang('upgrade')."</a></td>";
			}
			else #Must be installed
			{
				echo "<td>".$dbm[$key]['Version']."</td>";
				echo "<td>".lang('installed')."</td>";
				#Can't be removed if it has a dependency...
				if (!$modinstance->CheckForDependents())
				{
					echo "<td>".($dbm[$key]['Active']==="1"?"<a href='plugins.php?action=setfalse&amp;module=".$key."'>".$image_true."</a>":"<a href='plugins.php?action=settrue&amp;module=".$key."'>".$image_false."</a>")."</td>";
					echo "<td><a href=\"plugins.php?action=uninstall&amp;module=".$key."\" onclick=\"return confirm('".lang('uninstallconfirm')."');\">".lang('uninstall')."</a></td>";
				}
				else
				{
					echo "<td>".($dbm[$key]['Active']==="1"?$image_true:"<a href='plugins.php?action=settrue&amp;module=".$key."'>".$image_false."</a>")."</td>";
					echo "<td>".lang('hasdependents')."</td>";
				}
			}

			//Is there help?
			if ($modinstance->GetHelp() != '')
			{
				echo "<td><a href=\"plugins.php?action=showmodulehelp&amp;module=".$key."\">".lang('help')."</a></td>";
			}
			else
			{
				echo "<td>&nbsp;</td>";
			}

			//About is constructed from other details now
			echo "<td><a href=\"plugins.php?action=showmoduleabout&amp;module=".$key."\">".lang('about')."</a></td>";
		
			echo "</tr>\n";

			($curclass=="row1"?$curclass="row2":$curclass="row1");
		}

	?>

</table>

	<h3><?php echo lang('tags')?></h3>

	<table cellspacing="0" class="admintable">
		<tr>
			<td><?php echo lang('name')?></td>
			<td width="16">&nbsp;</td>
			<td width="16">&nbsp;</td>
			<td width="8%"><?php echo lang('help')?></td>
			<td width="8%"><?php echo lang('about')?></td>
		</tr>

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
				echo "<td><a href=\"edituserplugin.php?userplugin_id=".$gCms->userplugins[$oneplugin]."\">";
                echo $themeObject->DisplayImage('edit.gif', lang('edit'));
                echo "</a></td>\n";
			}
			else
			{
				echo "<td>&nbsp;</td>";
			}
			if (array_key_exists($oneplugin, $gCms->userplugins))
			{
				echo "<td><a href=\"deleteuserplugin.php?userplugin_id=".$gCms->userplugins[$oneplugin]."\" onclick=\"return confirm('".lang('deleteconfirm')."');\">";
                echo $themeObject->DisplayImage('delete.gif', lang('delete'));
                echo "</a></td>\n";
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

</table>

<div class="button"><a href="adduserplugin.php"><?php echo lang('addusertag')?></a></div>

<br />

	<?php

	}

}

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
