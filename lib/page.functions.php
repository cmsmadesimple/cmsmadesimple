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
#
#$Id$

/**
 * Page related functions.  Generally these are functions not necessarily
 * related to content, but more to the underlying mechanisms of the system.
 *
 * @package CMS
 * @license GPL
 */


/**
 * Gets the userid of the currently logged in user.
 *
 * If an effective uid has been set in the session, AND the primary user is a member of the admin group
 * then allow emulating that effective uid.
 *
 * @since 0.1
 * @param  boolean $redirect Redirect to the admin login page if the user is not logged in.
 * @return integer The UID of the logged in administrator, otherwise FALSE
 */
function get_userid($redirect = true)
{
    if( cmsms()->is_cli() ) return 1;
    $login_ops = \CMSMS\LoginOperations::get_instance();
    $uid = $login_ops->get_effective_uid();
    if( !$uid && $redirect ) {
        $config = \cms_config::get_instance();
        redirect($config['admin_url'] . '/login.php');
    }
    return $uid;
}


/**
 * Gets the username of the currently logged in user.
 *
 * If an effective username has been set in the session, AND the primary user is a member of the admin group
 * then return the effective username.
 *
 * @since 2.0
 * @param  boolean $check Redirect to the admin login page if the user is not logged in.
 * @return string the username of the logged in user.
 */
function get_username($check = true)
{
    if( cmsms()->is_cli() ) return '';
    $login_ops = \CMSMS\LoginOperations::get_instance();
    $uname = $login_ops->get_effective_username();
    if( !$uname && $check ) {
        $config = \cms_config::get_instance();
        redirect($config['admin_url'] . '/login.php');
    }
    return $uname;
}


/**
 * Checks to see if the user is logged in and the request has the proper key.  If not, redirects the browser
 * to the admin login.
 *
 * Note: Because this method validates that the secret key is in the URL and matches the one that is in the session
 * this method should only be called from admin actions.
 *
 * @since 0.1
 * @param string $no_redirect If true, then don't redirect if not logged in
 * @return boolean
 */
function check_login($no_redirect = false)
{
    $do_redirect = !$no_redirect;
    $uid = get_userid(!$no_redirect);
    $res = false;
    if( $uid > 0 ) {
        $res = true;
        $login_ops = \CMSMS\LoginOperations::get_instance();
        $res = $login_ops->validate_requestkey();
    }
    if( !$res ) {
        // logged in, but no url key on the request
        if( $do_redirect ) {
            // redirect to the admin login.php
            // use SCRIPT_FILENAME and make sure it validates with the root_path
            $config = \cms_config::get_instance();
            if( startswith(realpath($_SERVER['SCRIPT_FILENAME']),$config['root_path']) ) {
                $_SESSION['login_redirect_to'] = $_SERVER['REQUEST_URI'];
            }
            $config = \cms_config::get_instance();
            redirect($config['admin_url'] . '/login.php');
        }
    }
    return TRUE;
}



/**
 * Checks to see that the given userid has access to the given permission.
 * Members of the admin group have all permissions.
 *
 * @since 0.1
 * @param int $userid The user id
 * @param string $permname The permission name
 * @return boolean
 */
function check_permission($userid, $permname)
{
    return UserOperations::get_instance()->CheckPermission($userid,$permname);
}


/**
 * Checks that the given userid is the owner of the given contentid.
 * (members of the admin group have all permission)
 *
 * @internal
 * @since 0.1
 * @param   integer  The User ID
 * @param   integer  The content id
 * @return  boolean
 */
function check_ownership($userid, $contentid = '')
{
    $userops = UserOperations::get_instance();
    $adminuser = $userops->UserInGroup($userid,1);
    if( $adminuser ) return true;

    return ContentOperations::get_instance()->CheckPageOwnership($userid,$contentid);
}


/**
 * Checks that the given userid has access to modify the given
 * pageid.  This would mean that they were set as additional
 * authors/editors by the owner.
 *
 * @internal
 * @since 0.2
 * @param  integer The admin user id
 * @param  integer A valid content id.
 * @return boolean
 */
function check_authorship($userid, $contentid = '')
{
    return ContentOperations::get_instance()->CheckPageAuthorship($userid,$contentid);
}


/**
 * Prepares an array with the list of the pages $userid is an author of
 *
 * @internal
 * @since 0.11
 * @param  integer The user id.
 * @return array   An array of pages this user is an author of.
 */
function author_pages($userid)
{
    return ContentOperations::get_instance()->GetPageAccessForUser($userid);
}


/**
 * Put an event into the audit (admin) log.  This should be
 * done on most admin events for consistency.
 *
 * @since 0.3
 * @param integer $itemid The item id (perhaps a content id, or a record id from a module)
 * @param string  $itemname The item name (perhaps Content, or the module name)
 * @param string  $action The action that needs to be audited
 * @return void
 */
