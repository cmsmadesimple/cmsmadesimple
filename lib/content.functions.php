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

/**
 * @ignore
 */
$sorted_sections = array();

/**
 * @ignore
 */
$sorted_content = array();

/**
 * Extends the Smarty class for content.
 *
 * Extends the Smarty class for checking timestamps and rendering content to the browser.
 *
 * @package CMS
 * @since 0.1
 */
class Smarty_CMS extends Smarty {
	
        /**
	 * Constructor
	 *
	 * @param array The hash of CMSMS config settings
	 */
	function Smarty_CMS(&$config)
	{
		$this->Smarty();
		global $CMS_ADMIN_PAGE;

		if( isset($CMS_ADMIN_PAGE) && $CMS_ADMIN_PAGE == 1 )
		{
		  $this->template_dir = $config["root_path"].'/'.$config['admin_dir'].'/templates/';
		  $this->config_dir = $config["root_path"].'/'.$config['admin_dir'].'/configs/';
		}
		else
		{
		  $this->template_dir = $config["root_path"].'/tmp/templates/';
		  $this->config_dir = $config["root_path"].'/tmp/configs/';
		}
		$this->compile_dir = TMP_TEMPLATES_C_LOCATION;
		$this->cache_dir = TMP_CACHE_LOCATION;
		$this->plugins_dir = array($config["root_path"].'/lib/smarty/plugins',$config["root_path"].'/plugins');

		$this->compiler_file = 'CMS_Compiler.class.php';
		$this->compiler_class = 'CMS_Compiler';

		$this->assign('app_name','CMS');

		//$this->cache_plugins = true;
		//$this->caching = 1; // output caching disabled because output could come from databases, and can't strictly depend on the time of the template.
		//$this->force_compile = true;

		if ($config["debug"] == true)
		{
			//$this->caching = false;
			$this->force_compile = true;
			$this->debugging = true;
		}

		if (is_sitedown())
		{
			$this->caching = false;
			$this->force_compile = true;
		}

		if (isset($CMS_ADMIN_PAGE) && $CMS_ADMIN_PAGE == 1)
		{
			$this->caching = false;
			$this->force_compile = true;
		}

		load_plugins($this);

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
		$this->register_resource("tpl_top", array(&$this, "template_top_get_template",
						       "template_get_timestamp",
						       "db_get_secure",
						       "db_get_trusted"));
		$this->register_resource("tpl_head", array(&$this, "template_head_get_template",
						       "template_get_timestamp",
						       "db_get_secure",
						       "db_get_trusted"));
		$this->register_resource("tpl_body", array(&$this, "template_body_get_template",
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

    /**
     * wrapper for include() retaining $this
     *
     * @ignore
     * @access private
     * @param  string The input filename
     * @param  boolean A flag wether include_once should be called or just include
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


    /**
     * Wrapper for the trigger_error method
     *
     * @ignore
     * @access private
     * @param string The error message
     * @param int    The error type E_USER_WARNING or E_USER_NOTICE
     * @return void
     */
    function trigger_error($error_msg, $error_type = E_USER_WARNING)
    {   
        var_dump("Smarty error: $error_msg");
    }


    /**
     * Method to return module file template contents.
     * 
     * @access private
     * @param string The module template name
     * @param string (returned) template contents
     * @param object The smarty object
     * @return boolean
     */
    function module_file_template($tpl_name, &$tpl_source, &$smarty_obj)
    {
      
      $params = preg_split('/;/', $tpl_name);
      
	
        if (count($params) == 2 && file_exists(dirname(dirname(__FILE__)) . '/module_custom/' . $params[0] . '/templates/' . $params[1]))
	  {
            $tpl_source = @file_get_contents(dirname(dirname(__FILE__)) . '/module_custom/' . $params[0] . '/templates/' . $params[1]);
            return true;
	  }
        else if (count($params) == 2 && file_exists(dirname(dirname(__FILE__)) . '/modules/' . $params[0] . '/templates/' . $params[1]))
        {   
            $tpl_source = @file_get_contents(dirname(dirname(__FILE__)) . '/modules/' . $params[0] . '/templates/' . $params[1]);
            return true;
        }
        return false;
    }

    /** 
     *  A method to return the timestamp of a module file template
     *
     *  @access private
     *  @param  string The filename of the module template
     *  @param  int    (returned) The file timestamp
     *  @param  object The smarty object
     *  @return boolean
     */
	function module_file_timestamp($tpl_name, &$tpl_timestamp, &$smarty_obj)
	{
      
    $params = preg_split('/;/', $tpl_name);
      
		if (count($params) == 2 && file_exists(dirname(dirname(__FILE__)) . '/modules/' . $params[0] . '/templates/' . $params[1]))
		{
			$tpl_timestamp = filemtime(dirname(dirname(__FILE__)) . '/modules/' . $params[0] . '/templates/' . $params[1]);
			return true;
		}
		return false;
	}

    /**
     * A method to return a module database template.
     *
     * @access private
     * @param string The database template name.
     * @param  string (returned) The database template contents
     * @param  object The smarty object.
     * @return boolean
     */
    function module_db_template($tpl_name, &$tpl_source, &$smarty_obj)
    {   
        global $gCms;

        $db = &$gCms->GetDb();
        $config = $gCms->config;

        $query = "SELECT content from ".cms_db_prefix()."module_templates WHERE module_name = ? and template_name = ?";
        $row = $db->GetRow($query, preg_split('/;/', $tpl_name));

        if ($row)
        {
            $tpl_source = $row['content'];
            return true;
        }

        return false;
    }

    /** 
     *  A method to return the timestamp of a module database template
     *
     *  @access private
     *  @param  string The name of the module template
     *  @param  int    (returned) The file timestamp
     *  @param  object The smarty object
     *  @return boolean
     */
	function module_db_timestamp($tpl_name, &$tpl_timestamp, &$smarty_obj)
	{

		$db = cmsms()->GetDb();
		$module_template_cache = cms_utils::get_app_data('module_template_cache');
		if( isset($module_template_cache) && isset($module_template_cache[$tpl_name]) )
		  {
		    $tpl_timestamp = $module_template_cache[$tpl_name];
		    return true;
		  }
		
		$query = "SELECT module_name,template_name,modified_date 
                            FROM ".cms_db_prefix()."module_templates";
		$results = $db->GetArray($query);

		if( !count($results) ) return false;

		if( empty($module_template_cache) )
		  {
		    $module_template_cache = array();
		  }
		foreach( $results as $row )
		  {
		    $key = $row['module_name'].';'.$row['template_name'];
		    $val = $db->UnixTimeStamp($row['modified_date']);
		    $module_template_cache[$key] = $val;
		  }

		$tpl_timestamp = $module_template_cache[$tpl_name];
		cms_utils::set_app_data('module_template_cache',$module_template_cache);
		return true;
	}


    /**
     * A method to return the contents of a global content block.
     *
     * @access private
     * @param string The global content block name.
     * @param  string (returned) The database template contents
     * @param  object The smarty object.
     * @return boolean
     */
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
				$tpl_source = preg_replace("/\{\/?php\}/", "", $tpl_source);
			}
		}
		else
		{
			$tpl_source = "<!-- Html blob '" . $tpl_name . "' does not exist  -->";
		}
		debug_buffer('end global_content_get_template');
		return true;
	}


