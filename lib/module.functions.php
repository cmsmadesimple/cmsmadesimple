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
        // no id/prefix ... so lets generate one based on the params of this call.
        // it is reproducable... so same request will geneate the same idprefix.
        $mid_cache = cms_utils::get_app_data('mid_cache');
        if( empty($mid_cache) ) $mid_cache = [];
        $tmp = json_encode( $params );
        for( $i = 0; $i < 10; $i++ ) {
            $id = 'm'.substr(md5($tmp.__FILE__.$i),0,5);
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

    $mactinfo = $app->get_mact_encoder()->decode();
    if ($mactinfo) {
        // we're handling an action.  check if it is for this call.
        // we may be calling module plugins multiple times in the template,  but a POST or GET mact
        // can only be for one of them.
        $inline = false;
        if( 0 == strcasecmp($mactinfo->module, $modulename) && $id == $mactinfo->id && $mactinfo->inline) {
            $action = $mactinfo->action;
            $inline = $mactinfo->inline;
            // note: we mrege in mact params... but anything in the {cms_module} tag takes precidence
            // this also allows us to pass in other parameters and smarty variables on the module call
            // i.e:  {cms_module module=PressRoom foo=bar stuff=something}
            $params = array_merge($mactinfo->params, $params);
        }
    }

    // class_exists($modulename); // autoload?
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
