<?php
#BEGIN_LICENSE
#-------------------------------------------------------------------------
# Module: Content (c) 2013 by Robert Campbell 
#         (calguy1000@cmsmadesimple.org)
#  A module for managing content in CMSMS.
# 
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2004 by Ted Kulp (wishy@cmsmadesimple.org)
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

$smarty->assign('prettyurls_ok',cm_prettyurls_ok());
$smarty->assign('can_add_content',$this->CheckPermission('Add Pages') || $this->CheckPermission('Manage All Content'));
$smarty->assign('can_reorder_content',$this->CheckPermission('Manage All Content'));

// load all the content that this user can display... 
// organize it into a tree
$builder = new ContentListBuilder($this);
$columns  = $builder->get_display_columns();

// handle all of the possible ajaxy/sub actions.
$ajax = 0;
if( isset($params['ajax']) ) {
  $ajax = 1;
}
if( isset($params['expandall']) ) {
  $builder->expand_all();
}
if( isset($params['collapseall']) ) {
  $builder->collapse_all();
}
if( isset($params['expand']) ) {
  $builder->expand_section($params['expand']);
}
if( isset($params['collapse']) ) {
  $builder->collapse_section($params['collapse']);
}
if( isset($params['setinactive']) ) {
  $builder->set_active($params['setinactive'],FALSE);
}
if( isset($params['setactive']) ) {
  $builder->set_active($params['setactive'],TRUE);
}
if( isset($params['setdefault']) ) {
  $builder->set_default($params['setdefault'],TRUE);
}
if( isset($params['moveup']) ) {
  $builder->move_content($params['moveup'],-1);
}
if( isset($params['movedown']) ) {
  $builder->move_content($params['movedown'],1);
}
if( isset($params['delete']) ) {
  $res = $builder->delete_content($params['delete']);
  if( $res != '' ) {
    
  }
}

$editinfo = $builder->get_content_list();
$smarty->assign('columns',$columns);
$smarty->assign('content_list',$editinfo);
$smarty->assign('ajax',$ajax);

if( $ajax == 0 ) {
  $opts = array();
  if( $this->CheckPermission('Remove Pages') || 
      $this->CheckPermission('Manage All Content') ) {
    bulkcontentoperations::register_function(lang('delete'),'delete');
  }
  if( $this->CheckPermission('Manage All Content')) {
    bulkcontentoperations::register_function(lang('active'),'active');
    bulkcontentoperations::register_function(lang('inactive'),'inactive');
    bulkcontentoperations::register_function(lang('cachable'),'setcachable');
    bulkcontentoperations::register_function(lang('noncachable'),'setnoncachable');
    bulkcontentoperations::register_function(lang('showinmenu'),'showinmenu');
    bulkcontentoperations::register_function(lang('hidefrommenu'),'hidefrommenu');
    bulkcontentoperations::register_function(lang('secure'),'secure');
    bulkcontentoperations::register_function(lang('insecure'),'insecure');
    bulkcontentoperations::register_function(lang('settemplate'),'settemplate');
    bulkcontentoperations::register_function(lang('changeowner'),'changeowner');
  }
  $opts = bulkcontentoperations::get_operation_list();
  $smarty->assign('bulk_options',$opts);
}

echo $this->ProcessTemplate('admin_pages_tab.tpl');
#
# EOF
#
?>
