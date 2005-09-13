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
		$this->compile_dir = TMP_TEMPLATES_C_LOCATION;
		$this->config_dir = $config["root_path"].'/tmp/configs/';
		$this->cache_dir = TMP_CACHE_LOCATION;
		#$this->plugins_dir = array($config["root_path"].'/lib/smarty/plugins/',$config["root_path"].'/plugins/',$config["root_path"].'/plugins/cache/');
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

		$this->register_resource("db", array(&$this, "template_get_template",
						       "template_get_timestamp",
						       "db_get_secure",
						       "db_get_trusted"));
		$this->register_resource("print", array(&$this, "db_get_template",
						       "db_get_timestamp",
						       "db_get_secure",
						       "db_get_trusted"));
		$this->register_resource("htmlblob", array(&$this, "html_blob_get_template",
						       "html_blob_get_timestamp",
						       "db_get_secure",
						       "db_get_trusted"));
		$this->register_resource("preview", array(&$this, "preview_get_template",
						       "preview_get_timestamp",
						       "db_get_secure",
						       "db_get_trusted"));
		$this->register_resource("content", array(&$this, "content_get_template",
						       "content_get_timestamp",
						       "db_get_secure",
						       "db_get_trusted"));
		$this->register_resource("template", array(&$this, "template_get_template",
						       "template_get_timestamp",
						       "db_get_secure",
						       "db_get_trusted"));
		$this->register_resource("module", array(&$this, "module_get_template",
						       "module_get_timestamp",
						       "db_get_secure",
						       "db_get_trusted"));
	}

	function html_blob_get_template($tpl_name, &$tpl_source, &$smarty_obj)
	{
		debug_buffer('start html_blob_get_template');
		global $gCms;

		$oneblob = HtmlBlobOperations::LoadHtmlBlobByName($tpl_name);
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

			$tpl_source = $text;
		}
		else
		{
			$tpl_source = "<!-- Html blob '" . $tpl_name . "' does not exist  -->";
		}
		debug_buffer('end html_blob_get_template');
		return true;
	}

	function html_blob_get_timestamp($tpl_name, &$tpl_timestamp, &$smarty_obj)
	{
		debug_buffer('start html_blob_get_timestamp');
		$oneblob = HtmlBlobOperations::LoadHtmlBlobByName($tpl_name);
		if ($oneblob)
		{
			$tpl_timestamp = $oneblob->modified_date;
			debug_buffer('end html_blob_get_timestamp');
			return true;
		}
		else
		{
			return false;
		}
	}

	function preview_get_template ($tpl_name, &$tpl_source, &$smarty_obj)
	{
		global $gCms;
		$config = $gCms->config;

		$fname = '';
		if (is_writable($config["previews_path"]))
		{
			$fname = $config["previews_path"] . "/" . $tpl_name;
		}
		else
		{
			$fname = TMP_CACHE_LOCATION . '/' . $tpl_name;
		}
		$handle = fopen($fname, "r");
		$data = unserialize(fread($handle, filesize($fname)));
		fclose($handle);
		unlink($fname);

		$tpl_source = $data["template"];

		#Perform the content template callback
		foreach($gCms->modules as $key=>$value)
		{
			if ($gCms->modules[$key]['installed'] == true &&
				$gCms->modules[$key]['active'] == true)
			{
				$gCms->modules[$key]['object']->ContentTemplate($tpl_source);
			}
		}

		$gCms->variables['page'] = $data['content_id'];
		$gCms->variables['page_id'] = $data['content_id'];
		$gCms->variables['content_id'] = $data['content_id'];
		$gCms->variables['page_name'] = $data['title'];
		$gCms->variables['position'] = $data['hierarchy'];

		header("Content-Type: text/html; charset=" . (isset($data['encoding']) && $data['encoding'] != ''?$data['encoding']:get_encoding()));

		$stylesheet = '';

		if (isset($data["stylesheet"]))
		{
			$stylesheet .= $data["stylesheet"];
		}
		
		#Perform the content stylesheet callback
		foreach($gCms->modules as $key=>$value)
		{
			$gCms->modules[$key]['object']->ContentStylesheet($stylesheet);
		}

		$stylesheet = "<style type=\"text/css\">{literal}\n".$stylesheet."{/literal}</style>\n";

		$tpl_source = ereg_replace("\{stylesheet\}", $stylesheet, $tpl_source);

		$content = $data["content"];

		#Perform the content data callback
		foreach($gCms->modules as $key=>$value)
		{
			if ($gCms->modules[$key]['installed'] == true &&
				$gCms->modules[$key]['active'] == true)
			{
				$gCms->modules[$key]['object']->ContentData($content);
			}
		}

		$tpl_source = eregi_replace("\{content\}", $content, $tpl_source);

		$title = $data['title'];
		$menutext = $data['menutext'];

		#Perform the content title callback
		foreach($gCms->modules as $key=>$value)
		{
			if ($gCms->modules[$key]['installed'] == true &&
				$gCms->modules[$key]['active'] == true)
			{
				$gCms->modules[$key]['object']->ContentTitle($title);
			}
		}

		$tpl_source = ereg_replace("\{title\}", $title, $tpl_source);
		$tpl_source = ereg_replace("\{menutext\}", $menutext, $tpl_source);

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

	function preview_get_timestamp($tpl_name, &$tpl_timestamp, &$smarty_obj)
	{
		$tpl_timestamp = time();
		return true;
	}

	function template_get_template($tpl_name, &$tpl_source, &$smarty_obj)
	{
		debug_buffer('start template_get_template');
		global $gCms;

		if (get_site_preference('enablesitedownmessage') == "1")
		{
			$tpl_source = get_site_preference('sitedownmessage');
			return true;
		}
		else
		{
			$pageinfo = $gCms->variables['pageinfo'];

			$gCms->variables['content_id'] = $pageinfo->content_id;
			$gCms->variables['page'] = $tpl_name;
			$gCms->variables['page_id'] = $tpl_name;

			$gCms->variables['page_name'] = $pageinfo->content_alias;
			$gCms->variables['position'] = $pageinfo->hierarchy;

			if (isset($_GET['id']) && isset($_GET[$_GET['id'].'showtemplate']) && $_GET[$_GET['id'].'showtemplate'] == 'false')
			{
				$tpl_source = '{content}';
				return true;
			}
			else
			{
				$templateobj = TemplateOperations::LoadTemplateByID($pageinfo->template_id);
				if (isset($templateobj) && $templateobj !== FALSE)
				{
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
					debug_buffer('end template_get_template');
					return true;
				}
			}
			return false;
		}
	}

	function template_get_timestamp($tpl_name, &$tpl_timestamp, &$smarty_obj)
	{
		debug_buffer('start template_get_timestamp');
		global $gCms;

		if (get_site_preference('enablesitedownmessage') == "1")
		{
			$tpl_timestamp = time();
			return true;
		}
		else if (isset($_GET['id']) && isset($_GET[$_GET['id'].'showtemplate']) && $_GET[$_GET['id'].'showtemplate'] == 'false')
		{
			$tpl_timestamp = time();
			return true;
		}
		else
		{
			$pageinfo = &$gCms->variables['pageinfo'];

			$tpl_timestamp = $pageinfo->template_modified_date;
			debug_buffer('end template_get_timestamp');
			return true;
		}
	}

	function content_get_template($tpl_name, &$tpl_source, &$smarty_obj)
	{
		debug_buffer('start content_get_template');
		global $gCms;

		$pageinfo = $gCms->variables['pageinfo'];

		$contentobj = ContentManager::LoadContentFromId($pageinfo->content_id);
		if (isset($contentobj) && $contentobj !== FALSE)
		{
			if ($tpl_name != 'content_en')
			{
				//TODO: Fix Me.  This is a super hack.
				$contentobj->GetAdditionalContentBlocks();
			}
			$tpl_source = $contentobj->GetPropertyValue($tpl_name);

			#Perform the content prerender callback
			foreach($gCms->modules as $key=>$value)
			{
				if ($gCms->modules[$key]['installed'] == true &&
					$gCms->modules[$key]['active'] == true)
				{
					$gCms->modules[$key]['object']->ContentPreRender($tpl_source);
				}
			}

			debug_buffer('end content_get_template');
			return true;
		}
		return false;
	}

	function content_get_timestamp($tpl_name, &$tpl_timestamp, &$smarty_obj)
	{
		debug_buffer('start content_get_timestamp');
		global $gCms;

		$pageinfo = &$gCms->variables['pageinfo'];

		$tpl_timestamp = $pageinfo->content_modified_date;
		debug_buffer('end content_get_timestamp');
		return true;
	}
	
	function module_get_template ($tpl_name, &$tpl_source, &$smarty_obj)
	{
		global $gCms;
		$pageinfo =& $gCms->variables['pageinfo'];

		#Run the execute_user function and replace {content} with it's output 
		if (isset($gCms->modules[$tpl_name]))
		{
			@ob_start();

			$id = $smarty_obj->id;

			$params = @ModuleOperations::GetModuleParameters($id);
			$action = 'default';
			if (isset($params['action']))
			{
				$action = $params['action'];
			}
			echo $gCms->modules[$tpl_name]['object']->DoActionBase($action, $id, $params, isset($pageinfo)?$pageinfo->content_id:'');
			$modoutput = @ob_get_contents();
			@ob_end_clean();

			$tpl_source = $modoutput;

			#Perform the content data callback
			foreach($gCms->modules as $key=>$value)
			{
				if ($gCms->modules[$key]['installed'] == true &&
					$gCms->modules[$key]['active'] == true)
				{
					$gCms->modules[$key]['object']->ContentData($modoutput);
				}
			}
		}

		#Perform the content prerender callback
		foreach($gCms->modules as $key=>$value)
		{
			if ($gCms->modules[$key]['installed'] == true &&
				$gCms->modules[$key]['active'] == true)
			{
				$gCms->modules[$key]['object']->ContentPreRender($tpl_source);
			}
		}
		
		header("Content-Type: ".$gCms->variables['content-type']."; charset=" . (isset($line['encoding']) && $line['encoding'] != ''?$line['encoding']:get_encoding()));
		if (isset($gCms->variables['content-filename']) && $gCms->variables['content-filename'] != '')
		{
			header('Content-Disposition: attachment; filename="'.$gCms->variables['content-filename'].'"');
			header("Pragma: public");
		}

		return true;
		/*
		global $gCms;
		$config = $gCms->config;
		$db = $gCms->db;
		$cmsmodules = $gCms->modules;
		$pageinfo = $gCms->variables['pageinfo'];

		if (isset($pageinfo) && $pageinfo !== FALSE)
		{
			$gCms->variables['content_id'] = $tpl_name;
			$gCms->variables['page'] = $tpl_name;
			$gCms->variables['page_id'] = $pageinfo->cotent_id;

			$gCms->variables['page_name'] = $pageinfo->content_name;

			#Set the title
			$title = $pageinfo->content_name;
			$menu_text = $pageinfo->menu_text;

			#Perform the content title callback
			foreach($gCms->modules as $key=>$value)
			{
				if ($gCms->modules[$key]['installed'] == true &&
					$gCms->modules[$key]['active'] == true)
				{
					$gCms->modules[$key]['object']->ContentTitle($title);
				}
			}

			#Setup the stylesheet inclusion
			$template_id = $pageinfo->template_id;
			#$stylesheet = '<link rel="stylesheet" type="text/css" href="stylesheet.php?templateid='.$template_id.'" />';

			if ($smarty_obj->showtemplate == true)
			{
				$template = TemplateOperations::LoadTemplateByID($pageinfo->template_id);
				$tpl_source = $template->content;

				#Perform the content template callback
				foreach($gCms->modules as $key=>$value)
				{
					if ($gCms->modules[$key]['installed'] == true &&
						$gCms->modules[$key]['active'] == true)
					{
						$gCms->modules[$key]['object']->ContentTemplate($tpl_source);
					}
				}

				$gCms->variables['position'] = $pageinfo->hierarchy;

				#$tpl_source = ereg_replace("\{stylesheet\}", $stylesheet, $tpl_source);
				#$tpl_source = ereg_replace("\{title\}", $title, $tpl_source);

				#So no one can do anything nasty
				if (!(isset($config["use_smarty_php_tags"]) && $config["use_smarty_php_tags"] == true)) {
					$tpl_source = ereg_replace("\{\/?php\}", "", $tpl_source);
				}
			}

			#Run the execute_user function and replace {content} with it's output 
			if (isset($gCms->modules[$smarty_obj->module]))
			{
				@ob_start();
				#call_user_func_array($gCms->modules[$smarty_obj->module]['execute_admin_function'], array($gCms,"module_".$module."_"));
                $usePassedID = true;

                foreach ($_REQUEST as $key=>$value)
				{
                    if (strpos($key, 'cntnt01') !== FALSE)
					{
                        $usePassedID = false;
					}
				}

				if ($usePassedID)
				{
                    $id = $smarty_obj->id;
				}
                else
				{
				    $id = 'cntnt01';
				}

				$params = @ModuleOperations::GetModuleParameters($id);
				echo $gCms->modules[$smarty_obj->module]['object']->DoActionBase((isset($_REQUEST[$id.'action'])?$_REQUEST[$id.'action']:'default'), $id, $params, $tpl_name);
				$modoutput = @ob_get_contents();
				@ob_end_clean();

				if ($smarty_obj->showtemplate == true)
				{
					#Perform the content data callback
					foreach($gCms->modules as $key=>$value)
					{
						if ($gCms->modules[$key]['installed'] == true &&
							$gCms->modules[$key]['active'] == true)
						{
							$gCms->modules[$key]['object']->ContentData($modoutput);
						}
					}

					$tpl_source = eregi_replace("\{content\}", $modoutput, $tpl_source);

					#In case any lingering tags are coming in from the content
					$tpl_source = ereg_replace("\{stylesheet\}", $stylesheet, $tpl_source);
					$tpl_source = ereg_replace("\{title\}", $title, $tpl_source);
					$tpl_source = ereg_replace("\{menu_text\}", $menu_text, $tpl_source);
				}
				else
				{
					$tpl_source = $modoutput;
				}
			}

			#Perform the content prerender callback
			foreach($gCms->modules as $key=>$value)
			{
				if ($gCms->modules[$key]['installed'] == true &&
					$gCms->modules[$key]['active'] == true)
				{
					$gCms->modules[$key]['object']->ContentPreRender($tpl_source);
				}
			}
			
			header("Content-Type: ".$gCms->variables['content-type']."; charset=" . (isset($line['encoding']) && $line['encoding'] != ''?$line['encoding']:get_encoding()));
			if (isset($gCms->variables['content-filename']) && $gCms->variables['content-filename'] != '')
			{
				header('Content-Disposition: attachment; filename="'.$gCms->variables['content-filename'].'"');
				header("Pragma: public");
			}

			return true;
		}
		else
		{
			return false;
		}
		*/
	}

	function module_get_timestamp($tpl_name, &$tpl_timestamp, &$smarty_obj)
	{
		$tpl_timestamp = time();
		return true;
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
			if ($contentobj === FALSE || $contentobj->Active() === FALSE)
			{
				if (get_site_preference('custom404template') > 0 && get_site_preference('enablecustom404') == "1")
				{
					#We don't cache error pages
					$this->caching = false;
					$this->force_compile = true;
					$templateobj = TemplateOperations::LoadTemplateById(get_site_preference('custom404template'));
					$template_id = get_site_preference('custom404template');
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
				$stylesheet = '';

				foreach (get_stylesheet_media_types($template_id) as $media)
				{
					$stylesheet .= '<link rel="stylesheet" type="text/css" ';
					if ($media != '')
					{
						$stylesheet .= 'media="'.$media.'" ';
					}
					$stylesheet .= 'href="'.$config['root_url'].'/stylesheet.php?templateid='.$template_id;
					if ($media != '')
					{
						$stylesheet .= '&amp;mediatype='.urlencode($media);
					}
					$stylesheet .= '" />'."\n"; 
				}

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
				$content = '';
				$head_tags = '';
				$header_script = '';

				if ($contentobj !== FALSE)
				{
					$gCms->variables['content_id'] = $contentobj->Id();
					$gCms->variables['page'] = $contentobj->Id();
					$gCms->variables['page_id'] = $contentobj->Id();

					$gCms->variables['page_name'] = $contentobj->Alias();
					$gCms->variables['position'] = $contentobj->Hierarchy();

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
					$menutext = $contentobj->MenuText();

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
				}

				#Pop the head tags in if they exist
				if (isset($head_tags) && $head_tags != "")
				{
					$tpl_source = ereg_replace("<\/head>", $head_tags."</head>", $tpl_source);
				}

				#Check to see if Show() actually gave us something
				if ($content != '')
				{

					#If it's regular content, do this...
					$tpl_source = eregi_replace("\{content\}", $content, $tpl_source);

					// now do prerender
					$tpl_source = $contentobj->ContentPreRender($tpl_source);

					if ($config["use_bb_code"] == true && isset($gCms->bbcodeparser))
					{
						$tpl_source = $gCms->bbcodeparser->qparse($tpl_source);
					}
					
				}
				else
				{
					$tpl_source = eregi_replace("\{content\}", get_site_preference('custom404'), $tpl_source);
					$tpl_source = eregi_replace("\{title\}", 'Page Not Found!', $tpl_source);

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

				#Do html_blobs (they're recursive now... but only 15 deep...  deal!)
				#$safetycount = 0;
				#$regexstr = "|\{html_blob name=[\'\"]?(.*?)[\'\"]?\}|i";
				#while (1 == 1)
				#{
				#	$result = preg_replace_callback($regexstr, "html_blob_regex_callback", $tpl_source);
				#	if ($result != '' && $result != $tpl_source)
				#	{
				#		$tpl_source = $result;
				#	}
				#	else
				#	{
				#		break;
				#	}
				#
				#	$safetycount++;
				#
				#	if ($safetycount > 15)
				#	{
				#		# Remove the last one so it doesn't get sent to smarty
				#		$tpl_source = preg_replace($regexstr, '', $tpl_source);
				#		break;
				#	}
				#}

				#Replace stylesheet and title tags
				$tpl_source = ereg_replace("\{stylesheet\}", $stylesheet, $tpl_source);
				$tpl_source = ereg_replace("\{title\}", $title, $tpl_source);
				$tpl_source = ereg_replace("\{menutext\}", $menutext, $tpl_source);

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
					header("Content-Type: ".$gCms->variables['content-type']."; charset=" . (isset($line['encoding']) && $line['encoding'] != ''?$line['encoding']:get_encoding()));
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

	#if (is_dir(dirname(dirname(__FILE__))."/plugins/cache"))
	#{
	#	search_plugins($smarty, $plugins, dirname(dirname(__FILE__))."/plugins/cache", true);
	#}
	search_plugins($smarty, $plugins, dirname(dirname(__FILE__))."/plugins", false);

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
					$smarty->register_function($row['userplugin_name'], $functionname, false);
				}
			}
		}
	}
	sort($plugins);
}

