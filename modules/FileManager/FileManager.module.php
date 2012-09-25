<?php
# FileManager. A plugin for CMS - CMS Made Simple
# Copyright (c) 2006-12 by Morten Poulsen <morten@poulsen.org>
#
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
include_once(dirname(__FILE__)."/fileinfo.php");

class FileManager extends CMSModule {

  function LazyLoadFrontend() { return TRUE; }

  function InitializeAdmin() {
    //$this->RegisterModulePlugin();
  }

  function AccessAllowed() {
    //return ($this->CheckPermission("Modify Files") || $this->CheckPermission('Modify Site Preferences'));
	return $this->CheckPermission("Modify Files");
  }

  public function AdvancedAccessAllowed() {
    return $this->CheckPermission('Use FileManager Advanced',0);
  }

  function GetName() {
    return 'FileManager';
  }

  function GetChangeLog()	{
    return $this->ProcessTemplate('changelog.tpl');
  }
	
  function GetFriendlyName() {
    return $this->Lang('friendlyname');
  }
  
  function GetVersion() {
    return '1.4.1';
  }

  function GetHeaderHTML()
  {
    return $this->_output_header_javascript();
  }

	function GetHelp() {
		return $this->Lang('help');
	}

	function GetAuthor() {
		return 'Morten Poulsen (Silmarillion)';
	}

	function GetAuthorEmail()	{
		return 'morten@poulsen.org';
	}

	function IsPluginModule() {
		return true;
	}

	function HasAdmin() {
		return true;
	}

	function IsAdminOnly() {
	  return false;
	}

	function GetAdminSection() {
		return 'content';
	}

	function GetAdminDescription() {
		return $this->Lang('moddescription');
	}

	function MinimumCMSVersion() {
		return "1.11=alpha0";
	}

	function VisibleToAdminUser() {
		//return ($this->AccessAllowed() || $this->AdvancedAccessAllowed());
		return $this->AccessAllowed();
	}
	
	function InstallPostMessage() {
		return $this->Lang('postinstall');
	}

	function UninstallPostMessage()	{
		return $this->Lang('uninstalled');
	}

	function UninstallPreMessage() {
		return $this->Lang('really_uninstall');
	}

	function GetEventDescription($name)
	{
	  return $this->Lang('eventdesc_'.$name);
	}

	function GetEventHelp($name)
	{
	  return $this->Lang('eventhelp_'.$name);
	}

	function GetActionIcon($action) {
	  $config = cmsms()->GetConfig();
	  return "<img style='border:0;' src='".$config["root_url"]."/modules/FileManager/icons/themes/default/actions/".strtolower($action).".gif' ".
	    "alt='".$this->Lang($action)."' />";
	}

	function GetFileIcon($extension,$isdir=false) {
	  if (empty($extension)) $extension = '---'; // hardcode extension to something.
	  if ($extension[0]==".") $extension=substr($extension,1);    
	  $config = cmsms()->GetConfig();
	  $iconsize=$this->GetPreference("iconsize","32px");
		///for valid xhtml
	    $iconsizeHeight=str_replace("px","",$iconsize);
	    ///end
	      if ($extension[0]=='.') $extension=substr($extension, 1);

	      $result="";
	      if ($isdir) {
		//echo hi;
		$result="<img height=\"".$iconsizeHeight."\" style=\"border:0;\" src=\"".$config["root_url"]."/modules/FileManager/icons/themes/default/extensions/".$iconsize."/dir.png\" ".
		  "alt=\"directory\" ".
		  "align=\"middle\" />";
		return $result;
	      }
		//$extension=array_pop(explode('.', $filename));

		if (file_exists($config["root_path"]."/modules/FileManager/icons/themes/default/extensions/".$iconsize."/".strtolower($extension).".png")) {
			$result="<img height='".$iconsizeHeight."' style='border:0;' src='".$config["root_url"]."/modules/FileManager/icons/themes/default/extensions/".$iconsize."/".strtolower($extension).".png' ".
						 "alt='".$extension."-file' ".
						 "align='middle' />";
		} else {
			
			$result="<img height='".$iconsizeHeight."' style='border:0;' src='".$config["root_url"]."/modules/FileManager/icons/themes/default/extensions/".$iconsize."/0.png' ".
						 "alt=".$extension."-file' ".
						 "align='middle' />";
		}
		return $result;

	}

	function Slash($str,$str2="",$str3="") {
		if ($str=="") return $str2;
		if ($str2=="") return $str;
		if ($str[strlen($str)-1]!="/") {
			if ($str2[0]!="/") {
		    return $str."/".$str2;
			} else { 
			  return $str.$str2;
			}
		} else {
		  if ($str2[0]!="/") {
		    return $str.$str2;
			} else { 
			  return $str.substr($str2,1); //trim away one of the slashes
			}
		}
		//Three strings not supported yet...
		return "Error in Slash-function. Please report";
	}

