<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#CMS - CMS Made Simple
#(c)2004-2008 by Ted Kulp (ted@cmsmadesimple.org)
#This project's homepage is: http://cmsmadesimple.org
#
#This program is free software; you can redistribute it and/or modify
#it under the terms of the GNU General Public License as published by
#the Free Software Foundation; either version 2 of the License, or
#(at your option) any later version.
#
#This program is distributed in the hope that it will be useful,
#BUT withOUT ANY WARRANTY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
#You should have received a copy of the GNU General Public License
#along with this program; if not, write to the Free Software
#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
#$Id: editmultisite.php 4289 2007-12-05 21:00:12Z savagekabbage $

$CMS_ADMIN_PAGE=1;

require_once("../include.php");

check_login();
global $gCms;
$db =& $gCms->GetDb();
$error = "";

if (isset($_POST["cancel"]))
{
	redirect("listmultisites.php");
	return;
}

include_once("header.php");

// Set the possible database types
$databasetypes = array(
	array('name' => 'mysql', 'title' => 'MySQL'),
	array('name' => 'mysqli', 'title' => 'MySQL (4.1+)'),
	array('name' => 'postgres7', 'title' => 'PostgreSQL 7/8', 'extension' => 'pgsql'),
	array('name' => 'sqlite', 'title' => 'SQLite')
);

$userid = get_userid();