    /** 
     *  A method to return the timestamp of a global content block.
     *
     *  @access private
     *  @param  string The name of the global content block.
     *  @param  int    (returned) The file timestamp
     *  @param  object The smarty object
     *  @return boolean
     */
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


    /**
     * Given a page template, return the portion of a page template before the head tag.
     *
     * @access private
     * @param string The page template name.
     * @param  string (returned) The database template contents
     * @param  object The smarty object.
     * @return boolean
     */
	function template_top_get_template($tpl_name, &$tpl_source, &$smarty_obj)
	{
	  global $gCms;
	  $config = $gCms->GetConfig();
	  
	  if (is_sitedown())
	    {
	      $tpl_source = '';
	      return true;
	    }
	  else
	    {
	      if ($tpl_name == 'notemplate')
		{
		  $tpl_source = '';
		  return true;
		}

	      if( isset($gCms->variables['template']) )
		{
		  $tpl_source = $gCms->variables['template'];
		}
	      else
		{
		  $contentobj = $gCms->variables['content_obj'];
		  $templateops =& $gCms->GetTemplateOperations();
		  $templateobj =& $templateops->LoadTemplateByID($contentobj->TemplateId());
		  if (isset($templateobj) && $templateobj !== FALSE)
		    {
		      $tpl_source = $templateobj->content;
		      $gCms->variables['template'] = $tpl_source;
		    }
		}
		 
	      $pos = stripos($tpl_source,'<head');
	      if( $pos === FALSE )
		{
		  // return the whole template
		  return true;
		}
	      $tpl_source = substr($tpl_source,0,$pos);
	      return true;
	    }
	  return false;
	}


