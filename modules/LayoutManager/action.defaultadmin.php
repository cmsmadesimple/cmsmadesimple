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

if( isset($params['dofilter']) ) {
  $filter = array($params['dofilter']);
  cms_userprefs::set($this->GetName().'template_filter',serialize($filter));
}

$filter = cms_userprefs::get($this->GetName().'template_filter');
if( $filter )
  $filter = unserialize($filter);
else 
  $filter = array();
if( !$this->CheckPermission('Modify Templates') ) {
  $filter[] = 'e:'.get_userid();
}
$templates = CmsLayoutTemplate::template_query($filter);

if( count($templates) ) $smarty->assign('templates',$templates);


// build a list of the types, and categories, and later (themes).
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
$themes = CmsLayoutTheme::get_all();
if( count($themes) ) {
  $smarty->assign('list_themes',$themes);
  $tmp = array();
  for( $i = 0; $i < count($themes); $i++ ) {
    $tmp['h:'.$themes[$i]->get_id()] = $themes[$i]->get_name();
    $tmp2[$themes[$i]->get_id()] = $themes[$i]->get_name();
  }
  $smarty->assign('theme_names',$tmp2);
  $opts[$this->Lang('prompt_theme')] = $tmp;
}
if( $this->CheckPermission('Manage Themes') ) {
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
$smarty->assign('filter_options',$opts);

$smarty->assign('manage_templates',$this->CheckPermission('Modify Templates'));
$smarty->assign('manage_themes',$this->CheckPermission('Manage Themes'));
$smarty->assign('import_url',$this->create_url($id,'admin_import_template'));
$smarty->assign('has_add_right',
								$this->CheckPermission('Modify Templates') || 
								$this->CheckPermission('Add Templates'));

echo $this->ProcessTemplate('defaultadmin.tpl');

#
# EOF
#
?>