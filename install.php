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
        $cwd = getcwd();
        fwrite($file,"<?php\n".'$this->root_path = "'.$cwd.'";'."\n?>\n");
        fclose($file);
    } else {
        echo "Cannot create $config, please change permissions to allow this\n";
        exit;
    } ## if
} ## if

$pages = 4;
if ($_GET["page"]) {
    $currentpage = $_GET["page"];
} elseif ($_POST["page"]) {
    $currentpage = $_POST["page"];
} else {  
    $currentpage = 1;
} ## if

if ($currentpage > 1) { require_once("include.php"); }

?>

<html>
<head>
    <title>Install for CMS: CMS Made Simple</title>
    <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1">
    <link rel='stylesheet' href='style.css'>
    <style type="text/css">
    <!--
        @import url(style.css);
    -->
    </style>
</head>
<body>

<?php


echo "<h3>Thanks for installing CMS: CMS Made Simple.<br>\n";
echo "We're on page $currentpage of $pages of the install process.  Please follow below.</h3><p><hr width=80%>\n";

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

return;

function showPageOne() {
    ## test file permissions
    ## apache (or other webserver) user needs to have write access to the cache and template_c dirs
    ## as well as the cms root for config.php to be created.

    ## find the user running this script
    $userid = posix_getuid();
    $userdata = posix_getpwuid($userid);
    $username = $userdata['name'];

    ## echo "Userid ($userid) is named $username is running this script<p>\n";

    ## check file perms
    echo "<h3>Checking file permissions:</h3>\n";
    $files = array('smarty/cms/cache/install.test.txt', 'smarty/cms/templates_c/install.test.txt');

    echo "<table border=0 cellpadding=2 cellspacing=0>\n";
    echo "<tr><td class=\"label\"><b>Test</b></td><td class=\"label\"><b>Result</b></td></tr>\n";

    foreach ($files as $f) {
        if ($class == "body") {$class = "bodyalt";} else {$class = "body";}
        echo "<tr><td>\n";
        ## check if we can write to the this file
        echo "<tr><td class=\"$class\">Opening for write ($f)</td><td class=\"$class\">";
        $file = @fopen ($f, "w");
        if($file != 0) {
            echo "<p class=\"success\">Success!</p>\n";
            fclose($file); 
        } else {
            echo "<p class=\"failure\">Failure!</p>\n";
        } ## if 
        echo "</td></tr>";
    } ## foreach

    echo "</table><p>\n";
  
    echo "If all your tests show successful then it is time to setup your database.<br>\n";
    echo "<a href=\"install.php?page=2\">Continue</a>\n";

} ## showPageOne