    /**
     * Given a page template, return the head portion of a page template.
     *
     * @access private
     * @param string The page template name.
     * @param  string (returned) The database template contents
     * @param  object The smarty object.
     * @return boolean
     */
	function template_head_get_template($tpl_name, &$tpl_source, &$smarty_obj)
	{
	  global $gCms;
	  $config = $gCms->GetConfig();
	  
	  if (is_sitedown())
	    {
	      $tpl_source = '';
	      return true;
	    }
	  else
	    {
	      if ($tpl_name == 'notemplate')
		{
		  $tpl_source = '';
		  return true;
		}

	      if( isset($gCms->variables['template']) )
		{
		  $tpl_source = $gCms->variables['template'];
		}
	      else
		{
		  $contentobj = $gCms->variables['content_obj'];
		  $templateops =& $gCms->GetTemplateOperations();
		  $templateobj =& $templateops->LoadTemplateByID($contentobj->TemplateId());
		  if (isset($templateobj) && $templateobj !== FALSE)
		    {
		      $tpl_source = $templateobj->content;
		      $gCms->variables['template'] = $tpl_source;
		    }
		}
		 
	      $pos1 = stripos($tpl_source,'<head');
	      $pos2 = stripos($tpl_source,'</head>');
	      if( $pos1 === FALSE || $pos2 === FALSE )
		{
		  // return an empty string
		  // assume it was processed in the top
		  $tpl_source = '';
		  return true;
		}
	      $tpl_source = substr($tpl_source,$pos1,$pos2-$pos1+7);
	      return true;
	    }
	  return false;
	}


    /**
     * Given a page template, return the body portion of a page template.
     *
     * @access private
     * @param string The page template name.
     * @param  string (returned) The database template contents
     * @param  object The smarty object.
     * @return boolean
     */
	function template_body_get_template($tpl_name, &$tpl_source, &$smarty_obj)
	{
	  global $gCms;
	  $config = $gCms->GetConfig();
	  
	  if (is_sitedown())
	    {
	      header('HTTP/1.0 503 Service Unavailable');
	      header('Status: 503 Service Unavailable');

	      $tpl_source = get_site_preference('sitedownmessage');
	      return true;
	    }
	  else
	    {
	      if ($tpl_name == 'notemplate')
		{
		  $tpl_source = '{content}';
		  return true;
		}

	      if( isset($gCms->variables['template']) )
		{
		  $tpl_source = $gCms->variables['template'];
		}
	      else
		{
		  $contentobj = $gCms->variables['content_obj'];
		  $templateops =& $gCms->GetTemplateOperations();
		  $templateobj =& $templateops->LoadTemplateByID($contentobj->TemplateId());
		  if (isset($templateobj) && $templateobj !== FALSE)
		    {
		      $tpl_source = $templateobj->content;
		      $gCms->variables['template'] = $tpl_source;
		    }
		}
	      
	      $pos = stripos($tpl_source,'</head>');
	      if( $pos === FALSE )
		{
		  // this probably means it's not an html template
		  // just return an empty string
		  // and assume that the tpl_head stuff
		  // returned the entire template
		  $tpl_source = '';
		  return true;
		}

	      $tpl_source = substr($tpl_source,$pos+7);
	      return true;
	    }
	  return false;
	}

