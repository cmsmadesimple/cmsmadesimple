<?php

require_once("include.php");

$smarty = new Smarty_CMS($config);
$smarty->configCMS = &$config;
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

$pages = 4;
if ($_GET["page"]) {
    $currentpage = $_GET["page"];
} elseif ($_POST["page"]) {
    $currentpage = $_POST["page"];
} else {  
    $currentpage = 1;
}

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

<?php
    $smarty->assign('tableprefix', 'cms_');
    $contents = $smarty->fetch('mysql.tpl');

    echo "contents: ($contents)<p>\n";

} ## showPageTwo
?>


<!--
## 1. Check perms - Smarty needs the cache and template_c dirs to be writable by the web server user and I guess config.php also...
## 2. Install the schema (or update)
## 3. Setup default admin account - already done in step 2
## 4. Write settings out to config.php
-->
</body>
</html>
