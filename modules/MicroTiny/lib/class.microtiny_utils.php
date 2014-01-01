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
  public static function WYSIWYGGenerateHeader($htmlresult='', $elementid=null, $cssname='')
  {
    debug_to_log(__FUNCTION__." $elementid $cssname");
    static $first_time = true;

    // Check if we are in object instance
    $mod = cms_utils::get_module('MicroTiny');
    if(!is_object($mod)) throw new CmsLogicException('Could not find the microtiny module...');

    $frontend = cmsms()->is_frontend_request();
    $languageid = self::GetLanguageId($frontend);

    // get the latest modification time of this cssname
    // if fn does not exist or is older than the modification time, save the config.
    $mtime = time()-60; // by defaul cache for 1 minute ??
    if( $cssname ) {
      try {
	$css = CmsLayoutStylesheet::load($cssname);
	$mtime = $css->get_modified();
      }
      catch( Exception $e ) {
	// couldn't load the stylesheet for some reason.
	$cssname = null;
      }
    }

    $config = cms_utils::get_config();
    $output = '';
    if( $first_time ) {
      // only once per request.
      $output .= '<script type="text/javascript" src="'.$config->smart_root_url().'/modules/MicroTiny/tinymce/tinymce.min.js"></script>';

      // generate css for the plugins

      $first_time = FALSE;
    }

    $fn = cms_join_path(PUBLIC_CACHE_LOCATION,'mt_'.md5(__DIR__.session_id().$frontend.$elementid.$cssname.$languageid).'.js');
    if( !file_exists($fn) || filemtime($fn) < $mtime ) {
      // we have to generate an mt config js file.
      self::_save_static_config($fn,$frontend,$elementid,$cssname,$languageid);
    }

    //$configurl = $config->smart_root_url().'/tmp/cache/'.$fn.'?t='.time();
    $configurl = $config->smart_root_url().'/tmp/cache/'.basename($fn);
    $output.='<script type="text/javascript" src="'.$configurl.'" defer="defer"></script>';

    return $output;
  }	

  private static function _save_static_config($fn, $frontend=false, $elementid, $css_name = '', $languageid='') 
  {
    if( !$fn ) return;
    $configcontent = self::_generate_config($frontend, $elementid, $css_name, $languageid);
    $res = file_put_contents($fn,$configcontent);
    if( !$res ) throw new CmsFileSystemException('Problem writing data to '.$fn);
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
  private static function _generate_config($frontend=false, $elementid = null, $css_name = null, $languageid="en") 
  {
    $mod = cms_utils::get_module('MicroTiny');
    $config = cms_utils::get_config();	

    $smarty = cmsms()->GetSmarty();
    $smarty->assign('MicroTiny',$mod);
    $smarty->assign('mt_actionid','m1_');
    $smarty->clear_assign('mt_cssname');
    $smarty->clear_assign('mt_elementid');
    $smarty->assign('resize',($mod->GetPreference('allow_resize',1))?'true':'false');
    $smarty->assign('statusbar',($mod->GetPreference('show_statusbar',1))?'true':'false');
    $smarty->assign('isfrontend',$frontend);
    if( $elementid ) $smarty->assign('mt_elementid',$elementid);
    if( $css_name ) $smarty->assign('mt_cssname',$css_name);
    debug_to_log('mt_cssname is '.$css_name);
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