<?php // -*- mode:php; tab-width:2; indent-tabs-mode:t; c-basic-offset:2; -*-
#-------------------------------------------------------------------------
# Module: AdminSearch - A CMSMS addon module to provide template management.
# (c) 2012 by Robert Campbell <calguy1000@cmsmadesimple.org>
#
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
#
#-------------------------------------------------------------------------
if( !isset($gCms) ) exit;
if( !$this->VisibleToAdminUser() ) return;

$filter_rec = array('tpl'=>'','limit'=>100,'offset'=>0);
if( isset($params['dofilter']) ) {
	$filter_rec[] = $params['filter_tpl'];
	$filter_rec['limit'] = (int)$params['filter_limit'];
	$filter_rec['limit'] = max(2,min(100,$filter_rec['limit']));
	$filter_rec['offset'] = 0;
  cms_userprefs::set($this->GetName().'template_filter',serialize($filter_rec));
}
$tmp = cms_userprefs::get($this->GetName().'template_filter');
if( $tmp ) $filter_rec = unserialize($tmp);
if( isset($params['tpl_page']) ) {
	$page = max(1,(int)$params['tpl_page']);
	$filter_rec['offset'] = ($page - 1) * $filter_rec['limit'];
}

$efilter = $filter_rec;
if( !$this->CheckPermission('Modify Templates') ) {
  $efilter[] = 'e:'.get_userid();
}

$tpl_query = new CmsLayoutTemplateQuery($efilter);
$templates = $tpl_query->GetMatches();
if( count($templates) ) {
	$smarty->assign('templates',$templates);
	$tpl_nav = array();
	$tpl_nav['pagelimit'] = $tpl_query->limit;
	$tpl_nav['numpages'] = $tpl_query->numpages;
	$tpl_nav['numrows'] = $tpl_query->totalrows;
	$tpl_nav['curpage'] = (int)($tpl_query->offset / $tpl_query->limit) + 1;
	$smarty->assign('tpl_nav',$tpl_nav);
}



// build a list of the types, and categories, and later (designs).
$opts = array();
$opts[''] = $this->Lang('prompt_none');
$types = CmsLayoutTemplateType::get_all();
if( count($types) ) {
  $tmp = array();
  $tmp2 = array();
	$tmp3 = array();
  for( $i = 0; $i < count($types); $i++ ) {
    $tmp['t:'.$types[$i]->get_id()] = $types[$i]->get_langified_display_value();
    $tmp2[$types[$i]->get_id()] = $types[$i]->get_langified_display_value();
    $tmp3[$types[$i]->get_id()] = $types[$i];
  }
  $smarty->assign('list_all_types',$tmp3);
  $smarty->assign('list_types',$tmp2);
  $opts[$this->Lang('tpl_types')] = $tmp;
}
$cats = CmsLayoutTemplateCategory::get_all();
if( count($cats) ) {
  $smarty->assign('list_categories',$cats);
  $tmp = array();
  for( $i = 0; $i < count($cats); $i++ ) {
    $tmp['c:'.$cats[$i]->get_id()] = $cats[$i]->get_name();
  }
  $opts[$this->Lang('prompt_categories')] = $tmp;
}
$designs = CmsLayoutCollection::get_all();
if( count($designs) ) {
  $smarty->assign('list_designs',$designs);
  $tmp = array();
  for( $i = 0; $i < count($designs); $i++ ) {
    $tmp['h:'.$designs[$i]->get_id()] = $designs[$i]->get_name();
    $tmp2[$designs[$i]->get_id()] = $designs[$i]->get_name();
  }
  $smarty->assign('design_names',$tmp2);
  $opts[$this->Lang('prompt_design')] = $tmp;
}
if( $this->CheckPermission('Manage Designs') ) {
  $userops = cmsms()->GetUserOperations();
  $allusers = $userops->LoadUsers();
  $users = array(-1=>$this->Lang('prompt_unknown'));
  $tmp = array();
  for( $i = 0; $i < count($allusers); $i++ ) {
    $tmp['u:'.$allusers[$i]->id] = $allusers[$i]->username;
    $users[$allusers[$i]->id] = $allusers[$i]->username;
  }
  $smarty->assign('list_users',$users);
  $opts[$this->Lang('prompt_user')] = $tmp;
}

if( $this->CheckPermission('Modify Stylesheets') ) {
	$csslist = CmsLayoutStylesheet::get_all(TRUE);
	$smarty->assign('stylesheets',$csslist);
}

// give everything to smarty that we can.
$smarty->assign('filter_options',$opts);
$smarty->assign('filter',$filter_rec);

$tmp = ($this->CheckPermission('Modify Templates') || count($templates))?1:0;
$smarty->assign('has_templates',$tmp);
$smarty->assign('manage_stylesheets',$this->CheckPermission('Modify Stylesheets'));
$smarty->assign('manage_templates',$this->CheckPermission('Modify Templates'));
$smarty->assign('manage_designs',$this->CheckPermission('Manage Designs'));
$smarty->assign('import_url',$this->create_url($id,'admin_import_template'));
$smarty->assign('has_add_right',
								$this->CheckPermission('Modify Templates') || 
								$this->CheckPermission('Add Templates'));

echo $this->ProcessTemplate('defaultadmin.tpl');

#
# EOF
#
?>