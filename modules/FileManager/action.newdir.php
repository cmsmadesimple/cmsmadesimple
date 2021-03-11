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
if (isset($params["cancel"])) $this->Redirect($id, "defaultadmin", $returnid, $params);

$path = filemanager_utils::get_cwd();

$newdirname = "";
if (isset($params["newdirname"])) {
  $newdirname = trim($params["newdirname"]);

  if (!filemanager_utils::is_valid_filename($newdirname) ) {
    // $this->Redirect($id, 'defaultadmin',$returnid,array("fmerror"=>"invalidnewdir"));
    echo $this->ShowErrors($this->Lang("invalidnewdir"));
    //fallthrough
  } else {

    $config = cmsms()->GetConfig();
    $base = $config['root_path'];
    $newdir = $this->Slash($params["path"], $newdirname);
    $newdir = $this->Slash($base, $newdir);

    if (is_dir($newdir)) {
      echo $this->ShowErrors($this->Lang("direxists"));
      //fallthrough
    } else {
      if (mkdir($newdir)) {
        $params["fmmessage"] = "newdirsuccess"; //strips the file data
        $this->Audit(0, "File Manager", "Created new directory: " . $newdirname);
        $this->Redirect($id, "defaultadmin", $returnid, $params);
      } else {
        $params["fmerror"] = "newdirfail";
        $this->Redirect($id, "defaultadmin", $returnid, $params);
      }
    }
  }
}

$smarty->assign('startform', $this->CreateFormStart($id, 'fileaction', $returnid, "post", "", false, "", $params));
$smarty->assign('newdirtext', $this->lang("newdir"));
$smarty->assign('newdirname',$newdirname);
$smarty->assign('endform', $this->CreateFormEnd());
$smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', $this->Lang('create')));
$smarty->assign('cancel', $this->CreateInputSubmit($id, 'cancel', $this->Lang('cancel')));
echo $this->ProcessTemplate('newdir.tpl');

#
# EOF
#