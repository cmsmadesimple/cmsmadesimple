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

/* TEST - ALBY */
require_once("./html_entity_decode_utf8.php");

$CMS_ADMIN_PAGE=1;
$LOAD_ALL_MODULES=1;

require_once("../include.php");

check_login();
$thisurl=basename(__FILE__).'?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];

$module = "";
if (isset($_GET["module"])) $module = $_GET["module"];

$plugin = "";
if (isset($_GET["plugin"])) $plugin = $_GET["plugin"];

$action = "";
if (isset($_GET["action"])) $action = $_GET["action"];

$allowoverwritemodules = 0;
if (isset($_POST["allowoverwrite"])) $allowoverwritemodules = $_POST["allowoverwrite"];

$autoinstallupgrade = 0; // keep this here for a bit, just incase

$userid = get_userid();
$access = check_permission($userid, "Modify Modules");
if (!$access) {
	die('Permission Denied');
return;
}

$smarty = new Smarty_CMS($gCms->config);
$db =& $gCms->GetDb();

//Messagestring (success) for module operations
$modulemessage="";

include_once("header.php");

if ($access)
{
	if ($action == "chmod" )
	{
		$result = chmod_r( $config['root_path'].DIRECTORY_SEPARATOR.
		'modules'.DIRECTORY_SEPARATOR.$module, 0777 );
		if( !$result )
		{
			echo $themeObject->ShowErrors(lang('cantchmodfiles'));
		}
		else
		{
			redirect($thisurl);
		}
	}

	if ($action == "exportxml")
	{
		// get our xml
		$message = '';
		$files = 0;
		$modops =& $gCms->GetModuleOperations();
		$object = $gCms->modules[$module]['object'];
		$xmltxt = $modops->CreateXMLPackage($object,$message,$files);
		if( $files == 0 )
		{
			echo "<p class=\"error\">".lang('errornofilesexported')."</p>";
		}
		else 
		{
			$xmlname = $object->GetName().'-'.$object->GetVersion().'.xml';

			// and send the file
			ob_end_clean();
			header('Content-Description: File Transfer');
			header('Content-Type: application/force-download');
			header('Content-Disposition: attachment; filename='.$xmlname);
			//     header('Content-Type: text/xml');
			echo $xmltxt;
			exit();
		}
	}

	if ($action == "importxml" )
	{
		$fieldName = "browse_xml";
		if (!isset ($_FILES[$fieldName]) || !isset ($_FILES)
		|| !is_array ($_FILES[$fieldName]) || !$_FILES[$fieldName]['name'])
		{
			echo $themeObject->ShowErrors(lang('noxmlfileuploaded'));
		}
		else
		{
			// normalize the file variable
			$file = $_FILES[$fieldName];

			// $file['tmp_name'] is the file we have to parse
			$xml = file_get_contents( $file['tmp_name'] );

			// and parse it
			$modops =& $gCms->GetModuleOperations();
			$result = $modops->ExpandXMLPackage( $xml, $allowoverwritemodules );
			// at this point, all of the files in that module may have become unusable
			// in the current version of cms.
			if( !$result )
			{
				echo $themeObject->ShowErrors($modops->GetLastError());
			}
			else if( $autoinstallupgrade == 0 )
			{
				// no auto install or upgrade
				redirect($thisurl);
			}
		}  
	}

	if ($action == "install")
	{
		$modops =& $gCms->GetModuleOperations();
		$result = $modops->InstallModule($module,false);
		if( $result[0] == false )
		{
			echo '<div class="pagecontainer">';
			echo '<p class="pageheader">'.lang('moduleerrormessage', $module).'</p>';					
			echo $result[1];
			echo "</div>";
			echo '<p class="pageback"><a class="pageback" href="{$thisurl}">&#171; '.lang('back').'</a></p>';
			include_once("footer.php");
			exit;
		}
		else
		{
			$content = $gCms->modules[$module]['object']->InstallPostMessage();
			if( $content != FALSE )
			{
				//Redirect right away so that the installed module shows in the menu
				redirect($thisurl.'&amp;action=showpostinstall&amp;module='.$module);
			}
			// all is good, but no postinstall message
			redirect($thisurl);
		}
	}

	if ($action == 'showpostinstall')
	{
		// this is probably dead code now
		if (isset($gCms->modules[$module]))
		{
			$modinstance =& $gCms->modules[$module]['object'];
			if ($modinstance->InstallPostMessage() != FALSE)
			{
				@ob_start();
				echo $modinstance->InstallPostMessage();
				$content = @ob_get_contents();
				@ob_end_clean();
				echo $themeObject->ShowMessage($content);
			}
		}
	}

	if ($action == 'remove')
	{
		$result = recursive_delete( $config['root_path'].DIRECTORY_SEPARATOR.
		'modules'.DIRECTORY_SEPARATOR.$module );
		if( !$result )
		{
			echo '<div class="pagecontainer">';
			echo '<p class="pageheader">'.lang('moduleerrormessage', array($module)).'</p>';					
			echo lang('cantremovefiles');
			echo "</div>";
			echo '<p class="pageback"><a class="pageback" href="'.$thisurl.'">&#171; '.lang('back').'</a></p>';
			include_once("footer.php");
		}
		else
		{
			redirect($thisurl);
		}
	}

	if ($action == 'upgrade')
	{
		$modops =& $gCms->GetModuleOperations();
		$result = $modops->UpgradeModule( $module, $_GET['oldversion'], $_GET['newversion'] );	  
		if( !$result )
		{
			@ob_start();
			echo $modops->GetLastError();
			$content = @ob_get_contents();
			@ob_end_clean();
			echo $themeObject->ShowErrors(lang('moduleupgradeerror'));
		}
		redirect($thisurl);
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

				Events::SendEvent('Core', 'ModuleUninstalled', array('name' => $module));

				#and show the uninstallpost if necessary...
				if ($modinstance->UninstallPostMessage() != FALSE)
				{
					//Redirect right away so that the uninstalled module is removed from the menu
					redirect($thisurl.'&amp;action=showpostuninstall&amp;module='.$module);
				}
			}
			else
			{
				//TODO: Echo error
			}
		}

		redirect($thisurl);
	}

	if ($action == 'showpostuninstall')
	{
		// this is probably dead code now
		if (isset($gCms->modules[$module]))
		{
			$modinstance = $gCms->modules[$module]['object'];
			if ($modinstance->UninstallPostMessage() != FALSE)
			{
				@ob_start();
				echo $modinstance->UninstallPostMessage();
				$content = @ob_get_contents();
				@ob_end_clean();
				echo $themeObject->ShowMessage($content);
			}
		}
	}

	if ($action == "settrue")
	{
		$query = "UPDATE ".cms_db_prefix()."modules SET active = ? WHERE module_name = ?";
		$db->Execute($query, array(1,$module));
		redirect($thisurl);
	}

	if ($action == "setfalse")
	{
		$query = "UPDATE ".cms_db_prefix()."modules SET active = ? WHERE module_name = ?";
		$db->Execute($query, array(0,$module));
		redirect($thisurl);
	}
}

