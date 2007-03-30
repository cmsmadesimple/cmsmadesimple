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

class CMSInstallerPage5 extends CMSInstallerPage
{
	/**
	 * Class constructor
	*/
	function CMSInstallerPage5(&$smarty, $errors)
	{
		$this->CMSInstallerPage(5, $smarty, $errors);
	}
	
	function assignVariables()
	{
		$values = array();
		$values['admininfo']['username'] = $_POST['adminusername'];
		$values['admininfo']['email'] = $_POST['adminemail'];
		$values['admininfo']['password'] = $_POST['adminpassword'];
		$link = str_replace(" ", "%20", $_POST['docroot']);
				
		$this->smarty->assign('values', $values);
		$this->smarty->assign('homepage_link', $link);
		$this->smarty->assign('errors', $this->errors);
	}
	
	function preContent()
	{
		global $gCms;

		require_once(cms_join_path(CMS_BASE, 'lib', 'config.functions.php'));
		
		// check if db info is correct as it should at this point to prevent an undeleted installation dir
		// to be used for sending spam by messing up $_POST variables
		$db = ADONewConnection($_POST['dbms'], 'pear:date:extend:transaction');
		
		if (! $db->Connect($_POST['host'],$_POST['username'],$_POST['password'],$_POST['database']))
		{
			$this->errors[] = 'Could not connect to the database. Verify that username and password are correct, and that the user has access to the given database.';
			return;
		}
	
		$newconfig = cms_config_load();
	
		$newconfig['dbms'] = $_POST['dbms'];
		$newconfig['db_hostname'] = $_POST['host'];
		$newconfig['db_username'] = $_POST['username'];
		$newconfig['db_password'] = $_POST['password'];
		$newconfig['db_name'] = $_POST['database'];
		$newconfig['db_prefix'] = $_POST['prefix'];
		$newconfig['root_url'] = $_POST['docroot'];
		$newconfig['root_path'] = str_replace('\\\\', '\\', addslashes($_POST['docpath']));
		$newconfig['query_var'] = $_POST['querystr'];
		$newconfig['use_bb_code'] = false;
		$newconfig['use_smarty_php_tags'] = false;
		$newconfig['previews_path'] = TMP_CACHE_LOCATION;
		$newconfig["uploads_path"] = $newconfig['root_path'] . DIRECTORY_SEPARATOR . "uploads";
			// Note: leave the / slashes for the URLs
		$newconfig["uploads_url"] = $newconfig['root_url'] . "/uploads";	
		$newconfig["image_uploads_path"] = $newconfig['root_path'] . DIRECTORY_SEPARATOR . "uploads".DIRECTORY_SEPARATOR."images";
			// Note: leave the / slashes for the URLs
		$newconfig["image_uploads_url"] = $newconfig['root_url'] ."/uploads/images";
		$maxFileSize = ini_get('upload_max_filesize');
		if (!is_numeric($maxFileSize))
		{
			$l=strlen($maxFileSize);
			$i=0;$ss='';$x=0;
			while ($i < $l)
			{
				if (is_numeric($maxFileSize[$i]))
					{$ss .= $maxFileSize[$i];}
				else
				{
					if (strtolower($maxFileSize[$i]) == 'm') $x=1000000;
					if (strtolower($maxFileSize[$i]) == 'k') $x=1000;
				}
				$i ++;
			}
			$maxFileSize=$ss;
			if ($x >0) $maxFileSize = $ss * $x;
		}
		else
		{
			$maxFileSize = 1000000;
		}
		$newconfig["max_upload_size"] = $maxFileSize;
		//$newconfig["max_upload_size"] = 1000000;
		$newconfig["debug"] = false;
		$newconfig["assume_mod_rewrite"] = false;
		$newconfig["auto_alias_content"] = true;
		$newconfig["image_manipulation_prog"] = "GD";
		$newconfig["image_transform_lib_path"] = "/usr/bin/ImageMagick/";
		$newconfig["use_Indite"] = false;
		$newconfig["image_uploads_path"] = $newconfig['root_path'] . DIRECTORY_SEPARATOR . "uploads" . DIRECTORY_SEPARATOR . "images";
		$newconfig["image_uploads_url"] = $newconfig['root_url'] . "/uploads/images";
		$newconfig["default_encoding"] = "";
		$newconfig["disable_htmlarea_translation"] = false;
		$newconfig["admin_dir"] = "admin";
		$newconfig["persistent_db_conn"] = false;
		$newconfig["default_upload_permission"] = '664';
		$newconfig["page_extension"] = "";
		$newconfig["locale"] = "";
		$newconfig["admin_encoding"] = "utf-8";
		$newconfig["use_adodb_lite"] = true;
		$newconfig['internal_pretty_urls'] = false;
		$newconfig['use_hierarchy'] = false;
		$newconfig['old_stylesheet'] = false;
		$newconfig['wiki_url'] = "http://wiki.cmsmadesimple.org/index.php/User_Handbook/Admin_Panel";
		$newconfig['backwards_compatible'] = false;
	
		$configfile = CONFIG_FILE_LOCATION;
		## build the content for config file
	
		if ((file_exists($configfile) && is_writable($configfile)) || !file_exists($configfile)) {
			cms_config_save($newconfig);
		} 
		else 
		{
			echo "Error: Cannot write to $config.<br />\n";
			exit;
		}
	
		if (file_exists(TMP_CACHE_LOCATION.'/SITEDOWN'))
		{
			if (!unlink(TMP_CACHE_LOCATION.'/SITEDOWN'))
			{
				echo "Error: Could not remove the tmp/cache/SITEDOWN file. Please remove manually.";
			}
		}
	
		#Do module installation
		if (isset($_POST["createtables"]))
		{
			echo '<p>Updating hierarchy positions...';
	
			include_once cms_join_path(CMS_BASE, 'include.php');
			
			#Set $gCms->config - somehow it doesn't get set by include.php
			$gCms->config = $newconfig;
	
			$db->SetFetchMode(ADODB_FETCH_ASSOC);
			#$db->debug = true;
			$gCms->db =& $db;
	
			$contentops =& $gCms->GetContentOperations();
			$contentops->SetAllHierarchyPositions();
	
			echo "[done]</p>";
			
			echo '<p>Setting up core events...';
			
			Events::SetupCoreEvents();
			
			echo "[done]</p>";
	
			echo '<p>Installing modules...';
	
			foreach ($gCms->modules as $modulename=>$value)
			{
				if ($gCms->modules[$modulename]['object']->AllowAutoInstall() == true)
				{
				$query = "SELECT * FROM ".cms_db_prefix()."modules WHERE module_name = ?";
				$dbresult = $db->Execute($query, array($modulename));
				$count = $dbresult->RecordCount();
					if (!isset($count) || $count == 0)
					{
						$modinstance =& $gCms->modules[$modulename]['object'];
						$result = $modinstance->Install();
	
						#now insert a record
						if (!isset($result) || $result === FALSE)
						{
							$query = "INSERT INTO ".cms_db_prefix()."modules (module_name, version, status, active, admin_only) VALUES (".$db->qstr($modulename).",".$db->qstr($modinstance->GetVersion()).",'installed',1,".($modinstance->IsAdminOnly()==true?1:0).")";
							$db->Execute($query);
							$gCms->modules[$modulename]['installed'] = true;
							$gCms->modules[$modulename]['active'] = true;
							
							/*
							#and insert any dependancies
							if (count($modinstance->GetDependencies()) > 0) #Check for any deps
							{
								#Now check to see if we can satisfy any deps
								foreach ($modinstance->GetDependencies() as $onedepkey=>$onedepvalue)
								{
									$time = $db->DBTimeStamp(time());
									$query = "INSERT INTO ".cms_db_prefix()."module_deps (parent_module, child_module, minimum_version, create_date, modified_date) VALUES (?,?,?,".$time.",".$time.")";
									$db->Execute($query, array($onedepkey, $module, $onedepvalue));
								}
							}
	
							#and show the installpost if necessary...
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
							*/
						}
					}
				}
			}
			echo "[done]</p>";
			
			if (isset($gCms->modules['Search']) && isset($gCms->modules['Search']['object']))
			{
				echo '<p>Index Search...';
				
				$modinstance =& $gCms->modules['Search']['object'];
				@$modinstance->Reindex();
	
				echo "[done]</p>";
			}
			echo '<p>Clearing site cache (if any)...';
					$contentops->ClearCache();
			echo "[done]</p>";
		}
	
		$link = str_replace(" ", "%20", $_POST['docroot']);
		
		if (isset($_POST["email_accountinfo"])) 
		{
			echo "<p>E-mailing admin account information...";
			$to      = $_POST['adminemail'];
			$subject = 'CMS Made Simple Admin Account Information';
			$message = <<<EOF
Thank you for installing CMS Made Simple.

This is your new account information:
username: {$_POST['adminusername']}
password: {$_POST['adminpassword']}

Log into the site admin here: $link/admin/
EOF;
			echo (
				@mail($to, $subject, $message)
				? '[done]'
				: '<strong>[failed]</strong>'
			);
			echo "</p>";

		}
	}
}

?>
