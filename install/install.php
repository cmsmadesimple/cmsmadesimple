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

function return_bytes($val) {
   $val = trim($val);
   $last = strtolower($val{strlen($val)-1});
   switch($last) 
   {
       // The 'G' modifier is available since PHP 5.1.0
       case 'g':
           $val *= 1024;
       case 'm':
           $val *= 1024;
       case 'k':
           $val *= 1024;
   }
   return $val;
}


function test_cfg_var_bool( $name, $desc, $success, $row = 'row2', $warning_message = '' )
{
  $icon = 'false.gif';
  $alt = 'Failure';
  $ret = false;
  
  $str = (bool) ini_get( $name );
  $str = ($str) ? 1 : 0;
  if( $success == $str )
    {
      $icon = 'true.gif';
      $alt = 'Success';
      $ret = true;
    }

  echo "<tr class=\"$row\"><td>$desc";
  if ($warning_message != '' && $ret == false)
  {
    echo "<br/><br /><em>$warning_message</em>";
  }
  echo " </td><td class=\"col2\">";
  echo "<img src=\"../images/cms/install/$icon\" alt=\"$alt\" height=\"16\" width=\"16\" border=\"0\" />";
  echo "</td></tr>\n";
  return $ret;
}

function test_cfg_var_range( $name, $desc, $yellowlimit, $greenlimit, $row = 'row2', $compare_as_bytes = FALSE, $warning_message = '' )
{
  $icon = 'red.gif';
  $alt="Failure";
  $ret = false;
  
  if (! is_int( $yellowlimit ) && ! is_int( $greenlimit ) && $compare_as_bytes == TRUE)
    {
      $yellowlimit_org = $yellowlimit;
      $yellowlimit = return_bytes( $yellowlimit );
      $greenlimit  = return_bytes( $greenlimit );
    }
  
  if( is_int( $yellowlimit ) && is_int( $greenlimit ) )
    {
      if ($compare_as_bytes)
        {
          $str = ini_get( $name );
          if ( $str == '' )
            {
              $warning = "Could not retrieve a value.... passing anyways";
              $str = $yellowlimit;
              $show_value = $yellowlimit_org;
            }
          else
            {
              $show_value = $str;
              $str = return_bytes($str);
            }
        }
      else
        {
          $str = (int) ini_get( $name );
        }

    if( $str < 0 )
      {
        $alt = 'Success (unlimited)';
        $icon = 'green.gif';
        $ret = true;
      }
    else if( $str >= $greenlimit )
	{
	  $alt = 'Success';
	  $icon = 'green.gif';
	  $ret = true;
	}
    else if( $str >= $yellowlimit )
	{
	  $alt = 'Caution';
	  $icon = 'yellow.gif';
	  $ret = true;
	}
	if ($compare_as_bytes) 
    { 
      $str = $show_value; 
    }
    }
  else
    {
      $warning = "";
      $str = strtoupper(ini_get( $name ));
      if( $str == "" )
	{
	  $str = $yellowlimit;
	  $warning = "Could not retrieve a value.... passing anyways";
	}
      if( strcmp( $str, $yellowlimit ) >= 0 )
	{
	  $alt = 'Caution';
	  $icon = 'yellow.gif';
	  $ret = true;
	}
      if( strcmp( $str, $greenlimit ) >= 0 )
	{
	  $alt = 'Success';
	  $icon = 'green.gif';
	  $ret = true;
	}
    }
  echo "<tr class=\"$row\"><td><span class=\"have\">You have \"$str\"</span>$desc";
  if ($warning_message != '') {
    $warning = '<br /><em>' . $warning_message . '</em>';
  }
  if( isset( $warning ) && $warning != "" )
    {
      echo "<br/>$warning";
    }
  echo "</td><td class=\"col2\">";
  echo "<img src=\"../images/cms/install/$icon\" alt=\"$alt\" height=\"16\" width=\"16\" border=\"0\" />";
  echo "</td></tr>\n";
  return $ret;
}

$LOAD_ALL_MODULES=1;
require_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'fileloc.php');

