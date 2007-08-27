<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t;  -*-
#CMS - CMS Made Simple
#(c)2004-2007 by Ted Kulp (ted@cmsmadesimple.org)
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
#$Id$

$CMS_ADMIN_PAGE=1;
$CMS_TOP_MENU='admin';
$CMS_ADMIN_TITLE='preferences';

require_once("../include.php");

function siteprefs_display_permissions($permsarr)
{
  $tmparr = array(lang('owner'),lang('group'),lang('other'));
  if( count($permsarr) != 3 ) return lang('permissions_parse_error');

  $result = array();
  for( $i = 0; $i < 3; $i++ )
    {
      $str = $tmparr[$i].': ';
      $str .= implode(',',$permsarr[$i]);
      $result[] = $str;
    }
  $str = implode('<br/>&nbsp;&nbsp;',$result);
  return $str;
}

check_login();
global $gCms;
$db =& $gCms->GetDb();

$smarty = cms_smarty();

$error = "";
$message = "";

$disablesafemodewarning = 0;
if (isset($_POST["disablesafemodewarning"])) $disablesafemodewarning = 1;

$allowparamcheckwarnings = 0;
if (isset($_POST["allowparamcheckwarnings"])) 
  {
    $allowparamcheckwarnings = 1;
  }

$enablecustom404 = "0";
if (isset($_POST["enablecustom404"])) $enablecustom404 = "1";

$xmlmodulerepository = "";
if (isset($_POST["xmlmodulerepository"])) $xmlmodulerepository = $_POST["xmlmodulerepository"];

$custom404 = "<p>Page not found<//p>";
if (isset($_POST["custom404"])) $custom404 = $_POST["custom404"];

$custom404template = "-1";
if (isset($_POST["custom404template"])) $custom404template = $_POST["custom404template"];

$enablesitedownmessage = "0";
if (isset($_POST["enablesitedownmessage"])) $enablesitedownmessage = "1";

$sitedownmessage = "<p>Site is current down.  Check back later.</p>";
if (isset($_POST["sitedownmessage"])) $sitedownmessage = $_POST["sitedownmessage"];

$defaultpagecontent = '';
if (isset($_POST['defaultpagecontent'])) $defaultpagecontent = $_POST['defaultpagecontent'];

$sitedownmessagetemplate = "-1";
if (isset($_POST["sitedownmessagetemplate"])) $sitedownmessagetemplate = $_POST["sitedownmessagetemplate"];

$metadata = '';
if (isset($_POST['metadata'])) $metadata = $_POST['metadata'];

if (isset($_POST['parent_id'])) $default_parent_page = $_POST['parent_id'];

$sitename = '';
if (isset($_POST['sitename'])) $sitename = $_POST['sitename'];

#$useadvancedcss = "1";
#if (isset($_POST["useadvancedcss"])) $useadvancedcss = $_POST["useadvancedcss"];

$frontendlang = '';
if (isset($_POST['frontendlang'])) $frontendlang = $_POST['frontendlang'];

$global_umask = '022';
if (isset($_POST['global_umask'])) 
  {
    $global_umask = $_POST['global_umask'];
  }

// ADDED
$logintheme = "default";
if (isset($_POST["logintheme"])) $logintheme = $_POST["logintheme"];
// STOP


$userid = get_userid();
$access = check_permission($userid, 'Modify Site Preferences');

$use_javasyntax = false;
if (get_preference($userid, 'use_javasyntax') == "1") $use_javasyntax = true;

if (isset($_POST["cancel"])) {
	redirect("index.php");
	return;
}

