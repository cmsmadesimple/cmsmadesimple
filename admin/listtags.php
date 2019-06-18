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

$CMS_ADMIN_PAGE=1;
$CMS_LOAD_ALL_PLUGINS=1;

require_once("../lib/include.php");
$urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];

check_login();

$plugin = "";
if (isset($_GET["plugin"])) $plugin = basename(cleanValue($_GET["plugin"]));

$type = "";
if (isset($_GET["type"])) $type = basename(cleanValue($_GET["type"]));

$action = "";
if (isset($_GET["action"])) $action = cleanValue($_GET["action"]);

$userid = get_userid();
$access = check_permission($userid, "View Tag Help");

if (!$access) {
    die('Permission Denied');
    return;
}

$dirs = [];
$dirs[] = $config['root_path'].'/assets/plugins';
$dirs[] = $config['root_path'].'/plugins';
$dirs[] = $config['root_path'].'/lib/plugins';
$dirs[] = $config['admin_path'].'/plugins';
$config = cmsms()->GetConfig();

$find_file = function($filename) use ($dirs) {
    $filename = basename($filename); // no sneaky paths
    foreach( $dirs as $dir ) {
        $fn = "$dir/$filename";
        if( is_file($fn) ) return $fn;
    }
};

include_once("header.php");
$smarty = cmsms()->GetSmarty();
$smarty->assign('header',$themeObject->ShowHeader('tags'));

