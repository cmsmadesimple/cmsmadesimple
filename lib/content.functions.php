<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
#This project's homepage is: http://cmsmadesimple.sf.net
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
 * Handles content related functions
 *
 * @package CMS
 */
require_once(dirname(__FILE__) . '/smarty/Smarty.class.php');
require_once(dirname(__FILE__) . '/classes/class.htmlblob.inc.php');
require_once(dirname(__FILE__) . '/classes/class.template.inc.php');
$sorted_sections = array();
$sorted_content = array();

/**
 * Extends the Smarty class for content.
 *
 * Extends the Smarty class for checking timestamps and rendering
 * content to the browser.
 *
 * @since 0.1
 */
class Smarty_CMS extends Smarty {
	
	function Smarty_CMS(&$config)
	{
		$this->Smarty();

		$this->template_dir = $config["root_path"].'/tmp/templates/';
		$this->compile_dir = $config["root_path"].'/tmp/templates_c/';
		$this->config_dir = $config["root_path"].'/tmp/configs/';
		$this->cache_dir = $config["root_path"].'/tmp/cache/';
		$this->plugins_dir = array($config["root_path"].'/lib/smarty/plugins/',$config["root_path"].'/plugins/');

		$this->caching = true;
		$this->compile_check = true;
		$this->assign('app_name','CMS');
		$this->debugging = false;
		$this->force_compile = false;
		$this->cache_plugins = false;

		if ($config["debug"] == true)
		{
			$this->caching = false;
			$this->force_compile = true;
		}

		if (get_site_preference('enablesitedownmessage') == "1")
		{
			$this->caching = false;
			$this->force_compile = true;
		}

		load_plugins($this);

		$this->register_resource("db", array(&$this, "db_get_template",
						       "db_get_timestamp",
						       "db_get_secure",
						       "db_get_trusted"));
		$this->register_resource("print", array(&$this, "db_get_template",
						       "db_get_timestamp",
						       "db_get_secure",
						       "db_get_trusted"));
	}

