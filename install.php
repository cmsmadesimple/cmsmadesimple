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

$config = "config.php";
if (!file_exists($config)) {
    $file = @fopen($config, "w");
    if ($file != 0) {
		#Follow fix suggested by sig in the forums
        #$cwd = getcwd();
		$cwd = str_replace("\\","/",dirname(__FILE__));
        fwrite($file,"<?php\n".'$this->root_path = "'.$cwd.'";'."\n?>\n");
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
        fwrite($file,"<?php\n".'$this->root_path = "'.$cwd.'";'."\n?>\n");
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
if ($currentpage > 1) { require_once("include.php"); }

?>

<HTML>
<HEAD>
        <TITLE>CMS Made Simple Install</TITLE>
        <LINK REL="stylesheet" TYPE="text/css" HREF="install.css" />
</HEAD>

<BODY>

<DIV CLASS="body">

<IMG SRC="images/cms/cmsbanner.gif" WIDTH="449" HEIGHT="114" ALT="CMS Banner Logo" />

<DIV CLASS="headerish">

<H1>Install System</H1>

</DIV>

<DIV CLASS="main">
<?php

echo "<h3>Thanks for installing CMS: CMS Made Simple.</h3>\n";
echo "<table class=\"countdown\" cellspacing=\"2\" cellpadding=\"2\"><tr>";
echo "<td><IMG SRC=\"images/cms/install/".($currentpage>=1?"1":"1off").".gif\" ALT=\"Step 1\"></td>";
echo "<td><IMG SRC=\"images/cms/install/".($currentpage>=2?"2":"2off").".gif\" ALT=\"Step 2\"></td>";
echo "<td><IMG SRC=\"images/cms/install/".($currentpage>=3?"3":"3off").".gif\" ALT=\"Step 3\"></td>";
echo "<td><IMG SRC=\"images/cms/install/".($currentpage>=4?"4":"4off").".gif\" ALT=\"Step 4\"></td>";
echo "</tr></table>\n";
echo "<p><hr width=80%></p>\n";

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
    default:
        echo "You were supposed to turn <a href=install.php>right</a> at Alberquerque.<p>\n";
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
    $files = array('smarty/cms/cache', 'smarty/cms/templates_c', 'uploads', 'config.php');

    echo "<table class=\"regtable\" border=\"1\">\n";
    echo "<thead class=\"tbhead\"><tr><th>Test</th><th>Result</th></tr></thead><tbody>\n";

    echo "<tr class=\"row1\"><td>Checking for PHP version 4.0+</td><td>";
	echo (@phpversion() >= "4"?"Success!":"Failure!");
	(@phpversion() >= "4"?null:$continueon=false);
	echo "</td></tr>\n";

	$currow = "row2";

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
  
  	if ($continueon)
	{
		echo "<p>All of your tests show successful.  It is time to setup your database.</p>\n";
		echo "<p class=\"continue\" align=\"center\"><a href=\"install.php?page=2\">Continue</a></p>\n";
	}
	else
	{
		echo "<p>One or more tests have failed.  Please correct the problem and click the button below to recheck.</p>\n";
		echo "<p class=\"continue\" align=\"center\"><a href=\"install.php\">Try Again</a></p>\n";
	}

} ## showPageOne

function showPageTwo() {
?>
<P>Make sure you have created your database and granted full privileges to a user to use that database.</P>
<P>For MySQL, use the following:</P>
<P>Log in to mysql from a console and run the following commands:</P>
<OL>
<LI>create database cms; (use whatever name you want here but make sure to remember it, you'll need to enter it on this page)</LI>
<LI>grant all privileges on cms.* to cms_user@localhost identified by 'cms_pass';</LI>
</OL>
<P />

Please complete the following fields:
<FORM ACTION="install.php" METHOD="post" NAME="page2form" ID="page2form">

<TABLE CELLPADDING="2" BORDER="1" CLASS="regtable">
<TR CLASS="row2">
	<TD>Database Type:</TD>
	<TD>
		<SELECT NAME="dbms">
			<OPTION VALUE="mysql">MySQL (3 and 4.0)</OPTION>
			<OPTION VALUE="mysqli">MySQL (4.1+)</OPTION>
			<OPTION VALUE="postgres7">PostgreSQL 7</OPTION>
		</SELECT>
	</TD>
</TR>
<TR CLASS="row1">
<TD>Database host address</TD>
<TD><INPUT TYPE="text" NAME="host" VALUE="localhost" LENGTH="20" MAXLENGTH="50" /></TD>
</TR>
<TR CLASS="row2">
<TD>Database host port</TD>
<TD><INPUT TYPE="text" NAME="port" VALUE="3306" LENGTH="20" MAXLENGTH="50" /></TD>
</TR>
<TR CLASS="row1">
<TD>Database name</TD>
<TD><INPUT TYPE="text" NAME="database" VALUE="cms" LENGTH="20" MAXLENGTH="50" /></TD>
</TR>
<TR CLASS="row2">
<TD>Username</TD>
<TD><INPUT TYPE="text" NAME="username" VALUE="cms_user" LENGTH="20" MAXLENGTH="50" /></TD>
</TR>
<TR CLASS="row1">
<TD>Password</TD>
<TD><INPUT TYPE="password" NAME="password" VALUE="cms_pass" LENGTH="20" MAXLENGTH="50" /></TD>
</TR>
<TR CLASS="row2">
<TD>Table prefix</TD>
<TD><INPUT TYPE="text" NAME="prefix" VALUE="cms_" LENGTH="20" MAXLENGTH="50" /><INPUT TYPE="hidden" NAME="page" VALUE="3" /></TD>
</TR>
<TR CLASS="row1">
<TD>Create Tables (Warning: Deletes existing data)</TD>
<TD><INPUT TYPE="checkbox" NAME="createtables" CHECKED="true" /></TD>
</TR>
</TABLE>
<P ALIGN="center" CLASS="continue"><A onClick="document.page2form.submit()">Continue</A></P>
<!--<p><input type="submit" value="Continue" /></p>-->
</FORM>
<?php

} ## showPageTwo

function showPageThree($sqlloaded = 0) {
    ## don't load statements if they've already been loaded
    if ($sqlloaded == 0 && isset($_POST["createtables"])) {

        global $config, $CMS_SCHEMA_VERSION;

		$db = &ADONewConnection($_POST['dbms']);
		#$db->debug = true;
		$result = $db->Connect($_POST['host'].":".$_POST['port'],$_POST['username'],$_POST['password'],$_POST['database']);

		$db_prefix = $_POST['prefix'];

		if (!$result) die("Connection failed");
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
						die('Invalid query');
					} ## if
				}
			}
		}

		fclose($handle);

		echo "[done]</p>";

		include_once(dirname(__FILE__)."/schemas/createseq.php");

		$db->Close();
        echo "<p>Success!</p>";

    } ## if

    $docroot = 'http://'.$_SERVER['HTTP_HOST'].substr($_SERVER['SCRIPT_NAME'],0,strlen($_SERVER['SCRIPT_NAME'])-12);
    $docpath = substr($_SERVER['SCRIPT_FILENAME'],0,strlen($_SERVER['SCRIPT_FILENAME'])-12);

	?>

    <P>Now let's continue to setup your configuration file, we already have most of the stuff we need.</P>
    <P>Chances are you can leave all these values alone unless you have BBCode installed, so when you are ready, click Continue.</P>
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
			<TD><INPUT TYPE="text" NAME="querystr" VALUE="page" LENGTH="20" MAXLENGTH="20"></TD>
		</TR>
		<TR CLASS="row2">
			<TD>Use BBCode (must have this installed, see <A HREF="INSTALL" TARGET="_new">INSTALL</A></TD>
			<TD>
				<INPUT TYPE="text" NAME="bbcode" VALUE="false" LENGTH="5" MAXLENGTH="5">
				<INPUT TYPE="hidden" NAME="page" VALUE="4"><INPUT TYPE="hidden" NAME="host" VALUE="<?php echo $_POST['host']?>">
			    <INPUT TYPE="hidden" NAME="dbms" VALUE="<?php echo $_POST['dbms']?>">
			    <INPUT TYPE="hidden" NAME="database" VALUE="<?php echo $_POST['database']?>">
				<INPUT TYPE="hidden" NAME="port" VALUE="<?php echo $_POST['port']?>">
			    <INPUT TYPE="hidden" NAME="username" VALUE="<?php echo $_POST['username']?>">
				<INPUT TYPE="hidden" NAME="password" VALUE="<?php echo $_POST['password']?>">
			    <INPUT TYPE="hidden" NAME="prefix" VALUE="<?php echo $_POST['prefix']?>">
			</TD>
		</TR>
    </TABLE>
    <P ALIGN="center" CLASS="continue"><A onClick="document.page3form.submit()">Continue</A></P>
	</FORM>

	<?php
    
} ## showPageThree