function audit($itemid, $itemname, $action)
{
    if( !isset($action) ) $action = '-- unset --';
    $app = cmsms();
    $db = $app->GetDb();

    $userid = get_userid(FALSE);
    $username = get_username(FALSE);
    $ip_addr = null;
    if( $itemid == '' ) $itemid = -1;
    if( $userid < 1 ) $userid = 0;

    if( $userid > 0 && !$app->is_cli() ) $ip_addr = cms_utils::get_real_ip();

    $query = 'INSERT INTO ' . CMS_DB_PREFIX .
             'adminlog (timestamp, user_id, username, item_id, item_name, action, ip_addr) VALUES (?,?,?,?,?,?,?)';
    $db->Execute($query, [time(), $userid, $username, $itemid, $itemname, $action, $ip_addr]);
}


/**
 * Gets the given site prefernce
 *
 * @deprecated
 * @since 0.6
 * @see cms_siteprefs::get
 * @param string $prefname The preference name
 * @param mixed  $defaultvalue The default value if the preference does not exist
 * @return mixed
 */
function get_site_preference($prefname, $defaultvalue = '')
{
  return cms_siteprefs::get($prefname,$defaultvalue);
}


/**
 * Removes the given site preference
 *
 * @deprecated
 * @see cms_siteprefs::remove
 * @param string $prefname Preference name to remove
 * @param boolean $uselike Wether or not to remove all preferences that are LIKE the supplied name
 * @return void
 */
function remove_site_preference($prefname,$uselike=false)
{
  cms_siteprefs::remove($prefname,$uselike);
}


/**
 * Sets the given site perference with the given value.
 *
 * @deprecated
 * @see cms_siteprefs::set
 * @since 0.6
 * @param string $prefname The preference name
 * @param mixed  $value The preference value (will be stored as a string)
 * @return void
 */
function set_site_preference($prefname, $value)
{
  cms_siteprefs::set($prefname,$value);
}




/**
 * A method to create a text area control
 *
 * @internal
 * @access private
 * @param boolean $enablewysiwyg Wether or not we are enabling a wysiwyg.  If false, and forcewysiwyg is not empty then a syntax area is used.
 * @param string  $text The contents of the text area
 * @param string  $name The name of the text area
 * @param string  $classname An optional class name
 * @param string  $id An optional ID (HTML ID) value
 * @param string  $encoding The optional encoding
 * @param string  $stylesheet Optional style information
 * @param integer $width Width (the number of columns) (CSS can and will override this)
 * @param integer $height Height (the number of rows) (CSS can and will override this)
 * @param string  $forcewysiwyg Optional name of the syntax hilighter or wysiwyg to use.  If empty, preferences indicate which a syntax editor or wysiwyg should be used.
 * @param string  $wantedsyntax Optional name of the language used.  If non empty it indicates that a syntax highlihter will be used.
 * @param string  $addtext Optional additional text to include in the textarea tag
 * @return string
 * @deprecated
 * @see CmsFormUtils::create_textarea
 */
function create_textarea($enablewysiwyg, $text, $name, $classname = '', $id = '', $encoding = '', $stylesheet = '', $width = '80', $height = '15', $forcewysiwyg = '', $wantedsyntax = '', $addtext = '')
{
  $parms = [];
  $parms['enablewysiwyg'] = $enablewysiwyg;
  $parms['text'] = $text;
  $parms['name'] = $name;
  if( $classname ) $parms['class'] = $classname;
  if( $id ) $parms['id'] = $id;
  if( $encoding ) $parms['encoding'] = $encoding;
  if( $width ) $parms['rows'] = $height;
  if( $height ) $parms['cols'] = $width;
  if( $forcewysiwyg ) $parms['forcemodule'] = $forcewysiwyg;
  if( $wantedsyntax ) $parms['wantedsyntax'] = $wantedsyntax;
  if( $addtext ) $parms['addtext'] = $addtext;

  try {
    return CmsFormUtils::create_textarea($parms);
  }
  catch( CmsException $e ) {
    // do nothing.
    return '';
  }
}


/**
 * Creates a string containing links to all the pages.
 *
 * @deprecated
 * @internal
 * @ignore
 * @param page - the current page to display
 * @param totalrows - the amount of items being listed
 * @param limit - the amount of items to list per page
 * @return a string containing links to all the pages (ex. next 1,2 prev)
 */