    /**
     * Given a page template name, return it's entire contents.
     *
     * @access private
     * @param string The page template name.
     * @param  string (returned) The database template contents
     * @param  object The smarty object.
     * @return boolean
     */
	function template_get_template($tpl_name, &$tpl_source, &$smarty_obj)
	{
		global $gCms;
		$config = $gCms->GetConfig();

		if (is_sitedown())
		{
			$tpl_source = get_site_preference('sitedownmessage');
			return true;
		}
		else
		{
			if ($tpl_name == 'notemplate')
			{
				$tpl_source = '{content}';

				return true;
			}
			else if (isset($_GET["print"]))
			{
			  // this should really just go.
				$script = '';

				if (isset($_GET["js"]) and $_GET["js"] == 1)
					$script = '<script type="text/javascript">window.print();</script>';

				if (isset($_GET["goback"]) and $_GET["goback"] == 0)
				{
					$tpl_source = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"'."\n".'"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'.'<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">'.'<head><title>{title}</title><meta name="robots" content="noindex"></meta>{metadata}{stylesheet}{literal}<style type="text/css" media="print">#back {display: none;}</style>{/literal}</head><body style="background-color: white; color: black; background-image: none; text-align: left;">{content}'.$script.'</body></html>';
				}
				else
				{
				  $hm =& $gCms->GetHierarchyManager();
				  if ('mod_rewrite' == $config['url_rewriting'])
				    {
				      $curnode =& $hm->getNodeByAlias($tpl_name);
				    }
				  else
				    {
				      $curnode =& $hm->getNodeById($tpl_name);
				    }
				  $curcontent =& $curnode->GetContent();
				  $page_url = $curcontent->GetURL();
				  
				  $tpl_source = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"'."\n".'"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'.'<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">'.'<head><title>{title}</title><meta name="robots" content="noindex"></meta>{metadata}{stylesheet}{literal}<style type="text/css" media="print">#back {display: none;}</style>{/literal}</head><body style="background-color: white; color: black; background-image: none; text-align: left;"><p><a id="back" href="'.$page_url.'">&laquo; Go Back</a></p>{content}'.$script.'</body></html>';
				}

				return true;
			}
			if( isset($_SESSION['cms_preview']) )
			  {
			    debug_to_log('template_get_template preview');
			    // get serialized data filename
			    $tpl_name = trim($_SESSION['cms_preview']);
			    unset($_SESSION['cms_preview']);
			    $fname = '';
			    if (is_writable($config["previews_path"]))
			      {
				$fname = cms_join_path($config["previews_path"] , $tpl_name);
			      }
			    else
			      {
				$fname = cms_join_path(TMP_CACHE_LOCATION , $tpl_name);
			      }
			    if( !file_exists($fname) )
			      {
				$tpl_source = 'Error: Cache file: '.$tpl_name.' does not exist.';
				return false;
			      }

			    // get the serialized data
			    $handle = fopen($fname, "r");
			    $data = unserialize(fread($handle, filesize($fname)));
			    fclose($handle);
			    unlink($fname);
	
			    $tpl_source = $data["template"];
			    debug_to_log($tpl_source);

			    return true;
			  }
			else
			{
				global $gCms;
				$contentobj = $gCms->variables['content_obj'];
				$templateops =& $gCms->GetTemplateOperations();
				$templateobj =& $templateops->LoadTemplateByID($contentobj->TemplateId());
				if (isset($templateobj) && $templateobj !== FALSE)
				{
					$tpl_source = $templateobj->content;

					#So no one can do anything nasty, take out the php smarty tags.  Use a user
					#defined plugin instead.
					if (!(isset($config["use_smarty_php_tags"]) && $config["use_smarty_php_tags"] == true))
					{
						$tpl_source = preg_replace("/\{\/?php\}/", "", $tpl_source);
					}
					
					return true;
				}
			}
			return false;
		}
	}


