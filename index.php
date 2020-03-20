<?php
#---------------------------------------------------------------------------
# CMS Made Simple - Power for the professional, Simplicity for the end user.
# (c) 2004 - 2011 by Ted Kulp
# (c) 2011 - 2018 by the CMS Made Simple Development Team
# (c) 2018 and beyond by the CMS Made Simple Foundation
# This project's homepage is: https://www.cmsmadesimple.org
#---------------------------------------------------------------------------
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
#---------------------------------------------------------------------------

$starttime = microtime();
$orig_memory = (function_exists('memory_get_usage')?memory_get_usage():0);

/**
 * Entry point for all non-admin pages
 *
 * @package CMS
 */

clearstatcache();

if (!isset($_SERVER['REQUEST_URI']) && isset($_SERVER['QUERY_STRING'])) $_SERVER['REQUEST_URI'] = $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'];
require_once(__DIR__.'/lib/include.php');

if (file_exists(TMP_CACHE_LOCATION.'/SITEDOWN')) {
    echo "<html><head><title>Maintenance</title></head><body><p>Site down for maintenance.</p></body></html>";
    exit;
}

if (!is_writable(TMP_TEMPLATES_C_LOCATION) || !is_writable(TMP_CACHE_LOCATION)) {
    echo '<html><title>Error</title></head><body>';
    echo '<p>The following directories must be writable by the web server:<br />';
    echo 'tmp/cache<br />';
    echo 'tmp/templates_c<br /></p>';
    echo '<p>Please correct by executing:<br /><em>chmod 777 tmp/cache<br />chmod 777 tmp/templates_c</em><br />or the equivalent for your platform before continuing.</p>';
    echo '</body></html>';
    exit;
}

@ob_start();

// initial setup
$_app = CmsApp::get_instance(); // internal use only, subject to change.
$params = array_merge($_GET, $_POST);
$smarty = $_app->GetSmarty();
$smarty->params = $params;
$page = get_pageid_or_alias_from_url();
$contentops = ContentOperations::get_instance();
$contentobj = null;
$trycount = 0;

cms_content_cache::get_instance();
$_tpl_cache = new CmsTemplateCache();

