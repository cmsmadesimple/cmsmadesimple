<?php
#BEGIN_LICENSE
#-------------------------------------------------------------------------
# Module: Navigator (c) 2013 by Robert Campbell
#         (calguy1000@cmsmadesimple.org)
#  An module for CMS Made Simple to allow building hierarchical navigations.
#
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2005 by Ted Kulp (wishy@cmsmadesimple.org)
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
if( !defined('CMS_VERSION') ) exit;

debug_buffer('Start Navigator breadcrumbs action');

$template = null;
if( isset($params['template']) ) {
    $template = trim($params['template']);
}
else {
    $tpl = CmsLayoutTemplate::load_dflt_by_type('Navigator::breadcrumbs');
    if( !is_object($tpl) ) {
        audit('',$this->GetName(),'No default breadcrumbs template found');
        return;
    }
    $template = $tpl->get_name();
}

$cache_id = '|nav'.md5(serialize($params));
$tpl = $smarty->CreateTemplate($this->GetTemplateResource($template),$cache_id,null,$smarty);
if( !$tpl->isCached() ) {
    //
    // initialization
    //
    $hm = $gCms->GetHierarchyManager();
    $content_obj = $gCms->get_content_object();
    if( !$content_obj ) return; // no current page?
    $thispageid = $content_obj->Id();
    if( !$thispageid ) return; // no current page?
    $endNode = $hm->GetNodeById($thispageid);
    if( !$endNode ) return; // no current page?
    $starttext = $this->Lang('youarehere');
    if( isset($params['start_text']) ) $starttext = trim($params['start_text']);

    $deep = 1;
    $stopat = $this::__DFLT_PAGE;
    $showall = 0;
    if( isset($params['loadprops']) && $params['loadprops'] = 0 ) $deep = 0;
    if( isset($params['show_all']) && $params['show_all'] ) $showall = 1;
    if( isset($params['root']) ) $stopat = trim($params['root']);

    $pagestack = array();
    $curNode = $endNode;
    $have_stopnode = FALSE;

    while( is_object($curNode) && $curNode->get_tag('id') > 0 ) {
        $content = $curNode->getContent($deep,true,true);
        if( !$content ) {
            $curNode = $curNode->get_parent();
            break;
        }

        if( $content->Active() && ($showall || $content->ShowInMenu()) ) {
            $pagestack[$content->Id()] = Nav_utils::fill_node($curNode,$deep,-1,$showall);
        }
        if( $content->Alias() == $stopat || $content->Id() == (int) $stopat ) {
            $have_stopnode = TRUE;
            break;
        }
        $curNode = $curNode->get_parent();
    }

    // add in the 'default page'
    if( !$have_stopnode && $stopat == $this::__DFLT_PAGE ) {
        // get the 'home' page and push it on the list
        $dflt_content_id = ContentOperations::get_instance()->GetDefaultContent();
        $node = $hm->GetNodeById($dflt_content_id);
        $pagestack[$dflt_content_id] = Nav_utils::fill_node($node,$deep,0,$showall);
    }

    $tpl->assign('starttext',$starttext);
    $tpl->assign('nodelist',array_reverse($pagestack));
}
$tpl->display();
unset($tpl);

debug_buffer('End Navigator breadcrumbs action');


#
# EOF
#
?>
