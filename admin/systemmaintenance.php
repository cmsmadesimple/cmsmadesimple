<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
#This project's homepage is: http://www.cmsmadesimple.org
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
#$Id: supportinfo.php 4216 2007-10-06 19:28:55Z wishy $








$CMS_ADMIN_PAGE=1;

require_once("../include.php");
$urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];
check_login();


$userid = get_userid();
$access = check_permission($userid, "Modify Site Preferences");
if (!$access) {
  die('Permission Denied');
  return;
}

include_once("header.php");

define('CMS_BASE', dirname(dirname(__FILE__)));
require_once cms_join_path(CMS_BASE, 'lib', 'test.functions.php');

function sitemaintenance_lang($params,&$smarty)
{
  if( count($params) )
  {
    $tmp = array();
    foreach( $params as $k=>$v)
    {
      $tmp[] = $v;
    }

    $str = $tmp[0];
    $tmp2 = array();
    for( $i = 1; $i < count($tmp); $i++ )
      $tmp2[] = $params[$i];
    return lang($str,$tmp2);
  }
}

$gCms = cmsms();
$smarty = $gCms->GetSmarty();
$smarty->register_function('sm_lang','sitemaintenance_lang');
$smarty->caching = false;
$smarty->force_compile = true;
$db = $gCms->GetDb();

# give everything to smarty
{
  // warning: uber hack.
  $tmp = ModuleOperations::get_instance()->GetInstalledModules();
  for( $i = 0; $i < count($tmp); $i++ )
  {
    if( !ModuleOperations::get_instance()->IsSystemModule($tmp[$i]) ) continue;
    $mod = cms_utils::get_module($tmp[$i]);
    if( is_object($mod) ) break;
  }
  $smarty->assign('mod',$mod);
}

/*
 *
 * Database
 *
 */


$query = "SHOW TABLES";
$tablestmp=$db->GetArray($query);
$tables=array();
$nonseqtables=array();
foreach($tablestmp as $table) {
  foreach($table as $tabeinfo=>$tablename) {
    $tables[]=$tablename;
    if (!stripos($tablename,"_seq")) {
      $nonseqtables[]=$tablename;
    }
  }
}

$smarty->assign("tablecount",count($tables));
$smarty->assign("nonseqcount",count($nonseqtables));

//echo count($tables). " tables found (".count($nonseqtables)." non-seq tables)<br><br>";

function MakeCommaList($tables) {
  $out="";
  foreach($tables as $table) {
    if ($out!="") $out.=" ,";
    $out.="`".$table."`";
  }
  return $out;
}

if (isset($_POST["optimizeall"])) {
  $query = "OPTIMIZE TABLE ".MakeCommaList($nonseqtables);
  $optimizearray=$db->GetArray($query);
  //print_r($optimizearray);
  $errorsfound=0;
  $errordetails="";
  foreach ($optimizearray as $check) {
    if (isset($check["Msg_text"]) && $check["Msg_text"]!="OK") {
      $errorsfound++;
      $errordetails.="MySQL reports that table ".$check["Table"]." does not checkout OK.<br>";
    }
  }
  //echo $errorsfound." errors found in tables: ".$errordetails."<br>";
  // put mention into the admin log
  audit(-1,'System Maintenance', 'All db-tables optimized');
  $themeObject->ShowMessage(lang("sysmain_tablesoptimized"));
}

if (isset($_POST["repairall"])) {
  $query = "REPAIR TABLE ".MakeCommaList($tables);
  $repairarray=$db->GetArray($query);
  $errorsfound=0;
  $errordetails="";
  foreach ($repairarray as $check) {
    if (isset($check["Msg_text"]) && $check["Msg_text"]!="OK") {
      $errorsfound++;
      $errordetails.="MySQL reports that table ".$check["Table"]." does not checkout OK.<br>";
    }
  }
  //echo $errorsfound." errors found in tables: ".$errordetails."<br>";
  // put mention into the admin log
  audit(-1,'System Maintenance', 'All db-tables repaired');
  $themeObject->ShowMessage(lang("sysmain_tablesrepaired"));
  //$themeObject->ShowErrors("All tables repairs");
}


$smarty->assign("formurl","systemmaintenance.php".$urlext);


$query = "CHECK TABLE ".MakeCommaList($tables);
//echo $query;
$checkarray=$db->GetArray($query);
//print_r($checkarray);

$errortables=array();
foreach ($checkarray as $check) {
  if (isset($check["Msg_text"]) && $check["Msg_text"]!="OK") {
    $errortables[]=$check["Table"];
  }
}

$smarty->assign("errorcount",count($errortables));
if (count($errortables)>0) {
  $smarty->assign("errortables",implode(",",$errortables));
}


