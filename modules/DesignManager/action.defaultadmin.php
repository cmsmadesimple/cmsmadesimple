<?php
#-------------------------------------------------------------------------
# Module: DesignManager - A CMSMS addon module to provide template management.
# (c) 2014 by Robert Campbell <calguy1000@cmsmadesimple.org>
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

$filter_tpl_rec = array('tpl'=>'','limit'=>100,'offset'=>0,'sortby'=>'name','sortorder'=>'asc');
$filter_css_rec = array('limit'=>100,'offset'=>0,'sortby'=>'name','sortorder'=>'asc','design'=>'');
if( isset($params['submit_filter_tpl']) ) {
    if( $params['submit_filter_tpl'] == 1 ) {
        $filter_tpl_rec['tpl'] = $params['filter_tpl'];
        $filter_tpl_rec['sortby'] = trim($params['filter_sortby']);
        $filter_tpl_rec['sortorder'] = trim($params['filter_sortorder']);
        $filter_tpl_rec['limit'] = (int)$params['filter_limit_tpl'];
        $filter_tpl_rec['limit'] = max(2,min(100,$filter_tpl_rec['limit']));
    }
    unset($_SESSION[$this->GetName().'tpl_page']);
    cms_userprefs::set($this->GetName().'template_filter',serialize($filter_tpl_rec));
}
else if( isset($params['submit_filter_css']) ) {
    if( $params['submit_filter_css'] == 1 ) {
        $filter_css_rec['design'] = trim($params['filter_css_design']);
        $filter_css_rec['sortby'] = trim($params['filter_css_sortby']);
        $filter_css_rec['sortorder'] = trim($params['filter_css_sortorder']);
        $filter_css_rec['limit'] = max(2,min(100,(int)$params['filter_limit_css']));
    }
    $this->SetCurrentTab('stylesheets');
	unset($_SESSION[$this->GetName().'tpl_page']);
    cms_userprefs::set($this->GetName().'css_filter',serialize($filter_css_rec));
}
else if( isset($params['submit_create']) ) {
	$this->Redirect($id,'admin_edit_template',$returnid,array('import_type'=>$params['import_type']));
	return;
}
else if( isset($params['submit_bulk']) ) {
	$tmp = array('allparms'=>base64_encode(json_encode($params)));
	$this->Redirect($id,'admin_bulk_template',$returnid,$tmp);
}
else if( isset($params['submit_bulk_css']) ) {
	$tmp = array('allparms'=>base64_encode(json_encode($params)));
	$this->Redirect($id,'admin_bulk_css',$returnid,$tmp);
}
else if( isset($params['design_setdflt']) && $this->CheckPermission('Manage Designs') ) {
    $design_id = (int)$params['design_setdflt'];
    try {
        $cur_dflt = CmsLayoutCollection::load_default();
        if( is_object($cur_dflt) && $cur_dflt->get_id() != $design_id ) {
            $cur_dflt->set_default(false);
            $cur_dflt->save();
        }
    }
    catch( \Exception $e ) {
        // do nothing
    }

    $new_dflt = CmsLayoutCollection::load($design_id);
    $new_dflt->set_default(true);
    $new_dflt->save();

    $this->SetCurrentTab('designs');
    echo $this->ShowMessage($this->Lang('msg_dflt_design_saved'));
}

$tmp = cms_userprefs::get($this->GetName().'template_filter');
if( $tmp ) $filter_tpl_rec = unserialize($tmp);
if( isset($params['tpl_page']) ) {
	$this->SetCurrentTab('templates');
	$page = max(1,(int)$params['tpl_page']);
	$_SESSION[$this->GetName().'tpl_page'] = $page;
	$filter_tpl_rec['offset'] = ($page - 1) * $filter_tpl_rec['limit'];
} else if( isset($_SESSION[$this->GetName().'tpl_page']) ) {
	$page = max(1,(int)$_SESSION[$this->GetName().'tpl_page']);
	$filter_tpl_rec['offset'] = ($page - 1) * $filter_tpl_rec['limit'];
}

$efilter = $filter_tpl_rec;
if( isset($efilter['tpl']) && $efilter['tpl'] != '' ) {
	$efilter[] = $efilter['tpl'];
	unset($efilter['tpl']);
}

/*
$templates = null;
try {
    $tpl_query = new CmsLayoutTemplateQuery($efilter);
    $templates = $tpl_query->GetMatches();
}
catch( Exception $e ) {
    // nothing here
}
if( count($templates) ) {
	$smarty->assign('templates',$templates);
	$tpl_nav = array();
	$tpl_nav['pagelimit'] = $tpl_query->limit;
	$tpl_nav['numpages'] = $tpl_query->numpages;
	$tpl_nav['numrows'] = $tpl_query->totalrows;
	$tpl_nav['curpage'] = (int)($tpl_query->offset / $tpl_query->limit) + 1;
	$smarty->assign('tpl_nav',$tpl_nav);
}
*/