    /**
     * Given a page template name, return it's modification date.
     *
     * @access private
     * @param string The page template name.
     * @param  int (returned) The database template modification time.
     * @param  object The smarty object.
     * @return boolean
     */
	function template_get_timestamp($tpl_name, &$tpl_timestamp, &$smarty_obj)
	{
		global $gCms;

		if (is_sitedown() || $tpl_name == 'notemplate')
		{
			$tpl_timestamp = time();
			return true;
		}
		else if (isset($_GET['id']) && isset($_GET[$_GET['id'].'showtemplate']) && $_GET[$_GET['id'].'showtemplate'] == 'false')
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
		  $contentobj = $gCms->variables['content_obj'];
		  $templateops =& $gCms->GetTemplateOperations();
		  $templateobj =& $templateops->LoadTemplateByID($contentobj->TemplateId());
		  if (isset($templateobj) && $templateobj !== FALSE)
		    {
		      $tpl_timestamp = $templateobj->modified_date;
		      return true;
		    }
		}
	}


    /**
     * Given the name of a content block, return it's content.
     * This method assumes the use of the pageinfo information that is created in the CMSMS index.php
     * to determine the page id that should be used to identify which content object to use.
     * if the pageinfo is not set, it is possible for a 404 error message to be displayed.
     * This method also handles returning preview content if the data exists in the session.
     *
     * @access private
     * @param string The page template name.
     * @param  string (returned) The database template contents
     * @param  object The smarty object.
     * @return boolean
     */
	function content_get_template($tpl_name, &$tpl_source, &$smarty_obj)
	{
		global $gCms;
		$config = $gCms->GetConfig();
		$contentobj = $gCms->variables['content_obj'];

		if (!is_object($contentobj))
		{
			#We've a custom error message...  return it here
			header("HTTP/1.0 404 Not Found");
			header("Status: 404 Not Found");
			if ($tpl_name == 'content_en')
				$tpl_source = get_site_preference('custom404');
			else
				$tpl_source = '';
			return true;
		}
		else if( isset($_SESSION['cms_preview_data']) && $contentobj->Id() == '__CMS_PREVIEW_PAGE__' )
	        {
		  debug_to_log('content_get_template for preview');
		  if( !isset($_SESSION['cms_preview_data']['content_obj']) )
		    {
		      $contentops =& $gCms->GetContentOperations();
		      $_SESSION['cms_preview_data']['content_obj'] = $contentops->LoadContentFromSerializedData($_SESSION['cms_preview_data']);
		      $contentobj =& $_SESSION['cms_preview_data']['content_obj'];
		    }
		  $contentobj =& $_SESSION['cms_preview_data']['content_obj'];
		  $tpl_source = $contentobj->Show($tpl_name);

                  #So no one can do anything nasty, take out the php smarty tags.  Use a user
                  #defined plugin instead.
		  if (!(isset($config["use_smarty_php_tags"]) && $config["use_smarty_php_tags"] == true))
		    {
		      $tpl_source = preg_replace("/\{\/?php\}/", "", $tpl_source);
		    }

		  debug_to_log($tpl_source);
		  return true;
		}
		else
		{
			$manager =& $gCms->GetHierarchyManager();
			if (isset($contentobj) && $contentobj !== FALSE)
			{

				$tpl_source = $contentobj->Show($tpl_name);

				#So no one can do anything nasty, take out the php smarty tags.  Use a user
				#defined plugin instead.
				if (!(isset($config["use_smarty_php_tags"]) && $config["use_smarty_php_tags"] == true))
				{
					$tpl_source = preg_replace("/\{\/?php\}/", "", $tpl_source);
				}
				
				//do_cross_reference($pageinfo->content_id, 'content', $tpl_source);

				return true;
			}
		}
		return false;
	}

    /**
     * Return the modified date of the current page id (as specified in the pageinfo)
     * This method is used by smarty to indicate wether a content page should be recompiled and cached
     *
     * @access private
     * @param string The page template name (ignored)
     * @param  int (returned) The timestamp of the modification date of the matching content object.
     * @param  object The smarty object.
     * @return boolean
     */
	function content_get_timestamp($tpl_name, &$tpl_timestamp, &$smarty_obj)
	{
		global $gCms;

		$contentobj = $gCms->variables['content_obj'];

		if (!is_object($contentobj))
		{
			#We've a custom error message...  set a current timestamp
			$tpl_timestamp = time();
		}
		else
		{
		  if ($contentobj->Cachable())
			{
			  $db = $gCms->GetDb();
			  $tpl_timestamp = $db->UnixTimestamp($contentobj->GetModifiedDate());
			}
			else
			{
				$tpl_timestamp = time();
			}
		}
		return true;
	}
	
    /**
     * Retrieve output from a module.
     * This method grabs parameters from the request, and given the module name
     * will call the module with the specified parameters to retrieve the module output.
     * This is used to replace the contents of the {content} tag when a module action is
     * called in a non inline manner.
     *
     * @access private
     * @param string The module name.
     * @param  string (returned) The moduleoutput.
     * @param  object The smarty object.
     * @return boolean
     */
	function module_get_template ($tpl_name, &$tpl_source, &$smarty_obj)
	{
		global $gCms;
		$contentobj = $gCms->variables['content_obj'];
		$config = $gCms->config;

		#Run the execute_user function and replace {content} with it's output 
		if (isset($gCms->modules[$tpl_name]))
		{
			@ob_start();

			$id = $smarty_obj->id;
			$returnid = is_object($contentobj)?$contentobj->Id():'';
			$params = GetModuleParameters($id);
			$action = 'default';
			if (isset($params['action']))
			{
				$action = $params['action'];
			}
			echo $gCms->modules[$tpl_name]['object']->DoActionBase($action, $id, $params, is_object($contentobj)?$$contentobj->Id():'');
			$modoutput = @ob_get_contents();
			@ob_end_clean();

			$tpl_source = $modoutput;
		}
		
		header("Content-Type: ".$gCms->variables['content-type']."; charset=" . get_encoding());
		if (isset($gCms->variables['content-filename']) && $gCms->variables['content-filename'] != '')
		{
			header('Content-Disposition: attachment; filename="'.$gCms->variables['content-filename'].'"');
			header("Pragma: public");
		}

		#So no one can do anything nasty, take out the php smarty tags.  Use a user
		#defined plugin instead.
		if (!(isset($config["use_smarty_php_tags"]) && $config["use_smarty_php_tags"] == true))
		{
			$tpl_source = preg_replace("/\{\/?php\}/", "", $tpl_source);
		}

		return true;
	}

    /**
     * A dummy function to return the modified date of the latest module output.  
     * This function always returns the current time which essentially disables smarty compile caching
     * for non-inline module output.
     *
     * @access private
     * @param string The page template name (ignored)
     * @param  int (returned) The timestamp of the modification date of the matching template (always current time)
     * @param  object The smarty object.
     * @return boolean
     */
	function module_get_timestamp($tpl_name, &$tpl_timestamp, &$smarty_obj)
	{
		$tpl_timestamp = time();
		return true;
	}

	/** 
	 * A dummy function that indicates that all db templates are secure.
	 * (always returns true)
	 *
	 * @access private
	 * @param  string The template name (ignored)
	 * @param  object The smarty object
	 * @return boolean
	 */
	function db_get_secure($tpl_name, &$smarty_obj)
	{
		// assume all templates are secure
		return true;
	}

	/** 
	 * A dummy function that indicates that all db templates are truested.
	 * (always returns true)
	 *
	 * @access private
	 * @param  string The template name (ignored)
	 * @param  object The smarty object
	 * @return boolean
	 */
	function db_get_trusted($tpl_name, &$smarty_obj)
	{
		// not used for templates
	}
}

