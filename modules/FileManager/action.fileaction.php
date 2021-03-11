<?php
#---------------------------------------------------------------------------
# CMS Made Simple - Power for the professional, Simplicity for the end user.
# (c) 2004 - 2011 by Ted Kulp
# (c) 2011 - 2018 by the CMS Made Simple Development Team
# (c) 2018 and beyond by the CMS Made Simple Foundation
# This project's homepage is: https://www.cmsmadesimple.org
#---------------------------------------------------------------------------
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
# Or read it online: http://www.gnu.org/licenses/licenses.html#GPL
#---------------------------------------------------------------------------

if (!function_exists("cmsms")) exit;
if (!$this->CheckPermission("Modify Files") && !$this->AdvancedAccessAllowed()) exit;
if (!isset($params["path"])) $this->Redirect($id, 'defaultadmin');
if( !filemanager_utils::test_valid_path($params['path']) ) {
  $this->Redirect($id, 'defaultadmin',$returnid,array("fmerror"=>"fileoutsideuploads"));
}

$path=$params["path"];

$fileaction="";
if (isset($params["fileaction"])) $fileaction=$params["fileaction"];

$selfiles=array();
$seldirs=array();
$paramsnofiles=array();
//$somethingselected=false;
foreach ($params as $key=>$value) {
  if (substr($key,0,5)=="file_") {
    $selfiles[]=$this->decodefilename(substr($key,5));
  } elseif (substr($key,0,4)=="dir_") {
    $seldirs[]=$this->decodefilename(substr($key,4));
  } else {
    $paramsnofiles[$key]=$value;
  }
}

$selall=array_merge($seldirs,$selfiles);

// get the dirs from uploadspath
$dirlist=array();
$filerec = get_recursive_file_list($config['uploads_path'], array(), -1, 'DIRS');
//$dirlist[$this->Lang('selecttargetdir')] = '-';
foreach ($filerec as $key => $value) {
  $value1 = str_replace($config['root_path'], '', $value);
  //prevent current dir from showing up
  if ($value1 == ($path . "/")) continue;
  //Check for hidden files
  $dirs = explode("/", $value1);
  $hidden = false;
  foreach ($dirs as $dir) {
    if (substr($dir, 0, 1) == ".") {
      $hidden = true;
      break;
    }
  }
  if ($hidden) continue;

  //not hidden, add to list
  $dirlist[$this->Slashes($value1)] = $this->Slashes($value1);
}

if (isset($params["fileactionnewdir"]) || $fileaction=="newdir") {
  include_once(__DIR__."/action.newdir.php");
  return;
}

if (isset($params["fileactionrename"]) || $fileaction=="rename") {
  include_once(__DIR__."/action.rename.php");
  return;
}

if (isset($params["fileactiondelete"]) || $fileaction=="delete") {
  include_once(__DIR__."/action.delete.php");
  return;
}

if (isset($params["fileactioncopy"]) || $fileaction=="copy") {
  include_once(__DIR__."/action.copy.php");
  return;
}

if (isset($params["fileactionmove"]) || $fileaction=="move") {
  include_once(__DIR__."/action.move.php");
  return;
}

if (isset($params["fileactionunpack"]) || $fileaction=="unpack") {
  include_once(__DIR__."/action.unpack.php");
  return;
}

if (isset($params["fileactionthumb"]) || $fileaction=="thumb") {
  include_once(__DIR__."/action.thumb.php");
  return;
}

if (isset($params['fileactionresizecrop']) || $fileaction == 'resizecrop') {
  include_once(__DIR__.'/action.resizecrop.php');
  return;
}

if (isset($params['fileactionrotate']) || $fileaction == 'rotate') {
  include_once(__DIR__.'/action.rotate.php');
  return;
}

$this->Redirect($id,"defaultadmin",$returnid,array("path"=>$params["path"],"fmerror"=>"unknownfileaction"));

#
# EOF
#