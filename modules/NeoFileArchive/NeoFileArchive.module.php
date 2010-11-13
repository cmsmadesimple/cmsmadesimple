<?php
#A module for CMS Made Simple
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


$neomodule=dirname(dirname(__FILE__))."/NeoModule/NeoModule.module.php";
if (file_exists($neomodule)) {
  require_once($neomodule);
} else {  
  echo "<div style='background-color:red; font-weight: bold'>Warning, NeoLinker-module depends on the NeoModule-module which has to be installed prior to this</div>";
  return;
}

class NeoFileArchive extends NeoModule {
  var $headbodyseparator= "{* HEAD/BODY DIVIDER *}";
  function GetName() {
    return 'NeoFileArchive';
  }

  function GetFriendlyName() {
    return $this->Lang("friendlyname");
  }

  function IsPluginModule() {
    return true;
  }

  function GetVersion() {
    return '0.1.0';
  }

  function GetDependencies() {
    //'Thumbnails'=>'0.1.1','
    return array('NeoDataStorage'=>'0.3.1', "NeoModule"=>"0.2.6");
  }

  function MinimumCMSVersion() {
    return '1.8';
  }

  function GetAdminDescription() {
    return $this->Lang('description');
  }

  function HasAdmin() {
    return true;
  }

  function GetAdminSection() {
    return 'content';
  }

  function InstallPostMessage() {
    return $this->Lang('postinstall');
  }

  function UninstallPostMessage() {
    return $this->Lang('postuninstall');
  }

  function VisibleToAdminUser() {
    return (
      $this->CheckPermission('Modify Site Preferences')
      || $this->CheckPermission('Modify Templates')
      );
  }

  function GetHelp($lang='en_US') {
    return $this->Lang('help');
  }

  function GetAuthor() {
    return 'Morten Poulsen';
  }

  function GetAuthorEmail() {
    return 'morten@poulsen.org';
  }

  function GetChangeLog() {
    return $this->ProcessTemplate("changelog.tpl");
  }

  function SetParameters() {
//	  $this->RestrictUnknownParams();
		$this->RegisterModulePlugin();

		$this->CreateParameter('labels', '', $this->lang('helplabels'));
		$this->SetParameterType('labels',CLEAN_STRING);

    $this->CreateParameter('template', '', $this->lang('helptemplate'));
		$this->SetParameterType('template',CLEAN_STRING);

    /*$this->CreateParameter('noticemail', '1', $this->lang('helpnoticemail'));
		$this->SetParameterType('noticemail',CLEAN_STRING);
*/
  /*  $this->CreateParameter('message');
		$this->SetParameterType('message',CLEAN_STRING);
*/
    //$this->RegisterRoute('/NeoWiki\/(?P<entryid>.*?)$/', array('action'=>'default'));

	}

  function GetDefaultTemplate($filename) {
    return file_get_contents($this->GetModulePath()."/templates/".$filename);
  }

  function OutputHeaderInfo() {
     global $config;
     $template=$this->GetPreference('linktemplate');
     $template=$this->GetHeaderPart($template);
     $this->smarty->assign('modulepath', $config["root_url"]."/modules/NeoLinker"/*$this->GetModuleURLPath()*/);
     //$this->smarty->assign('includejquery', $this->GetPreference("includejquery",1));
     echo $this->ProcessTemplateFromData($template);
  }

  function GetHeaderPart($template) {
    $pos= stripos($template, $this->headbodyseparator);
    if ($pos===false) return $template;
    return substr($template,0,$pos);
  }

  function GetBodyPart($template) {
    //echo $template;
    $pos = stripos($template, $this->headbodyseparator);
    //echo $pos; echo $this->headbodyseparator; die();
    if ($pos===false) return $template;

    return substr($template,$pos+strlen($this->headbodyseparator));
  }

  function GetImageList() {
    //echo $this->Pref("imagelocation");
    global $config;
    $dir=@opendir($config["root_path"]."/".$this->Pref("imagelocation","uploads/images/links"));
    if (!$dir) return array("invalid imagelocation"=>"");
    $images=array();
    while ($file=  readdir($dir)) {
      if ($file[0]==".") continue;
      if (stripos($file, "thumb_")!==false) continue;
      $images[$file]=$file;
    }
    return $images;

  }




}

?>