$config = CONFIG_FILE_LOCATION;
if (!file_exists($config)) {
    $file = @fopen($config, "w");
    if ($file != 0) {
		$cwd = addslashes(dirname(dirname(__FILE__)));
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
      $cwd = addslashes(dirname(dirname(__FILE__)));
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

// Test for sessions if this is the first page of the install
if (1 == $currentpage)
  {
    @session_start();
    if (!isset($_GET['sessiontest']))
      {
	$_SESSION['test'] = TRUE;
	$scheme = ((! isset($_SERVER['HTTPS'])) || strtolower($_SERVER['HTTPS']) != 'on') ? 'http' : 'https';
	$redirect = $scheme . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?sessiontest=1&' . SID;
	header("Location: $redirect");
      }
  }

$DONT_LOAD_DB = true;

if ($currentpage > 1) { require_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."include.php"); }

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
        <title>CMS Made Simple Install</title>
        <link rel="stylesheet" type="text/css" href="install.css" />
        <script src="../admin/themes/default/includes/standard.js" type="text/javascript"></script>
</head>

<body>

<div class="body">

<img src="../images/cms/cmsbanner.jpg" width="800" height="100" alt="CMS Banner Logo" />

<div class="headerish">

<h1>Install System</h1>

</div>

<div class="main">
<?php

echo "<h2>Thanks for installing CMS Made Simple</h2>\n";
echo "<table class=\"countdown\" cellspacing=\"2\" cellpadding=\"2\"><tr>";
echo "<td><img src=\"../images/cms/install/".($currentpage==1?"1":"1off").".gif\" alt=\"Step 1\" /></td>";
echo "<td><img src=\"../images/cms/install/".($currentpage==2?"2":"2off").".gif\" alt=\"Step 2\" /></td>";
echo "<td><img src=\"../images/cms/install/".($currentpage==3?"3":"3off").".gif\" alt=\"Step 3\" /></td>";
echo "<td><img src=\"../images/cms/install/".($currentpage==4?"4":"4off").".gif\" alt=\"Step 4\" /></td>";
echo "<td><img src=\"../images/cms/install/".($currentpage==5?"5":"5off").".gif\" alt=\"Step 5\" /></td>";
echo "</tr></table>\n";
echo "<br />";


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
  echo '<p class="important">Please read the <a href="http://wiki.cmsmadesimple.org/index.php/User_Handbook/Installation/Troubleshooting">Installation Troubleshooting</a> page in the CMS Made Simple Documentation Wiki.</p>';
  echo "<h3>Checking permissions and PHP settings</h3>\n";
  #$files = array(TMP_CACHE_LOCATION, TMP_TEMPLATES_C_LOCATION, dirname(dirname(__FILE__)).'/uploads', CONFIG_FILE_LOCATION);
  $files = array(TMP_CACHE_LOCATION, TMP_TEMPLATES_C_LOCATION, CONFIG_FILE_LOCATION);



  // body
  echo "<table class=\"settings\" border=\"0\">\n";
  echo "<caption class=\"tbcaption\">Required settings</caption>\n";
  echo "<thead class=\"tbhead\"><tr><th>Test</th><th>Result</th></tr></thead><tbody>\n";

  echo "<tr class=\"row1\"><td>Checking for PHP version 4.2+</td><td class=\"col2\">";
  echo (@version_compare(phpversion(),"4.2.0") > -1?'<img src="../images/cms/install/true.gif" alt="Success" height="16" width="16" border="0" />':'<img src="../images/cms/install/false.gif" alt="Failure" height="16" width="16" border="0" />');
  (@version_compare(phpversion(),"4.2.0") > -1?null:$continueon=false);
  echo "</td></tr>\n";
    
  echo "<tr class=\"row2\"><td>Checking for Session Functions</td><td class=\"col2\">";
  if (function_exists("session_start"))
    {
      echo '<img src="../images/cms/install/true.gif" alt="Success" height="16" width="16" border="0" />';
    }
  else
    {
      echo '<img src="../images/cms/install/false.gif" alt="Failure" height="16" width="16" border="0" />';
      $continueon = false;
    }
  echo "</td></tr>\n";
    
  echo "<tr class=\"row1\"><td>Checking for md5 Function</td><td class=\"col2\">";
  if (function_exists("md5"))
    {
      echo '<img src="../images/cms/install/true.gif" alt="Success" height="16" width="16" border="0" />';
    }
  else
    {
      echo '<img src="../images/cms/install/false.gif" alt="Failure" height="16" width="16" border="0" />';
      $continueon = false;
    }
  echo "</td></tr>\n";
    
  $currow = 'row2';
  foreach ($files as $f) {
    #echo "<tr><td>\n";
    ## check if we can write to the this file
    echo "<tr class=\"$currow\"><td>Checking write permission on $f";

    echo "</td><td class=\"col2\">";
      
    if (is_writable($f))
      {
	echo '<img src="../images/cms/install/true.gif" alt="Success" height="16" width="16" border="0" />';
      }
    else
      {
	$continueon=false;
	echo '<img src="../images/cms/install/false.gif" alt="Failure" height="16" width="16" border="0" />';
      } ## if 
    echo "</td></tr>\n";
    ($currow=="row1"?$currow="row2":$currow="row1");
  } ## foreach
    
  echo "<tr class=\"row1\"><td>Checking for basic XML (expat) support";
  if( !function_exists( "xml_parser_create" ) )
    {
	  echo '<br /><br /><em>XML support is not compiled into your php install.  You can still use the system, but will not be able to use any of the remote module installation functions.</em>';
      echo '</td><td class="col2"><img src="../images/cms/install/yellow.gif" alt="Caution" height="16" width="16" border="0" />';
      $special_failed=true;
    }
  else
    {
      echo '</td><td class="col2"><img src="../images/cms/install/true.gif" alt="Success" height="16" width="16" border="0" />';
    }
  echo "</td></tr>\n";
  echo "</tbody></table>\n";
 
 echo "<br /><br />\n";
 
 // Checking for recommended settings
  echo "<table class=\"settings\" border=\"0\">\n";
  echo "<caption class=\"tbcaption\">Recommended settings</caption>\n";
  echo "<thead class=\"tbhead\"><tr><th>Test</th><th>Result</th></tr></thead><tbody>\n";
  
  ($currow=="row1"?$currow="row2":$currow="row1");
  if (!test_cfg_var_bool( "file_uploads", "Checking file uploads", 1, $currow, 'You will not be able to use any of the file uploading facilities included in CMS Made Simple.  If possible, this restriction should be lifted by your system admin to properly use all file management features of the system.  Proceed with caution.' ))
  {
    $special_failed=true;
  }
  
  $currow = ($currow == 'row1') ? 'row2' : 'row1';
  if(!test_cfg_var_range( "memory_limit", "Checking PHP memory limit (min 8M, recommend 16M)", "8M", "16M", $currow, TRUE, 'You may not have enough memory to run CMSMS correctly. If possible, you should try to get your system admin to raise this value to the minimum 8M or great. Proceed with caution.'))
  {
    $special_failed=true;
  }
  $currow = ($currow == 'row1') ? 'row2' : 'row1';
  if (!test_cfg_var_range( "upload_max_filesize", "Checking max upload file size (min 2M, recommend 10M)", "2M", "10M", $currow, TRUE, 'You probably will not be able to upload any files using any of the included file management functions.  Please be aware of this restriction.' ))
  {
    $special_failed=true;
  }
  $currow = ($currow == 'row1') ? 'row2' : 'row1';
 
  // is uploads dir writable?
  $f = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'uploads';
  echo "<tr class=\"$currow\"><td>Checking if $f is writable<br/><br/>
        <em>If uploads is not writable you can still install the system, but you will not be able to upload files via the Admin Panel.</em>
        </td><td class=\"col2\">";
  if (is_writable($f))
    {
      echo '<img src="../images/cms/install/green.gif" alt="Success" height="16" width="16" border="0" />';
    }
  else
    {
      echo '<img src="../images/cms/install/yellow.gif" alt="Caution" height="16" width="16" border="0" />';
    }
  echo "</td></tr>\n";
  $currow = ($currow == 'row1') ? 'row2' : 'row1';


  // is modules dir writable?
  $f = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'modules';
  echo "<tr class=\"$currow\"><td>Checking if $f is writable<br/><br/>
        <em>If modules is not writable you can still install the system, but you will not be able to install modules via the Admin Panel.</em>
        </td><td class=\"col2\">";
  if (is_writable($f))
    {
      echo '<img src="../images/cms/install/green.gif" alt="Success" height="16" width="16" border="0" />';
    }
  else
    {
      echo '<img src="../images/cms/install/yellow.gif" alt="Caution" height="16" width="16" border="0" />';
    }
  echo "</td></tr>\n";
  $currow = ($currow == 'row1') ? 'row2' : 'row1';

 
    // do we have the file_get_contents function
  echo "<tr class=\"$currow\"><td>Checking for file_get_contents<br/><br/>
        <em>The file_get_contents function was added in PHP 4.3 and although a workaround has been added that should allow most functionality that uses this function to work properly in PHP 4.2, it may be advisable to upgrade to PHP 4.3 or greater.</em>
        </td><td class=\"col2\">";
  if (function_exists("file_get_contents"))
    {
      echo '<img src="../images/cms/install/green.gif" alt="Success" height="16" width="16" border="0" />';
    }
  else
    {
      echo '<img src="../images/cms/install/yellow.gif" alt="Caution" height="16" width="16" border="0" />';
    }
  echo "</td></tr>\n";
  $currow = ($currow == 'row1') ? 'row2' : 'row1';

   // is session_save_path writable?
  echo "<tr class=\"$currow\"><td>Checking if session.save_path is writable<br/><br/>";
  echo '<em>Your session.save_path is "'.session_save_path().'". Not having this as writable may make logins to the Admin Panel not work. You may want to look into making this path writable if you have trouble logging into the Admin Panel.</em>
       </td><td class="col2">';
  if (is_writable(session_save_path()))
    {
      echo '<img src="../images/cms/install/green.gif" alt="Success" height="16" width="16" border="0" />';
	}
  else
    {
      echo '<img src="../images/cms/install/yellow.gif" alt="Caution" height="16" width="16" border="0" />';
    }
  echo "</td></tr>\n";
  $currow = ($currow == 'row1') ? 'row2' : 'row1';

  // can we set a php ini variable
  echo "<tr class=\"$currow\"><td>Checking if ini_set works<br/><br/>
        <em>Although the ability to override php ini settings is not mandatory, some addon (optional) functionality uses ini_set to extend timeouts, and allow uploading of larger files, etc.  You may have difficulty with some addon functionality without this capability.</em>
        </td><td class=\"col2\">";
  ini_set( 'max_execution_time', '123' );
  if( ini_get('max_execution_time') == 123 )
    {
      echo '<img src="../images/cms/install/green.gif" alt="Success" height="16" width="16" border="0" />';
    }
  else
    {
      echo '<img src="../images/cms/install/yellow.gif" alt="Caution" height="16" width="16" border="0" />';
    }
  echo "</td></tr>\n";
  $currow = ($currow == 'row1') ? 'row2' : 'row1';

  // are sessions enabled?
  echo "<tr class=\"$currow\"><td>Checking if sessions are enabled<br/><br/>
        <em>Although the PHP support for sessions is not mandatory, it is highly recommended. Logins and other things may slow down and you may have difficulty with some addon functionality without this capability.</em>
        </td><td class=\"col2\">";
  if( isset($_GET['sessiontest']) && isset($_SESSION['test']) )
    {
      echo '<img src="../images/cms/install/green.gif" alt="Success" height="16" width="16" border="0" />';
    }
  else
    {
      echo '<img src="../images/cms/install/yellow.gif" alt="Caution" height="16" width="16" border="0" />';
    }
  echo "</td></tr>\n";
  $currow = ($currow == 'row1') ? 'row2' : 'row1';
  
  // Do tokenizer functions exist?
  echo "<tr class=\"$currow\"><td>Checking for tokenizer functions<br/><br/>
        <em>Not having the tokenizer could cause pages to render as purely white.  We recommend you have this installed, but your website may work fine without it.</em>
        </td><td class=\"col2\">";
  if (function_exists("token_get_all"))
    {
     echo '<img src="../images/cms/install/green.gif" alt="Success" height="16" width="16" border="0" />';
    }
  else
    {
      echo '<img src="../images/cms/install/yellow.gif" alt="Caution" height="16" width="16" border="0" />';
    }
  echo "</td></tr>\n";

  $special_failed=false;
    echo "</tbody></table>\n";

  echo "<br /><br />\n";
	
  // legend
  echo "<table class=\"legend\" border=\"0\">\n";
  echo "<caption class=\"tbcaption\">Legend</caption>\n";
  echo "<thead class=\"tbhead\"><tr><th>Symbol</th><th>Definition</th></tr></thead>\n";
  echo "<tbody>\n";
  echo '<tr><td><img src="../images/cms/install/true.gif" alt="Success" height="16" width="16" border="0" /></td><td>A required test passed</td></tr>';
  echo '<tr><td><img src="../images/cms/install/false.gif" alt="Failure" height="16" width="16" border="0" /></td><td>A required test failed</td></tr>';
  echo '<tr><td><img src="../images/cms/install/red.gif" alt="Failure" height="16" width="16" border="0" /></td><td>A setting is below a required minumum value</td></tr>';
  echo '<tr><td><img src="../images/cms/install/yellow.gif" alt="Caution" height="16" width="16" border="0" /></td><td>A setting is above the required value, but below the recommended value<br /><br />or... A capability that <em>may</em> be required for some optional functionality is unavailable</td></tr>';
  echo '<tr><td><img src="../images/cms/install/green.gif" alt="Success" height="16" width="16" border="0" /></td><td>A setting meets or exceeds the recommended threshhold<br /><br />or... A capability that <em>may</em> be required for some optional functionality is available</td></tr>';
  echo "</tbody>\n";
  echo "</table><br/>\n";
  
	
  echo '<form method="post" action="install.php">';
    
  if ($continueon)
    {
      if($special_failed) {
	echo '<p class="failure" align="center">One or more tests have failed. You can still install the system but some functions may not work correctly. Please click the Continue button.</p>';
      } else {
	echo '<p class="success" align="center">All tests passed (at least at a minimum level). Please click the Continue button.</p>';
      }
      echo '<p class="continue" align="center"><input type="hidden" name="page" value="2" /><input type="submit" value="Continue" /></p>';
    }
  else
    {
      echo '<p class="failure" align="center">One or more tests have failed. Please correct the problem and click the button below to recheck.</p>';
      echo '<p class="continue" align="center"><input type="Submit" value="Try Again" /></p>';
    }
  echo '</form>';

} ## showPageOne

