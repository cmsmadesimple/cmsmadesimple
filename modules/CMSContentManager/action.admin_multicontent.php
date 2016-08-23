<?php
#BEGIN_LICENSE
#-------------------------------------------------------------------------
# Module: Content (c) 2013 by Robert Campbell
#         (calguy1000@cmsmadesimple.org)
#  A module for managing content in CMSMS.
#
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2004 by Ted Kulp (wishy@cmsmadesimple.org)
# Visit our homepage at: http://www.cmsmadesimple.org
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
?>