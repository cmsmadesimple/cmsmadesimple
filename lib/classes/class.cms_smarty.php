<?php

/**
 * Extends the Smarty class for content.
 *
 * Extends the Smarty class for checking timestamps and rendering
 * content to the browser.
 *
 * @since 0.1
 */
require_once(cms_join_path(dirname(dirname(__FILE__)),'smarty','Smarty.class.php'));

class CmsSmarty extends Smarty {
	
	static private $instance = NULL;
	
	function __construct()
	{
		parent::__construct();

		$config = cms_config();
		
		$this->template_dir = $config["root_path"].'/tmp/templates/';
		if (isset($GLOBALS['CMS_ADMIN_PAGE'])) {
			$this->template_dir = $config["root_path"].'/admin/templates/';
		}
		$this->compile_dir = TMP_TEMPLATES_C_LOCATION;
		$this->config_dir = $config["root_path"].'/tmp/configs/';
		$this->cache_dir = TMP_CACHE_LOCATION;
		#$this->plugins_dir = array($config["root_path"].'/lib/smarty/plugins/',$config["root_path"].'/plugins/',$config["root_path"].'/plugins/cache/');
		$this->plugins_dir = array($config["root_path"].'/lib/smarty/plugins/', $config["root_path"].'/plugins/', $config["root_path"].'/lib/module_plugins/');
		if (isset($GLOBALS['CMS_ADMIN_PAGE'])) {
			$this->plugins_dir[] = $config['root_path'] . '/admin/plugins/';
		}

		$this->compiler_file = 'CMS_Compiler.class.php';
		$this->compiler_class = 'CMS_Compiler';

		//use_sub_dirs doesn't work in safe mode
		//if (ini_get("safe_mode") != "1")
		//	$this->use_sub_dirs = true;
		//$this->caching = false;
		//$this->compile_check = true;
		$this->assign('app_name','CMS');
		//$this->debugging = false;
		//$this->force_compile = true;
		$this->cache_plugins = false;
		
		#Setup the site name
		$this->assign('sitename', get_site_preference('sitename', 'CMSMS Site'));

		if ($config["debug"] == true)
		{
			//$this->caching = false;
			$this->force_compile = true;
			$this->debugging = true;
		}

		if (get_site_preference('enablesitedownmessage') == "1")
		{
			$this->caching = false;
			$this->force_compile = true;
		}

		global $CMS_ADMIN_PAGE;
		if (isset($CMS_ADMIN_PAGE) && $CMS_ADMIN_PAGE == 1)
		{
			$this->caching = false;
			$this->force_compile = true;
		}

		$this->load_plugins();

		$this->register_resource("db", array(&$this, "template_get_template",
						       "template_get_timestamp",
						       "db_get_secure",
						       "db_get_trusted"));
		$this->register_resource("print", array(&$this, "template_get_template",
						       "template_get_timestamp",
						       "db_get_secure",
						       "db_get_trusted"));
		$this->register_resource("template", array(&$this, "template_get_template",
						       "template_get_timestamp",
						       "db_get_secure",
						       "db_get_trusted"));
		$this->register_resource("htmlblob", array(&$this, "global_content_get_template",
						       "global_content_get_timestamp",
						       "db_get_secure",
						       "db_get_trusted"));
		$this->register_resource("globalcontent", array(&$this, "global_content_get_template",
						       "global_content_get_timestamp",
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
		$this->register_resource("module", array(&$this, "module_get_template",
						       "module_get_timestamp",
						       "db_get_secure",
						       "db_get_trusted"));
		$this->register_resource("module_db_tpl", array(&$this, "module_db_template",
						       "module_db_timestamp",
						       "db_get_secure",
						       "db_get_trusted"));
		$this->register_resource("module_file_tpl", array(&$this, "module_file_template",
						       "module_file_timestamp",
						       "db_get_secure",
						       "db_get_trusted"));
	}
	
	static public function get_instance()
	{
		if (self::$instance == NULL)
		{
			self::$instance = new CmsSmarty();
		}
		return self::$instance;
	}
	
	/**
	 * Sets the internal id based on variables sent in from
	 * the request.
	 *
	 * @return void
	 * @author Ted Kulp
	 **/
	public function set_id_from_request()
	{
		if (isset($_REQUEST['mact']))
		{
			$ary = explode(',', $_REQUEST['mact'], 4);
			$this->id = (isset($ary[1])?$ary[1]:'');
		}
		else
		{
			$this->id = (isset($_REQUEST['id'])?$_REQUEST['id']:'');
		}
	}

    /**
     * wrapper for include() retaining $this
     * @return mixed
     */
    function _include($filename, $once=false, $params=null)
    {
        if ($filename != '')
        {
			if ($once) {
				return include_once($filename);
			} else {
				return include($filename);
			}
        }
    }

    function trigger_error($error_msg, $error_type = E_USER_WARNING)
    {   
        var_dump("Smarty error: $error_msg");
    }

	function module_file_template($tpl_name, &$tpl_source, &$smarty_obj)
    {
        $params = split(';', $tpl_name);
        if (count($params) == 2 && file_exists(dirname(dirname(dirname(__FILE__))) . '/modules/' . $params[0] . '/templates/' . $params[1]))
        {   
            $tpl_source = @file_get_contents(dirname(dirname(dirname(__FILE__))) . '/modules/' . $params[0] . '/templates/' . $params[1]);
            return true;
        }
        return false;
    }

	function module_file_timestamp($tpl_name, &$tpl_timestamp, &$smarty_obj)
	{
		$params = split(';', $tpl_name);
		if (count($params) == 2 && file_exists(dirname(dirname(dirname(__FILE__))) . '/modules/' . $params[0] . '/templates/' . $params[1]))
		{
			$tpl_timestamp = filemtime(dirname(dirname(dirname(__FILE__))) . '/modules/' . $params[0] . '/templates/' . $params[1]);
			return true;
		}
		return false;
	}

    function module_db_template($tpl_name, &$tpl_source, &$smarty_obj)
    {   
        global $gCms;

        $db = &$gCms->GetDb();
        $config = $gCms->config;

        $query = "SELECT content from ".cms_db_prefix()."module_templates WHERE module_name = ? and template_name = ?";
        $row = $db->GetRow($query, split(';', $tpl_name));

        if ($row)
        {
            $tpl_source = $row['content'];
            return true;
        }

        return false;
    }

	function module_db_timestamp($tpl_name, &$tpl_timestamp, &$smarty_obj)
	{
		global $gCms;

		$db = &$gCms->GetDb();
		$config = $gCms->config;

		$query = "SELECT modified_date from ".cms_db_prefix()."module_templates WHERE module_name = ? and template_name = ?";
		$row = $db->GetRow($query, split(';', $tpl_name));
		if ($row)
		{
			$tpl_timestamp = $db->UnixTimeStamp($row['modified_date']);
			return true;
		}

		return false;
	}

	function global_content_get_template($tpl_name, &$tpl_source, &$smarty_obj)
	{
		debug_buffer('start global_content_get_template');
		global $gCms;
		$config =& $gCms->config;
		$gcbops =& $gCms->GetGlobalContentOperations();

		$oneblob = $gcbops->LoadHtmlBlobByName($tpl_name);
		if ($oneblob)
		{
			$text = $oneblob->content;

			#Perform the content htmlblob callback
			/*
			reset($gCms->modules);
			while (list($key) = each($gCms->modules))
			{
				$value =& $gCms->modules[$key];
				if ($gCms->modules[$key]['installed'] == true &&
					$gCms->modules[$key]['active'] == true)
				{
					$gCms->modules[$key]['object']->ContentHtmlBlob($text);
				}
			}
			*/

			$tpl_source = $text;

			#So no one can do anything nasty, take out the php smarty tags.  Use a user
			#defined plugin instead.
			if (!(isset($config["use_smarty_php_tags"]) && $config["use_smarty_php_tags"] == true))
			{
				$tpl_source = ereg_replace("\{\/?php\}", "", $tpl_source);
			}
		}
		else
		{
			$tpl_source = "<!-- Html blob '" . $tpl_name . "' does not exist  -->";
		}
		debug_buffer('end global_content_get_template');
		return true;
	}

	function global_content_get_timestamp($tpl_name, &$tpl_timestamp, &$smarty_obj)
	{
		debug_buffer('start global_content_get_timestamp');
		global $gCms;
		$gcbops =& $gCms->GetGlobalContentOperations();
		$oneblob = $gcbops->LoadHtmlBlobByName($tpl_name);
		if ($oneblob)
		{
			$tpl_timestamp = $oneblob->modified_date;
			debug_buffer('end global_content_get_timestamp');
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
		reset($gCms->modules);
		while (list($key) = each($gCms->modules))
		{
			$value =& $gCms->modules[$key];
			if ($gCms->modules[$key]['installed'] == true &&
				$gCms->modules[$key]['active'] == true)
			{
				$gCms->modules[$key]['object']->ContentTemplate($tpl_source);
			}
		}
		
		Events::SendEvent('Core', 'ContentTemplate', array('template' => &$tpl_source));

		$gCms->variables['page'] = $data['content_id'];
		$gCms->variables['page_id'] = $data['content_id'];
		$gCms->variables['content_id'] = $data['content_id'];
		$gCms->variables['page_name'] = $data['title'];
		$gCms->variables['position'] = $data['hierarchy'];
		$pageinfo = new StdClass();
		$pageinfo->template_id =  $data['template_id'];
		$pageinfo->content_title = $data['title'];
		$gCms->variables['pageinfo'] = $pageinfo;
		header("Content-Type: text/html; charset=" . (isset($data['encoding']) && $data['encoding'] != ''?$data['encoding']:get_encoding()));

		$content = $data["content"];
		 
		reset($gCms->modules);
		while (list($key) = each($gCms->modules))
		{
			$value =& $gCms->modules[$key];
			if ($gCms->modules[$key]['installed'] == true &&
				$gCms->modules[$key]['active'] == true)
			{
				$gCms->modules[$key]['object']->ContentPreCompile($content);
			}
		}
		
		Events::SendEvent('Core', 'ContentPreCompile', array('content' => &$content));

		$tpl_source = eregi_replace("\{content\}", $content, $tpl_source);

		$title = $data['title'];
		$menutext = $data['menutext'];

		#Perform the content title callback
		/*
		reset($gCms->modules);
		while (list($key) = each($gCms->modules))
		{
			$value =& $gCms->modules[$key];
			if ($gCms->modules[$key]['installed'] == true &&
				$gCms->modules[$key]['active'] == true)
			{
				$gCms->modules[$key]['object']->ContentTitle($title);
			}
		}
		*/

		$tpl_source = ereg_replace("\{title\}", $title, $tpl_source);
		$tpl_source = ereg_replace("\{menutext\}", $menutext, $tpl_source);

		#So no one can do anything nasty, take out the php smarty tags.  Use a user
		#defined plugin instead.
		if (!(isset($config["use_smarty_php_tags"]) && $config["use_smarty_php_tags"] == true))
		{
			$tpl_source = ereg_replace("\{\/?php\}", "", $tpl_source);
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
		global $gCms;
		$config =& $gCms->GetConfig();

		if (get_site_preference('enablesitedownmessage') == "1")
		{
			$tpl_source = get_site_preference('sitedownmessage');
			return true;
		}
		else
		{
			$pageinfo = $gCms->variables['pageinfo'];

			if ($tpl_name == 'notemplate')
			{
				$tpl_source = '{content}';

				return true;
			}
			else if (CmsPreview::has_preview())
			{
				$data = CmsPreview::get_preview();

				$tpl_source = '{content}';
				if (is_array($data) && count($data) == 2)
				{
					if ($data[0] instanceof Template)
						$tpl_source = $data[0]->content;
				}

				return true;
			}
			else if (isset($_GET["print"]))
			{
				$script = '';

				if (isset($_GET["js"]) and $_GET["js"] == 1)
					$script = '<script language="JavaScript">window.print();</script>';

				if (isset($_GET["goback"]) and $_GET["goback"] == 0)
				{
					$tpl_source = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">'."\n".'<html><head><title>{title}</title><meta name="robots" content="noindex"></meta>{metadata}{stylesheet}{literal}<style type="text/css" media="print">#back {display: none;}</style>{/literal}</head><body style="background-color: white; color: black; background-image: none; text-align: left;">{content}'.$script.'</body></html>';
				}
				else
				{
				  $hm =& $gCms->GetHierarchyManager();
				  if (true == $config['assume_mod_rewrite'])
				    {
				      $curnode =& $hm->getNodeByAlias($tpl_name);
				    }
				  else
				    {
				      $curnode =& $hm->getNodeById($tpl_name);
				    }
				  $curcontent =& $curnode->GetContent();
				  $page_url = $curcontent->GetURL();
				  
				  $tpl_source = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">'."\n".'<html><head><title>{title}</title><meta name="robots" content="noindex"></meta>{metadata}{stylesheet}{literal}<style type="text/css" media="print">#back {display: none;}</style>{/literal}</head><body style="background-color: white; color: black; background-image: none; text-align: left;"><p><a id="back" href="'.$page_url.'">&laquo; Go Back</a></p>{content}'.$script.'</body></html>';
				}

				return true;
			}
			else
			{
				global $gCms;
				$templateops =& $gCms->GetTemplateOperations();
				$templateobj = $templateops->LoadTemplateByID($pageinfo->template_id);
				if (isset($templateobj) && $templateobj !== FALSE)
				{
					$tpl_source = $templateobj->content;

					#So no one can do anything nasty, take out the php smarty tags.  Use a user
					#defined plugin instead.
					if (!(isset($config["use_smarty_php_tags"]) && $config["use_smarty_php_tags"] == true))
					{
						$tpl_source = ereg_replace("\{\/?php\}", "", $tpl_source);
					}
					
					//do_cross_reference($pageinfo->template_id, 'template', $tpl_source);

					return true;
				}
			}
			return false;
		}
	}

	function template_get_timestamp($tpl_name, &$tpl_timestamp, &$smarty_obj)
	{
		global $gCms;

		if (get_site_preference('enablesitedownmessage') == "1" || $tpl_name == 'notemplate')
		{
			$tpl_timestamp = time();
			return true;
		}
		else if (isset($_GET['id']) && isset($_GET[$_GET['id'].'showtemplate']) && $_GET[$_GET['id'].'showtemplate'] == 'false')
		{
			$tpl_timestamp = time();
			return true;
		}
		else if (isset($_GET["tmpfile"]) && $_GET["tmpfile"] != "")
		{
			$tpl_timestamp = time();
			return true;
		}
		else if (isset($_GET['print']))
		{
			$tpl_timestamp = time();
			return true;
		}
		else
		{
			$pageinfo = &$gCms->variables['pageinfo'];

			$tpl_timestamp = $pageinfo->template_modified_date;
			return true;
		}
	}

	function content_get_template($tpl_name, &$tpl_source, &$smarty_obj)
	{
		global $gCms;
		$config =& $gCms->GetConfig();
		$pageinfo = &$gCms->variables['pageinfo'];

		if (isset($pageinfo) && $pageinfo->content_id == -1)
		{
			#We've a custom error message...  return it here
			header("HTTP/1.0 404 Not Found");
			header("Status: 404 Not Found");
			if ($tpl_name == 'default')
				$tpl_source = get_site_preference('custom404');
			else
				$tpl_source = '';
			return true;
		}
		else if (CmsPreview::has_preview())
		{
			$data = CmsPreview::get_preview();

			$tpl_source = 'No Content Found in Preview File';
			if (is_array($data) && count($data) == 2)
			{
				$tpl_source = $data[1]->show($tpl_name);
			}
			return true;
		}
		else
		{
			$manager =& $gCms->GetHierarchyManager();
			$node =& $manager->sureGetNodeById($pageinfo->content_id);
			$contentobj =& $node->getContent();

			if (isset($contentobj) && $contentobj !== FALSE)
			{
				$tpl_source = $contentobj->show($tpl_name);

				#Perform the content data callback
				#This needs to go...
				/*
				reset($gCms->modules);
				while (list($key) = each($gCms->modules))
				{
					$value =& $gCms->modules[$key];
					if ($gCms->modules[$key]['installed'] == true &&
						$gCms->modules[$key]['active'] == true)
					{
						$gCms->modules[$key]['object']->ContentData($tpl_source);
					}
				}
				*/

				#Perform the content prerender callback
				/*
				reset($gCms->modules);
				while (list($key) = each($gCms->modules))
				{
					$value =& $gCms->modules[$key];
					if ($gCms->modules[$key]['installed'] == true &&
						$gCms->modules[$key]['active'] == true)
					{
						$gCms->modules[$key]['object']->ContentPreRender($tpl_source);
					}
				}
				*/

				#So no one can do anything nasty, take out the php smarty tags.  Use a user
				#defined plugin instead.
				if (!(isset($config["use_smarty_php_tags"]) && $config["use_smarty_php_tags"] == true))
				{
					$tpl_source = ereg_replace("\{\/?php\}", "", $tpl_source);
				}
				
				//do_cross_reference($pageinfo->content_id, 'content', $tpl_source);

				return true;
			}
		}
		return false;
	}

	function content_get_timestamp($tpl_name, &$tpl_timestamp, &$smarty_obj)
	{
		global $gCms;

		$pageinfo =& $gCms->variables['pageinfo'];

		if (isset($pageinfo) && $pageinfo->content_id == -1)
		{
			#We've a custom error message...  set a current timestamp
			$tpl_timestamp = time();
		}
		else if (isset($_GET["tmpfile"]) && $_GET["tmpfile"] != "")
		{
			$tpl_timestamp = time();
			return true;
		}
		else
		{
			if ($pageinfo->cachable)
			{
				$tpl_timestamp = $pageinfo->content_modified_date;
			}
			else
			{
				$tpl_timestamp = time();
			}
		}
		return true;
	}
	
	function module_get_template ($tpl_name, &$tpl_source, &$smarty_obj)
	{
		global $gCms;
		$pageinfo =& $gCms->variables['pageinfo'];
		$config = $gCms->config;

		#Run the execute_user function and replace {content} with it's output 
		if (isset($gCms->modules[$tpl_name]))
		{
			@ob_start();

			$id = $smarty_obj->id;

			$params = GetModuleParameters($id);
			$action = 'default';
			if (isset($params['action']))
			{
				$action = $params['action'];
			}
			echo $gCms->modules[$tpl_name]['object']->DoActionBase($action, $id, $params, isset($pageinfo)?$pageinfo->content_id:'');
			$modoutput = @ob_get_contents();
			@ob_end_clean();

			$tpl_source = $modoutput;
		}
		
		header("Content-Type: ".$gCms->variables['content-type']."; charset=" . (isset($line['encoding']) && $line['encoding'] != ''?$line['encoding']:get_encoding()));
		if (isset($gCms->variables['content-filename']) && $gCms->variables['content-filename'] != '')
		{
			header('Content-Disposition: attachment; filename="'.$gCms->variables['content-filename'].'"');
			header("Pragma: public");
		}

		#So no one can do anything nasty, take out the php smarty tags.  Use a user
		#defined plugin instead.
		if (!(isset($config["use_smarty_php_tags"]) && $config["use_smarty_php_tags"] == true))
		{
			$tpl_source = ereg_replace("\{\/?php\}", "", $tpl_source);
		}

		return true;
	}

	function module_get_timestamp($tpl_name, &$tpl_timestamp, &$smarty_obj)
	{
		$tpl_timestamp = time();
		return true;
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
	
	/**
	 * Loads all plugins into the system
	 *
	 * @since 0.5
	 */
	function load_plugins()
	{
		$gCms = cmsms();
		$plugins = &$gCms->cmsplugins;
		$userplugins = &$gCms->userplugins;
		$userpluginfunctions = &$gCms->userpluginfunctions;
		$db = cms_db();

		#if (@is_dir(dirname(dirname(__FILE__))."/plugins/cache"))
		#{
		#	search_plugins($smarty, $plugins, dirname(dirname(__FILE__))."/plugins/cache", true);
		#}
		$this->search_plugins($plugins, cms_join_path(dirname(dirname(dirname(__FILE__))),"plugins"), false);

		$query = "SELECT * FROM ".cms_db_prefix()."userplugins";
		$result = &$db->Execute($query);
		while ($result && !$result->EOF)
		{
			if (!in_array($result->fields['userplugin_name'], $plugins))
			{
				$plugins[] =& $result->fields['userplugin_name'];
				$userplugins[$result->fields['userplugin_name']] = $result->fields['userplugin_id'];
				$functionname = "cms_tmp_".$result->fields['userplugin_name']."_userplugin_function";
				//Only register valid code
				if (!(@eval('function '.$functionname.'($params, &$smarty) {'.$result->fields['code'].'}') === FALSE))
				{
					$this->register_function($result->fields['userplugin_name'], $functionname, false);

					//Register the function in a hash so that we can call it from other places by name
					$userpluginfunctions[$result->fields['userplugin_name']] = $functionname;
				}
			}
			$result->MoveNext();
		}
		sort($plugins);
	}
	
	function search_plugins(&$plugins, $dir, $caching)
	{
		global $CMS_LOAD_ALL_PLUGINS;

		$types=array('function','compiler','prefilter','postfilter','outputfilter','modifier','block');
		$handle=opendir($dir);
		while ($file = readdir($handle))
		{
			$path_parts = pathinfo($file);
			if (isset($path_parts['extension']) && $path_parts['extension'] == 'php')
			{
				//Valid plugins will always have a 3 part filename
				$filearray = explode('.', $path_parts['basename']);
				if (count($filearray == 3))
				{
					$filename = cms_join_path($dir, $file);
					//The part we care about is the middle one...
					$file = $filearray[1];
					if (!isset($plugins[$file]) && in_array($filearray[0], $types))
					{
						$key=array_search($filearray[0],$types);
						$load=true;
						switch ($key)
						{
							case 0:
									if (isset($CMS_LOAD_ALL_PLUGINS))
										$this->register_function($file, "smarty_cms_function_" . $file, $caching);
									else
										$load=false;
									break;
							case 1:
									$this->register_compiler_function($file, "smarty_cms_compiler_" .  $file, $caching);
									break;
							case 2:
									$this->register_prefilter("smarty_cms_prefilter_" . $file);
									break;
							case 3:
									$this->register_postfilter("smarty_cms_postfilter_" . $file);
									break;
							case 4:
									$this->register_outputfilter("smarty_cms_outputfilter_" . $file);
									break;
							case 5:
									$this->register_modifier($file, "smarty_cms_modifier_" . $file);
									break;
							case 6:
									$this->register_block($file, "smarty_cms_block_" . $file);
									break;
						}

						if ($load)
						{
							$plugins[]=$file;
							require_once($filename);
						}
					}
				}
			}
		}
		closedir($handle);
	}
}

?>