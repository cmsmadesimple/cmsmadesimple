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

require_once("../include.php");

check_login();

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

$smarty = new Smarty_CMS($gCms->config);

include_once("header.php");

if ($access)
{
	if ($action == "exportxml")
	{
		// get our xml
		$message = '';
		$files = 0;
		$object = $gCms->modules[$module]['object'];
		$xmltxt = $object->CreateXMLPackage($message,$files);
		if( $files == 0 )
		{
			echo "<p class=\"error\">".lang('errornofilesexported')."</p>";
		}
		else 
		{
			$xmlname = $object->GetName().'-'.$object->GetVersion().'.xml';

			// and send the file
			while(@ob_end_clean());
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
			$result = ModuleOperations::ExpandXMLPackage( $xml, $allowoverwritemodules );
			// at this point, all of the files in that module may have become unusable
			// in the current version of cms.
			if( !$result )
			{
				echo $themeObject->ShowErrors(ModuleOperations::GetLastError());
			}
			else if( $autoinstallupgrade == 0 )
			{
				// no auto install or upgrade
				redirect("listmodules.php");
			}
			// note, wishy, when you dig in here next, everything below here
			// can probably go.
			/*
			else if( !isset( $gCms->modules[$result['name']] ) )
			{
				// looks like we're installing this module
				redirect("listmodules.php?action=install&module=".$result['name']);
			}
			else 
			{ 
				// allow the auto upgrade stuff to do it's thing
				// need new version and old version
				$oldversion = $gCms->modules[$result['name']]['object']->GetVersion();
				$newversion = $result['version'];
				redirect("listmodules.php?action=upgrade&module=".$result['name']."&oldversion=".$oldversion."&newversion=".$newversion);
			}
			*/
		}  
	}

	if ($action == "install")
	{
		$result = ModuleOperations::InstallModule($module,false);
		if( $result[0] == false )
		{
			echo '<div class="pagecontainer">';
			echo '<p class="pageheader">'.lang('moduleerrormessage', $module).'</p>';					
			echo $result[1];
			echo "</div>";
			echo '<p class="pageback"><a class="pageback" href="listmodules.php">&#171; '.lang('back').'</a></p>';
			include_once("footer.php");
			exit;
		}
		else
		{
			$content = $gCms->modules[$module]['object']->InstallPostMessage();
			if( $content != FALSE )
			{
				//Redirect right away so that the installed module shows in the menu
				redirect('listmodules.php?action=showpostinstall&module='.$module);
			}
			// all is good, but no postinstall message
			redirect("listmodules.php");
		}
	}

	if ($action == 'showpostinstall')
	{
		// this is probably dead code now
		if (isset($gCms->modules[$module]))
		{
			$modinstance = $gCms->modules[$module]['object'];
			if ($modinstance->InstallPostMessage() != FALSE)
			{
				@ob_start();
				echo $modinstance->InstallPostMessage();
				$content = @ob_get_contents();
				@ob_end_clean();
				echo '<div class="pagecontainer">';
				echo '<p class="pageheader">'.lang('moduleinstallmessage', array($module)).'</p>';					
				echo $content;
				echo "</div>";
				echo '<p class="pageback"><a class="pageback" href="listmodules.php">&#171; '.lang('back').'</a></p>';					
				include_once("footer.php");
				exit;
			}
		}
	}

	if ($action == 'upgrade')
	{
		$result = ModuleOperations::UpgradeModule( $module, $_GET['oldversion'], $_GET['newversion'] );	  
		if( !$result )
		{
			@ob_start();
			echo $modinstance->GetLastError();
			$content = @ob_get_contents();
			@ob_end_clean();
			echo '<div class="pagecontainer">';
			echo '<p class="pageheader">'.lang('moduleerrormessage', array($module)).'</p>';					
			echo $content;
			echo "</div>";
			echo '<p class="pageback"><a class="pageback" href="listmodules.php">&#171; '.lang('back').'</a></p>';
			include_once("footer.php");
			exit;
		}
		redirect("listmodules.php");
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
					redirect('listmodules.php?action=showpostuninstall&module='.$module);
				}
			}
			else
			{
				//TODO: Echo error
			}
		}

		redirect("listmodules.php");
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
				echo '<div class="pagecontainer">';
				echo '<p class="pageheader">'.lang('moduleuninstallmessage', array($module)).'</p>';					
				echo $content;
				echo "</div>";
				echo '<p class="pageback"><a class="pageback" href="listmodules.php">&#171; '.lang('back').'</a></p>';					
				include_once("footer.php");
				exit;
			}
		}
	}

	if ($action == "settrue")
	{
		$query = "UPDATE ".cms_db_prefix()."modules SET active = ? WHERE module_name = ?";
		$db->Execute($query, array(1,$module));
		redirect("listmodules.php");
	}

	if ($action == "setfalse")
	{
		$query = "UPDATE ".cms_db_prefix()."modules SET active = ? WHERE module_name = ?";
		$db->Execute($query, array(0,$module));
		redirect("listmodules.php");
	}
}

