<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (ted@cmsmadesimple.org)
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

class microtiny_utils 
{

  /**
   * Constructor
   *
   * @since 1.0
   */	
  private function __construct() { }

  /**
   * Module API wrapper function
   *
   * @since 1.0
   * @return string
   */		
  public static function WYSIWYGGenerateHeader($htmlresult='', $junk=false) 
  {
    // Check if we are in object instance
    $mod = cms_utils::get_module('MicroTiny');
    if(!is_object($mod)) throw new CmsLogicException('Could not find the microtiny module...');

    $frontend = cmsms()->is_frontend_request();
    $languageid = self::GetLanguageId($frontend);

    $fn = self::SaveStaticConfig($frontend,'',$languageid);

    $config = cms_utils::get_config();
    $output = '';
    $output .= '<script type="text/javascript" src="'.$config->smart_root_url().'/modules/MicroTiny/tinymce/tinymce.min.js"></script>';
    $configurl = $config->smart_root_url().'/tmp/cache/'.$fn.'?t='.time();
    $output.='<script type="text/javascript" src="'.$configurl.'" defer="defer"></script>';

    return $output;
  }	

  /**
   * Save Static configuration
   *
   * This function generates the microtiny initialization js file and returns its filename.
   *
   * @since 1.0
   * @param boolean $frontend	
   * @param string $templateid	
   * @param string $languageid	
   * @return string
   * @deleteme
   */		
  private static function SaveStaticConfig($frontend=false, $designid='', $languageid='') 
  { 
    $configcontent = self::GenerateConfig($frontend, $designid, $languageid);
    $fn = cms_join_path(PUBLIC_CACHE_LOCATION,'mt_'.md5(session_id().$frontend.$designid.$languageid).'.js');
    $res = file_put_contents($fn,$configcontent);
    if( !$res ) return;
    return basename($fn);
  }
	
  /**
   * Generate a tinymce initialization file.
   *
   * @since 1.0
   * @param boolean Frontend true/false
   * @param string Templateid
   * @param string A2 Languageid
   * @return string
   */	
  private static function GenerateConfig($frontend=false, $designid="", $languageid="en") 
  {
    $mod = cms_utils::get_module('MicroTiny');
    $config = cms_utils::get_config();	

    $smarty = cmsms()->GetSmarty();
    $smarty->assign('resize',($mod->GetPreference('allow_resize',1))?'true':'false');
    $smarty->assign('statusbar',($mod->GetPreference('show_statusbar',1))?'true':'false');
    $smarty->assign('isfrontend',$frontend);
    $smarty->assign('designid',$designid);
    $smarty->assign('languageid',$languageid);
    $smarty->assign('strip_background',$mod->GetPreference('strip_background',0));
    $smarty->assign('force_blackonwhite',$mod->GetPreference('force_blackonwhite',0));

    $result = $mod->ProcessTemplate('tinymce_config.tpl');
    return $result;
  }

  /**
   * Convert users current language to something tinymce can prolly understand (hopefully).
   *
   * @since 1.0
   * @return string
   */			
  private static function GetLanguageId() {
    $mylang = CmsNlsOperations::get_current_language();
    if ($mylang=="") return "en"; //Lang setting "No default selected"
    $mylang = substr($mylang,0,2);
		
    switch ($mylang) {			
    case "tw" : return strtolower($mylang);
    default : return $mylang;
    }
  }

  /**
   * Get an img tag for a thumbnail file if one exists.
   *
   * @since 1.0
   * @param string $file	
   * @param string $path	
   * @param string $url	
   * @return string
   */		
  public static function GetThumbnailFile($file,$path,$url) 
  {
    $image='';
    $imagepath = self::Slashes($path."/thumb_".$file);
    $imageurl = self::Slashes($url."/thumb_".$file);
    if (!file_exists($imagepath)) {
      $image='';
    } else {
      $image="<img src='".$imageurl."' alt='".$file."' title='".$file."' />";
    }
    return $image;
  }

  /**
   * Fix Slashes
   *
   * @since 1.0	
   * @return string
   */		
  private static function Slashes($url) 
  {
    $result=str_replace("\\","/",$url);
    return $result;
  }	

} // end of class

#
# EOF
#
?>