// build a list of the types, and categories, and later (designs).
$opts = array();
$opts[''] = $this->Lang('prompt_none');
$types = CmsLayoutTemplateType::get_all();
$originators = array();
if( $types && count($types) ) {
    $tmp = $tmp2 = $tmp3 = [];
    for( $i = 0; $i < count($types); $i++ ) {
        $tmp['t:'.$types[$i]->get_id()] = $types[$i]->get_langified_display_value();
        $tmp2[$types[$i]->get_id()] = $types[$i]->get_langified_display_value();
        $tmp3[$types[$i]->get_id()] = $types[$i];
		if( !isset($originators[$types[$i]->get_originator()]) ) {
			$originators['o:'.$types[$i]->get_originator()] = $types[$i]->get_originator(TRUE);
		}
    }
    usort($tmp3,function($a,$b){
            // core always beets alphabetic type
            // then sort by originator and then name.
            $ao = $a->get_originator();
            $bo = $b->get_originator();
            if( $ao == $a::CORE && $bo ==  $a::CORE ) return strcasecmp($a->get_name(),$b->get_name());
            if( $ao == $a::CORE ) return -1;
            if( $bo == $b::CORE ) return 1;
            return strcasecmp($a->get_langified_display_value(),$b->get_langified_display_value());
        });
    asort($tmp);
    asort($tmp2);
    asort($originators);
    $smarty->assign('list_all_types',$tmp3);
    $smarty->assign('list_types',$tmp2);
    $opts[$this->Lang('tpl_types')] = $tmp;
	$opts[$this->Lang('tpl_originators')] = $originators;
}
$cats = CmsLayoutTemplateCategory::get_all();
if( $cats && count($cats) ) {
    $smarty->assign('list_categories',$cats);
    $tmp = array();
    for( $i = 0; $i < count($cats); $i++ ) {
        $tmp['c:'.$cats[$i]->get_id()] = $cats[$i]->get_name();
    }
    $opts[$this->Lang('prompt_categories')] = $tmp;
}
$designs = CmsLayoutCollection::get_all();
if( $designs && count($designs) ) {
    $smarty->assign('list_designs',$designs);
    $tmp = array();
    for( $i = 0; $i < count($designs); $i++ ) {
        $tmp['d:'.$designs[$i]->get_id()] = $designs[$i]->get_name();
        $tmp2[$designs[$i]->get_id()] = $designs[$i]->get_name();
    }
    asort($tmp);
    asort($tmp2);
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
    asort($tmp);
    asort($users);
    $smarty->assign('list_users',$users);
    $opts[$this->Lang('prompt_user')] = $tmp;
}

if( $this->CheckPermission('Manage Stylesheets') ) {
	$tmp = cms_userprefs::get($this->GetName().'css_filter');
	if( $tmp ) $filter_css_rec = unserialize($tmp);
	if( isset($params['css_page']) ) {
		$this->SetCurrentTab('stylesheets');
		$page = max(1,(int)$params['css_page']);
		$_SESSION[$this->GetName().'css_page'] = $page;
		$filter_css_rec['offset'] = ($page - 1) * $filter_css_rec['limit'];
	} else if( isset($_SESSION[$this->GetName().'css_page']) ) {
		$page = max(1,(int)$_SESSION[$this->GetName().'css_page']);
		$filter_css_rec['offset'] = ($page - 1) * $filter_css_rec['limit'];
	}
}

// give everything to smarty that we can.
$smarty->assign('filter_tpl_options',$opts);
$smarty->assign('tpl_filter',$filter_tpl_rec); // used for filter form
$smarty->assign('css_filter',$filter_css_rec); // used for filter form
$smarty->assign('jsoncssfilter',json_encode($filter_css_rec)); // used for ajaxy stuff
$smarty->assign('jsonfilter',json_encode($efilter)); // used for ajaxy stuff.

$smarty->assign('has_add_right',
                $this->CheckPermission('Modify Templates') ||
                $this->CheckPermission('Add Templates'));
$smarty->assign('coretypename',CmsLayoutTemplateType::CORE);
$smarty->assign('manage_stylesheets',$this->CheckPermission('Manage Stylesheets'));
$smarty->assign('manage_templates',$this->CheckPermission('Modify Templates'));
$smarty->assign('manage_designs',$this->CheckPermission('Manage Designs'));
$smarty->assign('import_url',$this->create_url($id,'admin_import_template'));
$smarty->assign('admin_url',$config['admin_url']);
$smarty->assign('lock_timeout', $this->GetPreference('lock_timeout'));
$url = $this->create_url($id,'ajax_get_templates');
$smarty->assign('ajax_templates_url',str_replace('amp;','',$url));
$url = $this->create_url($id,'ajax_get_stylesheets');
$smarty->assign('ajax_stylesheets_url',str_replace('amp;','',$url));

echo $this->ProcessTemplate('defaultadmin.tpl');

#
# EOF
#
?>
