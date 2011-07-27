<?php
#BEGIN_LICENSE
#-------------------------------------------------------------------------
# Module: ModuleManager (c) 2008 by Robert Campbell 
#         (calguy1000@cmsmadesimple.org)
#  An addon module for CMS Made Simple to allow browsing remotely stored
#  modules, viewing information about them, and downloading or upgrading
# 
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2005 by Ted Kulp (wishy@cmsmadesimple.org)
# This project's homepage is: http://www.cmsmadesimple.org
#
#-------------------------------------------------------------------------
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# However, as a special exception to the GPL, this software is distributed
# as an addon module to CMS Made Simple.  You may not use this software
# in any Non GPL version of CMS Made simple, or in any version of CMS
# Made simple that does not indicate clearly and obviously in its admin 
# section that the site was built with CMS Made simple.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
# Or read it online: http://www.gnu.org/licenses/licenses.html#GPL
#
#-------------------------------------------------------------------------
#END_LICENSE
if( !isset($gCms) ) exit;

{
  global $_SESSION;

  if( !$this->CheckPermission('Modify Site Preferences' ) )
    {
	$this->_DisplayErrorPage( $id, $params, $returnid,
				  $this->Lang('accessdenied'));
	return;
    }

  if( isset($params['resetcache']) )
    {
      unset($_SESSION['modulemanager_cache']);
      $this->Redirect($id,'defaultadmin',$returnid,array('active_tab'=>'prefs'));
    }
  else if( isset($params['reseturl']) )
    {
      $this->SetPreference('module_repository',ModuleManager::_dflt_request_url);
      $this->Redirect($id,'defaultadmin',$returnid,array('active_tab'=>'prefs'));
    }

  if( !isset( $params['url'] ) )
    {
	$this->_DisplayErrorPage( $id, $params, $returnid,
				  $this->Lang('error_insufficientparams'));
	return;
    }

  if( isset($params['input_dl_chunksize']) )
    {
      $this->SetPreference('dl_chunksize',trim($params['input_dl_chunksize']));
    }

  $latestdepends = (isset($params['latestdepends']))?1:0;
  $this->SetPreference('latestdepends',$latestdepends);

  $disable_caching = (isset($params['disable_caching']))?1:0;
  $this->SetPreference('disable_caching',$disable_caching);

  $this->SetPreference('module_repository',trim($params['url']));
  $this->Redirect($id,'defaultadmin',$returnid,array('active_tab'=>'prefs',"module_message"=>$this->Lang("preferencessaved")));
}
?>