while( $trycount < 2 ) {
    $trycount++;
    try {
        // preview
        if( $page == -100) {
            setup_session(false);
            if( !isset($_SESSION['__cms_preview__']) ) throw new CmsException('preview selected, but temp data not found');

            // todo: get the content type, and load it.
            $contentops->LoadContentType($_SESSION['__cms_preview_type__']);
            $contentobj = unserialize($_SESSION['__cms_preview__']);
            $contentobj->SetCachable(FALSE);
            $contentobj->SetId(__CMS_PREVIEW_PAGE__);
        }
        else {
            $contentobj = $contentops->LoadContentFromAlias($page,true);
        }

        if( !is_object($contentobj) ) throw new CmsError404Exception('Page '.$page.' not found');

        // session stuff is needed from here on.
        $cachable = $contentobj->Cachable();
        $uid = get_userid(FALSE);
        if( $page == __CMS_PREVIEW_PAGE__ || $uid || $_SERVER['REQUEST_METHOD'] != 'GET' ) $cachable = false;
        setup_session($cachable);

        // from here in, we're assured to have a content object
        if( !$contentobj->IsViewable() ) {
            $url = $contentobj->GetURL();
            if( $url != '' && $url != '#' ) redirect($url);
            // not viewable, throw a 404.
            throw new CmsError404Exception('Cannot view an unviewable page');
        }

        if( $contentobj->Secure() && !$_app->is_https_request() ) {
	    $url = $contentobj->GetURL();
	    if( startswith($url,'http://') ) str_replace('http://','https://',$url);
	    if( startswith($url,'//') ) $url = 'https:'.$url;
            redirect($url); // if this page is marked to be secure, make sure we redirect to the secure page
        }

        if( !$contentobj->IsPermitted() ) throw new CmsError403Exception('Permission denied');

        $_app->set_content_object($contentobj);
        $smarty->assignGlobal('content_obj',$contentobj);
        $smarty->assignGlobal('content_id', $contentobj->Id());
        $smarty->assignGlobal('page_id', $page);
        $smarty->assignGlobal('page_alias', $contentobj->Alias());

        CmsNlsOperations::set_language(); // <- NLS detection for frontend
        $smarty->assignGlobal('lang',CmsNlsOperations::get_current_language());
        $smarty->assignGlobal('encoding',CmsNlsOperations::get_encoding());

        $html = '';
        $showtemplate = true;

        if ((isset($_REQUEST['showtemplate']) && $_REQUEST['showtemplate'] == 'false') ||
            (isset($smarty->id) && $smarty->id != '' && isset($_REQUEST[$smarty->id.'showtemplate']) &&
             $_REQUEST[$smarty->id.'showtemplate'] == 'false')) {
            $showtemplate = false;
        }

        $cache_id = 'p'.$contentobj->Id();
        $smarty->set_global_cacheid('p'.$contentobj->Id());
        if( $cachable && $showtemplate && $contentobj->Cachable() && cms_siteprefs::get('use_smartycache',0) ) {
            $smarty->setCaching(Smarty::CACHING_LIFETIME_CURRENT);
        }

        \CMSMS\HookManager::do_hook('Core::ContentPreRender', [ 'content' => &$contentobj ] );

        if( !$showtemplate ) {
            $smarty->setCaching(false);
            // in smarty 3, we could use eval:{content} I think
            $html = $smarty->fetch('cms_template:notemplate')."\n";
            $trycount = 99;
        }
        else {
            debug_buffer('process template top');
            $tpl_id = $contentobj->TemplateId();
            $top = $body = $head = null;

            \CMSMS\HookManager::do_hook('Core::PageTopPreRender', [ 'content'=>&$contentobj, 'html'=>&$top ]);
            $tpl = $smarty->createTemplate('tpl_top:'.$tpl_id,$cache_id);
            $top .= $tpl->fetch();
            unset($tpl);
            \CMSMS\HookManager::do_hook('Core::PageTopPostRender', [ 'content'=>&$contentobj, 'html'=>&$top ]);

            // if the request has a mact in it, process and cache the output.
            preprocess_mact($contentobj->Id());

            \CMSMS\HookManager::do_hook('Core::PageBodyPreRender', [ 'content'=>&$contentobj, 'html'=>&$body ]);
            $tpl = $smarty->createTemplate('tpl_body:'.$tpl_id,$cache_id);
            $body .= $tpl->fetch();
            unset($tpl);
            \CMSMS\HookManager::do_hook('Core::PageBodyPostRender', [ 'content'=>&$contentobj, 'html'=>&$body ]);

            \CMSMS\HookManager::do_hook('Core::PageHeadPreRender', [ 'content'=>&$contentobj, 'html'=>&$head ]);
            $tpl = $smarty->createTemplate('tpl_head:'.$tpl_id,$cache_id);
            $head .= $tpl->fetch();
            unset($tpl);
            \CMSMS\HookManager::do_hook('Core::PageHeadPostRender', [ 'content'=>&$contentobj, 'html'=>&$head ]);

            $html = $top.$head.$body;
            $trycount = 99; // no more iterations
        }
    }

    catch (CmsError404Exception $e) {
        // Catch CMSMS 404 error
        // 404 error thrown... gotta do this process all over again
        $page = 'error404';
        $showtemplate = true;
        unset($_REQUEST['mact']);
        unset($_REQUEST['module']);
        unset($_REQUEST['action']);
        $handlers = ob_list_handlers();
        for ($cnt = 0; $cnt < sizeof($handlers); $cnt++) { ob_end_clean(); }

        // specified page not found, load the 404 error page
        $contentobj = $contentops->LoadContentFromAlias('error404',true);
        if( is_object($contentobj) ) {
            // we have a 404 error page
            header("HTTP/1.0 404 Not Found");
            header("Status: 404 Not Found");
        }
        else {
            // no 404 error page
            @ob_end_clean();
            header("HTTP/1.0 404 Not Found");
            header("Status: 404 Not Found");
            echo '<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
	    <html><head>
	    <title>404 Not Found</title>
	    </head><body>
	    <h1>Not Found</h1>
	    <p>The requested URL was not found on this server.</p>
	    </body></html>';
            exit();
        }
    }

    catch (CmsError403Exception $e) // <- Catch CMSMS 403 error
    {
        //debug_display('handle 403 exception '.$e->getFile().' at '.$e->getLine().' -- '.$e->getMessage());
        // 404 error thrown... gotta do this process all over again.
        $page = 'error403';
        $showtemplate = true;
        unset($_REQUEST['mact']);
        unset($_REQUEST['module']);
        unset($_REQUEST['action']);
        $handlers = ob_list_handlers();
        for ($cnt = 0; $cnt < sizeof($handlers); $cnt++) { ob_end_clean(); }

        // specified page not found, load the 404 error page.
        $contentobj = $contentops->LoadContentFromAlias('error403',true);
        $msg = $e->GetMessage();
        if( !$msg ) $msg = '<p>We are sorry, but you do not have the appropriate permission to view this item.</p>';
        if( is_object($contentobj) ) {
            // we have a 403 error page.
            header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
            header("Cache-Control: no-store, no-cache, must-revalidate");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("HTTP/1.0 403 Forbidden");
            header("Status: 403 Forbidden");
        }
        else {
            @ob_end_clean();
            header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
            header("Cache-Control: no-store, no-cache, must-revalidate");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("HTTP/1.0 403 Forbidden");
            header("Status: 403 Forbidden");
            echo '<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html><head>
<title>403 Forbidden</title>
</head><body>
<h1>Forbidden</h1>'.$msg.'
</body></html>';
            exit();
        }
    }

    catch (Exception $e) {
        // Catch rest of exceptions
        $handlers = ob_list_handlers();
        for ($cnt = 0; $cnt < sizeof($handlers); $cnt++) { ob_end_clean(); }
        echo $smarty->errorConsole($e);
        exit();
    }
} // end while trycount

