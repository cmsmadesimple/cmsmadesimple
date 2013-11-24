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

class MicroTiny extends CMSModule 
{
  public function __construct() { parent::__construct(); }
  public function GetName() { return 'MicroTiny'; }
  public function GetFriendlyName() { return $this->Lang("friendlyname"); }
  public function GetVersion(){ return '1.3'; }
  public function HasAdmin() { return TRUE; }
  public function IsPluginModule() { return TRUE; }
  public function LazyLoadFrontend() { return TRUE; }
  public function LazyLoadAdmin() { return TRUE; }
  public function MinimumCMSVersion() { return "1.99-alpha0"; }
  public function GetDependencies() { return array('FileManager'=>'1.5'); }
  public function GetHelp() { return $this->Lang('help'); }
  public function GetAuthor() { return 'Morten Poulsen'; }
  public function GetAuthorEmail() { return '&lt;morten@poulsen.org&gt;'; }
  public function GetChangeLog() { return $this->ProcessTemplate('changelog.tpl'); }
  public function VisibleToAdminUser() { return $this->CheckPermission('Modify Site Preferences'); }
  public function GetAdminDescription() { return $this->Lang('admindescription'); }

  public function WYSIWYGGenerateHeader($htmlresult='', $frontend=false) {	
    return microtiny_utils::WYSIWYGGenerateHeader($htmlresult, $frontend);
  }
	
  public function HasCapability($capability, $params=array()) {
    if ($capability==CmsCoreCapabilities::WYSIWYG_MODULE) return true;
    return false;
  }
  
} // end of module class

#
# EOF
#
?>