	function db_get_template ($tpl_name, &$tpl_source, &$smarty_obj)
	{
		global $gCms;

		$cmsmodules = $gCms->modules;
		$db = $gCms->db;
		$config = $gCms->config;

		if (get_site_preference('enablesitedownmessage') == "1")
		{
			$tpl_source = get_site_preference('sitedownmessage');
			return true;
		}
		else
		{
			#Find valid content by id or alias
			$contentobj = ContentManager::LoadContentFromAlias($tpl_name, true);
			$templateobj = FALSE;

			#If the content object is false, then let's see if we should grab a template for a custom 404 error
			#If not, then let's see if there is a template for this content
			if ($contentobj === FALSE)
			{
				if (get_site_preference('custom404template') > 0 && get_site_preference('enablecustom404') == "1")
				{
					#We don't cache error pages
					$this->caching = false;
					$this->force_compile = true;
					$templateobj = TemplateOperations::LoadTemplateById(get_site_preference('custom404template'));
				}
			}
			else
			{
				#Grab template id and make sure it's actually "somewhat" valid
				$template_id = $contentobj->TemplateId();
				if (isset($template_id) && is_numeric($template_id) && $template_id > -1)
				{
					#Ok, it's valid, let's load the bugger
					$templateobj = TemplateOperations::LoadTemplateById($template_id);
				}
			}

			#If this fails, then it basically is a standard 404 error or a custom with no template
			if (!($contentobj === FALSE && $templateobj === FALSE))
			{
				$stylesheet = '<link rel="stylesheet" type="text/css" href="'.$config['root_url'].'/stylesheet.php?templateid='.$template_id.'" />'; 

				#Time to fill our template content
				#If it's in print mode, then just create a simple stupid template
				if (isset($_GET["print"]))
				{
					$tpl_source = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">'."\n".'<html><head><title>{title}</title>{stylesheet}{literal}<style type="text/css" media="print">#back {display: none;}</style>{/literal}</head><body style="background-color: white; color: black; background-image: none;"><form action="index.php?page='.$tpl_name.'" method="post"><input type="submit" value="Go Back"></form>{content}</body></html>';
				}
				else
				{
					$tpl_source = $templateobj->content;
				}

				#Perform the content template callback
				foreach($gCms->modules as $key=>$value)
				{
					if ($gCms->modules[$key]['installed'] == true &&
						$gCms->modules[$key]['active'] == true)
					{
						$gCms->modules[$key]['object']->ContentTemplate($tpl_source);
					}
				}

				#Fill some variables with various information
				$content = $contentobj->Show();

				#Perform the content data callback
				foreach($gCms->modules as $key=>$value)
				{
					if ($gCms->modules[$key]['installed'] == true &&
						$gCms->modules[$key]['active'] == true)
					{
						$gCms->modules[$key]['object']->ContentData($content);
					}
				}

				$title = $contentobj->Name();

				#Perform the content title callback
				foreach($gCms->modules as $key=>$value)
				{
					if ($gCms->modules[$key]['installed'] == true &&
						$gCms->modules[$key]['active'] == true)
					{
						$gCms->modules[$key]['object']->ContentTitle($title);
					}
				}

				$head_tags = $contentobj->mProperties->GetValue('headtags');
				$header_script = $contentobj->mProperties->GetValue('page_header');

				#Replace stylesheet and title tags
				$tpl_source = ereg_replace("\{stylesheet\}", $stylesheet, $tpl_source);
				$tpl_source = ereg_replace("\{title\}", $title, $tpl_source);

				#Pop the head tags in if they exist
				if (isset($head_tags) && $head_tags != "")
				{
					$tpl_source = ereg_replace("<\/head>", $head_tags."</head>", $tpl_source);
				}

				#Check to see if Show() actually gave us something
				if ($content != '')
				{

					#If it's regular content, do this...
					$tpl_source = ereg_replace("\{content\}", $content, $tpl_source);

					if ($config["use_bb_code"] == true && isset($gCms->bbcodeparser))
					{
						$tpl_source = $gCms->bbcodeparser->qparse($tpl_source);
					}
				}
				else
				{
					#If it's a module, do this instead...
					/*
					if (isset($cmsmodules[$line["page_type"]]['plugin_module'])
						&& $cmsmodules[$line["page_type"]]['Installed'] == true
						&& $cmsmodules[$line["page_type"]]['Active'] == true)
					{
						@ob_start();
						call_user_func_array($cmsmodules[$line["page_type"]]['execute_function'], array($gCms,"cmsmodule_".++$gCms->variables["modulenum"]."_",$params));
						$modoutput = @ob_get_contents();
						@ob_end_clean();
						$tpl_source = ereg_replace("\{content\}", $modoutput, $tpl_source);
					}
					else //Prolly a 404 message
					{
					*/
						$tpl_source = ereg_replace("\{title\}", 'Page Not Found!', $tpl_source);
						$tpl_source = ereg_replace("\{content\}", get_site_preference('custom404'), $tpl_source);
					//}

					if ($header_script && $header_script != '')
					{
						$tpl_source = $header_script.$tpl_source;
					}
				}

				#So no one can do anything nasty, take out the php smarty tags.  Use a user
				#defined plugin instead.
				if (!(isset($config["use_smarty_php_tags"]) && $config["use_smarty_php_tags"] == true))
				{
					$tpl_source = ereg_replace("\{\/?php\}", "", $tpl_source);
				}

				#Do html_blobs
				$tpl_source = preg_replace_callback("|\{html_blob name=[\'\"]?(.*?)[\'\"]?\}|", "html_blob_regex_callback", $tpl_source);

				#Perform the content prerender callback
				foreach($gCms->modules as $key=>$value)
				{
					if ($gCms->modules[$key]['installed'] == true &&
						$gCms->modules[$key]['active'] == true)
					{
						$gCms->modules[$key]['object']->ContentPreRender($tpl_source);
					}
				}

				return true;
			}
			else
			{
				if (get_site_preference('enablecustom404') == "1")
				{
					$tpl_source = get_site_preference('custom404');

					#Perform the content prerender callback
					foreach($gCms->modules as $key=>$value)
					{
						if ($gCms->modules[$key]['installed'] == true &&
							$gCms->modules[$key]['active'] == true)
						{
							$gCms->modules[$key]['object']->ContentPreRender($tpl_source);
						}
					}
					return true;	
				}
				else
				{
					return false;
				}
			}
		}
	}

	function db_get_timestamp($tpl_name, &$tpl_timestamp, &$smarty_obj)
	{
		global $gCms;
		$db = $gCms->db;
		$config = $gCms->config;

		if (get_site_preference('enablesitedownmessage') == "1")
		{
			$tpl_timestamp = time();
			return true;
		}
		else
		{
			if (is_numeric($tpl_name) && strpos($tpl_name,'.') === FALSE && strpos($tpl_name,',') === FALSE) //Fix for postgres
			{ 
				$query = "SELECT c.content_id, c.cachable, t.modified_date as template_date, c.modified_date as content_date, c.type, c.hierarchy, t.encoding FROM ".cms_db_prefix()."content c INNER JOIN ".cms_db_prefix()."templates t ON t.template_id = c.template_id WHERE (c.content_id = ".$tpl_name." OR c.content_alias=".$db->qstr($tpl_name).") AND c.active = 1";
			}
			else
			{
				$query = "SELECT c.content_id, c.cachable, t.modified_date as template_date, c.modified_date as content_date, c.type, c.hierarchy, t.encoding FROM ".cms_db_prefix()."content c INNER JOIN ".cms_db_prefix()."templates t ON t.template_id = c.template_id WHERE c.content_alias=".$db->qstr($tpl_name)." AND c.active = 1";
			}
			$result = $db->Execute($query);

			if ($result && $result->RowCount() > 0)
			{
				$line = $result->FetchRow();

				#This way the id is right, even if an alias is given
				$gCms->variables['content_id'] = $line['content_id'];
				$gCms->variables['page'] = $line['content_id'];
				$gCms->variables['page_id'] = $line['content_id'];

				$gCms->variables['page_name'] = $tpl_name;
				$gCms->variables['position'] = ContentManager::CreateFriendlyHierarchyPosition($line['hierarchy']);

				$content_date = $db->UnixTimeStamp($line['content_date']);
				$template_date = $db->UnixTimeStamp($line['template_date']);

				$smarty_obj->assign('modified_date',($content_date<$template_date?$template_date:$content_date));

				#We only want to cache "static" content
				if ($line['type'] == 'content')
				{
					header("Content-Type: text/html; charset=" . (isset($line['encoding']) && $line['encoding'] != ''?$line['encoding']:get_encoding()));
					if ($line['cachable'] == 1)
					{
						$tpl_timestamp = ($content_date<$template_date?$template_date:$content_date);
					}
					else
					{
						$tpl_timestamp = time();
					}
					return true;
				}
				else
				{
					$tpl_timestamp = time();
					return true;
				}
			}
			else
			{
				$smarty_obj->assign('modified_date',time());
				$tpl_timestamp = time();
				return true;
			}
		}
	}

