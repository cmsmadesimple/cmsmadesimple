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
$CMS_ADMIN_PAGE = 1;

//
// note, much of this code is mysql specific
//

require_once("../include.php");
$urlext = '?' . CMS_SECURE_PARAM_NAME . '=' . $_SESSION[CMS_USER_KEY];
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


$gCms = cmsms();
$smarty = $gCms->GetSmarty();
$smarty->caching = false;
$smarty->force_compile = true;
$db = $gCms->GetDb();


$smarty->assign('theme', $themeObject);

/*
 *
 * Database
 *
 */


$query = "SHOW TABLES LIKE ?";
$tablestmp = $db->GetArray($query,array(cms_db_prefix().'%'));
$tables = array();
$nonseqtables = array();
foreach ($tablestmp as $table) {
  foreach ($table as $tabeinfo => $tablename) {
    $tables[] = $tablename;
    if (!stripos($tablename, "_seq")) {
      $nonseqtables[] = $tablename;
    }
  }
}

$smarty->assign("tablecount", count($tables));
$smarty->assign("nonseqcount", count($nonseqtables));


function MakeCommaList($tables)
{
  $out = "";
  foreach ($tables as $table) {
    if ($out != "") $out .= " ,";
    $out .= "`" . $table . "`";
  }
  return $out;
}

if (isset($_POST["optimizeall"])) {
  $query = "OPTIMIZE TABLE " . MakeCommaList($nonseqtables);
  $optimizearray = $db->GetArray($query);
  //print_r($optimizearray);
  $errorsfound = 0;
  $errordetails = "";
  foreach ($optimizearray as $check) {
    if (isset($check["Msg_text"]) && $check["Msg_text"] != "OK") {
      $errorsfound++;
      $errordetails .= "MySQL reports that table " . $check["Table"] . " does not checkout OK.<br>";
    }
  }

  // put mention into the admin log
  audit('', 'System Maintenance', 'All db-tables optimized');
  $themeObject->ShowMessage(lang("sysmain_tablesoptimized"));
}

if (isset($_POST["repairall"])) {
  $query = "REPAIR TABLE " . MakeCommaList($tables);
  $repairarray = $db->GetArray($query);
  $errorsfound = 0;
  $errordetails = "";
  foreach ($repairarray as $check) {
    if (isset($check["Msg_text"]) && $check["Msg_text"] != "OK") {
      $errorsfound++;
      $errordetails .= "MySQL reports that table " . $check["Table"] . " does not checkout OK.<br>";
    }
  }

  // put mention into the admin log
  audit('', 'System Maintenance', 'All db-tables repaired');
  $themeObject->ShowMessage(lang("sysmain_tablesrepaired"));

}


$smarty->assign("formurl", "systemmaintenance.php" . $urlext);


$query = "CHECK TABLE " . MakeCommaList($tables);
//echo $query;
$checkarray = $db->GetArray($query);
//print_r($checkarray);

$errortables = array();
foreach ($checkarray as $check) {
  if (isset($check["Msg_text"]) && $check["Msg_text"] != "OK") {
    $errortables[] = $check["Table"];
  }
}

$smarty->assign("errorcount", count($errortables));
if (count($errortables) > 0) {
  $smarty->assign("errortables", implode(",", $errortables));
}

/*
 *
 * Cache and content
 *
 */
$contentops = cmsms()->GetContentOperations();

if (isset($_POST['updateurls'])) {
  cms_route_manager::rebuild_static_routes();
  audit('', 'System maintenance', 'Static routes rebuilt');
  $themeObject->ShowMessage(lang("routesrebuilt"));
  $smarty->assign("active_content", "true");
}

if (isset($_POST['clearcache'])) {
  cmsms()->clear_cached_files(-1);
  // put mention into the admin log
  audit('', 'System maintenance', 'Cache cleared');
  $themeObject->ShowMessage(lang("cachecleared"));
  $smarty->assign("active_content", "true");
}

if (isset($_POST["updatehierarchy"])) {
  $contentops->SetAllHierarchyPositions();
  audit('', 'System maintenance', 'Page hierarchy positions updated');
  $themeObject->ShowMessage(lang("sysmain_hierarchyupdated"));
  $smarty->assign("active_content", "true");
}

//Setting up types
$contenttypes = $contentops->ListContentTypes(false, true);
//print_r($contenttypes);
$simpletypes = array();
foreach ($contenttypes as $typeid => $typename) {
  $simpletypes[] = $typeid;
}


