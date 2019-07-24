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

$this->SetCurrentTab('pages');
if( !$this->CheckPermission('Manage All Content') ) {
    $this->SetError($this->Lang('error_bulk_permission'));
    $this->RedirectToAdminTab();
}
if( !isset($params['multicontent']) ) {
    $this->SetError($this->Lang('error_missingparam'));
    $this->RedirectToAdminTab();
}
if( isset($params['cancel']) ) {
    $this->SetMessage($this->Lang('msg_cancelled'));
    $this->RedirectToAdminTab();
}

$hm = $gCms->GetHierarchyManager();
$pagelist = unserialize(base64_decode($params['multicontent']));

if( isset($params['submit']) ) {
    if( !isset($params['confirm1']) || !isset($params['confirm2']) ) {
        $this->SetError($this->Lang('error_notconfirmed'));
        $this->RedirectToAdminTab();
    }
    if( !isset($params['templatersrc']) ) {
        $this->SetError($this->Lang('error_missingparam'));
        $this->RedirectToAdminTab();
    }

    // do the real work
    try {
        @set_time_limit(9999);
        ContentOperations::get_instance()->LoadChildren(-1,FALSE,FALSE,$pagelist);
        $rsrc = filter_var( $params['templatersrc'], FILTER_SANITIZE_STRING );
        if( !$rsrc ) throw new \Exception( $this->Lang('error_missingparam') );
        if( (int)$rsrc > 0 && (int)$rsrc == $rsrc ) $rsrc = 'cms_template:'.$rsrc;

        $i = 0;
        foreach( $pagelist as $pid ) {
            $node = $hm->find_by_tag('id',$pid);
            if( !$node ) continue;
            $content = $node->getContent(FALSE,FALSE,TRUE);
            if( !is_object($content) ) continue;

            $content->SetPropertyValue('template_rsrc',$rsrc);
            $content->SetLastModifiedBy(get_userid());
            $this->cms->GetContentOperations()->save_content($content);
            $i++;
        }
        if( $i != count($pagelist) ) throw new CmsException('Bulk operation to set design did not adjust all selected pages');
        cms_notice('Changed template resource on '.$i.' pages', 'Content');
        $this->SetMessage($this->Lang('msg_bulk_successful'));
        $this->RedirectToAdminTab();
    }
    catch( Exception $e ) {
        cms_warning('Changing design and template on multiple pages failed: '.$e->GetMessage(), 'Content');
        $this->SetError($e->GetMessage());
        $this->RedirectToAdminTab();
    }
}

$list = $this->cms->get_page_template_list();
$page_template_list = null;
foreach( $list as $row ) {
    $page_template_list[$row['value']] = $row['label'];
}

$displaydata = array();
foreach( $pagelist as $pid ) {
    $node = $hm->find_by_tag('id',$pid);
    if( !$node ) continue;  // this should not happen, but hey.
    $content = $node->getContent(FALSE,FALSE,FALSE);
    if( !is_object($content) ) continue; // this should never happen either

    $rec = array();
    $rec['id'] = $content->Id();
    $rec['name'] = $content->Name();
    $rec['menutext'] = $content->MenuText();
    $rec['owner'] = $content->Owner();
    $rec['alias'] = $content->Alias();
    $displaydata[] = $rec;
}

$smarty->assign('multicontent',$params['multicontent']);
$smarty->assign('displaydata',$displaydata);
$smarty->assign('template_list',$page_template_list);

echo $this->ProcessTemplate('admin_bulk_settemplatersrc.tpl');

#
# EOF
#