/**
 * Loads all plugins into the system
 *
 * @since 0.5
 * @param object The smarty object
 * @return void
 */
function load_plugins(&$smarty)
{
	global $gCms;
	$plugins = &$gCms->cmsplugins;
	$userplugins = &$gCms->userplugins;
	$userpluginfunctions = &$gCms->userpluginfunctions;
	$db = &$gCms->GetDb();
	if (isset($db))
	{
		#if (@is_dir(dirname(dirname(__FILE__))."/plugins/cache"))
		#{
		#	search_plugins($smarty, $plugins, dirname(dirname(__FILE__))."/plugins/cache", true);
		#}
		search_plugins($smarty, $plugins, dirname(dirname(__FILE__))."/plugins", false);

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
					$smarty->register_function($result->fields['userplugin_name'], $functionname, false);

					//Register the function in a hash so that we can call it from other places by name
					$userpluginfunctions[$result->fields['userplugin_name']] = $functionname;
				}
			}
			$result->MoveNext();
		}
		sort($plugins);
	}
}

/**
 * A function to load all valid plugins in the plugins directory.
 *
 * @internal
 * @access private
 * @param object The smarty object
 * @param array  A reference to the $gCms->plugins array.
 * @param string The directory name
 * @param boolean Wether caching should be applied
 * @return void
 */