function showPageTwo($errorMessage='',$username='',$email='',$email_accountinfo='0')
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

<table border="0" class="adminaccount">

	<tr class="row1">
		<td>Username</td>
		<td><input class="defaultfocus" type="text" name="adminusername" value="<?php echo $username?>" size="20" maxlength="50" /></td>
	</tr>

	<tr class="row2">
		<td>E-mail Address</td>
		<td><input type="text" name="adminemail" value="<?php echo $email?>" size="20" maxlength="50" /></td>
	</tr>

	<tr class="row1">
		<td>Password</td>
		<td><input type="password" name="adminpassword" value="" size="20" maxlength="50" /></td>
	</tr>

	<tr class="row2">
		<td>Password Again</td>
		<td><input type="password" name="adminpasswordagain" value="" size="20" maxlength="50" /></td>
	</tr>
<?php
if (function_exists('mail'))
{
?>
	<tr class="row1">
		<td>E-Mail Account Information</td>
		<td><input type="checkbox" name="email_accountinfo" value="1" <?php if ($email_accountinfo == 1) echo ' checked="checked"' ?> /></td>
	</tr>
<?php
}
?>
</table>

<p align="center" class="continue"><input type="hidden" name="page" value="3" /><input type="submit" value="Continue" /><!--<a onclick="document.page2form.submit()" href="#">Continue</a>--></p>

