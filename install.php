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
if (!file_exists($config) || filesize($config) == 0) {
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

$pages = 4;
if ($_POST["page"]) {
    $currentpage = $_POST["page"];
} elseif ($_GET["page"]) {
    $currentpage = $_GET["page"];
} else {  
    $currentpage = 1;
} ## if

$DONT_LOAD_DB = true;
if ($currentpage > 1) { require_once("include.php"); }

?>

<html>
<head>
        <title>CMS Made Simple Install</title>
        <link rel="stylesheet" type="text/css" href="install.css" />
</head>

<body>

<div class="body">

<img src="images/cmsbanner.png" width="400" height="96" alt="CMS Banner Logo" />

<div class="headerish">

<h1>Install System</h1>

</div>

<div class="main">

<?php

echo "<h3>Thanks for installing CMS: CMS Made Simple.</h3>\n";
echo "<table class=\"countdown\" cellspacing=\"2\" cellpadding=\"2\"><tr>";
echo "<td class=\"".($currentpage>=1?"complete":"todo")."\">1</td>";
echo "<td class=\"".($currentpage>=2?"complete":"todo")."\">2</td>";
echo "<td class=\"".($currentpage>=3?"complete":"todo")."\">3</td>";
echo "<td class=\"".($currentpage>=4?"complete":"todo")."\">4</td>";
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
    echo "<h3>Checking file permissions:</h3>\n";
    $files = array('smarty/cms/cache/install.test.txt', 'smarty/cms/templates_c/install.test.txt');

    echo "<table class=\"regtable\" border=\"1\">\n";
    echo "<thead class=\"tbhead\"><tr><th>Test</th><th>Result</th></tr></thead><tbody>\n";

    echo "<tr class=\"row1\"><td>Checking for PHP version 4.0+</td><td>";
	echo (@phpversion() >= "4"?"Success!":"Failure!");
	echo "</td></tr>\n";

    echo "<tr class=\"row2\"><td>Checking for PHP mysql support</td><td>";
	echo (function_exists('mysql_connect')?"Success!":"Failure!");
	echo "</td></tr>\n";

	$currow = "row1";

    foreach ($files as $f) {
        #echo "<tr><td>\n";
        ## check if we can write to the this file
        echo "<tr class=\"$currow\"><td>Opening for write ($f)</td><td>";
        $file = @fopen ($f, "w");
        if($file != 0) {
            fclose($file); 
			@unlink($f);
            echo "Success!";
        } else {
            echo "Failure!";
        } ## if 
        echo "</td></tr>\n";
		($currow=="row1"?$currow="row2":$currow="row1");
    } ## foreach

    echo "</tbody></table>\n";
  
    echo "<p>If all your tests show successful then it is time to setup your database.</p>\n";
    echo "<p class=\"continue\" align=\"center\"><a href=\"install.php?page=2\">Continue</a></p>\n";

} ## showPageOne