function search_plugins(&$smarty, &$plugins, $dir, $caching)
{
	global $CMS_LOAD_ALL_PLUGINS;

	$types=array('function','compiler','prefilter','postfilter','outputfilter','modifier','block');
	$handle=opendir($dir);
	while ($file = readdir($handle))
	{
		// This hides the dummy function.summarize.php
		// (function.summarize.php was renamed to modifier.summarize.php in 1.0.3)
		// This code can be deleted once the dummy is removed from the distribution
		// TODO: DELETE
		if (
			$file == 'function.summarize.php' &&
			substr(file_get_contents(cms_join_path($dir, $file)), 9, 9) == '__DUMMY__'
		)
		{
				continue;
		}
		// END TODO: DELETE

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
				if (!isset($plugins[$file]) && in_array($filearray[0],$types))
				{
					$key=array_search($filearray[0],$types);
					$load=true;
					switch ($key)
					{
						case 0:
								if (isset($CMS_LOAD_ALL_PLUGINS))
									$smarty->register_function($file, "smarty_cms_function_" . $file, $caching);
								else $load=false;
								break;
						case 1:
								$smarty->register_compiler_function($file, "smarty_cms_compiler_" .  $file, $caching);
								break;
						case 2:
								$smarty->register_prefilter("smarty_cms_prefilter_" . $file);
								break;
						case 3:
								$smarty->register_postfilter("smarty_cms_postfilter_" . $file);
								break;
						case 4:
								$smarty->register_outputfilter("smarty_cms_outputfilter_" . $file);
								break;
						case 5:
								$smarty->register_modifier($file, "smarty_cms_modifier_" . $file);
								break;
						case 6:
								$smarty->register_block($file, "smarty_cms_block_" . $file);
								break;
					}
					if ($load){ $plugins[]=$file;
						require_once($filename);}
				}
			}
		}
	}
	closedir($handle);
}

/**
 * A function to generate cross references between content types
 * This function can be used to generate which global content blocks are used by which content object
 *
 * @ignore
 * @access private
 * @param int the parent object id (usually a page template id or a page id.
 * @param string The parent object type
 * @param string The test content
 * @return void
 */