if ($action == "showpluginhelp") {
    $content = '';
    $file = $find_file("$type.$plugin.php");
    if( is_file($file) ) require_once($file);

    if( function_exists('smarty_cms_help_'.$type.'_'.$plugin) ) {
        // Get and display the plugin's help
        @ob_start();
        call_user_func_array('smarty_cms_help_'.$type.'_'.$plugin, array());
        $content = @ob_get_contents();
        @ob_end_clean();
    }
    else if( CmsLangOperations::key_exists("help_{$type}_{$plugin}",'tags') ) {
        $content = CmsLangOperations::lang_from_realm('tags',"help_{$type}_{$plugin}");
    }
    else if( CmsLangOperations::key_exists("help_{$type}_{$plugin}") ) {
        $content = lang("help_{$type}_{$plugin}");
    }

    if( $content ) {
        $smarty->assign('subheader',lang('pluginhelp',array($plugin)));
        $smarty->assign('content',$content);
    }
    else {
        $smarty->assign('error',lang('nopluginhelp'));
    }
}
else if ($action == "showpluginabout") {
    $file = $find_file("$type.$plugin.php");
    if( file_exists($file) ) require_once($file);

    $smarty->assign('subheader',lang('pluginabout',$plugin));
    $func_name = 'smarty_cms_about_'.$type.'_'.$plugin;
    if (function_exists($func_name)) {
        @ob_start();
        call_user_func_array($func_name, array());
        $content = @ob_get_contents();
        @ob_end_clean();
        $smarty->assign('content',$content);
    }
    else {
        $smarty->assign('error',lang('nopluginabout'));
    }
}
else {
    $files = array();
    foreach( $dirs as $one ) {
        $files = array_merge($files,glob($one.'/*.php'));
    }

    if( is_array($files) && count($files) ) {
        $file_array = array();
        foreach($files as $onefile) {
            $file = basename($onefile);
            $parts = explode('.',$file);
            if( startswith($file,'prefilter.') || startswith($file,'postfilter.') ) continue;
            if( !is_array($parts) || count($parts) != 3 ) continue;

            $rec = array();
            $rec['type'] = $parts[0];
            $rec['name'] = $parts[1];
            $rec['admin'] = 0;
            if( startswith($onefile,$config['admin_path']) ) $rec['admin'] = 1;

            include_once($onefile);

            // leave samrty_nocache for compatibility for a while
            if( !function_exists('smarty_'.$rec['type'].'_'.$rec['name']) &&
            !function_exists('smarty_nocache_'.$rec['type'].'_'.$rec['name']) &&
                !function_exists('smarty_cms_'.$rec['type'].'_'.$rec['name']) ) continue;

            if( function_exists("smarty_cms_help_".$rec['type']."_".$rec['name']) ) {
                $rec['help_url'] = 'listtags.php'.$urlext.'&amp;action=showpluginhelp&amp;plugin='.$rec['name'].'&amp;type='.$rec['type'];
            }
            else if( CmsLangOperations::key_exists('help_'.$rec['type'].'_'.$rec['name'],'tags') ) {
                $rec['help_url'] = 'listtags.php'.$urlext.'&amp;action=showpluginhelp&amp;plugin='.$rec['name'].'&amp;type='.$rec['type'];
            }
            else if( CmsLangOperations::key_exists('help_'.$rec['type'].'_'.$rec['name']) ) {
                $rec['help_url'] = 'listtags.php'.$urlext.'&amp;action=showpluginhelp&amp;plugin='.$rec['name'].'&amp;type='.$rec['type'];
            }

            if( function_exists("smarty_cms_about_".$rec['type']."_".$rec['name']) ) {
                $rec['about_url'] = 'listtags.php'.$urlext.'&amp;action=showpluginabout&amp;plugin='.$rec['name'].'&amp;type='.$rec['type'];
            }

            $file_array[] = $rec;
        }
    }

    // add in standard tags...
    $rec = array('type'=>'function','name'=>'content');
    $rec['help_url'] = 'listtags.php'.$urlext.'&amp;action=showpluginhelp&amp;plugin='.$rec['name'].'&amp;type='.$rec['type'];
    $file_array[] = $rec;

    $rec = array('type'=>'function','name'=>'content_image');
    $rec['help_url'] = 'listtags.php'.$urlext.'&amp;action=showpluginhelp&amp;plugin='.$rec['name'].'&amp;type='.$rec['type'];
    $file_array[] = $rec;

    $rec = array('type'=>'function','name'=>'content_module');
    $rec['help_url'] = 'listtags.php'.$urlext.'&amp;action=showpluginhelp&amp;plugin='.$rec['name'].'&amp;type='.$rec['type'];
    $file_array[] = $rec;

    $rec = array('type'=>'function','name'=>'content_text');
    $rec['help_url'] = 'listtags.php'.$urlext.'&amp;action=showpluginhelp&amp;plugin='.$rec['name'].'&amp;type='.$rec['type'];
    $file_array[] = $rec;

    $rec = array('type'=>'function','name'=>'process_pagedata');
    $rec['help_url'] = 'listtags.php'.$urlext.'&amp;action=showpluginhelp&amp;plugin='.$rec['name'].'&amp;type='.$rec['type'];
    $file_array[] = $rec;

    $rec = array('type'=>'function','name'=>'cms_pagestr->render');
    $rec['help_url'] = 'listtags.php'.$urlext.'&amp;action=showpluginhelp&amp;plugin='.$rec['name'].'&amp;type='.$rec['type'];
    $file_array[] = $rec;

    $rec = array('type'=>'function','name'=>'cms_pagestr->set');
    $rec['help_url'] = 'listtags.php'.$urlext.'&amp;action=showpluginhelp&amp;plugin='.$rec['name'].'&amp;type='.$rec['type'];
    $file_array[] = $rec;

    $rec = array('type'=>'function','name'=>'cms_set_theme');
    $rec['help_url'] = 'listtags.php'.$urlext.'&amp;action=showpluginhelp&amp;plugin='.$rec['name'].'&amp;type='.$rec['type'];
    $file_array[] = $rec;

    $rec = array('type'=>'function','name'=>'cms_theme_path');
    $rec['help_url'] = 'listtags.php'.$urlext.'&amp;action=showpluginhelp&amp;plugin='.$rec['name'].'&amp;type='.$rec['type'];
    $file_array[] = $rec;

    $rec = array('type'=>'function','name'=>'cms_theme_url');
    $rec['help_url'] = 'listtags.php'.$urlext.'&amp;action=showpluginhelp&amp;plugin='.$rec['name'].'&amp;type='.$rec['type'];
    $file_array[] = $rec;

    function listtags_plugin_sort($a,$b)
    {
        return strcmp($a['name'],$b['name']);
    }

    usort($file_array,'listtags_plugin_sort');

    $smarty->assign('plugins',$file_array);
}

echo $smarty->fetch('listtags.tpl');
include_once("footer.php");
