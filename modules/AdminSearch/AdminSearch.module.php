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

if( !isset($gCms) ) exit;

final class AdminSearch extends CMSModule
{
  function GetFriendlyName()  { return $this->Lang('friendlyname'); }
  function GetVersion()  { return '1.0.5'; }
  function MinimumCMSVersion()  { return '1.12-alpha0'; }
  function LazyLoadAdmin() { return TRUE; }
  function LazyLoadFrontend() { return TRUE; }
  function IsPluginModule() { return FALSE; }
  function GetAuthor() { return 'Calguy1000'; }
  function GetAuthorEmail() { return 'calguy1000@cmsmadesimple.org'; }
  function HasAdmin() { return true; }
  function GetAdminSection() { return 'extensions'; }
  function GetHelp() { return $this->Lang('help'); }
  function GetAdminDescription() { return $this->Lang('moddescription'); }

  function VisibleToAdminUser()
  {
    return $this->can_search();
  }

  protected function can_search()
  {
      return $this->CheckPermission('Use Admin Search');
  }

  function InstallPostMessage()
  {
    return $this->Lang('postinstall');
  }

  function UninstallPostMessage()
  {
    return $this->Lang('postuninstall');
  }

  public function DoAction($name,$id,$params,$returnid='')
  {
    $smarty = cmsms()->GetSmarty();
    $smarty->assign('mod',$this);
    return parent::DoAction($name,$id,$params,$returnid);
  }

  public function HasCapability($capability,$params=array())
  {
    if( $capability == CmsCoreCapabilities::ADMINSEARCH ) return TRUE;
    return FALSE;
  }

  public function get_adminsearch_slaves()
  {
    $dir = dirname(__FILE__).'/lib/';
    $files = glob($dir.'/class.AdminSearch*slave.php');
    if( count($files) ) {
      $output = array();
      foreach( $files as $onefile ) {
	$parts = explode('.',basename($onefile));
	$classname = implode('.',array_slice($parts,1,count($parts)-2));
	if( $classname == 'AdminSearch_slave' ) continue;
	$output[] = $classname;
      }
      return $output;
    }
  }

} // end of class

#
# EOF
#