  function ContainsIllegalChars($filename) {
    
    if (strpos($filename, "'")!==false) return true;
    if (strpos($filename, "\"")!==false) return true;
    if (strpos($filename, "/")!==false) return true;
    if (strpos($filename, "\\")!==false) return true;
    if (strpos($filename, "&")!==false) return true;
    if (strpos($filename, "\$")!==false) return true;
    if (strpos($filename, "+")!==false) return true;
    return false;
  }

	function FormatFileSize($_size) {
		$unit=$this->Lang("bytes");
  	//$size=$filelist[$i]["size"];
  	$size=$_size;
  	
  	if ($size>10000 && $size<(1024*1024)) {
  		$size=round($size/1024);
  		$unit=$this->Lang("kb");
  	}
  	
  	if ($size>(1024*1024)) {
  		$size=round($size/(1024*1024),1);
  		$unit=$this->Lang("mb");
  	}
  	
  	if ($this->GetPreference("thousanddelimiter")!="") {
  		$size=number_format($size,0,"",$this->GetPreference("thousanddelimiter"));  	  
  	}
  	
  	$result=array();
  	$result["size"]=$size;
  	$result["unit"]=$unit;
  	return $result;
	}

	function FormatPermissions($mode,$style="xxx") {
		//return octdec($mode & 0777);
		switch ($style) {
			case "xxx" : {
				$owner=0;
				if ($mode & 0400) $owner+=4;
				if ($mode & 0200) $owner+=2;
				if ($mode & 0100) $owner+=1;
				$group=0;
				if ($mode & 0040) $group+=4;
				if ($mode & 0020) $group+=2;
				if ($mode & 0010) $group+=1;
				$others=0;
				if ($mode & 0004) $others+=4;
				if ($mode & 0002) $others+=2;
				if ($mode & 0001) $others+=1;
				return $owner.$group.$others;
			}
			case "xxxxxxxxx" : {
				$owner="";					
				if ($mode & 0400) $owner.="r"; else $owner.="-";
				if ($mode & 0200) $owner.="w"; else $owner.="-";
				if ($mode & 0100) $owner.="x"; else $owner.="-";					
				$group="";
				if ($mode & 0040) $group.="r"; else $group.="-";
				if ($mode & 0020) $group.="w"; else $group.="-";
				if ($mode & 0010) $group.="x"; else $group.="-";					
				$others="";
				if ($mode & 0004) $others.="r"; else $others.="-";
				if ($mode & 0002) $others.="w"; else $others.="-";
				if ($mode & 0001) $others.="x"; else $others.="-";
				return $owner.$group.$others;
			}
		}
	}

	function SetMode($mode,$path,$file="") {
	  
	  $config = cmsms()->GetConfig();
	  $realfile="";
	  if ($file!="") {
		  //$realpath=$this->Slash($config["root_path"],$path);
		  $realfile=$this->Slash($path,$file);
	  } else {
	  	$realfile=$path;
	  }
	  
		//echo $realfile;
		//echo octdec($mode); die();
		//return chmod($realfile,decoct(octdec(77)));
		return chmod($realfile,"0".octdec($mode));
	}
	
	function SetModeWin($mode,$path,$file="") {
	  
	  $config = cmsms()->GetConfig();
	  $realfile="";
	  if ($file!="") {
		  //$realpath=$this->Slash($config["root_path"],$path);
		  $realfile=$this->Slash($path,$file);
	  } else {
	  	$realfile=$path;
	  }
	  $realfile=$this->WinSlashes($realfile);
	  //echo $realfile; echo $mode;die();
	  $returnvar="";
	  $output=array();
	  if ($mode=="777") {
	    //return chmod($realfile,"775");
	    exec("attrib -R ".$realfile,$output,$returnvar);
	  } else {
	  	exec("attrib +R ".$realfile,$output,$returnvar);
	  	//return chmod($realfile,"0666");
	  }
		/*echo $realfile;
		echo $returnvar;
		print_r($output);*/
		return ($returnvar==0);
		//return true;
		
		
	}

	function GetPermissions($path,$file) {
	  $config = cmsms()->GetConfig();
		$realpath=$this->Slash($config["root_path"],$path);
		$statinfo=stat($this->Slash($realpath,$file));
		return $statinfo["mode"];
	}

	function GetMode($path,$file) {	  
	  $config = cmsms()->GetConfig();
		$realpath=$this->Slash($config["root_path"],$path);
		$statinfo=stat($this->Slash($realpath,$file));
		return $this->FormatPermissions($statinfo["mode"]);
	}
	
  function GetModeWin($path,$file) {	  
    $config = cmsms()->GetConfig();
		$realpath=$this->Slash($config["root_path"],$path);
		$realpath=$this->Slash($realpath,$file);
		if (is_writable($realpath)) {
			return "777";
		} else {
			return "444";
		}
		//return $this->FormatPermissions($statinfo["mode"]);
	}