$testresults = lang('untested');
if (isset($_POST["testumask"]))
{

    $testdir = $gCms->config['root_path'].DIRECTORY_SEPARATOR.'tmp';
    $testfile = $testdir.DIRECTORY_SEPARATOR.'dummy.tst';
    
    if( !is_writable($testdir) )
      {
	$testresults = lang('errordirectorynotwritable');

      }
    else
      {

	@umask(octdec($global_umask));

	$fh = @fopen($testfile,"w");

	if( !$fh )
	  {
	    $testresults = lang('errorcantcreatefile').' ('.$testfile.')';
	  }
	else
	  {
	    @fclose($fh);
	    $filestat = stat($testfile);

	    if( $filestat == FALSE )
	      {
		$testresults = lang('errorcantcreatefile');
	      }
	      
		  if(function_exists("posix_getpwuid")) //function posix_getpwuid not available on WAMP systems
			{
	      $userinfo = @posix_getpwuid($filestat[4]);

  	    $username = isset($userinfo['name'])?$userinfo['name']:lang('unknown');
	      $permsstr = siteprefs_display_permissions(interpret_permissions($filestat[2]));
        $testresults = sprintf("%s: %s<br/>%s:<br/>&nbsp;&nbsp;%s",
				   lang('owner'),$username,
				   lang('permissions'),$permsstr);
	      
	    
      } else {
        $testresults = sprintf("%s: %s<br/>%s:<br/>&nbsp;&nbsp;%s",
				   lang('owner'),"N/A",
				   lang('permissions'),"N/A");

      }
	    @unlink($testfile);
	  }
	
      }
  }

if (isset($_POST['clearcache']))
{
	CmsContentOperations::clear_cache();
	CmsCache::clear();
	$message .= lang('cachecleared');
}
else if (isset($_POST["editsiteprefs"]))
{
	if ($access)
	{
		CmsApplication::set_preference('global_umask', $global_umask);
		CmsApplication::set_preference('frontendlang', $frontendlang);
		CmsApplication::set_preference('enablecustom404', $enablecustom404);
		CmsApplication::set_preference('xmlmodulerepository', $xmlmodulerepository);
		CmsApplication::set_preference('custom404', $custom404);
		CmsApplication::set_preference('custom404template', $custom404template);
		CmsApplication::set_preference('enablesitedownmessage', $enablesitedownmessage);
		CmsApplication::set_preference('sitedownmessage', $sitedownmessage);
		#CmsApplication::set_preference('sitedownmessagetemplate', $sitedownmessagetemplate);
		#CmsApplication::set_preference('useadvancedcss', $useadvancedcss);
		CmsApplication::set_preference('logintheme', $logintheme);
		CmsApplication::set_preference('metadata', $metadata);
		CmsApplication::set_preference('sitename', $sitename);
		audit(-1, '', 'Edited Site Preferences');
		//redirect("siteprefs.php");
		//return;
		$message .= lang('prefsupdated');
	}
	else
	{
		$error .= "<li>".lang('noaccessto', array('Modify Site Permissions'))."</li>";
	}
}
else if (!isset($_POST["submit"]))
{
	$global_umask = CmsApplication::get_preference('global_umask',$global_umask);
	
	$frontendlang = CmsApplication::get_preference('frontendlang');
	$enablecustom404 = CmsApplication::get_preference('enablecustom404');
	$custom404 = CmsApplication::get_preference('custom404');
	$custom404template = CmsApplication::get_preference('custom404template');
	$enablesitedownmessage = CmsApplication::get_preference('enablesitedownmessage');
	$sitedownmessage = CmsApplication::get_preference('sitedownmessage');
	$xmlmodulerepository = CmsApplication::get_preference('xmlmodulerepository');
	#$sitedownmessagetemplate = get_preference('sitedownmessagetemplate');
	#$useadvancedcss = get_preference('useadvancedcss');
	$logintheme = CmsApplication::get_preference('logintheme', 'default');
	$metadata = CmsApplication::get_preference('metadata', '');
	$sitename = CmsApplication::get_preference('sitename', 'CMSMS Site');
}

$smarty->assign('global_umask', $global_umask);
$smarty->assign('frontendlang', $frontendlang);
$smarty->assign('enablecustom404', $enablecustom404);
$smarty->assign('custom404', $custom404);
$smarty->assign('custom404template', $custom404template);
$smarty->assign('enablesitedownmessage', $enablesitedownmessage);
$smarty->assign('sitedownmessage', $sitedownmessage);
$smarty->assign('xmlmodulerepository', $xmlmodulerepository);
$smarty->assign('logintheme', $logintheme);
$smarty->assign('metadata', $metadata);
$smarty->assign('sitename', $sitename);

