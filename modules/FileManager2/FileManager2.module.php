<?php
# FileManager. A plugin for CMS - CMS Made Simple
# Copyright (c) 2006-08 by Morten Poulsen <morten@poulsen.org>
#
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
#

include_once(dirname(__FILE__)."/fileinfo.php");

class FileManager2 extends CMSModule {
	function AccessAllowed() {
		return ($this->CheckPermission("Modify Files") || $this->CheckPermission('Modify Site Preferences'));
	}

	function AdvancedAccessAllowed() {
		return $this->CheckPermission('Use FileManager Advanced');
	}

	function GetName() {
		return 'FileManager2';
	}

  function GetChangeLog()	{
		return $this->ProcessTemplate('changelog.tpl');
	}
	
	function GetFriendlyName() {
		return $this->Lang('friendlyname');
	}

	function GetVersion() {
		return '1.6.6';
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
		return "1.5.4";
	}

	function VisibleToAdminUser() {
		return ($this->AccessAllowed() || $this->AdvancedAccessAllowed());
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

	function GetActionIcon($action) {
	  $config =$this->cms->GetConfig();
		//$iconsize=$this->GetPreference("iconsize","32px");
		//if (file_exists($config["root_path"].
		//$iconsize="16px";
		return "<img style='border:0;' src='".$config["root_url"]."/modules/FileManager/icons/themes/default/actions/".strtolower($action).".gif' ".
						 "alt='".$this->Lang($action)."' />";
		//				 "align='middle' />";
	}

	function GetFileIcon($extension,$isdir=false) {
	  if (empty($extension)) $extension = '---'; // hardcode extension to something.
	  if ($extension[0]==".") $extension=substr($extension,1);    
	  $config =$this->cms->GetConfig();
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

	
	function FileManagerCompareFiles($a,$b,$forcesort="") {
		$sortby=$this->GetPreference("sortby","nameasc");
		if ($forcesort!="") $sortby=$forcesort;
		if ($a["name"]=="..") return -1;
		if ($b["name"]=="..") return 1;
		/*print_r($a);
		 print_r($b);*/
		//Handle if only one is a dir
		if ($a["dir"] XOR $b["dir"]) {
		  if ($a["dir"]) return -1; else return 1;
		}
	  
		switch($sortby) {
			case "nameasc" : return strncasecmp($a["name"],$b["name"],strlen($a["name"]));
			case "namedesc" : return strncasecmp($b["name"],$a["name"],strlen($b["name"]));
			case "sizeasc" : {
				if ($a["dir"] && $b["dir"]) return $this->FileManagerCompareFiles($a,$b,"nameasc");
				return ($a["size"]>$b["size"]);
			}
			case "sizedesc" : {
				if ($a["dir"] && $b["dir"]) return $this->FileManagerCompareFiles($a,$b,"nameasc");
				return ($b["size"]>$a["size"]);
			}
			default : strncasecmp($a["name"],$b["name"],strlen($a["name"]));				
		}
		/*if ($a["dir"] || $b["dir"]) {
			if ($a["dir"] && $b["dir"]) {
				return strncasecmp($a["name"],$b["name"],strlen($a["name"]));
			} else {
				if ($a["dir"]) return -1; else return 1;
			}
		}*/
		return 0;
	}

	function columnSort($unsorted) {
		$sorted = $unsorted;
		for ($i=0; $i < sizeof($sorted)-1; $i++) {
			for ($j=0; $j<sizeof($sorted)-1-$i; $j++) {
				if ($this->FileManagerCompareFiles($sorted[$j],$sorted[$j+1])>0) {
					$tmp = $sorted[$j];
					$sorted[$j] = $sorted[$j+1];
					$sorted[$j+1] = $tmp;
				}
			}
		}
		return $sorted;
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

	function upDir() {
	  global $gCms;
	  $config =& $gCms->GetConfig();
		//static $udir="";
		//if ($udir!="") return $udir;
		$udir=substr($config["uploads_path"],strlen($config["root_path"]));

		return $this->Slashes($udir);

	}

	function SetMode($mode,$path,$file="") {
	  
	  $config =$this->cms->GetConfig();
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
	  
	  $config =$this->cms->GetConfig();
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
	  $config =&$this->cms->GetConfig();
		$realpath=$this->Slash($config["root_path"],$path);
		$statinfo=stat($this->Slash($realpath,$file));
		return $statinfo["mode"];
	}

	function GetMode($path,$file) {	  
	  $config =&$this->cms->GetConfig();
		$realpath=$this->Slash($config["root_path"],$path);
		$statinfo=stat($this->Slash($realpath,$file));
		return $this->FormatPermissions($statinfo["mode"]);
	}
	
  function GetModeWin($path,$file) {	  
	  $config =&$this->cms->GetConfig();
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

	function IntruderCheck($path,$advancedmode="check") {
	  global $gCms;
	  
	  if ($advancedmode=="check") $advancedmode=$this->AdvancedAccessAllowed();
	  $config =& $gCms->GetConfig();
	  
	  $path=$this->Slash($config["root_path"],$path);

		if (!$advancedmode) {
			if (stripos($this->Slashes($path),$this->Slashes($config["uploads_path"]))===false) {
				return true;
			}
		}
		return false;
	}
	
	function GetFileList(&$path,$advancedmode=false) {
		//echo "<b>$path</b>";		
		$showhiddenfiles=$this->GetPreference("showhiddenfiles","1");
		$result=array();
	  global $gCms;
	  $config =& $gCms->GetConfig();
		$realpath=$this->Slash($config["root_path"],$path);
		if ($this->IntruderCheck($realpath,$advancedmode)) {
			$realpath=$config["uploads_path"];
			$path=str_replace($config["root_path"],"",$realpath);
			$path=$this->Slashes($path);
		}
		$dir=@opendir($realpath);
		if (!$dir) return false;
		while ($file=readdir($dir)) {
			if ($file==".") continue;
			if ($file=="..") {
				if ($advancedmode) {
					if ($path=="/") continue;
				} else {
					if ($path==$this->upDir()) continue;
				}
			} else {
				if ($file[0]==".") {
					if (($showhiddenfiles!=1) || (!$advancedmode)) continue;
				}
			}
			
			if (substr($file,0,6)=="thumb_") {
				//Ignore thumbnail files of showing thumbnails is off
				if ($this->GetPreference("showthumbnails","1")=="1") continue;
			}
				
			$info=array();
			$info["name"]=$file;
			$statinfo=stat($this->Slash($realpath,$file));
      //print_r($statinfo);
			if (is_dir($this->Slash($realpath,$file))) {
				$info["dir"]=true;
				$info["ext"]="";
				$info["fileinfo"]=GetFileInfo($this->Slash($realpath,$file),"",true);
			} else {
				$info["dir"]=false;
				$info["size"]=$statinfo["size"];
        
				$info["date"]=$statinfo["mtime"];
				$info["url"]=$this->Slashes($config["root_url"].$this->Slash($path,$file));
				$explodedfile=explode('.', $file);
				$info["ext"]=array_pop($explodedfile);
				$info["fileinfo"]=GetFileInfo($this->Slash($realpath,$file),$info["ext"],false);
			}
			if (in_array(strtolower($info["ext"]),array("gif","jpg","jpeg","png"))) {
				$info["image"]=true;				
			} else {
				$info["image"]=false;
			}
			//print_r($statinfo);
			if (function_exists("posix_getpwuid")) {
				$userinfo = @posix_getpwuid($statinfo["uid"]);
				$info["fileowner"]= isset($userinfo['name'])?$userinfo['name']:$this->Lang('unknown');
			} else {
				$info["fileowner"]="N/A";
			}
			$info["writable"]=is_writable($this->Slash($realpath,$file));
			if (function_exists("posix_getpwuid")) {
			  $info["permissions"]=$this->FormatPermissions($statinfo["mode"],$this->GetPreference("permissionstyle","xxx"));
			} else {
				if ($info["writable"]) {
				  $info["permissions"]="R";
				} else {
					$info["permissions"]="R";
				}
			}
			$info["writable"]=is_writable($this->Slash($realpath,$file));
			$result[]=$info;
		}
		$result=$this->columnSort($result);
		return $result;
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
	  global $gCms;
	  $config =& $gCms->GetConfig();
		$filepath=$config["root_path"].$path;
		$url=$config["root_url"].$path;
		//echo $url;
		$result="<a href=\"".$file["url"]."\" target=\"_blank\">";
		$image="";
//		$pos=strrpos($file["url"],"/");
					 
			$imagepath=$this->Slashes($filepath."/thumb_".$file["name"]);
			$imageurl=$this->Slashes($url."/thumb_".$file["name"]);
			//echo "<pre>".$imageurl."</pre><br/>";
			if (!file_exists($imagepath)) {
				$image=$this->GetFileIcon($file["ext"],$file["dir"]);
			} else {
				$image="<img src=\"".$imageurl."\" alt=\"".$file["name"]."\" title=\"".$file["name"]."\" />";
			}
		
		$result.=$image;
		/*$imgurl=str_replace($config["image_uploads_url"],"",$file["url"]);
		if (substr($imgurl,0,1)=="/") $imgurl=substr($imgurl,1);
		//if (substr($imgurl,0,1)==DIRECTORY_SEPARATOR) $imgurl=substr($imgurl,1);
		$result.="<img src='".$config["root_url"]."/lib/filemanager/ImageManager/thumbs.php?img=".$imgurl."' alt='".$file["name"]."' title='".$file["name"]."' />";
		*/
		$result.="</a>";
		
		return $result;
	}
	function WinSlashes($url) {
	  return str_replace("/","\\",$url);
	}
	function Slashes($url) {
		$result=str_replace("\\","/",$url);
		
		//$result=str_replace("/",DIRECTORY_SEPARATOR,$url);
		//return str_replace("\\",DIRECTORY_SEPARATOR,$result);
		return $result;
	}

  function CreateFilePickerButton($id,$inputname,$params) {
    /*Params=array(
      "allowcreatedir"=>false,
      "allowchangedir"=>false,
      "initialdir"=>"/uploads/",
      "filetypes"=>"images"/"pdf,doc",
      "allowupload"=>false
    );
    */
    $output="";
  }
	
  
	
}


?>
