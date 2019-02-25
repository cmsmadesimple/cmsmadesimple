<?php
#CMS - CMS Made Simple
#(c)2004-2010 by Ted Kulp (wishy@users.sf.net)
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
#$Id$

/**
 * Extend smarty for moduleinterface.php
 *
 * @package CMS
 */


/**
 * A function to call a module as a smarty plugin
 * This method is used by the {cms_module} plugin, and internally when {ModuleName} is called
 *
 * @internal
 * @access private
 * @param array A hash of parameters
 * @param object The smarty template object
 * @return string The module output
 */
function cms_module_plugin($params,&$smarty)
{
    //if( get_class($smarty) == 'Smarty_Parser' ) return; // if we are in the parser, we don't process module calls.
    $app = cmsms();
    $modops = $app->GetModuleOperations();
    $modulename = '';
    $action = 'default';
    $inline = false;
    $returnid = $app->get_content_id();
    $id = null;
    if (isset($params['module'])) {
        $modulename = $params['module'];
        unset($params['module']);
    }
    else {
        return '<!-- ERROR: module name not specified -->';
    }

    // get a unique id/prefix for this modle call.
    if( isset( $params['idprefix']) ) {
        $id = $params['idprefix'];
        unset($params['idprefix']);
    } else {
        $mid_cache = cms_utils::get_app_data('mid_cache');
        if( empty($mid_cache) ) $mid_cache = [];
        $tmp = serialize( $params );
        for( $i = 0; $i < 10; $i++ ) {
            $id = 'm'.substr(md5($tmp.$i),0,5);
            if( !isset($mid_cache[$id]) ) {
                $mid_cache[$id] = $id;
                break;
            }
        }
        cms_utils::set_app_data('mid_cache',$mid_cache);
    }

    if (isset($params['action']) && $params['action'] != '') {
        // action was set in the module tag
        $action = $params['action'];
        unset( $params['action']);
    }

    if (isset($_REQUEST['mact'])) {
        // we're handling an action.  check if it is for this call.
        // we may be calling module plugins multiple times in the template,  but a POST or GET mact
        // canx only be for one of them.
        $checkid = null;
        $ary = explode(',', cms_htmlentities($_REQUEST['mact']), 4);
        $mactmodulename = (isset($ary[0])?$ary[0]:'');
        if( 0 == strcasecmp($mactmodulename,$modulename) ) {
            $checkid = (isset($ary[1])?$ary[1]:'');
            $mactaction = (isset($ary[2])?$ary[2]:'');
            $inline = (isset($ary[3]) && $ary[3] == 1?true:false);

            if ($checkid == $id && $inline == true ) {
                // the action is for this instance of the module and we're inline (the results are supposed to replace
                // the tag, not {content}
                $action = $mactaction;
                $params = array_merge($params, $modops->GetModuleParameters($id));
            }
        }
    }

    class_exists($modulename); // autoload? why
    $module = $modops->get_module_instance($modulename);
    global $CMS_ADMIN_PAGE, $CMS_LOGIN_PAGE, $CMS_INSTALL;
    if( $module && ($module->isPluginModule() || (isset($CMS_ADMIN_PAGE) && !isset($CMS_INSTALL) && !isset($CMS_LOGIN_PAGE) ) ) ) {
        @ob_start();
        $result = $module->DoActionBase($action, $id, $params, $returnid,$smarty);
        if ($result !== FALSE) echo $result;
        $modresult = @ob_get_contents();
        @ob_end_clean();

        if( isset($params['assign']) ) {
            $smarty->assign(trim($params['assign']),$modresult);
            return;
        }
        return $modresult;
    }
    else {
        return "<!-- $modulename is not a plugin module -->\n";
    }
} // module_plugin function