function showPageTwo() {
?>
<p>Make sure you have created your database and granted full privileges to a user to use that database.</p>
<p>Log in to mysql from a console and run the following commands:</p>
<ol>
<li>create database cms; (use whatever name you want here but make sure to remember it, you'll need to enter it on this page)</li>
<li>grant all privileges on cms.* to cms_user@localhost identified by 'cms_pass';</li>
</ol>
<p />

Please complete the following fields:
<form action="install.php" method="post" name="page2form" id="page2form">

<table cellpadding="2" border="1" class="regtable">
<tr class="row1">
<td>Database host address</td>
<td><input type="text" name="host" value="localhost" length="20" maxlength="50" /></td>
</tr>
<tr class="row2">
<td>Database host port</td>
<td><input type="text" name="port" value="3306" length="20" maxlength="50" /></td>
</tr>
<tr class="row1">
<td>Database name</td>
<td><input type="text" name="database" value="cms" length="20" maxlength="50" /></td>
</tr>
<tr class="row2">
<td>Username</td>
<td><input type="text" name="username" value="cms_user" length="20" maxlength="50" /></td>
</tr>
<tr class="row1">
<td>Password</td>
<td><input type="password" name="password" value="cms_pass" length="20" maxlength="50" /></td>
</tr>
<tr class="row2">
<td>Table prefix</td>
<td><input type="text" name="prefix" value="cms_" length="20" maxlength="50" /><input type="hidden" name="page" value="3" /></td>
</tr>
<tr class="row1">
<td>Create Tables (Warning: Deletes existing data)</td>
<td><input type="checkbox" name="createtables" checked="true" /></td>
</tr>
</table>
<p align="center" class="continue"><a onclick="document.page2form.submit()">Continue</a></p>
<!--<p><input type="submit" value="Continue" /></p>-->
</form>
<?php

} ## showPageTwo

function showPageThree($sqlloaded = 0) {
    ## don't load statements if they've already been loaded
    if ($sqlloaded == 0 && isset($_POST["createtables"])) {

        global $config, $CMS_SCHEMA_VERSION;

		$db = &ADONewConnection('mysql');
		$result = $db->Connect($_POST['host'].":".$_POST['port'],$_POST['username'],$_POST['password'],$_POST['database']);

		$db_prefix = $_POST['prefix'];

		if (!$result) die("Connection failed");
		$db->SetFetchMode(ADODB_FETCH_ASSOC);

		$CMS_INSTALL_DROP_TABLES=1;
		$CMS_INSTALL_CREATE_TABLES=1;

		include_once(dirname(__FILE__)."/schemas/schema.php");

		echo "<p>Inporting initial data...";

		$handle = fopen(dirname(__FILE__)."/schemas/mysql.sql", 'r');
		while (!feof($handle)) {
			set_magic_quotes_runtime(false);
			$result = $db->Execute("USE ".$_POST['database'].";");
			$s = fgets($handle, 32768);
			if ($s != "") {
				$s = trim(str_replace("{DB_PREFIX}", $db_prefix, $s));
				$result = $db->Execute($s);
				if (!$result) {
					die('Invalid query: ' . mysql_error());
				} ## if
			}
		}
		fclose($handle);

		echo "[done]</p>";

		$db->Close();
        echo "<p>Success!</p>";

    } ## if

    $docroot = 'http://'.$_SERVER['HTTP_HOST'].substr($_SERVER['SCRIPT_NAME'],0,strlen($_SERVER['SCRIPT_NAME'])-12);
    $docpath = substr($_SERVER['SCRIPT_FILENAME'],0,strlen($_SERVER['SCRIPT_FILENAME'])-12);

	?>

    <p>Now let's continue to setup your configuration file, we already have most of the stuff we need.</p>
    <p>Chances are you can leave all these values alone unless you have BBCode installed, so when you are ready, click Write Config.</p>
    <form action="install.php" method="post" name="page3form" id="page3form">
	<table cellpadding="2" border="1" class="regtable">
		<tr class="row1">
			<td>CMS Document root (as seen from the webserver)</td>
			<td><input type="text" name="docroot" value="<?=$docroot?>" length="50" maxlength="100"></td>
		</tr>
		<tr class="row2">
			<td>Path to the Document root</td>
			<td><input type="text" name="docpath" value="<?=$docpath?>" length="50" maxlength="100"></td>
		</tr>
		<tr class="row1">
			<td>Query string (leave this alone unless you have trouble, then edit config.php by hand)</td>
			<td><input type="text" name="querystr" value="page" length="20" maxlength="20"></td>
		</tr>
		<tr class="row2">
			<td>Use BBCode (must have this installed, see <a href="INSTALL" target="_new">INSTALL</a></td>
			<td>
				<input type="text" name="bbcode" value="false" length="5" maxlength="5">
				<input type="hidden" name="page" value="4"><input type="hidden" name="host" value="<?=$_POST['host']?>">
			    <input type="hidden" name="database" value="<?=$_POST['database']?>">
				<input type="hidden" name="port" value="<?=$_POST['port']?>">
			    <input type="hidden" name="username" value="<?=$_POST['username']?>">
				<input type="hidden" name="password" value="<?=$_POST['password']?>">
			    <input type="hidden" name="prefix" value="<?=$_POST['prefix']?>">
			</td>
		</tr>
    </table>
    <p align="center" class="continue"><a onclick="document.page3form.submit()">Continue</a></p>
	</form>

	<?php
    
} ## showPageThree

function showPageFour() {

    if ($_POST['bbcode'] != 'false' and $_POST['bbcode'] != 'true') {
        echo "<p>BB Code needs to be either 'true' or 'false'</p>\n";
        showPageThree(1);
        exit;
    } ## if

    $config = "config.php";
    ## build the content for config file

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
    $content .= "\n?>\n";

    if ((file_exists($config) && is_writable($config)) || !file_exists($config)) {
        $file = @fopen($config, "w");
        if ($file != 0) {
            if (fwrite($file,$content) === FALSE) {
                echo "Cannot write to $config\n";
                exit;
            } ## if
            fclose($file);
        } ## if
    } else {
        echo "Error: Cannot write to $config.<br />\n";
        exit;
    } ## if
 
	$link = str_replace(" ", "%20", $_POST['docroot']);
    echo "<h4>Congratulations, you are all setup.</h4><h4>Here is your <a href=\"".$link."\">CMS site</a></h4>\n";

} ## showPageFour
?>

</div>
</div>

</body>
</html>
<?php

# vim:ts=4 sw=4 noet
?>