function showPageFour() {

    if ($_POST['bbcode'] != 'false' and $_POST['bbcode'] != 'true') {
        echo "<p>BB Code needs to be either 'true' or 'false'</p>\n";
        showPageThree(1);
        exit;
    } ## if

	require_once("lib/config.functions.php");

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
	$newconfig['use_bb_code'] = ($_POST['bbcode'] == "true"?true:false);
	$newconfig['use_smarty_php_tags'] = false;
	$newconfig['previews_path'] = $newconfig['root_path'] . "/smarty/cms/cache";
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

    $configfile = dirname(__FILE__)."/config.php";
    ## build the content for config file

	/*
    $content = "<?php\n\n#Database connection information'\n";
	$content .= '$this->dbms = "mysql";'."\n";
    $content .= '$this->db_hostname = "'.$_POST['host'].'";'."\n";
    $content .= '$this->db_username = "'.$_POST['username'].'";'."\n";
    $content .= '$this->db_password = "'.$_POST['password'].'";'."\n";
    $content .= '$this->db_name = "'.$_POST['database'].'";'."\n";
    $content .= '$this->db_prefix = "'.$_POST['prefix'].'";'."\n";
    $content .= '$this->root_url = "'.$_POST['docroot'].'";'."\n";
    $content .= '$this->root_path = "'.addslashes($_POST['docpath']).'";'."\n";
    $content .= '$this->query_var = "'.$_POST['querystr'].'";'."\n";
    $content .= '$this->use_bb_code = '.$_POST['bbcode'].';'."\n";
	$content .= '$this->use_smarty_php_tags = false;'."\n";
	$content .= '$this->previews_path = $this->root_path . "/smarty/cms/cache";'."\n";
    $content .= "\n?>\n";
	*/

    if ((file_exists($configfile) && is_writable($configfile)) || !file_exists($configfile)) {
		cms_config_save($newconfig);
    } else {
        echo "Error: Cannot write to $config.<BR />\n";
        exit;
    } ## if
 
	$link = str_replace(" ", "%20", $_POST['docroot']);
    echo "<H4>Congratulations, you are all setup.</H4><H4>Here is your <A HREF=\"".$link."\">CMS site</A></H4>\n";

} ## showPageFour
?>

</DIV>
</DIV>

</BODY>
</HTML>
<?php

# vim:ts=4 sw=4 noet
?>
