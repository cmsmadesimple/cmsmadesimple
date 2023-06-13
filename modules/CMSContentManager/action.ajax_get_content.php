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

$handlers = ob_list_handlers();
for ($cnt = 0; $cnt < sizeof($handlers); $cnt++) { ob_end_clean(); }

$opts = NULL;

try {
    $smarty->assign('can_add_content',$this->CheckPermission('Add Pages') || $this->CheckPermission('Manage All Content'));
    $smarty->assign('can_reorder_content',$this->CheckPermission('Manage All Content'));
    $smarty->assign('template_list',CmsLayoutTemplate::template_query(array('as_list'=>1))); // this is just to aide loading.

    // load all the content that this user can display...
    // organize it into a tree
    $builder = new ContentListBuilder($this);
    $curpage = (isset($_SESSION[$this->GetName().'_curpage']) && !isset($params['seek'])) ? (int) $_SESSION[$this->GetName().'_curpage'] : 1;
    if( isset($params['curpage']) ) $curpage = (int)$params['curpage'];
    $filter = cms_userprefs::get($this->GetName().'_userfilter');
    if( $filter ) {
        $filter = unserialize($filter);
        $builder->set_filter($filter);
    }
    $smarty->assign('have_filter',is_object($filter));


    //
    // handle all of the possible ajaxy/sub actions.
    //

    //
    // build the display
    //
    $smarty->assign('prettyurls_ok',$builder->pretty_urls_configured());

    if( isset($params['setoptions']) ) cms_userprefs::set($this->GetName().'_pagelimit',(int)$params['pagelimit']);
    $pagelimit = cms_userprefs::get($this->GetName().'_pagelimit',100);

    $builder->set_pagelimit($pagelimit);
    if( isset($params['seek']) && $params['seek'] != '' ) {
        $builder->seek_to((int)$params['seek']);
    }
    else {
        $builder->set_page($curpage);
    }

    $editinfo = $builder->get_content_list();
    $npages = $builder->get_numpages();
    $pagelist = array();
    for( $i = 0; $i < $npages; $i++ ) {
        $pagelist[$i+1] = $i+1;
    }

    $smarty->assign('indent',!$filter && cms_userprefs::get('indent',1));
    $locks = $builder->get_locks();
    $have_locks = (is_array($locks) && count($locks))?1:0;
    $smarty->assign('locking',CmsContentManagerUtils::locking_enabled());
    $smarty->assign('have_locks',$have_locks);
    $smarty->assign('pagelimit',$pagelimit);
    $smarty->assign('pagelist',$pagelist);
    $smarty->assign('curpage',$builder->get_page());
    $smarty->assign('npages',$npages);
    $smarty->assign('multiselect',$builder->supports_multiselect());
    $smarty->assign('columns',$builder->get_display_columns());
    $url = $this->create_url($id,'ajax_get_content',$returnid);
    $smarty->assign('ajax_get_content_url',str_replace('amp;','',$url));

    if( CmsContentManagerUtils::get_pagenav_display() == 'title' ) {
        $smarty->assign('colhdr_page',$this->Lang('colhdr_name'));
        $smarty->assign('coltitle_page',$this->Lang('coltitle_name'));
    }
    else {
        $smarty->assign('colhdr_page',$this->Lang('colhdr_menutext'));
        $smarty->assign('coltitle_page',$this->Lang('coltitle_menutext'));
    }
    if( $editinfo ) $smarty->assign('content_list',$editinfo);
    if( $filter && !$editinfo ) {
        $smarty->assign('error',$this->Lang('err_nomatchingcontent'));
    }


    if( $this->CheckPermission('Remove Pages') && $this->CheckPermission('Modify Any Page') ) {
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
    
  if( $opts ) $smarty->assign('bulk_options',$opts);
  
  $out = $this->ProcessTemplate('ajax_get_content.tpl'); // check: ok without $opts?

    $out = $this->ProcessTemplate('ajax_get_content.tpl');
    echo $out;
}
catch( \Exception $e ) {
    echo '<div class="red">'.$e->GetMessage().'</div>';
    debug_to_log($e);
}
exit;

#
# EOF
#
?>