function do_cross_reference($parent_id, $parent_type, $content)
{
	global $gCms;
	$db =& $gCms->GetDb();
	
	//Delete old ones from the database
	$query = 'DELETE FROM '.cms_db_prefix().'crossref WHERE parent_id = ? AND parent_type = ?';
	$db->Execute($query, array($parent_id, $parent_type));
	
	//Do global content blocks
	$matches = array();
	preg_match_all('/\{(?:html_blob|global_content).*?name=["\']([^"]+)["\'].*?\}/', $content, $matches);
	if (isset($matches[1]))
	{
		$selquery = 'SELECT htmlblob_id FROM '.cms_db_prefix().'htmlblobs WHERE htmlblob_name = ?';
		$insquery = 'INSERT INTO '.cms_db_prefix().'crossref (parent_id, parent_type, child_id, child_type, create_date, modified_date)
						VALUES (?,?,?,\'global_content\','.$db->DBTimeStamp(time()).','.$db->DBTimeStamp(time()).')';
		foreach ($matches[1] as $name)
		{
			$result = &$db->Execute($selquery, array($name));
			while ($result && !$result->EOF)
			{
				$db->Execute($insquery, array($parent_id, $parent_type, $result->fields['htmlblob_id']));
				$result->MoveNext();
			}
			if ($result) $result->Close();
		}
	}
}

/**
 * A function to remove all cross references for a parent object
 *
 * @ignore
 * @access private
 * @param int The parent object id
 * @param string The parent object type
 * @return void
 */
function remove_cross_references($parent_id, $parent_type)
{
	global $gCms;
	$db =& $gCms->GetDb();
	
	//Delete old ones from the database
	$query = 'DELETE FROM '.cms_db_prefix().'crossref WHERE parent_id = ? AND parent_type = ?';
	$db->Execute($query, array($parent_id, $parent_type));
}

/**
 * A function to remove all cross references for a child
 *
 * @ignore
 * @access private
 * @param int The child object id
 * @param string The child object type
 * @return void
 */
function remove_cross_references_by_child($child_id, $child_type)
{
	global $gCms;
	$db =& $gCms->GetDb();
	
	//Delete old ones from the database
	$query = 'DELETE FROM '.cms_db_prefix().'crossref WHERE child_id = ? AND child_type = ?';
	$db->Execute($query, array($child_id, $child_type));
}

/**
 * A utility function to load the specified global content blocks and call the GlobalContentPrecompile method.
 *
 * @ignore
 * @access private
 * @param array Array containing the name of 1 global content block
 * @return void
 */
function global_content_regex_callback($matches)
{
	global $gCms;
	if (isset($matches[1]))
	{
		$gcbops =& $gCms->GetGlobalContentOperations();
		$oneblob = $gcbops->LoadHtmlBlobByName($matches[1]);
		if ($oneblob)
		{
			$text = $oneblob->content;

			#Perform the content htmlblob callback
			#reset($gCms->modules);
			#while (list($key) = each($gCms->modules))
			#{
			#	$value =& $gCms->modules[$key];
			#	if ($gCms->modules[$key]['installed'] == true &&
			#		$gCms->modules[$key]['active'] == true)
			#	{
			#		$gCms->modules[$key]['object']->GlobalContentPreCompile($text);
			#	}
			#}
			
			Events::SendEvent('Core', 'GlobalContentPreCompile', array('content' => &$text));

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


/**
 * A convenience function to test if the site is marked as down according to the config panel.
 * This method includes handling the preference that indicates that site-down behaviour should
 * be disabled for certain IP address ranges.
 *
 * @return boolean
 */
function is_sitedown()
{
  if( get_site_preference('enablesitedownmessage') !== '1' ) return FALSE;

  if( get_site_preference('sitedownexcludeadmins') )
    {
      $uid = get_userid(FALSE);
      if( $uid ) return FALSE;
    }

  if( !isset($_SERVER['REMOTE_ADDR']) ) return TRUE;
  $excludes = get_site_preference('sitedownexcludes','');
  if( empty($excludes) ) return TRUE;
  
  $ret = cms_ipmatches($_SERVER['REMOTE_ADDR'],$excludes);
  if( $ret ) return FALSE;

  return TRUE;
}

# vim:ts=4 sw=4 noet
?>
