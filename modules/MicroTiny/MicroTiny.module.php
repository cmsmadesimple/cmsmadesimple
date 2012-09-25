<?php
/*
 #CMS - CMS Made Simple
 #(c)2004 by Simon Brown (simon@uptoeleven.ws)
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
 #$Id$
 #
*/

class MicroTiny extends CMSModule {

	//var $wysiwygactive; // Stikki: CMSModule variable, why this is here? It shouldn't be, must a remnant from before I put it into the module class
	var $templateid;

	public function __construct() {
	  parent::__construct();
	  $this->templateid = -1;
	}
	
	public function GetName() {
		return 'MicroTiny';
	}

	public function GetFriendlyName() {
		return $this->Lang("friendlyname");
	}

	public function GetVersion()	{
	  return '1.2.3';
	}

	public function HasAdmin() {
		return true;
	}

	public function IsWYSIWYG() {
		return true;
	}

	public function InitializeFrontend() {
	  //$this->RegisterModulePlugin();
	}

    public function InitializeAdmin() {

    }

	public function IsPluginModule() {
	  return true;
	}

	public function LazyLoadFrontend() {
		return true;
	}

	public function LazyLoadAdmin() {
		return true;
        }

	public function HasCapability($capability, $params=array()) {
		if ($capability=="wysiwyg") return true;
		return false;
	}

	public function MinimumCMSVersion() {
    return "1.11=alpha0";
	}

	public function GetDependencies() {
     return array('FileManager'=>'1.4.0');
  }

	public function GetHelp($lang='en_US') {
		return $this->Lang('help');
	}

	public function GetAuthor() {
		return 'Morten Poulsen';
	}

	public function GetAuthorEmail()	{
		return '&lt;morten@poulsen.org&gt;';
	}

	public function GetChangeLog()	{
		return $this->ProcessTemplate('changelog.tpl');
	}
	
	public function VisibleToAdminUser() {
		return $this->CheckPermission('Modify Site Preferences');
	}	

	public function WYSIWYGGenerateHeader($htmlresult='', $frontend=false) {	
		return microtiny_utils::WYSIWYGGenerateHeader($htmlresult, $frontend);
	}	
	
	public function WYSIWYGTextarea($name='textarea',$columns='80',$rows='15',$encoding='',$content='',$stylesheet='',$addtext='') {	
		return microtiny_utils::WYSIWYGTextarea($name,$columns,$rows,$encoding,$content,$stylesheet,$addtext);
	}

	public function WYSIWYGPageFormSubmit() {
		return 'tinyMCE.triggerSave();';
	}
	
	public function GetAdminDescription() {
	  return $this->Lang('admindescription');
	}
} // end of module class
?>