	function GetModeTable($id,$permissions) {
		$this->smarty->assign('ownertext', $this->Lang("owner"));
		$this->smarty->assign('groupstext', $this->Lang("group"));
		$this->smarty->assign('otherstext', $this->Lang("others"));

		$ownerr="0"; if ($permissions & 0400) $ownerr="1";
		$this->smarty->assign('ownerr', $this->CreateInputCheckbox($id,"ownerr","1",$ownerr));

		$ownerw="0"; if ($permissions & 0200) $ownerw="1";
		$this->smarty->assign('ownerw', $this->CreateInputCheckbox($id,"ownerw","1",$ownerw));

		$ownerx="0"; if ($permissions & 0100) $ownerx="1";
		$this->smarty->assign('ownerx', $this->CreateInputCheckbox($id,"ownerx","1",$ownerx));


		$groupr="0"; if ($permissions & 0040) $groupr="1";
		$this->smarty->assign('groupr', $this->CreateInputCheckbox($id,"groupr","1",$groupr));

		$groupw="0"; if ($permissions & 0020) $groupw="1";
		$this->smarty->assign('groupw', $this->CreateInputCheckbox($id,"groupw","1",$groupw));

		$groupx="0"; if ($permissions & 0010) $groupx="1";
		$this->smarty->assign('groupx', $this->CreateInputCheckbox($id,"groupx","1",$groupx));


		$othersr="0"; if ($permissions & 0004) $othersr="1";
		$this->smarty->assign('othersr', $this->CreateInputCheckbox($id,"othersr","1",$othersr));

		$othersw="0"; if ($permissions & 0002) $othersw="1";
		$this->smarty->assign('othersw', $this->CreateInputCheckbox($id,"othersw","1",$othersw));

		$othersx="0"; if ($permissions & 0001) $othersx="1";
		$this->smarty->assign('othersx', $this->CreateInputCheckbox($id,"othersx","1",$othersx));


		return $this->ProcessTemplate('modetable.tpl');
	}

	function GetModeFromTable($params) {
		$owner=0;
		if (isset($params["ownerr"])) $owner+=4;
		if (isset($params["ownerw"])) $owner+=2;
		if (isset($params["ownerx"])) $owner+=1;
		$group=0;
		if (isset($params["groupr"])) $group+=4;
		if (isset($params["groupw"])) $group+=2;
		if (isset($params["groupx"])) $group+=1;
		$others=0;
		if (isset($params["othersr"])) $others+=4;
		if (isset($params["othersw"])) $others+=2;
		if (isset($params["othersx"])) $others+=1;
		return $owner.$group.$others;

	}

	function CHModMakesSense() {
		return function_exists("posix_getpwuid");
	}

	function ParamsArray($params,$additional=array()) {
		$path=""; if (isset($params["path"]) && $params["path"]!="") $path=$params["path"];
	}
	function ConfirmModeSanity($newmode) {
		return true;
	}
	
	function GetThumbnailLink($file,$path) {
	  $gCms = cmsms();

	  $config = $gCms->GetConfig();
	  $advancedmode = filemanager_utils::check_advanced_mode();
	  $basedir = $config['root_path'];
	  $baseurl = $config['root_url'];
	  if( $advancedmode ) {
	    $basedir = dirname($basedir);
	    $baseurl = dirname($baseurl);
	  }

	  $filepath=$basedir.'/'.$path;
	  $url=$baseurl.'/'.$path;
	  $image="";
	  $imagepath=$this->Slashes($filepath."/thumb_".$file["name"]);
	  if (!file_exists($imagepath)) {
	    $image=$this->GetFileIcon($file["ext"],$file["dir"]);
	  } else {
	    $imageurl=$url.'/thumb_'.$file["name"];
	    $image="<img src=\"".$imageurl."\" alt=\"".$file["name"]."\" title=\"".$file["name"]."\" />";
	  }
	  
	  $result="<a href=\"".$file["url"]."\" target=\"_blank\">";
	  $result.=$image;
	  $result.="</a>";
		
	  return $result;
	}

	function WinSlashes($url) {
	  return str_replace("/","\\",$url);
	}
	function Slashes($url) {
		$result=str_replace("\\","/",$url);
		$result=str_replace("//","/",$result);
		return $result;
	}

  function CreateFilePickerButton($id,$inputname,$params) {
    $output="";
  }

  protected function _output_header_javascript()
  {
    $urlpath = $this->GetModuleURLPath()."/js";
    $files = array('jquery-file-upload/jquery.iframe-transport.js');
    $files[] = 'jquery-file-upload/jquery.fileupload.js';
    $fmt = '<script type="text/javascript" src="%s/%s"></script>';

    $out = '';
    foreach( $files as $one )
      {
	$out .= sprintf($fmt,$urlpath,$one)."\n";
      }
    return $out;
  }

  function encodefilename($filename) {
    return str_replace("==","",base64_encode($filename));
  }
  function decodefilename($encodedfilename) {
    return base64_decode($encodedfilename."==");
  }
} // end of class
?>