\CMSMS\HookManager::do_hook( 'Core::ContentPostRender', [ 'content' => &$html ] );

if( !headers_sent() ) {
    $ct = $_app->get_content_type();
    header("Content-Type: $ct; charset=" . CmsNlsOperations::get_encoding());
}
echo $html;

@ob_flush();

if( $page == __CMS_PREVIEW_PAGE__ && isset($_SESSION['__cms_preview__']) ) unset($_SESSION['__cms_preview__']);

$debug = (defined('CMS_DEBUG') && CMS_DEBUG)?TRUE:FALSE;
if( $debug || isset($config['log_performance_info']) || (isset($config['show_performance_info']) && ($showtemplate == true)) ) {
    $memory = (function_exists('memory_get_usage')?memory_get_usage():0);
    $memory = $memory - $orig_memory;
    $db = $_app->GetDb();
    $sql_time = round($db->query_time_total,5);
    $sql_queries = $db->query_count;
    $memory_peak = (function_exists('memory_get_peak_usage')?memory_get_peak_usage():'n/a');
    $endtime = microtime();
    $time = microtime_diff($starttime,$endtime);

    if( isset($config['log_performance_info']) ) {
        $out = [ time(), $_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD'], $time, $sql_time, $queries, $memory, $memory_peak ];
        $filename = TMP_CACHE_LOCATION.'/performance.log';
        error_log(implode('|',$out)."\n", 3, $filename);
    } else {
        $txt = "Time: $time / SQL: {$sql_time}s for $sql_queries queries / Net Memory: {$memory} / Peak: {$memory_peak}";
        echo '<div style="clear: both;"><pre><code>'.$txt.'</code></pre></div>';
    }
}

if( $debug || is_sitedown() ) $smarty->clear_compiled_tpl();
if ( $debug && !is_sitedown() ) {
    $arr = $_app->get_errors();
    foreach ($arr as $error) {
        echo $error;
    }
}

exit();

#
# EOF
#