$templates = array();
$templates['-1'] = 'None';

$result = cmsms()->template->find_all(array('order' => 'template_name ASC'));

foreach ($result as &$onetemplate)
{
	$templates[$onetemplate['id']] = $onetemplate['name'];
}

$smarty->assign('templates', $templates);

include_once("header.php");

if ($error != "") {
	echo "<div class=\"pageerrorcontainer\"><ul class=\"error\">".$error."</ul></div>";	
}
if ($message != "") {
	echo $themeObject->ShowMessage($message);
}

// Make sure cache folder is writable
if (FALSE == is_writable(cms_join_path(CmsConfig::get('root_path'),'tmp','cache')))
{
	echo $themeObject->ShowErrors(lang('cachenotwritable'));
}

/*
?>

<div class="pagecontainer">
	<?php echo $themeObject->ShowHeader('siteprefs'); ?>
	<form id="siteprefform" method="post" action="siteprefs.php">
	<?php if ($access) { ?>
	<div class="pageoverflow">
		<p class="pagetext">&nbsp;</p>
		<p class="pageinput">
			<input type="hidden" name="editsiteprefs" value="true" />
			<input type="submit" name="submit" value="<?php echo lang('submit')?>" class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" />
			<input type="submit" name="cancel" value="<?php echo lang('cancel')?>" class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" />
		</p>
	</div>
	<?php } ?>
		<div class="pageoverflow">
			<p class="pagetext"><?php echo lang('clearcache') ?>:</p>
			<p class="pageinput">
				<input class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" type="submit" name="clearcache" value="<?php echo lang('clear') ?>" />
			</p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext"><?php echo lang('sitename')?>:</p>
			<p class="pageinput"><input type="text" class="pagesmalltextarea" name="sitename" size="30" value="<?php echo $sitename?>" /></p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext"><?php echo lang('global_umask')?>:</p>
			<p class="pageinput"><input type="text" class="pagesmalltextarea" name="global_umask" size="4" value="<?php echo $global_umask?>" /></p>
		</div>
		<div class="pageoverflow">
  <p class="pagetext">&nbsp;</p>
  <p class="pageinput"><input type="submit" name="testumask" value="<?php echo lang('test')?>" class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" /></p>
		</div>
  <div class="pageoverflow">
  <p class="pagetext"><?php echo lang('results')?></p>
  <p class="pageinput"><strong><?php echo $testresults ?></strong></p>
		</div>
		<div class="pageoverflow">
                   <p class="pagetext"><?php echo lang('frontendlang')?>:</p>
	              <select name="frontendlang" style="vertical-align: middle;">
                      <option value=""><?php echo lang('nodefault'); ?></option>
		      <?php
		        asort($nls["language"]);
                        foreach ($nls["language"] as $key=>$val) {
			  echo "<option value=\"$key\"";
			  if ($frontendlang == $key) {
			    echo " selected=\"selected\"";
			  }
			  echo ">$val";
			  if (isset($nls["englishlang"][$key]))
			    {
			      echo " (".$nls["englishlang"][$key].")";
			    }
			  echo "</option>\n";
			}
                      ?>
		      </select>
		      <br />
		</div>
		<div class="pageoverflow">
			<p class="pagetext"><?php echo lang('globalmetadata')?>:</p>
			<p class="pageinput"><textarea class="pagesmalltextarea" name="metadata" cols="" rows=""><?php echo $metadata?></textarea></p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext"><?php echo lang('enablecustom404') ?>:</p>
			<p class="pageinput"><input class="pagenb" type="checkbox" name="enablecustom404" <?php if ($enablecustom404 == "1") echo "checked=\"checked\""?> /></p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext"><?php echo lang('custom404')?>:</p>
			<p class="pageinput"><textarea class="pagesmalltextarea" name="custom404" cols="" rows=""><?php echo $custom404?></textarea></p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext"><?php echo lang('template')?>:</p>
			<p class="pageinput">
				<select name="custom404template">
				<?php
					foreach ($templates as $key=>$value)
					{
						echo "<option value=\"".$key."\"";
						if ($key == $custom404template)
						{
							echo " selected=\"selected\"";
						}
						echo ">".$value."</option>";
					}
				?>
				</select>
			</p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext"><?php echo lang('enablesitedown')?>:</p>
			<p class="pageinput"><input class="pagenb" type="checkbox" name="enablesitedownmessage" <?php if ($enablesitedownmessage == "1") echo "checked=\"checked\""?> /></p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext"><?php echo lang('sitedownmessage')?>:</p>
			<p class="pageinput"><textarea class="pagesmalltextarea" name="sitedownmessage" cols="" rows=""><?php echo $sitedownmessage?></textarea></p>
		</div>
		<!--
		<div class="pageoverflow">
			<p class="pagetext"><?php echo lang('xmlmodulerepository') ?>:</p>
			<p class="pageinput"><input class="pagenb" type="text" name="xmlmodulerepository" size="80" maxlength="255" value="<?php echo $xmlmodulerepository; ?>"/></p>
		</div>
		-->
		<!--
		<div class="pageoverflow">
			<p class="pagetext"><?php echo lang('template')?>:</p>
			<p class="pageinput">
			<select>
			<?php
				foreach ($templates as $key=>$value)
				{
					echo "<option value=\"".$key."\"";
					if ($key == $sitedownmessagetemplate)
					{
						echo " selected=\"selected\"";
					}
					echo ">".$value."</option>";
				}
			?>
			</select>
			</p>
		</div>
		-->
  <?php
	if ($dir=opendir(dirname(__FILE__)."/themes/")) { //Does the themedir exist at all, it should...
	?>
	<div class="pageoverflow">
		<p class="pagetext"><?php echo lang('admintheme') ?>:</p>
		<p class="pageinput">
		<select name="logintheme">
			<?php
			  while (($file = readdir($dir)) !== false) {
				  	if (@is_dir("themes/".$file) && ($file[0]!='.')) {
			?>
		  		<option value="<?php echo $file?>"<?php echo (get_site_preference('logintheme', 'default')==$file?" selected=\"selected\"":"")?>><?php echo $file?></option>				  
				  <?php
		  	}
		  }
				?>				
			</select>
		</p>
	</div>
	<?php }?>
	
		<div class="pageoverflow">
			<p class="pagetext"><?php echo lang('defaultpagecontent')?>:</p>
			<p class="pageinput"><textarea class="pagesmalltextarea" name="defaultpagecontent" cols="" rows=""><?php echo $defaultpagecontent?></textarea></p>
		</div>	
		<div class="pageoverflow">
			<p class="pagetext"><?php echo lang('defaultparentpage')?>:</p>
			<p class="pageinput">
			<?php
			$contentops =& $gCms->GetContentOperations();
			echo $contentops->CreateHierarchyDropdown(0, $default_parent_page);
			?>
			</p>
		</div>	
                <div class="pageoverflow">
			<p class="pagetext"><?php echo lang('disablesafemodewarning')?>:</p>
			<p class="pageinput"><input class="pagenb" type="checkbox" name="disablesafemodewarning" <?php if($disablesafemodewarning) echo "checked=\"checked\""?> /></p>
                </div>
                <div class="pageoverflow">
			<p class="pagetext"><?php echo lang('allowparamcheckwarnings')?>:</p>
			<p class="pageinput"><input class="pagenb" type="checkbox" name="allowparamcheckwarnings" <?php if($allowparamcheckwarnings) echo "checked=\"checked\""?> /></p>
                </div>
	<?php if ($access) { ?>
	<div class="pageoverflow">
		<p class="pagetext">&nbsp;</p>
		<p class="pageinput">
			<input type="hidden" name="editsiteprefs" value="true" />
			<input type="submit" name="submit" value="<?php echo lang('submit')?>" class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" />
			<input type="submit" name="cancel" value="<?php echo lang('cancel')?>" class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" />
		</p>
	</div>
	<?php } ?>
	</form>
</div>

<?php
*/

$smarty->display('siteprefs.tpl');
echo '<p class="pageback"><a class="pageback" href="'.$themeObject->BackUrl().'">&#171; '.lang('back').'</a></p>';
include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