</form>

	<?php

} ## showPageTwo()

function showPageThree($errorMessage='')
{
    $email_accountinfo = isset($_POST['email_accountinfo']) ? true : false;

	if ($errorMessage != '')
	{
		echo "<p class=\"error\">$errorMessage</p>";
	}
# Do check that username and password are filled out.
# Skip back to showPageTwo() if necessary
# Put given variables into hidden fields so they can up UPDATEd later on in step 4 (post schema install)
	if ($_POST['adminusername'] == '')
	{
		showPageTwo('Username not given!', '', $_POST['adminemail'], ($email_accountinfo ? '1' : '0'));
		return;
	}
	else if ($_POST['adminpassword'] == '' || $_POST['adminpasswordagain'] == '')
	{
		showPageTwo('Both password fields not given!', $_POST['adminusername'], $_POST['adminemail'], ($email_accountinfo ? '1' : '0'));
		return;
	}
	else if ($_POST['adminpassword'] != $_POST['adminpasswordagain'])
	{
		showPageTwo('Password fields do not match!', $_POST['adminusername'], $_POST['adminemail'], ($email_accountinfo ? '1' : '0'));
		return;
	}
	if ($email_accountinfo && trim($_POST['adminemail'] == ''))
    {
		showPageTwo('E-mail accountinfo selected, but no E-mail address given!', $_POST['adminusername'], $_POST['adminemail'], '1');
		return;
    }
	
	$adminusername = $_POST['adminusername'];
	$adminemail = $_POST['adminemail'];
	$adminpassword = $_POST['adminpassword'];
?>

<form action="install.php" method="post" name="page3form" id="page3form">

<h3>Site Name</h3>

<p>This is the name of your site.  It will be used in various places of the default templates and can be used anywhere with the
{sitename} tag.</p>
<p style="text-align: center;"><input class="defaultfocus selectall" type="text" name="sitename" size="40" value="<?php echo isset($_POST['sitename'])? htmlentities($_POST['sitename']):'CMS Made Simple Site' ?>" /></p>

<h3>Database Information</h3>

<p>Make sure you have created your database and granted full privileges to a user to use that database.</p>
<p>For MySQL, use the following:</p>
<p>Log in to mysql from a console and run the following commands:</p>
<ol>
<li>create database cms; (use whatever name you want here but make sure to remember it, you&apos;ll need to enter it on this page)</li>
<li>grant all privileges on cms.* to cms_user@localhost identified by 'cms_pass';</li>
</ol>

<p>Please complete the following fields:</p>

<table cellpadding="2" border="1" class="regtable">
<tr class="row2">
	<td>Database Type:</td>
	<td>
		<select name="dbms">
<?php
$valid_database = false;
if (extension_loaded('mysql'))
	{
	echo '<option value="mysql" ';
	echo (isset($_POST['dbms']) && $_POST['dbms'] == 'mysql'?'selected="selected"':'');
	echo '>MySQL (3 and 4.0)</option>';
	$valid_database = true;
	}
if (extension_loaded('mysqli'))
	{
	echo '<option value="mysqli" ';
	echo (isset($_POST['dbms']) && $_POST['dbms'] == 'mysqli'?'selected="selected"':'');
	echo '>MySQL (4.1+)</option>';
	$valid_database = true;
	}
if (extension_loaded('pgsql'))
	{
	echo '<option value="postgres7" ';
	echo (isset($_POST['dbms']) && $_POST['dbms'] == 'postgres7'?'selected="selected"':'');
	echo '>PostgreSQL 7/8</option>';
	$valid_database = true;
	}
/*
if (extension_loaded('sqlite'))
	{
	echo '<option value="sqlite" ';
	echo (isset($_POST['dbms']) && $_POST['dbms'] == 'sqlite'?'selected="selected"':'');
	echo '>SQLite</option>';
	$valid_database = true;
	}
*/
?>
		</select>
<?php if (! $valid_database) { ?>
<div class="error">No valid database drivers appear to be compiled into your PHP install. Please confirm that you have mysql, mysqli, and/or postgres7 support installed, and try again.</div>
<?php } ?>
	</td>
</tr>
<tr class="row1">
<td>Database host address</td>
<td><input type="text" name="host" value="<?php echo (isset($_POST['host'])?$_POST['host']:'localhost') ?>" size="20" maxlength="50" /></td>
</tr>
<tr class="row2">
<td>Database name</td>
<td><input type="text" name="database" value="<?php echo (isset($_POST['database'])?$_POST['database']:'cms') ?>" size="20" maxlength="50" /></td>
</tr>
<tr class="row1">
<td>Username</td>
<td><input type="text" name="username" value="<?php echo (isset($_POST['username'])?$_POST['username']:'cms_user') ?>" size="20" maxlength="50" /></td>
</tr>
<tr class="row2">
<td>Password</td>
<td><input type="password" name="password" value="<?php echo (isset($_POST['password'])?$_POST['password']:'cms_pass') ?>" size="20" maxlength="50" /></td>
</tr>
<tr class="row1">
<td>Table prefix</td>
<td><input type="text" name="prefix" value="<?php echo (isset($_POST['prefix'])?$_POST['prefix']:'cms_') ?>" size="20" maxlength="50" />
<input type="hidden" name="page" value="4" />
<input type="hidden" name="adminusername" value="<?php echo $adminusername ?>" />
<input type="hidden" name="adminemail" value="<?php echo $adminemail ?>" />
<input type="hidden" name="adminpassword" value="<?php echo $adminpassword ?>" />
<input type="hidden" name="adminpasswordagain" value="<?php echo $adminpassword ?>" />
<?php
if ($email_accountinfo) 
{
?>
<input type="hidden" name="email_accountinfo" value="<?php echo $_POST['adminemail'] ?>" />
<?php
}
?>
</td>
</tr>
<tr class="row2">
<td>Create Tables (Warning: Deletes existing data)</td>
<td><input type="checkbox" name="createtables" checked="true" /></td>
</tr>
<?php if (is_file(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'schemas' . DIRECTORY_SEPARATOR . 'extra.sql')) { ?>
<tr class="row1">
<td>Install sample content and templates</td>
<td><input type="checkbox" name="createextra" checked="true" /></td>
</tr>
<?php } ?>
</table>
<p align="center" class="continue"><!--<a onclick="document.page3form.submit()" href="#">Continue</a>-->
<?php if (! $valid_database) { ?>
<input type="submit" value="Retry" /></p>
<?php } else { ?>
<input type="submit" value="Continue" /></p>
<?php } ?>
<!--<p><input type="submit" value="Continue" /></p>-->
</form>
<?php

} ## showPageThree

