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
$error = '';

if( !function_exists('cm_prettyurls_ok') ) {
  function cm_prettyurls_ok()
  {
    static $_prettyurls_ok = -1;
    if( -1 < $_prettyurls_ok ) return $_prettyurls_ok;

    $config = cmsms()->GetConfig();
    if( isset($config['url_rewriting']) && $config['url_rewriting'] != 'none' )
      $_prettyurls_ok = 1;
    else
      $_prettyurls_ok = 0;
    return $_prettyurls_ok;
  }
}

if( isset($params['multisubmit']) && isset($params['multiaction']) && 
    isset($params['multicontent']) && is_array($params['multicontent']) && count($params['multicontent']) > 0 ) {
  list($module,$bulkaction) = explode('::',$params['multiaction'],2);
  if( $module == '' || $module == '-1' || $bulkaction == '' || $bulkaction == '-1' ) {
    $this->SetMessage($this->Lang('error_nobulkaction'));
    $this->RedirectToAdminTab();
  }
  // redirect to special action to handle bulk content stuff.
  $this->Redirect($id,'admin_multicontent',$returnid,
		  array('multicontent'=>base64_encode(serialize($params['multicontent'])),
			'multiaction'=>$params['multiaction']));
}

$smarty->assign('prettyurls_ok',cm_prettyurls_ok());
$smarty->assign('can_add_content',$this->CheckPermission('Add Pages') || $this->CheckPermission('Manage All Content'));
$smarty->assign('can_reorder_content',$this->CheckPermission('Manage All Content'));

// load all the content that this user can display... 
// organize it into a tree
$builder = new ContentListBuilder($this);
$curpage = 1;
if( isset($params['curpage']) ) $curpage = (int)$params['curpage'];

//
// handle all of the possible ajaxy/sub actions.
//
$ajax = 0;
if( isset($params['ajax']) ) {
  $ajax = 1;
}
if( isset($params['expandall']) || isset($_GET['expandall']) ) {
  $builder->expand_all();
  $curpage = 1;
}
if( isset($params['collapseall']) || isset($_GET['collapseall']) ) {
  $builder->collapse_all();
  $curpage = 1;
}
if( isset($params['expand']) ) {
  $builder->expand_section($params['expand']);
}
if( isset($params['collapse']) ) {
  $builder->collapse_section($params['collapse']);
  $curpage = 1;
}
if( isset($params['setinactive']) ) {
  $builder->set_active($params['setinactive'],FALSE);
  if( !$res ) $error = $this->Lang('error_setinactive');
}
if( isset($params['setactive']) ) {
  $res = $builder->set_active($params['setactive'],TRUE);
  if( !$res ) $error = $this->Lang('error_setactive');
}
if( isset($params['setdefault']) ) {
  $res = $builder->set_default($params['setdefault'],TRUE);
  if( !$res ) $error = $this->Lang('error_setdefault');
}
if( isset($params['moveup']) ) {
  $res = $builder->move_content($params['moveup'],-1);
  if( !$res ) $error = $this->Lang('error_movecontent');
}
if( isset($params['movedown']) ) {
  $res = $builder->move_content($params['movedown'],1);
  if( !$res ) $error = $this->Lang('error_movecontent');
}
if( isset($params['delete']) ) {
  $res = $builder->delete_content($params['delete']);
  if( $res ) $error = $res;
}

//
// build the display
//

if( isset($params['setoptions']) ) {
  cms_userprefs::set($this->GetName().'_pagelimit',(int)$params['pagelimit']);
}
$pagelimit = cms_userprefs::get($this->GetName().'_pagelimit',500);

$builder->set_pagelimit($pagelimit);
$builder->set_page($curpage);
$editinfo = $builder->get_content_list();
$npages = $builder->get_numpages();
$pagelimits = array(10=>10,25=>25,100=>100,250=>250,500=>500);
$smarty->assign('pagelimits',$pagelimits);
$pagelist = array();
for( $i = 0; $i < $npages; $i++ ) {
  $pagelist[$i+1] = $i+1;
}
$smarty->assign('pagelimit',$pagelimit);
$smarty->assign('pagelist',$pagelist);
$smarty->assign('curpage',$curpage);
$smarty->assign('npages',$npages);
$columns  = $builder->get_display_columns();
$smarty->assign('columns',$columns);
if( $this->GetPreference('list_namecolumn','menutext') == 'title' ) {
  $smarty->assign('colhdr_page',$this->Lang('colhdr_name'));
}
else {
  $smarty->assign('colhdr_page',$this->Lang('colhdr_menutext'));
}
$smarty->assign('content_list',$editinfo);
$smarty->assign('ajax',$ajax);
if( $error ) $smarty->assign('error',$error);

$opts = array();
if( $this->CheckPermission('Remove Pages') || 
    $this->CheckPermission('Manage All Content') ) {
  bulkcontentoperations::register_function($this->Lang('bulk_delete'),'delete');
}
if( $this->CheckPermission('Manage All Content')) {
  bulkcontentoperations::register_function($this->Lang('bulk_active'),'active');
  bulkcontentoperations::register_function($this->Lang('bulk_inactive'),'inactive');
  bulkcontentoperations::register_function($this->Lang('bulk_cachable'),'setcachable');
  bulkcontentoperations::register_function($this->Lang('bulk_noncachable'),'setnoncachable');
  bulkcontentoperations::register_function($this->Lang('bulk_showinmenu'),'showinmenu');
  bulkcontentoperations::register_function($this->Lang('bulk_hidefrommenu'),'hidefrommenu');
  bulkcontentoperations::register_function($this->Lang('bulk_secure'),'secure');
  bulkcontentoperations::register_function($this->Lang('bulk_insecure'),'insecure');
  bulkcontentoperations::register_function($this->Lang('bulk_setdesign'),'setdesign');
  bulkcontentoperations::register_function($this->Lang('bulk_changeowner'),'changeowner');
}
$opts = bulkcontentoperations::get_operation_list();
$smarty->assign('bulk_options',$opts);

echo $this->ProcessTemplate('admin_pages_tab.tpl');
#
# EOF
#
?>