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
#$Id: CMSInstallPage7.class.php 162 2009-05-10 16:19:13Z calguy1000 $

class CMSInstallerPage7 extends CMSInstallerPage
{
	/**
	 * Class constructor
	*/
	function CMSInstallerPage7(&$smarty, $errors, $debug)
	{
		$this->CMSInstallerPage(7, $smarty, $errors, $debug);
	}

	function assignVariables()
	{
		$values = array();
		$values['umask'] = isset($_POST['umask']) ? $_POST['umask'] : '';
		$values['admininfo']['username'] = $_POST['adminusername'];
		$values['admininfo']['email'] = $_POST['adminemail'];
		$values['admininfo']['password'] = $_POST['adminpassword'];
		$base_url = str_replace(" ", "%20", rtrim($_POST['docroot'], '/'));
		$link = '<a href="'.$base_url.'">'.lang('cms_site').'</a>';

		$this->smarty->assign('base_url', $base_url);
		$this->smarty->assign('values', $values);
		$this->smarty->assign('site_link', $link);

		$this->smarty->assign('errors', $this->errors);
	}

	function preContent()
	{
		global $gCms;

		require_once(cms_join_path(CMS_BASE, 'lib', 'config.functions.php'));
		
		// check if db info is correct as it should at this point to prevent an undeleted installation dir
		// to be used for sending spam by messing up $_POST variables
		$db = ADONewConnection($_POST['dbms'], 'pear:date:extend:transaction');
		if (! empty($_POST['db_port'])) $db->port = $_POST['db_port'];
		if (! $db->Connect($_POST['host'],$_POST['username'],$_POST['password'],$_POST['database']))
		{
			$this->errors[] = lang('could_not_connect_db');
			return;
		}

		$newconfig = cms_config_load();
		$newconfig['dbms'] = $_POST['dbms'];
		$newconfig['db_hostname'] = $_POST['host'];
		$newconfig['db_username'] = $_POST['username'];
		$newconfig['db_password'] = $_POST['password'];
		$newconfig['db_name'] = $_POST['database'];
		$newconfig['db_prefix'] = $_POST['prefix'];
		$newconfig['db_port'] = $_POST['db_port'];
                // $newconfig['db_socket'] = $_POST['db_socket'];

		$newconfig['root_url'] = rtrim($_POST['docroot'], '/');
		$newconfig['root_path'] = rtrim($_POST['docpath'], '\\/');
		$newconfig['query_var'] = $_POST['querystr'];
		$newconfig['use_bb_code'] = false;
		$newconfig['use_smarty_php_tags'] = false;
		$newconfig['previews_path'] = TMP_CACHE_LOCATION;
		$newconfig['uploads_path'] = cms_join_path($newconfig['root_path'], 'uploads');
		// Note: leave the / slashes for the URLs
		$newconfig['uploads_url'] = '/uploads';
		$newconfig['image_uploads_path'] = cms_join_path($newconfig['uploads_path'], 'images');
		// Note: leave the / slashes for the URLs
		$newconfig['image_uploads_url'] = $newconfig['uploads_url'] . '/images';
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
		$newconfig['max_upload_size'] = $maxFileSize;
		$newconfig['debug'] = false;
		//$newconfig['assume_mod_rewrite'] = false; //Not in use now
		$newconfig['auto_alias_content'] = true;
		$newconfig['image_manipulation_prog'] = 'GD';
		$newconfig['image_transform_lib_path'] = ('WIN' === strtoupper(substr(PHP_OS, 0, 3))) ? 'C:/Program Files/VisualMagick/bin/' : '/usr/bin/ImageMagick/';
		$newconfig['default_encoding'] = $_POST['default_encoding'];
		$newconfig['disable_htmlarea_translation'] = false;
		$newconfig['admin_dir'] = 'admin';
		$newconfig['persistent_db_conn'] = false;
		$newconfig['default_upload_permission'] = '664';
		$newconfig['page_extension'] = "";
		$newconfig['locale'] = "";
		$newconfig['admin_encoding'] = "utf-8";
		$newconfig['use_adodb_lite'] = true;
		//$newconfig['internal_pretty_urls'] = false; //Not in use now
		$newconfig['use_hierarchy'] = true; //Now true
		$newconfig['wiki_url'] = 'http://wiki.cmsmadesimple.org/index.php/User_Handbook/Admin_Panel';
		$newconfig['set_names'] = true;
		$newconfig['url_rewriting'] = 'none';
	
		$configfile = CONFIG_FILE_LOCATION;
		## build the content for config file
	
		if ((file_exists($configfile) && is_writable($configfile)) || !file_exists($configfile)) {
			cms_config_save($newconfig);
		}
		else 
		{
			echo lang('cannot_write_config', $configfile);
			exit;
		}

		if (file_exists(cms_join_path(TMP_CACHE_LOCATION, 'SITEDOWN')))
		{
			if (!unlink(cms_join_path(TMP_CACHE_LOCATION, 'SITEDOWN')))
			{
				echo lang('install_admin_sitedown');
			}
		}

		#Make sure $gCms->db is set
		#Do module installation
		if (isset($_POST["createtables"]) && $_POST['createtables'] != 0 )
		{
			global $DONT_SET_DB;
			echo '<p>' . lang('install_admin_update_hierarchy');

			#Set $gCms->config - somehow it doesn't get set by include.php
			$gCms->config = $newconfig;

			$db->SetFetchMode(ADODB_FETCH_ASSOC);
			#$db->debug = true;
			$gCms->db =& $db;
			unset($GLOBALS['DONT_SET_DB']);
			unset($DONT_SET_DB);


			$contentops =& $gCms->GetContentOperations();
			$contentops->SetAllHierarchyPositions();

			echo " [" . lang('done') . "]</p>";


			echo '<p>' . lang('install_admin_set_core_event');

			Events::SetupCoreEvents();

			echo " [" . lang('done') . "]</p>";


			echo '<p>' . lang('install_admin_install_modules');

			foreach ($gCms->modules as $modulename=>$value)
			{
			  // only deal with system modules
			  if( !in_array($modulename,$gCms->cmssystemmodules) ) continue;

				if ($gCms->modules[$modulename]['object']->AllowAutoInstall() == true)
				{
				$query = "SELECT * FROM ".cms_db_prefix()."modules WHERE module_name = ?";
				$dbresult = $db->Execute($query, array($modulename));
				if( $dbresult )
				  {
				    $count = $dbresult->RecordCount();
				  }
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
			echo " [" . lang('done') . "]</p>";


			echo '<p>' . lang('install_admin_clear_cache');
			$contentops->ClearCache();
			echo " [" . lang('done') . "]</p>";

			// Insert new site preferences
			set_site_preference('global_umask', $_POST['umask']);
			set_site_preference('frontendlang', $_POST['frontendlang']);
			set_preference(1, 'default_cms_language', $_POST['default_cms_lang']);
		}
		else
		{
		  $this->smarty->assign('tables_notinstalled',1);
		}
		  

		$link = str_replace(" ", "%20", $_POST['docroot']);

		if ( ($_POST['email_accountinfo'] == 1) && (! empty($_POST['adminemail'])) )
		{

			echo '<p>' . lang('install_admin_emailing');
			$to      = $_POST['adminemail'];
			$subject = lang('email_accountinfo_subject');
			$message = lang('email_accountinfo_message', $_POST['adminusername'], $_POST['adminpassword'], "$link/admin/");
			$message = html_entity_decode($message, ENT_QUOTES); // Encoded from TC
			echo (
				@mail($to, $subject, $message)
					? " [" . lang('done') . "]"
					: "<strong>[" . lang('failed') . "]</strong>"
			);
			echo "</p>";

		}
	}
}
?>
