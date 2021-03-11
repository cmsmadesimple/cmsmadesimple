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

if (isset($params["cancel"])) {
  $this->Redirect($id,"defaultadmin",$returnid,$params);
}
$selall = $params['selall'];
if( !is_array($selall) ) {
  $selall = unserialize($selall);
}
if (count($selall)==0) {
  $params["fmerror"]="nofilesselected";
  $this->Redirect($id,"defaultadmin",$returnid,$params);
}
if (count($selall)>1) {
  $params["fmerror"]="morethanonefiledirselected";
  $this->Redirect($id,"defaultadmin",$returnid,$params);
}


$config=cmsms()->GetConfig();
$filename=$this->decodefilename($selall[0]);
$src = filemanager_utils::join_path($config['root_path'],filemanager_utils::get_cwd(),$filename);
if( !file_exists($src) ) {
  $params["fmerror"]="filenotfound";
  $this->Redirect($id,"defaultadmin",$returnid,$params);
}

include_once(dirname(__FILE__).'/easyarchives/EasyArchive.class.php');
$archive = new EasyArchive;
$destdir = filemanager_utils::join_path($config['root_path'],filemanager_utils::get_cwd());
if( !endswith($destdir,'/') ) $destdir .= '/';
$res = $archive->extract($src,$destdir);

$paramsnofiles["fmmessage"]="unpacksuccess"; //strips the file data
$this->Audit('',"File Manager", "Unpacked file: ".$src);
$this->Redirect($id,"defaultadmin",$returnid,$paramsnofiles);

#
# EOF
#