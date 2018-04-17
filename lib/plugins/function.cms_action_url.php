<?php
#CMS - CMS Made Simple
#(c)2013 by Robert Campbell (calguy1000@cmsmadesimple.org)
#Visit our homepage at: http://www.cmsmadesimple.org
#
#This program is free software; you can redistribute it and/or modify
#it under the terms of the GNU General Public License as published by
#the Free Software Foundation; either version 2 of the License, or
#(at your option) any later version.
#
#This program is distributed in the hope that it will be useful,
#but WITHOUT ANY WARRANTY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
#You should have received a copy of the GNU General Public License
#along with this program; if not, write to the Free Software
#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#

function smarty_cms_function_cms_action_url($params, &$smarty)
{
    $module = $smarty->getTemplateVars('actionmodule');
    $returnid = $smarty->getTemplateVars('returnid');
    $mid = $smarty->getTemplateVars('actionid');
    $action = null;
    $assign = null;
    $forjs  = 0;

    $actionparms = array();
    foreach( $params as $key => $value ) {
        switch( $key ) {
        case 'module':
            $module = trim($value);
            break;
        case 'action':
            $action = trim($value);
            break;
        case 'returnid':
            $returnid = (int)trim($value);
            break;
        case 'mid':
            $mid = trim($value);
            break;
        case 'assign':
            $assign = trim($value);
            break;
        case 'forjs':
            $forjs = 1;
            break;
        default:
            $actionparms[$key] = $value;
            break;
        }
    }

    // validate params
    $gCms = CmsApp::get_instance();
    if( $module == '' ) return;
    if( $gCms->test_state(CmsApp::STATE_ADMIN_PAGE) && $returnid == '' ) {
        if( $mid == '' ) $mid = 'm1_';
        if( $action == '' ) $action = 'defaultadmin';
    }
    else if( $gCms->is_frontend_request() ) {
        if( $mid == '' ) $mid = 'cntnt01';
        if( $action == '' ) $action = 'default';
        if( $returnid == '' ) {
	    $returnid = \cms_utils::get_current_pageid();
	    if( $returnid < 1 ) {
            	$contentops = $gCms->GetContentOperations();
            	$returnid = $contentops->GetDefaultContent();
	    }
        }
    }
    if( $action == '' ) return;

    $obj = cms_utils::get_module($module);
    if( !$obj ) return;

    $url = $obj->create_url($mid,$action,$returnid,$actionparms);
    if( !$url ) return;

    if( $forjs ) {
        $url = str_replace('&amp;','&',$url);
    }
    if( $assign ) {
        $smarty->assign($assign,$url);
        return;
    }
    return $url;
}

?>