//echo $errorsfound." errors found in tables: ".$errordetails."<br>";

/*echo '<a href="systemmaintenance.php'.$urlext.'&amp;optimizeall=1">Optimize all non-seq tables</a>';
echo '<br>';
echo '<a href="systemmaintenance.php'.$urlext.'&amp;repairall=1">Optimize all tables</a>';
echo '<br>';
*/
/*
 *
 * Cache and content
 *
 */
$contentops = cmsms()->GetContentOperations();

if (isset($_POST['clearcache'])) {
  cmsms()->clear_cached_files(-1);
  // put mention into the admin log
  audit(-1,'System maintenance', 'Cache cleared');
  $themeObject->ShowMessage(lang("cachecleared"));
  $smarty->assign("active_content","true");
}

if (isset($_POST["updatehierarchy"])) {
  $contentops->SetAllHierarchyPositions();
  audit(-1,'System maintenance', 'Page hierarchy positions updated');
  $themeObject->ShowMessage(lang("sysmain_hierarchyupdated"));
  $smarty->assign("active_content","true");
}



$all = $contentops->GetAllContent(false);
$pages=array();
$withoutalias=array();
foreach ($all as $thisitem) {
  if( is_object($thisitem) && $thisitem instanceof ContentBase ) {
 	  $pages[]=$thisitem->Name();
    if (trim($thisitem->Alias())=="") {
      $withoutalias[]=$thisitem->Name();
    }
   // echo $thisitem->Type();
  }
}

$smarty->assign("pagecount",count($pages));

$contenttypes = $contentops->ListContentTypes(false,true);
//print_r($contenttypes);
$simpletypes=array();
foreach ($contenttypes as $typeid=>$typename) {
  $simpletypes[]=$typeid;
}
$invalidtypes=0;
foreach ($all as $thisitem) {
  if( is_object($thisitem) && $thisitem instanceof ContentBase ) {
    if (!in_array($thisitem->Type(),$simpletypes)) {
      $invalidtypes++;
    }
  }
}

$smarty->assign("invalidtypes",$invalidtypes);

/*echo "<br><br>";
echo count($pages). " contentpages found, ";*/
//echo count($withoutalias)." of which did not have an alias";



/*
 *
 * Changelog
 *
 */
$ch_filename= cms_join_path(CMS_BASE, 'doc', 'CHANGELOG.txt');
$changelog=file($ch_filename);

for($i=0; $i<count($changelog); $i++) {
  if (substr($changelog[$i],0,7)=="Version") {
    $changelog[$i]="<strong>".$changelog[$i]."</strong>";
  }
}


$changelog=implode("<br/>",$changelog);

$smarty->assign("changelog",$changelog);
$smarty->assign("changelogfilename",$ch_filename);
$smarty->assign('backurl', $themeObject->BackUrl());

echo $smarty->fetch('systemmaintenance.tpl');


include_once("footer.php");


function deletecontent($contentid)
{
	$userid = get_userid();
	$mypages = author_pages($userid);

	$access = (check_permission($userid, 'Remove Pages') &&
	  (check_ownership($userid,$contentid) ||
	   quick_check_authorship($contentid,$mypages)))
	  || check_permission($userid, 'Manage All Content');

	$gCms = cmsms();
	$hierManager = $gCms->GetHierarchyManager();

	if ($access)
	{
		$node = $hierManager->getNodeById($contentid);
		if ($node)
		{
		        $contentobj = $node->getContent(true,false,true);
			$childcount = 0;
			$parentid = -1;
			$parent = $node->getParent();
			if ($parent)
			{
			  $parentContent = $parent->getContent(true,false,true);
			  if (is_object($parentContent))
			    {
			      $parentid = $parentContent->Id();
			      $childcount = $parent->getChildrenCount();
			    }
			}

			if ($contentobj)
			{
				$title = $contentobj->Name();

				#Check for children
				if ($contentobj->HasChildren())
				{
					$_GET['error'] = 'errorchildcontent';
				}

				#Check for default
				if ($contentobj->DefaultContent())
				{
					$_GET['error'] = 'errordefaultpage';
				}

				$title = $contentobj->Name();
				$contentobj->Delete();

				$contentops = $gCms->GetContentOperations();
				$contentops->SetAllHierarchyPositions();

				#See if this is the last child... if so, remove
				#the expand for it
				if ($childcount == 1 && $parentid > -1)
				{
					toggleexpand($parentid, true);
				}

				#Do the same with this page as well
				toggleexpand($contentid, true);

				// put mention into the admin log
				audit($contentid, 'Content Item: '.$title, 'Deleted');

				$contentops->ClearCache();

				$_GET['message'] = 'contentdeleted';
			}
		}
	}
}


# vim:ts=4 sw=4 noet
?>