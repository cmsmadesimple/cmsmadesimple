<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
#This project's homepage is: http://www.cmsmadesimple.org
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

require_once("../include.php");
$urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];

check_login();

$plugin = "";
if (isset($_GET["plugin"])) $plugin = cms_htmlentities($_GET["plugin"]);

$type = "";
if (isset($_GET["type"])) $type = cms_htmlentities($_GET["type"]);

$action = "";
if (isset($_GET["action"])) $action = cms_htmlentities($_GET["action"]);

$userid = get_userid();
$access = check_permission($userid, "View Tag Help");

if (!$access) 
  {
    die('Permission Denied');
    return;
  }

$config = cmsms()->GetConfig();

include_once("header.php");
$smarty = cmsms()->GetSmarty();
$smarty->assign('header',$themeObject->ShowHeader('tags'));
$smarty->assign('back_url'.$themeObject->BackURL());

if ($action == "showpluginhelp")
{
  $content = '';
  $file = $config['root_path']."/plugins/$type.$plugin.php";
  if( file_exists($file) )
    {
      require_once($file);
    }
  if( function_exists('smarty_cms_help_'.$type.'_'.$plugin) )
    {
      // Get and display the plugin's help
      @ob_start();
      call_user_func_array('smarty_cms_help_'.$type.'_'.$plugin, array());
      $content = @ob_get_contents();
      @ob_end_clean();
    }
  else if( CmsLangOperations::key_exists("help_{$type}_{$plugin}") )
    {
      $content = lang("help_{$type}_{$plugin}");
    }

  if( $content )
    {
      $smarty->assign('subheader',lang('pluginhelp',array($plugin)));
      
      $wikiUrl = $config['wiki_url'];
      $module_name = $plugin;
      // Turn ModuleName into _Module_Name
      $moduleName =  preg_replace('/([A-Z])/', "_$1", $module_name);
      $moduleName =  preg_replace('/_([A-Z])_/', "$1", $moduleName);
      if ($moduleName{0} == '_')
	{
	  $moduleName = substr($moduleName, 1);
	}
      $wikiUrl .= '/Tags/'.$moduleName;
      if (FALSE == get_preference($userid, 'hide_help_links'))
	{
	  // Clean up URL
	  $wikiUrl = str_replace(' ', '_', $wikiUrl);
	  $wikiUrl = str_replace('&amp;', 'and', $wikiUrl);
	  
	  //$image_help = $themeObject->DisplayImage('icons/system/info.gif', lang('help'),'','','systemicon');
	  $image_help_external = $themeObject->DisplayImage('icons/system/info-external.gif', lang('help'),'','','systemicon');		
	  $smarty->assign('wiki_url',$wikiUrl);
	  $smarty->assign('image_help_external',$image_help_external);
	}
      
      $smarty->assign('content',$content);
    }
  else
    {
      $smarty->assign('error',lang('nopluginhelp'));
    }
}
else if ($action == "showpluginabout")
{
  $file = $config['root_path']."/plugins/$type.$plugin.php";
  if( file_exists($file) )
    {
      require_once($file);
    }
  $smarty->assign('subheader',lang('pluginabout',$plugin));
  if (function_exists('smarty_cms_about_'.$type.'_'.$plugin))
    {
      @ob_start();
      call_user_func_array('smarty_cms_about_'.$type.'_'.$plugin, array());
      $content = @ob_get_contents();
      @ob_end_clean();
      $smarty->assign('content',$content);
    }
  else
    {
      $smarty->assign('error',lang('nopluginabout'));
    }
}
else
{
  $config = cmsms()->GetConfig();
  $files = glob($config['root_path'].'/plugins/*php');
  
  if( is_array($files) && count($files) )
    {
      $file_array = array();
      foreach($files as $onefile)
	{
	  $file = basename($onefile);
	  $parts = explode('.',$file);
	  if( !is_array($parts) || count($parts) != 3 ) continue;
	  
	  $rec = array();
	  $rec['type'] = $parts[0];
	  $rec['name'] = $parts[1];
	  include_once($onefile);
	  
	  if( !function_exists('smarty_'.$rec['type'].'_'.$rec['name']) ) continue;

	  if( function_exists("smarty_cms_help_".$rec['type']."_".$rec['name']) )
	    {
	      $rec['help_url'] = 'listtags.php'.$urlext.'&amp;action=showpluginhelp&amp;plugin='.$rec['name'].'&amp;type='.$rec['type'];
	    }
	  else if( CmsLangOperations::key_exists('help_'.$rec['type'].'_'.$rec['name']) )
	    {
	      $rec['help_url'] = 'listtags.php'.$urlext.'&amp;action=showpluginhelp&amp;plugin='.$rec['name'].'&amp;type='.$rec['type'];
	    }

	  if( function_exists("smarty_cms_about_".$rec['type']."_".$rec['name']) )
	    {
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

  $rec = array('type'=>'function','name'=>'process_pagedata');
  $rec['help_url'] = 'listtags.php'.$urlext.'&amp;action=showpluginhelp&amp;plugin='.$rec['name'].'&amp;type='.$rec['type'];
  $file_array[] = $rec;

  function listtags_plugin_sort($a,$b)
  {
    return strcmp($a['name'],$b['name']);
  }
  
  usort($file_array,'listtags_plugin_sort');
  
  $smarty->assign('plugins',$file_array);
}

$smarty->assign('back_url',$themeObject->BackURL());
echo $smarty->fetch('listtags.tpl');
include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
