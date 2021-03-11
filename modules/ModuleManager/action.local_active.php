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
if( !$this->CheckPermission('Modify Modules') ) return;

$this->SetCurrentTab('installed');

if( !isset($params['mod']) ) {
  $this->SetError($this->Lang('error_missingparam'));
  $this->RedirectToAdminTab();
}

$state = 0;
if( isset($params['state']) ) $state = (int)$params['state'];
$module = trim(get_parameter_value($params,'mod'));
$ops = ModuleOperations::get_instance();

$query = "UPDATE ".CMS_DB_PREFIX."modules SET active = ? WHERE module_name = ?";
$dbr = $db->Execute($query, array($state,$module));
if( !$dbr ) {
  $this->SetError($this->Lang('error_active_failed'));
  $this->RedirectToAdminTab();
}

cmsms()->clear_cached_files();


if( $state ) {
  $this->SetMessage($this->Lang('msg_module_activated',$module));
  audit('',$this->GetName(),'Activated module '.$module);
}
else {
  $this->SetMessage($this->Lang('msg_module_deactivated',$module));
  audit('',$this->GetName(),'Dectivated module '.$module);
}
$this->RedirectToAdminTab();

#
# EOF
#