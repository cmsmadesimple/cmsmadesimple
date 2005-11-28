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

$LOAD_ALL_MODULES=1;
require(dirname(dirname(__FILE__)).'/fileloc.php');

$config = CONFIG_FILE_LOCATION;
if (!file_exists($config)) {
    $file = @fopen($config, "w");
    if ($file != 0) {
		#Follow fix suggested by sig in the forums
        #$cwd = getcwd();
		$cwd = str_replace("\\","/",dirname(__FILE__));
        fwrite($file,"<?php\n".'$config[\'root_path\'] = "'.$cwd.'";'."\n?>\n");
        fclose($file);
    } else {
        echo "Cannot create $config, please change permissions to allow this\n";
        exit;
    } ## if
} ## if
else if (filesize($config) == 0) {
    $file = @fopen($config, "w");
    if ($file != 0) {
		#Follow fix suggested by sig in the forums
        #$cwd = getcwd();
		$cwd = str_replace("\\","/",dirname(__FILE__));
        fwrite($file,"<?php\n".'$config[\'root_path\'] = "'.$cwd.'";'."\n?>\n");
        fclose($file);
    } else {
        echo "Cannot modify $config, please change permissions to allow this\n";
        exit;
    } ## if
}

$pages = 4;
if (isset($_POST["page"])) {
    $currentpage = $_POST["page"];
} elseif (isset($_GET["page"])) {
    $currentpage = $_GET["page"];
} else {  
    $currentpage = 1;
} ## if

$DONT_LOAD_DB = true;

if ($currentpage > 1) { require_once(dirname(dirname(__FILE__))."/include.php"); }

?>

<html>
<head>
        <title>CMS Made Simple Install</title>
        <link rel="stylesheet" type="text/css" href="install.css" />
</head>

<body>

<div class="body">

<img src="../images/cms/cmsbanner.gif" width="449" height="114" alt="CMS Banner Logo" />

<div class="headerish">

<H1>Install System</H1>

</DIV>

<DIV CLASS="main">
<?php

echo "<h3>Thanks for installing CMS: CMS Made Simple.</h3>\n";
echo "<table class=\"countdown\" cellspacing=\"2\" cellpadding=\"2\"><tr>";
echo "<td><img src=\"../images/cms/install/".($currentpage>=1?"1":"1off").".gif\" alt=\"Step 1\"></td>";
echo "<td><img src=\"../images/cms/install/".($currentpage>=2?"2":"2off").".gif\" alt=\"Step 2\"></td>";
echo "<td><img src=\"../images/cms/install/".($currentpage>=3?"3":"3off").".gif\" alt=\"Step 3\"></td>";
echo "<td><img src=\"../images/cms/install/".($currentpage>=4?"4":"4off").".gif\" alt=\"Step 4\"></td>";
echo "<td><img src=\"../images/cms/install/".($currentpage>=5?"5":"5off").".gif\" alt=\"Step 5\"></td>";
echo "</tr></table>\n";
echo "<p><hr width=\"80%\"></p>\n";

switch ($currentpage) {
    case 1:
        showPageOne();
        break;
    case 2:
        showPageTwo();
        break;
    case 3:
        showPageThree();
        break;
    case 4:
        showPageFour();
        break;
    case 5:
        showPageFive();
        break;
    default:
        echo "You were supposed to turn <a href=\"install.php\">right</a> at Alberquerque.<p>\n";
        break;
} ## switch

