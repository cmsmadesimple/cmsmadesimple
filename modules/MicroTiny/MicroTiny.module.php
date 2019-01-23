<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (ted@cmsmadesimple.org)
#Visit our homepage at: http://www.cmsmadesimple.org
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
  const PROFILE_FRONTEND = '__frontend__';
  const PROFILE_ADMIN = '__admin__';

  public function __construct() { parent::__construct(); }
  public function GetName() { return 'MicroTiny'; }
  public function GetFriendlyName() { return $this->Lang("friendlyname"); }
  public function GetVersion(){ return '2.2.4'; }
  public function HasAdmin() { return TRUE; }
  public function IsPluginModule() { return TRUE; }
  public function LazyLoadFrontend() { return TRUE; }
  public function LazyLoadAdmin() { return TRUE; }
  public function MinimumCMSVersion() { return '2.2.2'; }
  public function GetDependencies() { return array('FileManager'=>'1.6.4'); }
  public function GetHelp() { return $this->Lang('help'); }
  public function GetAuthor() { return 'Morten Poulsen'; }
  public function GetAuthorEmail() { return 'morten@poulsen.org'; }
  public function GetChangeLog() { return $this->ProcessTemplate('changelog.tpl'); }
  public function VisibleToAdminUser() { return $this->CheckPermission('Modify Site Preferences'); }
  public function GetAdminDescription() { return $this->Lang('admindescription'); }

  public function WYSIWYGGenerateHeader($selector = null,$cssname = null) {
    return microtiny_utils::WYSIWYGGenerateHeader($selector, $cssname);
  }

  public function HasCapability($capability, $params=array()) {
    if ($capability==CmsCoreCapabilities::WYSIWYG_MODULE) return true;
    return false;
  }

} // end of module class

function mt_jsbool($val)
{
  $val = cms_to_bool($val);
  if( $val ) return 'true';
  return 'false';
}
#
# EOF
#
?>
