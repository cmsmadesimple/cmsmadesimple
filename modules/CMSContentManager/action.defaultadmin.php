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
// no permissions checks here.

echo '<noscript><h3 style="color: red; text-align: center;">'.$this->Lang('info_javascript_required').'</h3></noscript>'."\n";
$error = '';

$builder = new ContentListBuilder($this);
$pagelimit = cms_userprefs::get($this->GetName().'_pagelimit',500);
$filter = cms_userprefs::get($this->GetName().'_userfilter');
if( $filter ) $filter = unserialize($filter);

$ajax = 0;
if( isset($params['ajax']) ) $ajax = 1;
if( isset($params['curpage']) ) {
    $curpage = max(1,min(500,(int)$params['curpage']));
}

if( isset($params['expandall']) || isset($_GET['expandall']) ) {
    $builder->expand_all();
    $curpage = 1;
}
else if( isset($params['collapseall']) || isset($_GET['collapseall']) ) {
    $builder->collapse_all();
    $curpage = 1;
}
if( isset($params['setoptions']) ) {
    $pagelimit = max(1,min(500,(int)$params['pagelimit']));
    cms_userprefs::set($this->GetName().'_pagelimit',$pagelimit);

    $filter = null;
    $filter_type = (isset($params['filter_type'])) ? $params['filter_type'] : null;
    switch( $filter_type ) {
    case ContentListFilter::EXPR_DESIGN:
        $filter = new ContentListFilter;
        $filter->type = ContentListFilter::EXPR_DESIGN;
        $filter->expr = $params['filter_design'];
        break;
    case ContentListFilter::EXPR_TEMPLATE:
        $filter = new ContentListFilter;
        $filter->type = ContentListFilter::EXPR_TEMPLATE;
        $filter->expr = $params['filter_template'];
        break;
    case ContentListFilter::EXPR_OWNER:
        $filter = new ContentListFilter;
        $filter->type = ContentListFilter::EXPR_OWNER;
        $filter->expr = $params['filter_owner'];
        break;
    case ContentListFilter::EXPR_EDITOR:
        $filter = new ContentListFilter;
        $filter->type = ContentListFilter::EXPR_EDITOR;
        $filter->expr = $params['filter_editor'];
        break;
    default:
        cms_userprefs::remove($this->GetName().'_userfilter');
    }
    if( $filter ) cms_userprefs::set($this->GetName().'_userfilter',serialize($filter));
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
    $res = $builder->set_active($params['setinactive'],FALSE);
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
    if( $res ) $error = $res; // @todo Rolf: ???
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

if( isset($curpage) ) $_SESSION[$this->GetName().'_curpage'] = $curpage; // for use by ajax_get_content

$url = $this->create_url($id,'ajax_get_content',$returnid);
$smarty->assign('ajax_get_content',str_replace('amp;','',$url));
$smarty->assign('ajax',$ajax);
$smarty->assign('can_add_content',$this->CheckPermission('Add Pages') || $this->CheckPermission('Manage All Content'));
$smarty->assign('can_manage_content',$this->CheckPermission('Manage All Content'));
$smarty->assign('admin_url',$config['admin_url']);
$smarty->assign('filter',$filter);
$locks = $builder->get_locks();
$have_locks = (is_array($locks) && count($locks))?1:0;
$smarty->assign('have_locks',$have_locks);
$pagelimits = array(10=>10,25=>25,100=>100,250=>250,500=>500);
$smarty->assign('pagelimits',$pagelimits);
$smarty->assign('pagelimit',$pagelimit);
$smarty->assign('locking',CmsContentManagerUtils::locking_enabled());
// get a list of admin users
$smarty->assign('user_list',UserOperations::get_instance()->GetList());
$smarty->assign('design_list',\CmsLayoutCollection::get_list());
// get a list of templates
$smarty->assign('template_list',CmsLayoutTemplate::template_query(array('as_list'=>1)));
// get a list of designs
if( $error ) $smarty->assign('error',$error);

$res = $this->ProcessTemplate('defaultadmin.tpl');
echo $res;

#
# EOF
#
?>