function showPageOne() {
    ## test file permissions
    ## apache (or other webserver) user needs to have write access to the cache and template_c dirs
    ## as well as the cms root for config.php to be created.

    ## find the user running this script
    #$userid = posix_getuid();
    #$userdata = posix_getpwuid($userid);
    #$username = $userdata['name'];

    ## echo "Userid ($userid) is named $username is running this script<p>\n";

    ## check file perms
	$continueon = true;
    echo "<h3>Checking file permissions:</h3>\n";
    #$files = array(TMP_CACHE_LOCATION, TMP_TEMPLATES_C_LOCATION, dirname(dirname(__FILE__)).'/uploads', CONFIG_FILE_LOCATION);
    $files = array(TMP_CACHE_LOCATION, TMP_TEMPLATES_C_LOCATION, CONFIG_FILE_LOCATION);

    echo "<table class=\"regtable\" border=\"1\">\n";
    echo "<thead class=\"tbhead\"><tr><th>Test</th><th>Result</th></tr></thead><tbody>\n";

    echo "<tr class=\"row1\"><td>Checking for PHP version 4.1+</td><td>";
	echo (@version_compare(phpversion(),"4.1.0") > -1?"Success!":"Failure!");
	(@version_compare(phpversion(),"4.1.0") > -1?null:$continueon=false);
	echo "</td></tr>\n";

	echo "<tr class=\"row2\"><td>Checking for Session Functions</td><td>";
	if (function_exists("session_start"))
	{
		echo "Success!";
	}
	else
	{
		echo "Failed!";
		$continueon = false;
	}
	echo "</td></tr>\n";

	$currow = "row1";

    foreach ($files as $f) {
        #echo "<tr><td>\n";
        ## check if we can write to the this file
        echo "<tr class=\"$currow\"><td>Checking write permission on $f</td><td>";
		if (is_writable($f))
		{
            echo "Success!";
        }
		else
		{
			$continueon=false;
            echo "Failure!";
        } ## if 
        echo "</td></tr>\n";
		($currow=="row1"?$currow="row2":$currow="row1");
    } ## foreach

    echo "</tbody></table>\n";

	echo '<form method="post" action="install.php">';
  
  	if ($continueon)
	{
		echo "<p>All of your tests show successful.  Please click the Continue button.</p>\n";
		echo '<p class="continue" align="center"><input type="hidden" name="page" value="2" /><input type="submit" value="Continue" /></p>';
	}
	else
	{
		echo "<p>One or more tests have failed.  Please correct the problem and click the button below to recheck.</p>\n";
		echo '<p class="continue" align="center"><input type="Submit" value="Try Again" /></p>';
	}
	echo '</form>';

} ## showPageOne

function showPageTwo($errorMessage='',$username='',$email='')
{

	if ($errorMessage != '')
	{
		echo "<p class=\"error\">$errorMessage</p>";
	}
	?>

<h3>Admin Account Information</h3>

<p>
Select the username, password and email address for your admin account.  Please make sure you record this password somewhere, as
there will be no other way to login to your CMS Made Simple admin system without it.
</p>

<form action="install.php" method="post" name="page2form" id="page2form">

<table cellpadding="2" border="1" class="regtable">

	<tr class="row1">
		<td>Username</td>
		<td><input type="text" name="adminusername" value="<?php echo $username?>" length="20" maxlength="50" /></td>
	</tr>

	<tr class="row2">
		<td>Email Address</td>
		<td><input type="text" name="adminemail" value="<?php echo $email?>" length="20" maxlength="50" /></td>
	</tr>

	<tr class="row1">
		<td>Password</td>
		<td><input type="password" name="adminpassword" value="" length="20" maxlength="50" /></td>
	</tr>

	<tr class="row2">
		<td>Password Again</td>
		<td><input type="password" name="adminpasswordagain" value="" length="20" maxlength="50" /></td>
	</tr>

</table>

<p align="center" class="continue"><input type="hidden" name="page" value="3" /><input type="submit" value="Continue" /><!--<a onclick="document.page2form.submit()" href="#">Continue</a>--></p>

</form>

	<?php

} ## showPageTwo()