function pagination($page, $totalrows, $limit)
{
   $urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];
   $page_string = "";
   $from = ($page * $limit) - $limit;
   $numofpages = (int)($totalrows / $limit);
   if( ($totalrows % $limit) != 0 ) ++$numofpages;
   if ($numofpages > 1) {
     if($page != 1) {
       $pageprev = $page-1;
       $page_string .= '<a href="'.$_SERVER['PHP_SELF'].$urlext.'&amp;page=1">'.lang('first').'</a>&nbsp;';
       $page_string .= "<a href=\"".$_SERVER['PHP_SELF'].$urlext."&amp;page=$pageprev\">".lang('previous')."</a>&nbsp;";
     }
     else {
       $page_string .= lang('first')." ";
       $page_string .= lang('previous')." ";
     }

     $page_string .= '&nbsp;'.lang('page')."&nbsp;$page&nbsp;".lang('of')."&nbsp;$numofpages&nbsp;";

     if(($totalrows - ($limit * $page)) > 0) {
       $pagenext = $page+1;
       $page_string .= "<a href=\"".$_SERVER['PHP_SELF'].$urlext."&amp;page=$pagenext\">".lang('next')."</a>&nbsp;";
       $page_string .= '<a href="'.$_SERVER['PHP_SELF'].$urlext.'&amp;page='.$numofpages.'">'.lang('last').'</a>';
     }
     else {
       $page_string .= lang('next')." ";
       $page_string .= lang('last')." ";
     }
   }
   return $page_string;
 }


/**
 * Create a dropdown form element containing a list of files that match certain conditions
 *
 * @internal
 * @param string The name for the select element.
 * @param string The directory name to search for files.
 * @param string The name of the file that should be selected
 * @param string A comma separated list of extensions that should be displayed in the list
 * @param string An optional string with which to prefix each value in the output by
 * @param boolean Wether 'none' should be an allowed option
 * @param string Text containing additional parameters for the dropdown element
 * @param string A prefix to use when filtering files
 * @param boolean A flag indicating wether the files matching the extension and the prefix should be included or excluded from the result set
 * @param boolean A flag indicating wether the output should be sorted.
 * @return string
 */
function create_file_dropdown($name,$dir,$value,$allowed_extensions,$optprefix='',$allownone=false,$extratext='',
			      $fileprefix='',$excludefiles=1,$sortresults = 0)
{
  $files = get_matching_files($dir,$allowed_extensions,true,true,$fileprefix,$excludefiles);
  if( $files === false ) return false;
  $out = "<select name=\"{$name}\" id=\"{$name}\" {$extratext}>\n";
  if( $allownone ) {
    $txt = '';
    if( empty($value) ) $txt = 'selected="selected"';
    $out .= "  <option value=\"-1\" $txt>--- ".lang('none')." ---</option>\n";
  }

  if( $sortresults ) natcasesort($files);
  foreach( $files as $file ) {
    $txt = '';
    $opt = $file;
    if( !empty($optprefix) ) $opt = $optprefix.'/'.$file;
    if( $opt == $value ) $txt = 'selected="selected"';
    $out .= "  <option value=\"{$opt}\" {$txt}>{$file}</option>\n";
  }
  $out .= "</select>";
  return $out;
}


/**
 * A function that, given the current request information will return
 * a pageid or an alias that should be used for the display
 * This method also handles matching routes and specifying which module
 * should be called with what parameters
 *
 * @internal
 * @ignore
 * @access private
 * @return string
 */
