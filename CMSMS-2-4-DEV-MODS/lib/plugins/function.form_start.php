<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
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

function smarty_function_form_start($params, $smarty)
{
    $gCms = CmsApp::get_instance();
    $tagparms = array();
    $mactparms = array();
    $mactparms['module'] = $smarty->getTemplateVars('actionmodule');
    $mactparms['mid'] = $smarty->getTemplateVars('actionid');
    $mactparms['returnid'] = $smarty->getTemplateVars('returnid');
    $mactparms['inline'] = 0;

    $tagparms['method'] = 'post';
    $tagparms['enctype'] = 'multipart/form-data';
    if( $gCms->test_state(CmsApp::STATE_ADMIN_PAGE) ) {
        // check if it's a module action
        if( $mactparms['module'] ) {
            $tmp = $smarty->getTemplateVars('actionparams');
            if( is_array($tmp) && isset($tmp['action']) ) $mactparms['action'] = $tmp['action'];

            $tagparms['action'] = 'moduleinterface.php';
            if( !isset($mactparms['action']) ) $mactparms['action'] = 'defaultadmin';
            $mactparms['returnid'] = '';
            if( !$mactparms['mid'] ) $mactparms['mid'] = 'm1_';
        }
    }
    else if( $gCms->is_frontend_request() ) {
        if( $mactparms['module'] ) {
            $tmp = $smarty->getTemplateVars('actionparams');
            if( is_array($tmp) && isset($tmp['action']) ) $mactparms['action'] = $tmp['action'];

            $tagparms['action'] = 'moduleinterface.php';
            if( !$mactparms['returnid'] ) $mactparms['returnid'] = CmsApp::get_instance()->get_content_id();
            $hm = $gCms->GetHierarchyManager();
            $node = $hm->sureGetNodeById($mactparms['returnid']);
            if( $node ) {
                $content_obj = $node->getContent();
                if( $content_obj ) $tagparms['action'] = $content_obj->GetURL();
            }
        }
    }

    $parms = array();
    foreach( $params as $key => $value ) {
        switch( $key ) {
        case 'module':
        case 'action':
        case 'mid':
        case 'returnid':
        case 'inline':
            $mactparms[$key] = trim($value);
            break;

        case 'inline':
            $mactparms[$key] = (bool) $value;
            break;

        case 'prefix':
            $mactparms['mid'] = trim($value);
            break;

        case 'method':
            $tagparms[$key] = strtolower(trim($value));
            break;

        case 'url':
            $key = 'action';
            if( dirname($value) == '.' ) {
                $config = $gCms->GetConfig();
                $value = $config['admin_url'].'/'.trim($value);
            }
            $tagparms[$key] = trim($value);
            break;

        case 'enctype':
        case 'id':
        case 'class':
            $tagparms[$key] = trim($value);
            break;

        case 'extraparms':
            if( is_array($value) && count($value) ) {
                foreach( $value as $key=>$value2 ) {
                    $parms[$key] = $value2;
                }
            }
            break;

        case 'assign':
            break;

        default:
            if( startswith($key,'form-') ) {
                $key = substr($key,5);
                $tagparms[$key] = $value;
            } else {
                $parms[$key] = $value;
            }
            break;
        }
    }

    $out = '<form';
    foreach( $tagparms as $key => $value ) {
        if( $value ) {
            $out .= " $key=\"$value\"";
        } else {
            $out .= " $key";
        }
    }
    $out .= '><div class="hidden">';
    if( $mactparms['module'] && $mactparms['action'] ) {
        $mact = $mactparms['module'].','.$mactparms['mid'].','.$mactparms['action'].','.(int)$mactparms['inline'];
        $out .= '<input type="hidden" name="mact" value="'.$mact.'"/>';
        if( $mactparms['returnid'] != '' ) {
            $out .= '<input type="hidden" name="'.$mactparms['mid'].'returnid" value="'.$mactparms['returnid'].'"/>';
        }
    }
    if( !$gCms->is_frontend_request() ) {
        if( !isset($mactparms['returnid']) || $mactparms['returnid'] == '' ) {
            $out .= '<input type="hidden" name="'.CMS_SECURE_PARAM_NAME.'" value="'.$_SESSION[CMS_USER_KEY].'"/>';
        }
    }
    foreach( $parms as $key => $value ) {
        $out .= '<input type="hidden" name="'.$mactparms['mid'].$key.'" value="'.$value.'"/>'; // @todo Notice: Array to string conversion in /lib/plugins/function.form_start.php on line 141 -> $value
    }
    $out .= '</div>';

    if( isset($params['assign']) ) {
        $smarty->assign($params['assign'],$out);
        return;
    }
    return $out;
}