function showPageThree($errorMessage='')
{

	if ($errorMessage != '')
	{
		echo "<p class=\"error\">$errorMessage</p>";
	}
# Do check that username and password are filled out.
# Skip back to showPageTwo() if necessary
# Put given variables into hidden fields so they can up UPDATEd later on in step 4 (post schema install)
	if ($_POST['adminusername'] == '')
	{
		showPageTwo('Username not given!', '', $_POST['adminemail']);
		return;
	}
	else if ($_POST['adminpassword'] == '' || $_POST['adminpasswordagain'] == '')
	{
		showPageTwo('Both password fields not given!', $_POST['adminusername'], $_POST['adminemail']);
		return;
	}
	else if ($_POST['adminpassword'] != $_POST['adminpasswordagain'])
	{
		showPageTwo('Password fields do not match!', $_POST['adminusername'], $_POST['adminemail']);
		return;
	}
	$adminusername = $_POST['adminusername'];
	$adminemail = $_POST['adminemail'];
	$adminpassword = md5($_POST['adminpassword']);

?>

<h3>Database Information</h3>

<P>Make sure you have created your database and granted full privileges to a user to use that database.</P>
<P>For MySQL, use the following:</P>
<P>Log in to mysql from a console and run the following commands:</P>
<OL>
<LI>create database cms; (use whatever name you want here but make sure to remember it, you'll need to enter it on this page)</LI>
<LI>grant all privileges on cms.* to cms_user@localhost identified by 'cms_pass';</LI>
</OL>
<P />

Please complete the following fields:
<FORM ACTION="install.php" METHOD="post" NAME="page3form" ID="page3form">

<TABLE CELLPADDING="2" BORDER="1" CLASS="regtable">
<TR CLASS="row2">
	<TD>Database Type:</TD>
	<TD>
		<SELECT NAME="dbms">
<?php
$valid_database = false;
if (function_exists('mysql_connect'))
	{
	echo '<OPTION VALUE="mysql" ';
	echo (isset($_POST['dbms']) && $_POST['dbms'] == 'mysql'?'selected="selected"':'');
	echo '>MySQL (3 and 4.0)</OPTION>';
	$valid_database = true;
	}
if (function_exists('mysqli_connect'))
	{
	echo '<OPTION VALUE="mysqli" ';
	echo (isset($_POST['dbms']) && $_POST['dbms'] == 'mysqli'?'selected="selected"':'');
	echo '>MySQL (4.1+)</OPTION>';
	$valid_database = true;
	}
if (function_exists('pg_connect'))
	{
	echo '<OPTION VALUE="postgres7" ';
	echo (isset($_POST['dbms']) && $_POST['dbms'] == 'postgres7'?'selected="selected"':'');
	echo '>PostgreSQL 7/8</OPTION>';
	$valid_database = true;
	}
?>
		</SELECT>
<?php if (! $valid_database) { ?>
<div class="error">No valid database drivers appear to be compiled into your PHP install. Please confirm that you have mysql, mysqli, and/or postgres7 support installed, and try again.</div>
<?php } ?>
	</TD>
</TR>
<TR CLASS="row1">
<TD>Database host address</TD>
<TD><INPUT TYPE="text" NAME="host" VALUE="<?php echo (isset($_POST['host'])?$_POST['host']:'localhost') ?>" LENGTH="20" MAXLENGTH="50" /></TD>
</TR>
<TR CLASS="row2">
<TD>Database host port</TD>
<TD><INPUT TYPE="text" NAME="port" VALUE="<?php echo (isset($_POST['port'])?$_POST['port']:'3306') ?>" LENGTH="20" MAXLENGTH="50" /></TD>
</TR>
<TR CLASS="row1">
<TD>Database name</TD>
<TD><INPUT TYPE="text" NAME="database" VALUE="<?php echo (isset($_POST['database'])?$_POST['database']:'cms') ?>" LENGTH="20" MAXLENGTH="50" /></TD>
</TR>
<TR CLASS="row2">
<TD>Username</TD>
<TD><INPUT TYPE="text" NAME="username" VALUE="<?php echo (isset($_POST['username'])?$_POST['username']:'cms_user') ?>" LENGTH="20" MAXLENGTH="50" /></TD>
</TR>
<TR CLASS="row1">
<TD>Password</TD>
<TD><INPUT TYPE="password" NAME="password" VALUE="<?php echo (isset($_POST['password'])?$_POST['password']:'cms_pass') ?>" LENGTH="20" MAXLENGTH="50" /></TD>
</TR>
<TR CLASS="row2">
<TD>Table prefix</TD>
<TD><INPUT TYPE="text" NAME="prefix" VALUE="<?php echo (isset($_POST['prefix'])?$_POST['prefix']:'cms_') ?>" LENGTH="20" MAXLENGTH="50" />
<INPUT TYPE="hidden" NAME="page" VALUE="4" />
<input type="hidden" name="adminusername" value="<?php echo $adminusername ?>" />
<input type="hidden" name="adminemail" value="<?php echo $adminemail ?>" />
<input type="hidden" name="adminpassword" value="<?php echo $adminpassword ?>" />
<input type="hidden" name="adminpasswordagain" value="<?php echo $adminpassword ?>" />
</TD>
</TR>
<TR CLASS="row1">
<TD>Create Tables (Warning: Deletes existing data)</TD>
<TD><INPUT TYPE="checkbox" NAME="createtables" CHECKED="true" /></TD>
</TR>
</TABLE>
<P ALIGN="center" CLASS="continue"><!--<a onclick="document.page3form.submit()" href="#">Continue</a>--><input type="submit" value="Continue" /></P>
<!--<p><input type="submit" value="Continue" /></p>-->
</FORM>
<?php

} ## showPageThree

function showPageFour($sqlloaded = 0) {
    ## don't load statements if they've already been loaded
    if ($sqlloaded == 0 && isset($_POST["createtables"])) {

        global $config, $CMS_SCHEMA_VERSION;

		$db = ADONewConnection($_POST['dbms']);

		#$db->debug = true;
		$result = @$db->Connect($_POST['host'],$_POST['username'],$_POST['password'],$_POST['database']);

		$db_prefix = $_POST['prefix'];

		if (!$result)
		{
			showPageThree('Could not connect to database.  Verify that username and password are correct, and that the user has access to the given database.');
			return;
		}

		//Try to create and drop a dummy table (with appropriate prefix)
		@$db->Execute('DROP TABLE ' . $db_prefix . 'dummyinstall');
		$result = $db->Execute('CREATE TABLE ' . $db_prefix . 'dummyinstall (i int)');
		if ($result)
		{
			$result = $db->Execute('DROP TABLE ' . $db_prefix . 'dummyinstall');
			if (!$result)
			{
				//could not drop table
				showPageThree('Could not drop a table.  Verify that the user has privileges to drop tables in the given database.');
				return;
			}
		}
		else
		{
			//could not create table
			showPageThree('Could not create a table.  Verify that the user has privileges to create tables in the given database.');
			return;
		}

		$db->SetFetchMode(ADODB_FETCH_ASSOC);

		$CMS_INSTALL_DROP_TABLES=1;
		$CMS_INSTALL_CREATE_TABLES=1;

		include_once(dirname(__FILE__)."/schemas/schema.php");

		echo "<p>Importing initial data...";

		$handle = fopen(dirname(__FILE__)."/schemas/initial.sql", 'r');
		if ($handle) {
			while (!feof($handle)) {
				set_magic_quotes_runtime(false);
				$s = fgets($handle, 32768);
				if ($s != "") {
					$s = trim(str_replace("{DB_PREFIX}", $db_prefix, $s));
					$result = $db->Execute($s);
					if (!$result) {
						die("Invalid query: $s");
					} ## if
				}
			}
		}

		fclose($handle);

		echo "[done]</p>";

		echo "<p>Setting admin account information...";

		$sql = 'UPDATE ' . $db_prefix . 'users SET username = ?, password = ?, email = ? WHERE user_id = 1';
		$db->Execute($sql, array($_POST['adminusername'], $_POST['adminpassword'], $_POST['adminemail']));

		echo "[done]</p>";

		include_once(dirname(__FILE__)."/schemas/createseq.php");

		$db->Close();
        echo "<p>Success!</p>";

    } ## if

    $docroot = 'http://'.$_SERVER['HTTP_HOST'].substr($_SERVER['SCRIPT_NAME'],0,strlen($_SERVER['SCRIPT_NAME'])-20);
    $docpath = dirname(dirname(__FILE__)); 

	?>

    <P>Now let's continue to setup your configuration file, we already have most of the stuff we need.</P>
    <P>Chances are you can leave all these values alone, so when you are ready, click Continue.</P>
    <FORM ACTION="install.php" METHOD="post" NAME="page3form" ID="page3form">
	<TABLE CELLPADDING="2" BORDER="1" CLASS="regtable">
		<TR CLASS="row1">
			<TD>CMS Document root (as seen from the webserver)</TD>
			<TD><INPUT TYPE="text" NAME="docroot" VALUE="<?php echo $docroot?>" LENGTH="50" MAXLENGTH="100"></TD>
		</TR>
		<TR CLASS="row2">
			<TD>Path to the Document root</TD>
			<TD><INPUT TYPE="text" NAME="docpath" VALUE="<?php echo $docpath?>" LENGTH="50" MAXLENGTH="100"></TD>
		</TR>
		<TR CLASS="row1">
			<TD>Query string (leave this alone unless you have trouble, then edit config.php by hand)</TD>
			<TD>
				<INPUT TYPE="text" NAME="querystr" VALUE="page" LENGTH="20" MAXLENGTH="20">
				<INPUT TYPE="hidden" NAME="page" VALUE="5"><INPUT TYPE="hidden" NAME="host" VALUE="<?php echo $_POST['host']?>">
			    <INPUT TYPE="hidden" NAME="dbms" VALUE="<?php echo $_POST['dbms']?>">
			    <INPUT TYPE="hidden" NAME="database" VALUE="<?php echo $_POST['database']?>">
				<INPUT TYPE="hidden" NAME="port" VALUE="<?php echo $_POST['port']?>">
			    <INPUT TYPE="hidden" NAME="username" VALUE="<?php echo $_POST['username']?>">
				<INPUT TYPE="hidden" NAME="password" VALUE="<?php echo $_POST['password']?>">
			    <INPUT TYPE="hidden" NAME="prefix" VALUE="<?php echo $_POST['prefix']?>">
				<INPUT TYPE="hidden" NAME="bbcode" VALUE="false">
				<?php if (isset($_POST["createtables"])) { ?>
				<INPUT TYPE="hidden" NAME="createtables" VALUE="true">
				<?php } ?>
			</TD>
		</TR>
		<!--
		<TR CLASS="row2">
			<TD>Use BBCode (must have this installed, see <A HREF="INSTALL" TARGET="_new">INSTALL</A></TD>
			<TD>
				<INPUT TYPE="text" NAME="bbcode" VALUE="false" LENGTH="5" MAXLENGTH="5">
			</TD>
		</TR>
		-->
    </TABLE>
    <P ALIGN="center" CLASS="continue"><!--<a onclick="document.page3form.submit()" href="#">Continue</a>--><input type="submit" value="Continue" /></P>
	</FORM>

	<?php
    
} ## showPageFour

function showPageFive() {

	/*
    if ($_POST['bbcode'] != 'false' and $_POST['bbcode'] != 'true') {
        echo "<p>BB Code needs to be either 'true' or 'false'</p>\n";
        showPageFour(1);
        exit;
    } ## if
	*/

	require_once(dirname(dirname(__FILE__))."/lib/config.functions.php");

	$newconfig = cms_config_load();;

	$newconfig['dbms'] = $_POST['dbms'];
	$newconfig['db_hostname'] = $_POST['host'];
	$newconfig['db_username'] = $_POST['username'];
	$newconfig['db_password'] = $_POST['password'];
	$newconfig['db_name'] = $_POST['database'];
	$newconfig['db_prefix'] = $_POST['prefix'];
	$newconfig['root_url'] = $_POST['docroot'];
	$newconfig['root_path'] = addslashes($_POST['docpath']);
	$newconfig['query_var'] = $_POST['querystr'];
	$newconfig['use_bb_code'] = false;
	$newconfig['use_smarty_php_tags'] = false;
	$newconfig['previews_path'] = TMP_CACHE_LOCATION;
	$newconfig["uploads_path"] = $newconfig['root_path'] . "/uploads";
	$newconfig["uploads_url"] = $newconfig['root_url'] ."/uploads";	
	$newconfig["image_uploads_path"] = $newconfig['root_path'] . "/uploads/images";
	$newconfig["image_uploads_url"] = $newconfig['root_url'] ."/uploads/images";
	$newconfig["max_upload_size"] = 1000000;
	$newconfig["debug"] = false;
	$newconfig["assume_mod_rewrite"] = false;
	$newconfig["auto_alias_content"] = true;
	$newconfig["image_manipulation_prog"] = "GD";
	$newconfig["image_transform_lib_path"] = "/usr/bin/ImageMagick/";
	$newconfig["use_Indite"] = false;
	$newconfig["image_uploads_path"] = $newconfig['root_path'] . "/uploads/images";
	$newconfig["image_uploads_url"] = $newconfig['root_url'] ."/uploads/images";
	$newconfig["default_encoding"] = "";
	$newconfig["disable_htmlarea_translation"] = false;
	$newconfig["admin_dir"] = "admin";
	$newconfig["persistent_db_conn"] = true;
	$newconfig["default_upload_permission"] = '664';
    $newconfig["page_extension"] = ".html";
	$newconfig["locale"] = "en_US";
	$newconfig["admin_encoding"] = "utf-8";

    $configfile = CONFIG_FILE_LOCATION;
    ## build the content for config file

    if ((file_exists($configfile) && is_writable($configfile)) || !file_exists($configfile)) {
		cms_config_save($newconfig);
    } else {
        echo "Error: Cannot write to $config.<br />\n";
        exit;
    } ## if

	if (file_exists(TMP_CACHE_LOCATION.'/SITEDOWN'))
	{
		if (!unlink(TMP_CACHE_LOCATION.'/SITEDOWN'))
		{
			echo "Error: Could not remove the tmp/cache/SITEDOWN file.  Please remove manually.";
		}
	}

	#Do module installation
	if (isset($_POST["createtables"]))
	{
		echo 'Installing modules...';

		include_once dirname(dirname(__FILE__)) . '/include.php';

		global $gCms;

		$db = &ADONewConnection($newconfig['dbms'], 'cms:pear');
		$db->Connect($newconfig["db_hostname"],$newconfig["db_username"],$newconfig["db_password"],$newconfig["db_name"]);
		$db->SetFetchMode(ADODB_FETCH_ASSOC);
		$gCms->db =& $db;

		foreach ($gCms->modules as $modulename=>$value)
		{
			if ($gCms->modules[$modulename]['object']->AllowAutoInstall() == true)
			{
				$modinstance = $gCms->modules[$modulename]['object'];
				$result = $modinstance->Install();

				#now insert a record
				if (!isset($result) || $result === FALSE)
				{
					$query = "INSERT INTO ".cms_db_prefix()."modules (module_name, version, status, active) VALUES (".$db->qstr($modulename).",".$db->qstr($modinstance->GetVersion()).",'installed',1)";
					$db->Execute($query);
					
					/*
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
		echo 'done<br />';
	}
 
	$link = str_replace(" ", "%20", $_POST['docroot']);
    echo "<H4>Congratulations, you are all setup.</H4><H4>Here is your <A HREF=\"".$link."\">CMS site</A></H4>\n";

} ## showPageFour
?>

</div>
</div>

</body>
</html>
<?php

# vim:ts=4 sw=4 noet
?>