function showPageFour($sqlloaded = 0) {

# Do check that database information has been entered
# Skip back to showPageThree() if necessary

        if ($_POST['dbms'] == '')
        {
                showPageThree('No dbms selected!');
                return;
        }


    ## don't load statements if they've already been loaded
    if ($sqlloaded == 0 && isset($_POST["createtables"])) {

        global $config, $CMS_SCHEMA_VERSION;

		$db = ADONewConnection($_POST['dbms'], 'pear:date:extend:transaction');

		$result = $db->Connect($_POST['host'],$_POST['username'],$_POST['password'],$_POST['database']);

		$db_prefix = $_POST['prefix'];
		#$db->debug = true;

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
		
		include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."schemas".DIRECTORY_SEPARATOR."schema.php");
		
		echo "<p>Importing sample data...";
		
		$handle = '';

		if (isset($_POST["createextra"]))
		{
			$handle = fopen(dirname(__FILE__).DIRECTORY_SEPARATOR."schemas".DIRECTORY_SEPARATOR."extra.sql", 'r');
		}
		else
		{
			$handle = fopen(dirname(__FILE__).DIRECTORY_SEPARATOR."schemas".DIRECTORY_SEPARATOR."initial.sql", 'r');
		}

		if ($handle) {
			while (!feof($handle)) {
				set_magic_quotes_runtime(false);
				$s = fgets($handle, 32768);
				if ($s != "") {
					$s = trim(str_replace("{DB_PREFIX}", $db_prefix, $s));
					$s = str_replace("\\r\\n", "\r\n", $s);
					$s = str_replace("\\'", "''", $s);
					$s = str_replace('\\"', '"', $s);
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
		$db->Execute($sql, array($_POST['adminusername'], md5($_POST['adminpassword']), $_POST['adminemail']));

		echo "[done]</p>";
		
		echo "<p>Setting sitename...";

		$query = "INSERT INTO ". $db_prefix ."siteprefs (sitepref_name, sitepref_value) VALUES (?,?)";
		$db->Execute($query, array('sitename', htmlentities($_POST['sitename'])));

		echo "[done]</p>";

		include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."schemas".DIRECTORY_SEPARATOR."createseq.php");

		$db->Close();
        echo '<p class="success">Success!</p>';

    } ## if

    $docroot = 'http://'.$_SERVER['HTTP_HOST'].substr($_SERVER['PHP_SELF'],0,strlen($_SERVER['PHP_SELF'])-20);
    $docpath = dirname(dirname(__FILE__)); 

	?>

    <p>Now let's continue to setup your configuration file, we already have most of the stuff we need. Chances are you can leave all these values alone, so when you are ready, click Continue.</p>
    <form action="install.php" method="post" name="page3form" id="page3form">
	<table cellpadding="2" border="1" class="regtable">
		<tr class="row1">
			<td>CMS Document root (as seen from the webserver)</td>
			<td><input type="text" name="docroot" value="<?php echo $docroot?>" size="50" maxlength="100" /></td>
		</tr>
		<tr class="row2">
			<td>Path to the Document root</td>
			<td><input type="text" name="docpath" value="<?php echo $docpath?>" size="50" maxlength="100" /></td>
		</tr>
		<tr class="row1">
			<td>Query string (leave this alone unless you have trouble, then edit config.php by hand)</td>
			<td>
				<input type="text" name="querystr" value="page" size="20" maxlength="20" />
				<input type="hidden" name="page" value="5" /><input type="hidden" name="host" value="<?php echo $_POST['host']?>" />
			   <input type="hidden" name="dbms" value="<?php echo $_POST['dbms']?>" />
			   <input type="hidden" name="database" value="<?php echo $_POST['database']?>" />
			   <input type="hidden" name="username" value="<?php echo $_POST['username']?>" />
				<input type="hidden" name="password" value="<?php echo $_POST['password']?>" />
			   <input type="hidden" name="prefix" value="<?php echo $_POST['prefix']?>" />
				<input type="hidden" name="bbcode" value="false" />
				<?php if (isset($_POST["createtables"])) { ?>
				<input type="hidden" name="createtables" value="true" />
				<?php } ?>
				<?php if (isset($_POST["email_accountinfo"])) { ?>
				<input type="hidden" name="email_accountinfo" value="<?php echo $_POST['email_accountinfo'] ?>" />
				<input type="hidden" name="adminusername" value="<?php echo $_POST['adminusername'] ?>" />
				<input type="hidden" name="adminpassword" value="<?php echo $_POST['adminpassword'] ?>" />
				<?php } ?>
			</td>
		</tr>
		<!--
		<tr class="row2">
			<td>Use BBCode (must have this installed, see <a href="INSTALL" target="_new">INSTALL</a></td>
			<td>
				<input type="text" name="bbcode" value="false" size="5" maxlength="5">
			</td>
		</tr>
		-->
    </table>
    <p align="center" class="continue"><!--<a onclick="document.page3form.submit()" href="#">Continue</a>--><input type="submit" value="Continue" /></p>
	</form>

	<?php
    
} ## showPageFour

function showPageFive() {

	global $gCms;

	/*
    if ($_POST['bbcode'] != 'false' and $_POST['bbcode'] != 'true') {
        echo "<p>BB Code needs to be either 'true' or 'false'</p>\n";
        showPageFour(1);
        exit;
    } ## if
	*/
	
	require_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR."config.functions.php");
	
	// check if db info is correct as it should at this point to prevent an undeleted installation dir
	// to be used for sending spam by messing up $_POST variables
	$db = ADONewConnection($_POST['dbms'], 'pear:date:extend:transaction');
	
    if (! $db->Connect($_POST['host'],$_POST['username'],$_POST['password'],$_POST['database']))
	{
		showPageThree('Could not connect to database.  Verify that username and password are correct, and that the user has access to the given database.');
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
	$newconfig['root_path'] = addslashes($_POST['docpath']);
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
    } else {
        echo "Error: Cannot write to $config.<br />\n";
        exit;
    } ## if

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

		include_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'include.php';
		
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
        $to      = $_POST['email_accountinfo'];
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
        echo '</p>';
    }
    echo "<h4 class=\"success\">Congratulations, you are all setup - here is your <a href=\"".$link."\">CMS site</a>.</h4>";

} ## showPageFour
?>

</div>
</div>

</body>
</html>
<?php

# vim:ts=4 sw=4 noet
?>