if ($action == "showmoduleabout")
{
	if (isset($gCms->modules[$module]['object']))
	{
		echo '<div class="pagecontainer">';
		echo '<p class="pageheader">'.lang('moduleabout', array($module)).'</p>';
		echo $gCms->modules[$module]['object']->GetAbout();
		echo "</div>";
	}
	echo '<p class="pageback"><a class="pageback" href="'.$thisurl.'">&#171; '.lang('back').'</a></p>';
}
else if ($action == "showmodulehelp")
{
	if (isset($gCms->modules[$module]['object']))
	{
		echo '<div class="pagecontainer">';
		// Commented out because of bug #914 and had to use code extra below
		// echo $themeObject->ShowHeader(lang('modulehelp', array($module)), '', lang('wikihelp', $module), 'wiki');

		$header  = '<div class="pageheader">';
		$header .= lang('modulehelp', array($module));
		$wikiUrl = $config['wiki_url'];
		$module_name = $gCms->modules[$module]['object']->GetName();
		// Turn ModuleName into _Module_Name
		$moduleName =  preg_replace('/([A-Z])/', "_$1", $module_name);
		$moduleName =  preg_replace('/_([A-Z])_/', "$1", $moduleName);
		if ($moduleName{0} == '_')
		{
			$moduleName = substr($moduleName, 1);
		}
		// Include English translation of titles. (Can't find better way to get them)
		$dirname = dirname(__FILE__);
		include($dirname.'/lang/en_US/admin.inc.php');
		$section = $lang['admin'][$gCms->modules[$module]['object']->GetAdminSection()];
		$wikiUrl .= '/'.$section.'/'.$moduleName;
		if (FALSE == get_preference($userid, 'hide_help_links'))
		{
			// Clean up URL
			$wikiUrl = str_replace(' ', '_', $wikiUrl);
			$wikiUrl = str_replace('&amp;', 'and', $wikiUrl);

			$help_title = lang('help_external');

			$image_help = $themeObject->DisplayImage('icons/system/info.gif', lang('help'),'','','systemicon');
			$image_help_external = $themeObject->DisplayImage('icons/system/info-external.gif', lang('help'),'','','systemicon');		
			$header .= '<span class="helptext"><a href="'.$wikiUrl.'" target="_blank">'.$image_help_external.'</a> <a href="'.$wikiUrl.'" target="_blank">'.lang('help').'</a> ('.lang('new_window').')</span>';
		}

		$header .= '</div>';
		echo $header;     

		echo $gCms->modules[$module]['object']->GetHelpPage();
		echo "</div>";
	}

	echo '<p class="pageback"><a class="pageback" href="'.$thisurl.'">&#171; '.lang('back').'</a></p>';
}
else if ($action == 'missingdeps')
{
	echo '<div class="pagecontainer">';
	echo '<p class="pageheader">'.lang('depsformodule', array($module)).'</p>';
	echo '<table cellspacing="0" class="AdminTable">';
	echo '<thead>';
	echo '<tr><th>'.lang('name').'</th><th>'.lang('minimumversion').'</th><th>'.lang('installed').'</th></tr>';
	echo '</thead>';
	echo '<tbody>';

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
				version_compare($gCms->modules[$onedepkey]['object']->GetVersion(), $onedepvalue) > -1)
				{
					$havedep = true;
				}

				echo lang(($havedep?'true':'false'));
				echo '</td></tr>';
				($curclass=="row1"?$curclass="row2":$curclass="row1");
			}
		}
	}

	echo '</tbody>';
	echo '</table>';
	echo '</div>';
	echo '<p class="pageback"><a class="pageback" href="'.$thisurl.'">&#171; '.lang('back').'</a></p>';
}
else
{

	if ($action != "" && !$access) {
		echo "<p class=\"error\">".lang('needpermissionto', array('Modify Modules'))."</p>";
	}

	if (count($gCms->modules) > 0) {

		$query = "SELECT * from ".cms_db_prefix()."modules";
		$result = $db->Execute($query);
		while ($result && $row = $result->FetchRow()) {
			$dbm[$row['module_name']]['Status'] = $row['status'];
			$dbm[$row['module_name']]['Version'] = $row['version'];
			$dbm[$row['module_name']]['Active'] = ($row['active'] == 1?true:false);
		}

		?>

		<div class="pagecontainer">
		<div class="pageoverflow">
		<?php

		if (isset($_SESSION['modules_messages']) && count($_SESSION['modules_messages']) > 0)
		{
			echo '<ul class="messages">';

			// do we need to worry about this for XSS?
			foreach ($_SESSION['modules_messages'] as $onemessage)
			{
				echo "<li>" . $onemessage . "</li>";
			}
			echo "</ul>";
			unset($_SESSION['modules_messages']);
		}

		?>

		<?php echo $themeObject->ShowHeader('modules').'</div>'; ?>
		<table cellspacing="0" class="pagetable">
		<thead>
		<tr>
		<th><?php echo lang('name')?></th>
		<th><?php echo lang('version')?></th>
		<th><?php echo lang('status')?></th>
		<th class="pagepos"><?php echo lang('active')?></th>
		<th><?php echo lang('action')?></th>
		<th><?php echo lang('help')?></th>
		<th><?php echo lang('about')?></th>
		<th><?php echo lang('export')?></th>
		</tr>
		</thead>
		<tbody>
		<?php

		$curclass = "row1";
		// construct true/false button images
		$image_true = $themeObject->DisplayImage('icons/system/true.gif', lang('true'),'','','systemicon');
		$image_false = $themeObject->DisplayImage('icons/system/false.gif', lang('false'),'','','systemicon');

		foreach($gCms->modules as $key=>$value)
		{
			$modinstance =& $value['object'];
			$is_sysmodule = (array_search( $key, $gCms->cmssystemmodules ) !== FALSE);
			$namecol = $key;
			$versioncol = "&nbsp;";
			$statuscol = array();
			$statusspans = false;
			$actioncol = array();
			$activecol = "&nbsp;";
			$helpcol = "&nbsp;";
			$aboutcol = "&nbsp;";

			$xmlcol = "&nbsp;";

			$xmlcol = '<a href="'.$thisurl.'&amp;action=exportxml&amp;module='.$key.'"><img border="0" src="../images/cms/xml_rss.gif" alt="'.lang('xml').'" /></a>';

			//Is there help?
			if ($modinstance->GetHelp() != '')
			{
				$namecol = "<a href=\"{$thisurl}&amp;action=showmodulehelp&amp;module=".$key."\">".$key."</a>";
			}

			// check these modules permissions to see if we can uninstall this thing
			$permsok = is_directory_writable( $config['root_path'].DIRECTORY_SEPARATOR.
			'modules'.DIRECTORY_SEPARATOR.$key );
			$maxverok = version_compare($modinstance->MaximumCMSVersion(), $CMS_VERSION);
			$maxverok = ($maxverok >= 0 )?1:0;

			#Make sure it's a valid module for this version of CMSMS
			if (version_compare($modinstance->MinimumCMSVersion(), $CMS_VERSION) == 1)
			{
				// Fix undefined index error if module is not already installed.
				$statuscol[] = '<span class="important">'.lang('minimumversionrequired').': '.$modinstance->MinimumCMSVersion().'</span>';
				$xmlcol = "&nbsp;";
				$statusspans = true;
			}
			else if( $maxverok == 0 )
			{
			  // maximum cms version exceeded
			  $xmlcol = "&nbsp;";
			  $statuscol[]  = '<span class="important">'.lang('maximumversionsupported').' = '.$modinstance->MaximumCMSVersion()."</span>";
			}

			if (!isset($dbm[$key])) #Not installed, lets put up the install button
			{
				$brokendeps = 0;
				$xmlcol = "&nbsp;";

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

				$versioncol = $modinstance->GetVersion();
				$statuscol[] = lang('notinstalled');

				if ($brokendeps > 0)
				{
					$actioncol[] = '<a href="'.$thisurl.'&amp;action=missingdeps&amp;module='.$key.'">'.lang('missingdependency').'</a>';
				}
				else if( $maxverok == 1)
				{
					$actioncol[] = "<a href=\"{$thisurl}&amp;action=install&amp;module=".$key."\">".lang('install')."</a>";
					$xmlcol = '&nbsp;';
				}

				if( !$is_sysmodule )
				{
					if( $permsok )
					{
						$actioncol[] .= "<a href=\"{$thisurl}&amp;action=remove&amp;module=".$key."\" onclick=\"return confirm('".cms_html_entity_decode_utf8(lang('removeconfirm'),true)."');\">".lang('remove')."</a>";
					}
					else
					{
						$actioncol[] = "<a href=\"{$thisurl}&amp;action=chmod&amp;module=".$key."\" onclick=\"return confirm('".cms_html_entity_decode_utf8(lang('changepermissionsconfirm'),true)."');\">".lang('changepermissions')."</a>";
					}
				}
			}
			else if (version_compare($modinstance->GetVersion(), 
						 $dbm[$key]['Version']) == 1) 
                           #Check for an upgrade
			{
			        $xmlcol = "&nbsp;";
				$versioncol = $dbm[$key]['Version'];
				$statuscol[]  = '<span class="important">'.lang('needupgrade').'</span>';
				$activecol  = ($dbm[$key]['Active']==true?"<a href='{$thisurl}&amp;action=setfalse&amp;module=".$key."'>".$image_true."</a>":"<a href='{$thisurl}&amp;action=settrue&amp;module=".$key."'>".$image_false."</a>");
			  if( $maxverok == 1)
			    {
				$actioncol[]  = "<a href=\"{$thisurl}&amp;action=upgrade&amp;module=".$key."&amp;oldversion=".$dbm[$key]['Version']."&amp;newversion=".$modinstance->GetVersion()."\" onclick=\"return confirm('".cms_html_entity_decode_utf8(lang('upgradeconfirm'),true)."');\">".lang('upgrade')."</a>";
			    }
			  $xmlcol = '&nbsp;';
			}
			else #Must be installed
			{
				$versioncol = $dbm[$key]['Version'];
				$statuscol[]  = lang('installed');
				//$actioncol  = "&nbsp;";

				#Can't be removed if it has a dependency...
				if (!$modinstance->CheckForDependents())
				{
					$activecol = ($dbm[$key]['Active']==true?"<a href='{$thisurl}&amp;action=setfalse&amp;module=".$key."'>".$image_true."</a>":"<a href='{$thisurl}&amp;action=settrue&amp;module=".$key."'>".$image_false."</a>");
					$actioncol[] = "<a href=\"{$thisurl}&amp;action=uninstall&amp;module=".$key."\" onclick=\"return confirm('".($modinstance->UninstallPreMessage() !== FALSE? cms_utf8entities($modinstance->UninstallPreMessage()):lang('uninstallconfirm').' '.$key)."');\">".lang('uninstall')."</a>";
				}
				else
				{
					// HAS DEPENDENTS ===============
					$result = $db->Execute("SELECT child_module from
					".cms_db_prefix()."module_deps WHERE parent_module='$key'");

					$dependentof = array();;
					while ($result && $row = $result->FetchRow()) {
						$dependentof[$row['child_module']] = "";
					}
					$str = implode(array_keys($dependentof),", ");
					$activecol = ($dbm[$key]['Active']==true?$image_true:"<a href='{$thisurl}&amp;action=settrue&amp;module=".$key."'>".$image_false."</a>");
					$statuscol[] = lang('hasdependents')." (<strong>$str</strong>)";
					// END HAS DEPENDENTS ===========
				}

				if( !$permsok )
				{
					$statuscol[] = lang('cantremove');
					$actioncol[] = "<a href=\"{$thisurl}&amp;action=chmod&amp;module=".$key."\" onclick=\"return confirm('".cms_html_entity_decode_utf8(lang('changepermissionsconfirm'),true)."');\">".lang('changepermissions')."</a>";
				}
			}

			//Is there help?
			if ($modinstance->GetHelp() != '')
			{
				$helpcol = "<a href=\"{$thisurl}&amp;action=showmodulehelp&amp;module=".$key."\">".lang('help')."</a>";
			}

			//About is constructed from other details now
			$aboutcol = "<a href=\"{$thisurl}&amp;action=showmoduleabout&amp;module=".$key."\">".lang('about')."</a>";

			// row output
			echo "<tr class=\"".$curclass."\" onmouseover=\"this.className='".$curclass.'hover'."';\" onmouseout=\"this.className='".$curclass."';\">\n";
			echo "<td>$namecol</td>";
			echo "<td>$versioncol</td>";
			if( $statusspans === true)
			{
			  echo '<td colspan="3">'.implode('<br/>',$statuscol)."</td>";
			}
			else
			{
			  echo "<td>".implode('<br/>',$statuscol)."</td>";
			  echo '<td class="pagepos">'.$activecol.'</td>';
			  echo '<td>'.implode('<br/>',$actioncol).'</td>';
			}
			echo "<td>$helpcol</td>";
			echo "<td>$aboutcol</td>";
			echo "<td>$xmlcol</td>";
			echo "</tr>\n";

			($curclass=="row1"?$curclass="row2":$curclass="row1");
		}

		?>

		</tbody>
		</table>
		<?php
		// Only show XML upload form if the modules folder is writable
		//if (FALSE == is_writable($config['root_path'].DIRECTORY_SEPARATOR.'modules'))
	        if (FALSE == can_admin_upload())
		  {
		    echo $themeObject->ShowErrors(lang('modulesnotwritable'));
		  }
		else
		  {
			?>
			<form method="post" action="<?php echo $thisurl?>&amp;action=importxml" enctype="multipart/form-data">
			<fieldset>
			<legend><?php echo lang('uploadxmlfile')?></legend>
			<div class="pageoverflow">
			<p class="pagetext"><?php echo lang('uploadfile')?>:</p>
			<p class="pageinput">
			<input type="file" name="browse_xml"/>
			</p>
			</div>
			<div class="pageoverflow">
			<p class="pagetext"><?php echo lang('overwritemodule')?>:</p>
			<p class="pageinput">
			<input type="checkbox" name="allowoverwrite" value="1" />
			</p>
			</div>
			<div class="pageoverflow">
			<p class="pagetext">&nbsp;</p>
			<p class="pageinput">
			<input type="submit" name="submit" value="<?php echo lang('submit')?>" />
			</p>
			</div>
			</fieldset>
			</form>
			<?php
		}
		echo '</div>';
	}
	echo '<p class="pageback"><a class="pageback" href="'.$themeObject->BackUrl().'">&#171; '.lang('back').'</a></p>';
}

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