function get_pageid_or_alias_from_url()
{
    $gCms = CmsApp::get_instance();
    $config = \cms_config::get_instance();
    $contentops = ContentOperations::get_instance();
    $smarty = \Smarty_CMS::get_instance();

    $params = $_REQUEST;
    if (isset($params['mact'])) {
        $ary = explode(',', cms_htmlentities((string) $params['mact']), 4);
        $smarty->id = (isset($ary[1])?$ary[1]:'');
    }

    $page = '';
    $query_var = $config['query_var'];
    if (isset($smarty->id) && isset($params[$smarty->id . 'returnid'])) {
        // get page from returnid parameter in module action
        $page = (int)$params[$smarty->id . 'returnid'];
    }
    else if( isset($_REQUEST[$query_var]) ) {
        // using non friendly urls... get the page alias/id from the query var.
        $page = @trim((string) $_REQUEST[$query_var]);
    }
    else {
        // either we're using pretty urls
        // or this is the default page.
        if (isset($_SERVER["REQUEST_URI"]) && !endswith($_SERVER['REQUEST_URI'], 'index.php')) {
            $matches = [];
            if (preg_match('/.*index\.php\/(.*?)$/', $_SERVER['REQUEST_URI'], $matches)) {
                // pretty urls... grab all the stuff after the index.php
                $page = $matches[1];
            }
        }
    }

    unset($_GET['query_var']);

    // by here, if page is empty, use the default page id
    if ($page == '') $page = $contentops->GetDefaultContent(); // assume default content

    // by here, if we're not assuming pretty urls of any sort,
    // and we have a value... we're done.
    if( $config['url_rewriting'] == 'none' ) return $page;

    // some kind of a pretty url.
    // strip off GET params.
    if( ($tmp = strpos($page,'?')) !== FALSE ) $page = substr($page,0,$tmp);

    // strip off page extension
    if ($config['page_extension'] != '' && endswith($page, $config['page_extension'])) {
        $page = substr($page, 0, strlen($page) - strlen($config['page_extension']));
    }

    // trim trailing and leading /
    // it appears that some servers leave in the first / of a request some times which will stop rout matching.
    $page = trim($page, '/');

    // see if there's a route that matches.
    $matched = false;
    $route = cms_route_manager::find_match($page);
    if( is_object($route) ) {
        $matched = true;
        if( $route['key1'] == '__CONTENT__' ) {
            // a route to a page.
            $page = (int)$route['key2'];
        }
        else {
            $matches = $route->get_results();

            // it's a module route
            //Now setup some assumptions
            if (!isset($matches['id'])) $matches['id'] = 'cntnt01';
            if (!isset($matches['action'])) $matches['action'] = 'defaulturl';
            if (!isset($matches['inline'])) $matches['inline'] = 0;
            if (!isset($matches['returnid']))	$matches['returnid'] = ''; #Look for default page
            if (!isset($matches['module'])) $matches['module'] = $route->get_dest();

            //Get rid of numeric matches
            foreach ($matches as $key=>$val) {
                if (is_int($key)) {
                    unset($matches[$key]);
                }
                else {
                    if ($key != 'id') $_REQUEST[$matches['id'] . $key] = $val;
                }
            }

            //Now set any defaults that might not have been in the url
            $tmp = $route->get_defaults();
            if (is_array($tmp) && count($tmp) > 0) {
                foreach ($tmp as $key=>$val) {
                    $_REQUEST[$matches['id'] . $key] = $val;
                    if (array_key_exists($key, $matches)) $matches[$key] = $val;
                }
            }

            //Get a decent returnid
            if( $matches['returnid'] == '' ) $matches['returnid'] = $contentops->GetDefaultContent();

            // Put the resulting mact into the request so that the subsequent smarty plugins can grab it...
            $_REQUEST['mact'] = $matches['module'] . ',' . $matches['id'] . ',' . $matches['action'] . ',' . $matches['inline'];

            $page = $matches['returnid'];
            $smarty->id = $matches['id'];
        }
    }

    // if no route matched... grab the alias from the last /
    if( $matched == false && ($pos = strrpos($page,'/')) !== FALSE ) $page = substr($page, $pos + 1);

    // if there's nothing use the default content.
    if( empty($page) ) $page = $contentops->GetDefaultContent(); // maybe it's the home page.
    return $page;
}

/**
 * @ignore
 */
function preprocess_mact($returnid)
{
    if( \CMS_Content_Block::has_primary_content() ) return;
    $config = \cms_config::get_instance();
    if( !$config['startup_mact_processing'] ) return;
    if( !isset($_REQUEST['mact']) ) return;
    $tmp = explode(',',$_REQUEST['mact'],4);

    if( count($tmp) < 4) return;
    list($module,$id,$action,$inline) = $tmp;
    if( !$module || $inline || $id != 'cntnt01' ) return;

    $modops = ModuleOperations::get_instance();
    $module_obj = $modops->get_module_instance($module);
    if( !$module_obj ) {
        // module not found... couldn't even autoload it.
        @trigger_error('Attempt to access module '.$module.' which could not be found (is it properly installed and configured?');
        throw new \CmsError404Exception('Attempt to access module '.$module.' which could not be found (is it properly installed and configured?');
    }
    if( !$module_obj->IsPluginModule() ) {
        @trigger_error('Attempt to access module '.$module.' on a frontend request, which is not a plugin module');
        throw new \CmsError404Exception('Attempt to access module '.$module.' which could not be found (is it properly installed and configured?');
    }

    $smarty = \Smarty_CMS::get_instance();
    @ob_start();
    $parms = $modops->GetModuleParameters($id);
    $oldcache = $smarty->caching;
    $smarty->caching = false;
    $result = $module_obj->DoActionBase($action, $id, $parms, $returnid, $smarty);
    $smarty->caching = $oldcache;

    if( $result !== FALSE ) echo $result;
    $result = @ob_get_contents();
    @ob_end_clean();
    \CMS_Content_Block::set_primary_content($result);
}

/**
 * Replacement for deprecated strftime function
 * @since 2.2.16
 *
 * @param string $format strftime()- and/or date()-compatible format specifier
 * @param mixed $datevar timestamp | date-time-string | DateTime object | empty to use time() value
 * @return string
 */
function locale_ftime($format, $datevar = null)
{
    // hack to access shared code - do this properly in future!
    $fp = cms_join_path(CMS_ROOT_PATH, 'lib', 'plugins', 'modifier.localedate_format.php');   
    require_once $fp;
    return smarty_modifier_localedate_format($datevar, $format);
}
