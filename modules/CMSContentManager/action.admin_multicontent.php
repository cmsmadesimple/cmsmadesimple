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

//
// init
//
$this->SetCurrentTab('pages');
$multiaction = null;
$multicontent = null;
$module = null;
$bulkaction = null;
$pages = null;


//
// get data
//
if( isset($params['multicontent']) ) $multicontent = unserialize(base64_decode($params['multicontent']));
if( isset($params['multiaction']) ) $multiaction = $params['multiaction'];

//
// validate 1
//
if( !is_array($multicontent) || count($multicontent) == 0 ) {
  $this->SetError($this->Lang('error_missingparam'));
  $this->RedirectToAdminTab();
}
if( !$multiaction ) {
  $this->SetError($this->Lang('error_missingparam'));
  $this->RedirectToAdminTab();
}

//
// get data 2
//
list($module,$bulkaction) = explode('::',$multiaction,2);
if( $module == '' || $module == '-1' || $bulkaction == '' || $bulkaction == -1 ) {
    $this->SetError($this->Lang('error_invalidbulkaction'));
    $this->RedirectToAdminTab();
}
if( $module != 'core' ) {
    $modobj = cms_utils::get_module($module);
    if( !is_object($modobj) ) {
        $this->SetError($this->Lang('error_invalidbulkaction'));
        $this->RedirectToAdminTab();
    }
    $url = $modobj->create_url($id,$bulkaction,$returnid,array('contentlist'=>implode(',',$multicontent)));
    $url = str_replace('&amp;','&',$url);
    redirect($url);
}

$parms = array('multicontent'=>$params['multicontent']);
switch( $bulkaction ) {
 case 'inactive':
   $parms['active'] = 0;
   $this->Redirect($id,'admin_bulk_active',$returnid,$parms);
   break;

 case 'active':
   $parms['active'] = 1;
   $this->Redirect($id,'admin_bulk_active',$returnid,$parms);
   break;

 case 'setcachable':
   $parms['cachable'] = 1;
   $this->Redirect($id,'admin_bulk_cachable',$returnid,$parms);
   break;

 case 'setnoncachable':
   $parms['cachable'] = 0;
   $this->Redirect($id,'admin_bulk_cachable',$returnid,$parms);
   break;

 case 'secure':
   $parms['secure'] = 1;
   $this->Redirect($id,'admin_bulk_secure',$returnid,$parms);
   break;

 case 'insecure':
   $parms['secure'] = 0;
   $this->Redirect($id,'admin_bulk_secure',$returnid,$parms);
   break;

 case 'showinmenu':
   $parms['showinmenu'] = 1;
   $this->Redirect($id,'admin_bulk_showinmenu',$returnid,$parms);
   break;

 case 'hidefrommenu':
   $parms['showinmenu'] = 0;
   $this->Redirect($id,'admin_bulk_showinmenu',$returnid,$parms);
   break;

 case 'setdesign':
 case 'changeowner':
 case 'delete':
   $this->Redirect($id,'admin_bulk_'.$bulkaction,$returnid,$parms);
   break;

}

$this->SetError($this->Lang('error_nobulkaction'));
$this->RedirectToAdminTab();

#
# EOF
#