function search_plugins(&$smarty, &$plugins, $dir, $caching)
{
	$ls = dir($dir);
	while (($file = $ls->read()) != "")
	{
		if (is_file("$dir/$file") && (strpos($file, ".") === false || strpos($file, ".") != 0))
		{
			//Valid plugins will always have a 3 part filename
			$filearray = explode('.', $file);
			if (count($filearray == 3))
			{
				$filename = $dir . '/' . $file;
				//The part we care about is the middle one...
				$file = $filearray[1];
				if (strpos($filename, 'function') !== false && filesize($filename) > 50)
				{
					require_once $filename;
					if (function_exists("smarty_cms_function_" . $file))
					{
						$smarty->register_function($file, "smarty_cms_function_" . $file, $caching);
						array_push($plugins, $file);
					}
				}
				else if (strpos($filename, 'compiler') !== false)
				{
					require_once $filename;
					if (function_exists("smarty_cms_compiler_" . $file))
					{
						$smarty->register_compiler_function($file, "smarty_cms_compiler_" . $file, $caching);
						array_push($plugins, $file);
					}
				}
				else if (strpos($filename, 'prefilter') !== false)
				{
					require_once $filename;
					if (function_exists("smarty_cms_prefilter_" . $file))
					{
						$smarty->register_prefilter($file, "smarty_cms_prefilter_" . $file, $caching);
						array_push($plugins, $file);
					}
				}
				else if (strpos($filename, 'modifier') !== false)
				{
					require_once $filename;
					if (function_exists("smarty_cms_modifier_" . $file))
					{
						$smarty->register_modifier($file, "smarty_cms_modifier_" . $file, $caching);
						array_push($plugins, $file);
					}
				}
			}
		}
	}
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