include_once("header.php");

if ($action == "showmoduleabout")
{
	if (isset($gCms->modules[$module]['object']))
	{
		echo '<div class="pagecontainer">';
		echo '<p class="pageheader">'.lang('moduleabout', array($module)).'</p>';
		echo $gCms->modules[$module]['object']->GetAbout();
		echo "</div>";
	}
	echo '<p class="pageback"><a class="pageback" href="listmodules.php">&#171; '.lang('back').'</a></p>';
}
else if ($action == "showmodulehelp")
{
	if (isset($gCms->modules[$module]['object']))
	{
		echo '<div class="pagecontainer">';
		echo $themeObject->ShowHeader(lang('modulehelp', array($module)), '', lang('wikihelp', $module), 'wiki');
		echo $gCms->modules[$module]['object']->GetHelpPage();
		echo "</div>";
	}
	echo '<p class="pageback"><a class="pageback" href="listmodules.php">&#171; '.lang('back').'</a></p>';
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
	echo '<p class="pageback"><a class="pageback" href="listmodules.php">&#171; '.lang('back').'</a></p>';
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
			$modinstance = $value['object'];

			echo "<tr class=\"".$curclass."\" onmouseover=\"this.className='".$curclass.'hover'."';\" onmouseout=\"this.className='".$curclass."';\">\n";
			//Is there help?
			if ($modinstance->GetHelp() != '')
			{
				echo "<td><a href=\"listmodules.php?action=showmodulehelp&amp;module=".$key."\">".$key."</a></td>";
			}
			else
			{
				echo "<td>$key</td>\n";
			}
			#Make sure it's a valid module for this version of CMSMS
			if (version_compare($modinstance->MinimumCMSVersion(), $CMS_VERSION) == 1)
			{
				// Fix undefined index error if module is not already installed.
				if (FALSE == empty($dbm[$key]['Version'])) {
					echo "<td>".$dbm[$key]['Version']."</td>";
				} else {
					echo "<td>&nbsp</td>";
				}
				echo '<td colspan="3">'.lang('minimumversionrequired').': '.$modinstance->MinimumCMSVersion().'</td>';
			}
			else if (version_compare($modinstance->MaximumCMSVersion(), $CMS_VERSION) == -1)
			{
				// Fix undefined index error if module is not already installed.
				if (FALSE == empty($dbm[$key]['Version'])) {
					echo "<td>".$dbm[$key]['Version']."</td>";
				} else {
					echo "<td>&nbsp</td>";
				}
				echo '<td colspan="3">'.lang('maximumversionsupported').': '.$modinstance->MaximumCMSVersion().'</td>';
			}
            else if (!isset($dbm[$key])) #Not installed, lets put up the install button
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
                	echo '<td><a href="listmodules.php?action=missingdeps&amp;module='.$key.'">'.lang('missingdependency').'</a></td>';
                }
                else
                {
                	echo "<td>&nbsp;</td>";
                    echo "<td><a href=\"listmodules.php?action=install&amp;module=".$key."\">".lang('install')."</a></td>";
                }
            }
			else if (version_compare($modinstance->GetVersion(), $dbm[$key]['Version']) == 1) #Check for an upgrade
			{
				echo "<td>".$dbm[$key]['Version']."</td>";
				echo "<td>".lang('needupgrade')."</td>";
				echo "<td class=\"pagepos\">".($dbm[$key]['Active']==true?"<a href='listmodules.php?action=setfalse&amp;module=".$key."'>".$image_true."</a>":"<a href='listmodules.php?action=settrue&amp;module=".$key."'>".$image_false."</a>")."</td>";
				echo "<td><a href=\"listmodules.php?action=upgrade&amp;module=".$key."&amp;oldversion=".$dbm[$key]['Version']."&amp;newversion=".$modinstance->GetVersion()."\" onclick=\"return confirm('".lang('upgradeconfirm')."');\">".lang('upgrade')."</a></td>";
			}
			else #Must be installed
			{
				echo "<td>".$dbm[$key]['Version']."</td>";
				echo "<td>".lang('installed')."</td>";
				#Can't be removed if it has a dependency...
				if (!$modinstance->CheckForDependents())
				{
					echo "<td class=\"pagepos\">".($dbm[$key]['Active']==true?"<a href='listmodules.php?action=setfalse&amp;module=".$key."'>".$image_true."</a>":"<a href='listmodules.php?action=settrue&amp;module=".$key."'>".$image_false."</a>")."</td>";
					echo "<td><a href=\"listmodules.php?action=uninstall&amp;module=".$key."\" onclick=\"return confirm('".($modinstance->UninstallPreMessage() !== FALSE?$modinstance->UninstallPreMessage():lang('uninstallconfirm').' '.$key)."');\">".lang('uninstall')."</a></td>";
				}
				else
				{
				  // HAS DEPENDENTS ===============
				  $result = $db->Execute("SELECT child_module from
".cms_db_prefix()."module_deps WHERE parent_module='$key'");

				  $dependentof = "";
				  while ($result && $row = $result->FetchRow()) {
				    $dependentof .= $row['child_module'].",";
				  }

				  echo "<td class=\"pagepos\">".($dbm[$key]['Active']==true?$image_true:"<a href='listmodules.php?action=settrue&amp;module=".$key."'>".$image_false."</a>")."&quot;</td>";
				  echo "<td>".lang('hasdependents')."
					     (<strong>$dependentof</strong>)</td>";
				  // END HAS DEPENDENTS ===========
				}
			}

			//Is there help?
			if ($modinstance->GetHelp() != '')
			{
				echo "<td><a href=\"listmodules.php?action=showmodulehelp&amp;module=".$key."\">".lang('help')."</a></td>";
			}
			else
			{
				echo "<td>&nbsp;</td>";
			}

			//About is constructed from other details now
			echo "<td><a href=\"listmodules.php?action=showmoduleabout&amp;module=".$key."\">".lang('about')."</a></td>";

			//xml export
			echo '<td><a href="listmodules.php?action=exportxml&amp;module='.$key.'"><img border="0" src="../images/cms/xml_rss.gif" alt="'.lang('xml').'" /></a></td>';
			echo "</tr>\n";

			($curclass=="row1"?$curclass="row2":$curclass="row1");
		}

	?>

	</tbody>
</table>
<?php
// Only show XML upload form if the modules folder is writable
if (FALSE == is_writable($config['root_path'].DIRECTORY_SEPARATOR.'modules'))
{
	echo $themeObject->ShowErrors(lang('modulesnotwritable'));
}
else
{
?>
<form method="post" action="listmodules.php?action=importxml" enctype="multipart/form-data">
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