if (isset($_POST["addmultisite"]))
{
	$validinfo = true;

	// Make sure the HTTP Host field was filled out.
	// If it's filled out make sure it doesn't exist
	if ($_POST['http_host'] == "")
	{
		$validinfo = false;
		$error .= "<li>".lang('nofieldgiven', array(lang('http_host')))."</li>";
	}
	elseif(is_dir($sites_directory.DIRECTORY_SEPARATOR.$_POST['http_host']))	
	{
		$validinfo = false;
		$error .= "<li>".lang('directoryexists')."</li>";		
	}
	
	// Make sure we filled out a username
	if ($_POST['user']['username'] == "")
	{
		$validinfo = false;
		$error .= "<li>".lang('nofieldgiven', array(lang('username')))."</li>";
	}
	
	// Make sure we filled out a password
	if ($_POST['user']['password'] == "")
	{
		$validinfo = false;
		$error .= "<li>".lang('nofieldgiven', array(lang('password')))."</li>";
	}	
	
	// Make sure that the passwords match
	if($_POST['user']['password'] != $_POST['user']['passwordagain'])
	{
		$validinfo = false;
		$error .= "<li>".lang('nopasswordmatch')."</li>";
		$_POST['user']['password'] = "";
		$_POST['user']['passwordagain'] = "";				
	}
	
	// Make sure the directory is writable
	$sites_directory = $config['root_path'].DIRECTORY_SEPARATOR.'sites';
	if(!is_writable($sites_directory))
	{
		$validinfo = false;
		$error .= "<li>".lang('errordirectorynotwritable')."</li>";	
		$error .= "<li>".$directory."</li>";			
	}
			
	// Check for database conectivity
	require_once('../install/lib/class.cms_install_operations.php');
	$connection_result = CmsInstallOperations::test_database_connection($_POST['database']['dbms'], $_POST['database']['db_hostname'], $_POST['database']['db_username'], $_POST['database']['db_password'], $_POST['database']['db_name']);
	if($connection_result['have_connection'] == 0)
	{
		$validinfo = false;
		$error .= "<li>".lang('Could not connect to the database!')."</li>";		
	}
	elseif($connection_result['have_existing_db'] == 0)
	{
		$validinfo = false;
		$error .= "<li>".lang('Could not locate the database, please create it.')."</li>";				
	}	
	
	// Ok, no validation issues lets create the subsite & database
	if ($validinfo)
	{
		// Make the directories
		$directory = $sites_directory.DIRECTORY_SEPARATOR.$_POST['http_host'];
		mkdir($directory);
		mkdir($directory.DIRECTORY_SEPARATOR.'modules');
		mkdir($directory.DIRECTORY_SEPARATOR.'uploads');
		mkdir($directory.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'images');	
		
		// Create values for the config file
		$config_data = $_POST['database'];
		
// Create the config file
$db_dbms_key = $_POST['database']['dbms'];
$file_data = '<?php
$config[\'dbms\'] = \'' . $databasetypes[$db_dbms_key]['name'] . '\';
$config[\'db_hostname\'] = \'' . $_POST['database']['db_hostname'] . '\';
$config[\'db_username\'] = \'' . $_POST['database']['db_username'] . '\';
$config[\'db_password\'] = \'' . $_POST['database']['db_password'] . '\';
$config[\'db_name\'] = \'' . $_POST['database']['db_name'] . '\';
$config[\'db_prefix\'] = \'' . $_POST['database']['db_prefix'] . '\';
$config[\'persistent_db_conn\'] = false;

// The root_path should be the absolute path from the server root.
// The root_url should be the web accessible address
$config[\'root_path\'] = dirname(__FILE__);
$config[\'root_url\'] = \'http://\'.$_SERVER[\'HTTP_HOST\'];

if(isset($_SERVER[\'HTTPS\']) && $_SERVER[\'HTTPS\']==\'on\')
{
	$config[\'root_url\'] = str_replace(\'http\',\'https\',$config[\'root_url\']);
}

$config[\'admin_dir\'] = \'admin\';

$config[\'previews_path\'] = $config[\'root_path\'] . \'/tmp/cache\';
$config[\'uploads_path\'] = $config[\'root_path\'] . \'/uploads\';
$config[\'uploads_url\'] = $config[\'root_url\'] . \'/uploads\';
?>';
		file_put_contents($directory.DIRECTORY_SEPARATOR.'config.php', $file_data);
		
		// Open a connection and dump the contents into the new database
		$installed = CmsInstallOperations::install_schema($_POST['database']['dbms'], $_POST['database']['db_hostname'], $_POST['database']['db_username'], $_POST['database']['db_password'], $_POST['database']['db_name'], $_POST['database']['db_prefix']);

		// Install the new user account
		$account_installed = CmsInstallOperations::install_account($_POST['database']['dbms'], $_POST['database']['db_hostname'], $_POST['database']['db_username'], $_POST['database']['db_password'], $_POST['database']['db_name'], $_POST['database']['db_prefix'], $_POST['user']['username'], $_POST['user']['password']);	
		
		// Create the basic schema
		$info_installed = CmsInstallOperations::load_basic_schema($_POST['database']['dbms'], $_POST['database']['db_hostname'], $_POST['database']['db_username'], $_POST['database']['db_password'], $_POST['database']['db_name'], $_POST['database']['db_prefix']);
		
		// And finally redirect
		CmsResponse::redirect("listmultisites.php");
		return;
	}
}
else
{
	$_POST['database']['db_hostname'] = 'localhost';
	$_POST['database']['db_prefix'] = 'cms_';
}


$dbms_options = array();
foreach ($databasetypes as $db)
{
	$extension = isset($db['extension']) ? $db['extension'] : $db['name'];
	if (extension_loaded($extension))
	{
		$dbms_options[] = $db;
	}
}

// Display errors
if ($error != "")
{
	echo '<div class="pageerrorcontainer"><p class="pageerror">'.$error.'</p></div>';
}
?>

	<?php echo $themeObject->ShowHeader('addmultisite'); ?>
	<form method="post" action="addmultisite.php">
		<h4><?php echo lang('multisitedetails')?></h4>
		<div class="row">
			<label><?php echo lang('http_host')?>:</label>
			<input type="text" name="http_host" maxlength="255" value="<?php echo $_POST['http_host']?>" />
		</div>
		<h4><?php echo lang('accountdetails')?></h4>		
		<div class="row">
			<label><?php echo lang('username')?>:</label>
			<input type="text" name="user[username]" maxlength="255" value="<?php echo $_POST['user']['username']?>" />
		</div>
		<div class="row">
			<label><?php echo lang('password')?>:</label>
			<input type="password" name="user[password]" maxlength="255"  value="<?php echo $_POST['user']['password']?>" />
		</div>
		<div class="row">
			<label><?php echo lang('passwordagain')?>:</label>
			<input type="password" name="user[passwordagain]" maxlength="255" value="<?php echo $_POST['user']['passwordagain']?>" />
		</div>		
		<h4><?php echo lang('databasedetails')?></h4>	
		<div class="row">
			<label><?php echo lang('databasetype'); ?></label>
			<select name="database[dbms]">
				<?php 
				foreach($dbms_options AS $key => $option)
				{
					echo '<option value="' . $key . '"' . ($_POST['database']['dbms'] == $option['name']?' selected':'') . '/>' . $option['title'] . '</option>';
				}
				?>
			</select>
		</div>
		<div class="row">
			<label><?php echo lang('hostname')?>:</label>
			<input type="text" name="database[db_hostname]" maxlength="255" value="<?php echo $_POST['database']['db_hostname']?>" />
		</div>
		<div class="row">
			<label><?php echo lang('username')?>:</label>
			<input type="text" name="database[db_username]" maxlength="255" value="<?php echo $_POST['database']['db_username']?>" />
		</div>
		<div class="row">
			<label><?php echo lang('password')?>:</label>
			<input type="text" name="database[db_password]" maxlength="255" value="<?php echo $_POST['database']['db_password']?>" />
		</div>
		<div class="row">
			<label><?php echo lang('databasename')?>:</label>
			<input type="text" name="database[db_name]" maxlength="255" value="<?php echo $_POST['database']['db_name']?>" />
		</div>
		<div class="row">
			<label><?php echo lang('databaseprefix')?>:</label>
			<input type="text" name="database[db_prefix]" maxlength="255" value="<?php echo $_POST['database']['db_prefix']?>" />
		</div>								
		<input type="hidden" name="addmultisite" value="true" />
		<div class="submitrow">
			<button class="positive disabled" name="submitbutton" type="submit" disabled=""><?php echo lang('submit')?></button>
			<button class="negative" name="cancel" type="submit"><?php echo lang('cancel')?></button>
		</div>
	</form>
<?php

echo '<p class="pageback"><a class="pageback" href="'.$themeObject->BackUrl().'">&#171; '.lang('back').'</a></p>';
include_once("footer.php");

# vim:ts=4 sw=4 noet
?>