if (isset($_POST["addaliases"])) {
  //$contentops->SetAllHierarchyPositions();
  $count = 0;
  $query = "SELECT * FROM " . cms_db_prefix() . "content";
  $allcontent = $db->Execute($query);
  while ($contentpiece = $allcontent->FetchRow()) {
    $content_id = $contentpiece["content_id"];
    if (trim($contentpiece["content_alias"]) == '' && $contentpiece['type'] != 'separator' ) {

      $alias = trim($contentpiece["menu_text"]);
      if ($alias == '') {
        $alias = trim($contentpiece["content_name"]);
      }

      $tolower = true;
      $alias = munge_string_to_url($alias, $tolower);
      if ($contentops->CheckAliasError($alias, $content_id)) {
        $alias_num_add = 2;
        // If a '-2' version of the alias already exists
        // Check the '-3' version etc.
        while ($contentops->CheckAliasError($alias . '-' . $alias_num_add) !== FALSE) {
          $alias_num_add++;
        }
        $alias .= '-' . $alias_num_add;
      }
      $query2 = "UPDATE " . cms_db_prefix() . "content SET content_alias=? WHERE content_id=?";
      $params2 = array($alias, $content_id);
      $dbresult = $db->Execute($query2, $params2);
      $count++;

    }
  }
  audit('', 'System maintenance', 'Fixed pages missing aliases, count:' . $count);
  $themeObject->ShowMessage($count . " " . lang("sysmain_aliasesfixed"));
  $smarty->assign("active_content", "true");
}


if (isset($_POST["fixtypes"])) {
  //$contentops->SetAllHierarchyPositions();

  $count = 0;
  $query = "SELECT * FROM " . cms_db_prefix() . "content";
  $allcontent = $db->Execute($query);
  while ($contentpiece = $allcontent->FetchRow()) {
    if (!in_array($contentpiece["type"], $simpletypes)) {
      $query2 = "UPDATE " . cms_db_prefix() . "content SET type='content' WHERE content_id=?";
      $params2 = array($contentpiece["content_id"]);
      $dbresult = $db->Execute($query2, $params2);
      $count++;
    }
  }

  audit('', 'System maintenance', 'Converted pages with invalid content types, count:' . $count);
  $themeObject->ShowMessage($count . " " . lang("sysmain_typesfixed"));
  $smarty->assign("active_content", "true");
}


$query = "SELECT * FROM " . cms_db_prefix() . "content";
$allcontent = $db->Execute($query);
$pages = array();
$withoutalias = array();
$invalidtypes = array();
if( is_object($allcontent) ) {
  while ($contentpiece = $allcontent->FetchRow()) {
    $pages[] = $contentpiece["content_name"];
    if (trim($contentpiece["content_alias"]) == "" && $contentpiece['type'] != 'separator') {
      $withoutalias[] = $contentpiece;
    }
    if (!in_array($contentpiece["type"], $simpletypes)) {
      $invalidtypes[] = $contentpiece;
    }
    //print_r($contentpiece);
  }
}
$smarty->assign_by_ref("pagesmissingalias", $withoutalias);
$smarty->assign_by_ref("pageswithinvalidtype", $invalidtypes);

$smarty->assign("pagecount", count($pages));
$smarty->assign("invalidtypescount", count($invalidtypes));
$smarty->assign("withoutaliascount", count($withoutalias));

/*
*
* Changelog
*
*/
$ch_filename = cms_join_path(CMS_BASE, 'doc', 'CHANGELOG.txt');
$changelog = file($ch_filename);

for ($i = 0; $i < count($changelog); $i++) {
  if (substr($changelog[$i], 0, 7) == "Version") {
      if ($i == 0) {
          $changelog[$i] = "<div class=\"version\"><h3>" . $changelog[$i] . "</h3>";
      } else {
          $changelog[$i] = "</div><div class=\"version\"><h3>" . $changelog[$i] . "</h3>";
      }
      
  }
}


$changelog = implode("<br/>", $changelog);

$smarty->assign("changelog", $changelog);
$smarty->assign("changelogfilename", $ch_filename);
$smarty->assign('backurl', $themeObject->BackUrl());

echo $smarty->fetch('systemmaintenance.tpl');


include_once("footer.php");

?>