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
function get_userid(bool $redirect = true)
{
    $app = cmsms();
    if( $app->is_cli() ) return 1;
    $login_ops = \CMSMS\LoginOperations::get_instance();
    $uid = $login_ops->get_effective_uid();
    if( !$uid && $redirect ) {
        $config = $app->GetConfig();
        redirect($config['admin_url']."/login.php");
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
function get_username(bool $check = true)
{
    $app = cmsms();
    if( $app->is_cli() ) return '';
    $login_ops = \CMSMS\LoginOperations::get_instance();
    $uname = $login_ops->get_effective_username();
    if( !$uname && $check ) {
        $config = $app->GetConfig();
        redirect($config['admin_url']."/login.php");
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
function check_login(bool $no_redirect = false)
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
            $config = cmsms()->GetConfig();
            if( startswith($_SERVER['SCRIPT_FILENAME'],$config['root_path']) ) {
                $_SESSION['login_redirect_to'] = $_SERVER['REQUEST_URI'];
            }
            $login_ops->deauthenticate();
            redirect($config['admin_url']."/login.php");
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
function check_permission(int $userid = null, string $permname)
{
    return UserOperations::get_instance()->CheckPermission($userid,$permname);
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
function check_authorship(int $userid = null, int $contentid = null)
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
function author_pages(int $userid = null)
{
    return ContentOperations::get_instance()->GetPageAccessForUser($userid);
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
function get_site_preference(string $prefname, $defaultvalue = null)
{
    return cms_siteprefs::get($prefname,$defaultvalue);
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
function create_textarea(bool $enablewysiwyg, string $text, string $name, string $classname = '',
                         string $id = '', string $encoding = '', string $stylesheet = '',
                         int $width = 80, int $height = 15,
                         string $forcewysiwyg = '', string $wantedsyntax = '', string $addtext = '')
{
    $parms = array();
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
 * A convenience function to test if the site is marked as down according to the config panel.
 * This method includes handling the preference that indicates that site-down behaviour should
 * be disabled for certain IP address ranges.
 *
 * @return boolean
 */
function is_sitedown()
{
    global $CMS_INSTALL_PAGE;
    if( isset($CMS_INSTALL_PAGE) ) return TRUE;

    if( cms_siteprefs::get('enablesitedownmessage') !== '1' ) return FALSE;

    $uid = get_userid(FALSE);
    if( $uid && cms_siteprefs::get('sitedownexcludeadmins') ) return FALSE;

    $ip = cms_utils::get_real_ip();
    if( !$ip ) return TRUE;
    $excludes = cms_siteprefs::get('sitedownexcludes','');
    if( empty($excludes) ) return TRUE;

    $tmp = explode(',',$excludes);
    $ret = cms_ipmatches($ip, $excludes);
    if( $ret ) return FALSE;
    return TRUE;
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
function create_file_dropdown(string $name,string $dir,string $value,string $allowed_extensions,string $optprefix='',
                              bool $allownone=false,string $extratext='',
                              string $fileprefix='',bool $excludefiles=true,bool $sortresults = false)
{
  $files = array();
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
 * This is the main routine to do route dispatching
 *
 * @internal
 * @ignore
 * @access private
 * @return string
 */
function get_pageid_or_alias_from_url()
{
    $gCms = cmsms();
    $config = $gCms->GetConfig();
    $contentops = $gCms->GetContentOperations();

    $page = '';
    $query_var = $config['query_var'];
    if( isset($_GET[$query_var]) ) {
        // using non friendly urls... get the page alias/id from the query var.
        $page = @trim((string) $_REQUEST[$query_var]);
    }
    else {
        // either we're using internal pretty urls or this is the default page.
        if (isset($_SERVER["REQUEST_URI"]) && !endswith($_SERVER['REQUEST_URI'], 'index.php')) {
            $matches = array();
            if (preg_match('/.*index\.php\/(.*?)$/', $_SERVER['REQUEST_URI'], $matches)) {
                // pretty urls... grab all the stuff after the index.php
                $page = trim($matches[1]);
            }
        }
    }
    unset($_GET['query_var']);

    $dflt_content = $contentops->GetDefaultContent();
    if( !$page ) {
        // by here, if page is empty, use the default page id
        return $dflt_content;
    }

    // by here, if we're not assuming pretty urls of any sort
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
    // note: we handle content routing separately at this time.
    // it'd be cool if contents were just another mact.
    $route = cms_route_manager::find_match($page);
    if( ! $route ) {
        // if no route matched... assume it is an alias and that the alias begins after the last /
        if( ($pos = strrpos($page,'/')) !== FALSE ) $page = substr($page, $pos + 1);
    }
    else {
        if( $route['key1'] == '__CONTENT__' ) {
            // a route to a page.
            $page = (int)$route['key2'];
        }
        else {
            $results = $route->get_results();

            // it's a module route... setup some default parameters.
            $orig = $matches = ['id'=>'cntnt01', 'action'=>'defaulturl', 'inline'=>false, 'module'=>$route->get_dest() ];
            $dflts = $route->get_defaults();
            if( $dflts ) $matches = array_merge( $matches, $dflts );
            if( $results ) $matches = array_merge( $matches, $results );

            // Get rid of numeric matches, and put the data into the _REQUEST for later processing.
            foreach ($matches as $key=>$val) {
                if ( is_int($key) || isset($orig[$key]) ) {
                    continue;
                }
                else if ($key != 'id' && $key != 'returnid' && $key != 'action') {
                    $_REQUEST[$matches['id'] . $key] = $val;
                }
            }

            // Put the resulting mact into the request for later processing.
            // this is essentially our translation from pretty URLs to non-pretty URLS.
            $_REQUEST['mact'] = $matches['module'] . ',' . $matches['id'] . ',' . $matches['action'] . ',' . (int) $matches['inline'];

            // Get a decent returnid
            $page = $dflt_content;
            if( $matches['returnid'] ) {
                $page = (int) $matches['returnid'];
                unset( $matches['returnid'] );
            } else {
                throw new \CmsException('Could not determine a content page id from matched route');
            }
        }
    }

    return $page;
}
