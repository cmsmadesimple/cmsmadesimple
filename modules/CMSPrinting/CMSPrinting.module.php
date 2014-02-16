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
#$Id: News.module.php 2114 2005-11-04 21:51:13Z wishy $

class CMSPrinting extends CMSModule 
{
  public function GetName() { return 'CMSPrinting'; }
  public function GetFriendlyName() { return $this->Lang("friendlyname"); }
  public function IsPluginModule() { return true; }
  public function HasAdmin() { return true; }
  public function GetVersion() { return '1.50'; }
  public function MinimumCMSVersion() { return '1.12-alpha0'; }
  public function GetAdminDescription() { return $this->Lang('description'); }
  public function GetAdminSection() { return 'extensions'; }
  public function InstallPostMessage() { return $this->Lang('postinstall'); }
  public function LazyLoadFrontend() { return TRUE; }  
  public function LazyLoadAdmin() { return TRUE; }  
  public function VisibleToAdminUser() { return $this->CheckPermission('modifyprintingsettings') || $this->CheckPermission('Modify Templates'); }
  public function UninstallPostMessage() { return $this->Lang('postuninstall'); }
  public function SetOverrideStyle($stylesheet) { $this->SetPreference('overridestyle',$stylesheet); }
  public function GetHelp($lang='en_US') { return $this->Lang('help'); }
  public function GetAuthor() { return 'Morten Poulsen'; }
  public function GetAuthorEmail() { return 'morten@poulsen.org'; }
  public function GetChangeLog() { return $this->ProcessTemplate ('changelog.tpl'); }

  function relativeToAbsolute($prefix, $text) {
    // search for single quotes and replace them by double quotes
    $search = '\'';
    $replace = '"';
    $text = str_replace($search, $replace, $text);
    // replace relative urls by absolute (prefix them with $prefix)
    $pattern = '/href="(?!http|https|ftp|irc|feed|mailto)([\/]?)(.*)"/i';
    $replace = 'href="'.$prefix.'/$2"';
    $text = preg_replace($pattern, $replace, $text);
    // return
    return $text;
  }

  function GetBody($html) {
    $pos1=stripos($html,"<body");
    if ($pos1 === FALSE) return $html; //no <body
    $pos2=stripos($html,">",$pos1+1);
    if ($pos2 === FALSE) return $html; //no end of <body>tag
    $pos2++; //increase to just after > of body-tag
    $pos3=stripos($html,"</body>",$pos2);
    if ($pos3 === FALSE) return substr($html,$pos2); //no end tag, just return from after <body>
    //then return from after <body> to before </body>
    return "\n<!-- body-->\n".substr($html,$pos2,$pos3-$pos2)."\n<!-- /body-->\n"; 
  }

  function GetCurrentURL($showtemplate=false) {
    $url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    if ($showtemplate) return $url;
    if (strpos($url,'?')) { //other params present
      $url .= "&showtemplate=false";
    } else {
      $url .= '?showtemplate=false';
    }
    return $url;
  }

  public function InitializeAdmin() {	  
    $this->CreateParameter('text', $this->Lang("defaultlinktext"), $this->Lang('help_text'));
    $this->CreateParameter('popup', "false", $this->Lang('help_popup'));
    $this->CreateParameter('script', "false", $this->Lang('help_script'));
    $this->CreateParameter('includetemplate', "false", $this->Lang('help_includetemplate'));
    $this->CreateParameter('showbutton', "false", $this->Lang('help_showbutton'));
    $this->CreateParameter('class', "false", $this->Lang('help_class'));
    $this->CreateParameter('class_img', "false", $this->Lang('help_class_img'));
    $this->CreateParameter('src_img', "false", $this->Lang('help_src_img'));
    $this->CreateParameter('more', "false", $this->Lang('help_more'));
    $this->CreateParameter('onlyurl', "false", $this->Lang('help_onlyurl'));
    $this->CreateParameter('linktemplate','',$this->lang('help_linktemplate'));
    $this->CreateParameter('printtemplate','',$this->lang('help_printtemplate'));
  }

  public function InitializeFrontend() {  
    $this->RestrictUnknownParams();
    
    $this->SetParameterType('url',CLEAN_STRING);
    $this->SetParameterType('pageid',CLEAN_INT);
    $this->SetParameterType('text',CLEAN_STRING);
    $this->SetParameterType('popup',CLEAN_STRING);
    $this->SetParameterType('script',CLEAN_STRING);
    $this->SetParameterType('includetemplate',CLEAN_STRING);
    $this->SetParameterType('showbutton',CLEAN_STRING);
    $this->SetParameterType('class',CLEAN_STRING);
    $this->SetParameterType('class_img',CLEAN_STRING);
    $this->SetParameterType('src_img',CLEAN_STRING);
    $this->SetParameterType('more',CLEAN_STRING);
    $this->SetParameterType('onlyurl',CLEAN_STRING);
    $this->SetParameterType('linktemplate',CLEAN_STRING);
    $this->SetParameterType('printtemplate',CLEAN_STRING);
  }

  function ResetOverrideStyle() {
    $fn = dirname(__FILE__).DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'override.css';
    if( file_exists( $fn ) ) {
      $template = @file_get_contents($fn);
      $this->SetOverrideStyle($template);      
    }
  }

  public static function page_type_lang_callback($str)
  {
    $mod = cms_utils::get_module('CMSPrinting');
    if( is_object($mod) ) return $mod->Lang('type_'.$str);
  }

  public static function reset_page_type_defaults(CmsLayoutTemplateType $type)
  {
    if( $type->get_originator() != 'CMSPrinting' ) {
      throw new CmsLogicException('Cannot reset contents for this template type');
    }

    $fn = null;
    switch( $type->get_name() ) {
    case 'link':
      $fn = 'linktemplate.tpl';
      break;

    case 'print':
      $fn = 'printtemplate.tpl';
      break;
    }

    $fn = cms_join_path(dirname(__FILE__),'templates',$fn);
    if( file_exists($fn) ) {
      return @file_get_contents($fn);
    }
  }

}

?>