function showPageTwo() {
?>
Make sure you have created your database and granted full privileges to a user to use that database.<br>
Log in to mysql from a console and run the following commands:<br>
- create database cms; (use whatever name you want here but make sure to remember it, you'll need to enter it on this page)<br>
- grant all privileges on cms.* to cms_user@localhost identified by 'cms_pass';<p>

Please complete the following fields:
<form action=install.php method=post>

<table cellpadding=2 border=1>
<tr>
<td class="body">Database host address</td>
<td class="body"><input type=text name=host value="localhost" length=20 maxlength=50></td>
</tr>
<tr>
<td class="body">Database host port</td>
<td class="body"><input type=text name=port value="3306" length=20 maxlength=50></td>
</tr>
<tr>
<td class="body">Database name</td>
<td class="body"><input type=text name=database value="cms" length=20 maxlength=50></td>
</tr>
<tr>
<td class="bodyalt">Username</td>
<td class="bodyalt"><input type=text name=username value="cms_user" length=20 maxlength=50></td>
</tr>
<tr>
<td class="body">Password</td>
<td class="body"><input type=password name=password value="cms_pass" length=20 maxlength=50></td>
</tr>
<tr>
<td class="bodyalt">Table prefix</td>
<td class="bodyalt"><input type=text name=prefix value="cms_" length=20 maxlength=50></td>
</tr>
<tr>
<td class="body"><input type=hidden name=page value=3></td>
<td class="body"><input type=submit value="Continue"> <input type=reset></td>
</tr>

</table>

</form>
<?php

} ## showPageTwo

function showPageThree($sqlloaded = 0) {
    ## don't load statements if they've already been loaded
    if ($sqlloaded == 0) {
        global $config, $CMS_SCHEMA_VERSION;
        $smarty = new Smarty_DB($config);
        $smarty->assign('tableprefix', $_POST["prefix"]);
        $smarty->assign('schemaversion', $CMS_SCHEMA_VERSION);
        $contents = $smarty->fetch('mysql.tpl');

        $statements = preg_split("/\;\r?\n?$/m", $contents);
 
        echo "<textarea name=code rows=15 cols=80>$contents</textarea><p>\n";
        $link = @mysql_connect($_POST['host'].":".$_POST['port'], $_POST['username'], $_POST['password']);
        if (!$link) {
           die('Could not connect: ' . mysql_error());
        } ## if
        mysql_select_db($_POST['database']);


        foreach ($statements as $s) {
            $s = str_replace("\n", "", $s);
            if ($s != "") {
                $result = mysql_unbuffered_query($s, $link);
                if (!$result) {
                    die('Invalid query: ' . mysql_error());
                } ## if
            } ## if
        } ## foreach

        mysql_close($link);
        echo "Success!<p>";

        ## foreach ($_SERVER as $key => $value) { echo "$key: $value<br>\n"; }
    } ## if

    $docroot = 'http://'.$_SERVER['HTTP_HOST'].substr($_SERVER['SCRIPT_NAME'],0,strlen($_SERVER['SCRIPT_NAME'])-12);
    $docpath = substr($_SERVER['SCRIPT_FILENAME'],0,strlen($_SERVER['SCRIPT_FILENAME'])-12);
    echo "<p>\n";

    echo "Now let's continue to setup your configuration file, we already have most of the stuff we need.<br>\n";
    echo "Chances are you can leave all these values alone unless you have BBCode installed, so when you are ready, click Write Config.<p>\n";
    echo "<form action=install.php method=post>\n";
    echo "<table border=0 cellpadding=2>\n";
    echo "<tr><td class=\"body\">CMS Document root (as seen from the webserver)</td><td class=\"body\"><input type=text name=docroot value=\"$docroot\" length=50 maxlength=100></td></tr>\n";
    echo "<tr><td class=\"bodyalt\">Path to the Document root</td><td class=\"bodyalt\"><input type=text name=docpath value=\"$docpath\" length=50 maxlength=100></td></tr>\n";
    echo "<tr><td class=\"body\">Query string (leave this alone unless you have trouble, then edit config.php by hand)</td><td class=\"body\"><input type=text name=querystr value=\"page\" length=20 maxlength=20></td></tr>\n";
    echo "<tr><td class=\"bodyalt\">Use BBCode (must have this installed, see <a href=INSTALL target=_new>INSTALL</a></td><td class=\"bodyalt\"><input type=text name=bbcode value=\"false\" length=5 maxlength=5></td></tr>\n";

    echo '<tr><td class="body"><input type=hidden name=page value=4><input type=hidden name=host value="'.$_POST['host'].'">';
    echo '<input type=hidden name=database value="'.$_POST['database'].'"><input type=hidden name=port value="'.$_POST['port'].'">';
    echo '<input type=hidden name=username value="'.$_POST['username'].'"><input type=hidden name=password value="'.$_POST['password'].'">';
    echo '<input type=hidden name=prefix value="'.$_POST['prefix'].'">';
    echo "</td><td class=\"body\"><input type=submit value=\"Write config\"> <input type=reset></td></tr>\n";
    echo "</table></form>\n";

    
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
    $content .= '$this->db_hostname = "'.$_POST['host'].'";'."\n";
    $content .= '$this->db_username = "'.$_POST['username'].'";'."\n";
    $content .= '$this->db_password = "'.$_POST['password'].'";'."\n";
    $content .= '$this->db_name = "'.$_POST['database'].'";'."\n";
    $content .= '$this->db_prefix = "'.$_POST['prefix'].'";'."\n";
    $content .= '$this->root_url = "'.$_POST['docroot'].'";'."\n";
    $content .= '$this->root_path = "'.$_POST['docpath'].'";'."\n";
    $content .= '$this->query_var = "'.$_POST['querystr'].'";'."\n";
    $content .= '$this->use_bb_code = '.$_POST['bbcode'].';'."\n";
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
        echo "Error: Cannot write to $config.<br>\n";
        exit;
    } ## if
 
    echo "<h4>Congratulations, you are all setup.<br>Here is your <a href=".$_POST['docroot'].">CMS site</a><br>\n";

} ## showPageFour
?>


<!--
## 1. Check perms - Smarty needs the cache and template_c dirs to be writable by the web server user and I guess config.php also...
## 2. Install the schema (or update)
## 3. Setup default admin account - already done in step 2
## 4. Write settings out to config.php
-->
</body>
</html>