	function db_get_secure($tpl_name, &$smarty_obj)
	{
		// assume all templates are secure
		return true;
	}

	function db_get_trusted($tpl_name, &$smarty_obj)
	{
		// not used for templates
	}
}

/**
 * Loads all plugins into the system
 *
 * @since 0.5
 */
function load_plugins(&$smarty)
{
	global $gCms;
	$plugins = &$gCms->cmsplugins;
	$userplugins = &$gCms->userplugins;
	$db = &$gCms->db;

	$dir = dirname(dirname(__FILE__))."/plugins";
	$ls = dir($dir);
	while (($file = $ls->read()) != "") {
		if (is_file("$dir/$file") && (strpos($file, ".") === false || strpos($file, ".") != 0)) {
			if (preg_match("/^(.*?)\.(.*?)\.php/", $file, $matches)) {
				$filename = $smarty->_get_plugin_filepath($matches[1], $matches[2]);
				if (strpos($filename, 'function') !== false)
				{
					require_once $filename;
					$smarty->register_function($matches[2], "smarty_cms_function_" . $matches[2], $smarty->cache_plugins);
					array_push($plugins, $matches[2]);
				}
				else if (strpos($filename, 'compiler') !== false)
				{
					require_once $filename;
					$smarty->register_compiler_function($matches[2], "smarty_cms_compiler_" . $matches[2], $smarty->cache_plugins);
					array_push($plugins, $matches[2]);
				}
				else if (strpos($filename, 'prefilter') !== false)
				{
					require_once $filename;
					$smarty->register_prefilter($matches[2], "smarty_cms_prefilter_" . $matches[2]);
					array_push($plugins, $matches[2]);
				}
				else if (strpos($filename, 'modifier') !== false)
				{
					require_once $filename;
					$smarty->register_modifier($matches[2], "smarty_cms_modifier_" . $matches[2]);
					array_push($plugins, $matches[2]);
				}
			}
		}
	}

	$query = "SELECT * FROM ".cms_db_prefix()."userplugins";
	$result = $db->Execute($query);
	if ($result && $result->RowCount() > 0)
	{
		while ($row = $result->FetchRow())
		{
			if (!in_array($row['userplugin_name'], $plugins))
			{
				array_push($plugins, $row['userplugin_name']);
				$userplugins[$row['userplugin_name']] = $row['userplugin_id'];
				$functionname = "cms_tmp_".$row['userplugin_name']."_userplugin_function";
				//Only register valid code
				if (!(@eval('function '.$functionname.'($params, &$smarty) {'.$row['code'].'}') === FALSE))
				{
					$smarty->register_function($row['userplugin_name'], $functionname, $smarty->cache_plugins);
				}
			}
		}
	}
	sort($plugins);
}

function html_blob_regex_callback($matches)
{
	global $gCms;
	if (isset($matches[1]))
	{
		$oneblob = HtmlBlobOperations::LoadHtmlBlobByName($matches[1]);
		if ($oneblob)
		{
			$text = $oneblob->content;

			#Perform the content htmlblob callback
			foreach($gCms->modules as $key=>$value)
			{
				if ($gCms->modules[$key]['installed'] == true &&
					$gCms->modules[$key]['active'] == true)
				{
					$gCms->modules[$key]['object']->ContentHtmlBlob($text);
				}
			}

			return $text;
		}
		else
		{
			return "<!-- Html blob '" . $matches[1] . "' does not exist  -->";
		}
	}
	else
	{
		return "<!-- Html blob has no name parameter -->";
	}
}

# vim:ts=4 sw=4 noet
?>
