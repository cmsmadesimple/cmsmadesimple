<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#CMS - CMS Made Simple
#(c)2004-2010 by Ted Kulp (ted@cmsmadesimple.org)
#This project's homepage is: http://cmsmadesimple.org
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

class CmsModuleUrlExtension extends CmsModuleExtension
{
	function __construct($module)
	{
		parent::__construct($module);
	}
	
	public function link($params = array(), $check_keys = false)
	{
		$default_params = array(
			'action' => coalesce_key($params, 'action', 'default', FILTER_SANITIZE_URL),
			'contents' => coalesce_key($params, 'contents', ''),
			'warn_message' => coalesce_key($params, 'warn_message', '', FILTER_SANITIZE_STRING),
			'only_href' => coalesce_key($params, 'only_href', false, FILTER_VALIDATE_BOOLEAN),
			'inline' => coalesce_key($params, 'inline', false, FILTER_VALIDATE_BOOLEAN),
			'target_content_only' => coalesce_key($params, 'target_content_only', false, FILTER_VALIDATE_BOOLEAN),
			'id_suffix' => coalesce_key($params, 'id_suffix', '', FILTER_SANITIZE_STRING),
			'pretty_url' => coalesce_key($params, 'pretty_url', '', FILTER_SANITIZE_URL),
			'class' => coalesce_key($params, 'class', '', FILTER_SANITIZE_STRING),
			'extra' => coalesce_key($params, 'extra', ''), 
			'params' => coalesce_key($params, 'params', array()),
			'id' => coalesce_key($params, 'id', $this->id),
			'return_id' => coalesce_key($params, 'return_id', $this->return_id)
		);
		$default_params['html_id'] = coalesce_key($params,
			'html_id',
			CmsResponse::make_dom_id($default_params['id'].$default_params['action'].$default_params['id_suffix']),
			FILTER_SANITIZE_STRING
		);
		
		if ($check_keys && !are_all_keys_valid($params, $default_params))
			throw new CmsInvalidKeyException(invalid_key($params, $default_params));

		//Combine EVERYTHING together into a big managerie
		//Merge in anything if it was passed in the params key to the method
		$extra_params = strip_extra_params($params, $default_params, 'params');
		
		$link_params = array(
			'id' => $params['html_id'],
			'class' => $params['class']
		);
		
		//Handle pretty url stuff
		if ($params['pretty_url'] != '' && CmsConfig::get('url_rewriting') == 'mod_rewrite')
		{
			$text = CmsConfig::get('root_url') . '/' . $params['pretty_url'] . CmsConfig::get('page_extension');
		}
		else if ($params['pretty_url'] != '' && CmsConfig::get('url_rewriting') == 'internal')
		{
			$text = CmsConfig::get('root_url') . '/index.php/' . $params['pretty_url'] . CmsConfig::get('page_extension');
		}
		else
		{
			$href = '';
			if ($params['target_content_only'] || ($params['return_id'] != '' && !$params['inline']))
			{
				$params['id'] = 'cntnt01';
			}
			
			$goto = 'index.php';
			if ($params['return_id'] == '')
			{
				$goto = 'moduleinterface.php';
			}
			
			$href .= CmsConfig::get('root_url');
			if (!($params['return_id'] != '' && $params['return_id'] > -1))
			{
				$href .= '/'.CmsConfig::get('admin_dir');
			}

			$secureparam = '';
			if ($params['return_id'] == '')
			{
				$secureparam = '&amp;' . CMS_SECURE_PARAM_NAME . '=' . $_SESSION[CMS_USER_KEY];
			}

			$href .= '/'.$goto.'?mact='.$this->module->get_name().','.$params['id'].','.$params['action'].','.($params['inline'] == true?1:0).$secureparam;
			
			foreach ($extra_params as $key => $value)
			{
				$key = cms_htmlentities($key);
				$value = cms_htmlentities($value);
				if ($key != 'module' && $key != 'action' && $key != 'id')
					$href .= '&amp;'.$params['id'].$key.'='.rawurlencode($value);
			}
			
			if ($params['return_id'] != '')
			{
				$href .= '&amp;'.$params['id'].'returnid='.$params['return_id'];
				if ($params['inline'])
				{
					$href .= '&amp;'.CmsConfig::get('query_var').'='.$params['return_id'];
				}
			}
		}
		
		if ($params['only_href'])
			return $href;
		
		$link_params['href'] = $href;
		
		if ($params['warn_message'] != '')
		{
			$link_params['onclick'] = "return confirm('{$warn_message}');";
		}
		
		$extra = '';
		if ($params['extra'])
		{
			$extra = $params['extra'];
		}
		
		return start_tag('a', $link_params, false, $extra) . $params['contents'] . end_tag('a');
	}

	function cms_module_CreateContentLink(&$modinstance, $pageid, $contents='')
	{
	  $pageid = cms_htmlentities($pageid);
	  $contents = cms_htmlentities($contents);

		global $gCms;
		$config = $gCms->GetConfig();
		$text = '<a href="';
		if ($config["url_rewriting"] == 'mod_rewrite')
		{
			# mod_rewrite
			$contentops = $gCms->GetContentOperations();
			$alias = $contentops->GetPageAliasFromID( $pageid );
			if( $alias == false )
			{
				return '<!-- ERROR: could not get an alias for pageid='.$pageid.'-->';
			}
			else
			{
				$text .= $config["root_url"]."/".$alias.
				(isset($config['page_extension'])?$config['page_extension']:'.shtml');
			}
		}
		else
		{
			# not mod rewrite
			$text .= $config["root_url"]."/index.php?".$config["query_var"]."=".$pageid;
		}
		$text .= '">'.$contents.'</a>';
		return $text;
	}

	function cms_module_CreateReturnLink(&$modinstance, $id, $returnid, $contents='', $params=array(), $onlyhref=false)
	{
	  $id = cms_htmlentities($id);
	  $returnid = cms_htmlentities($returnid);
	  $contents = $contents;

		$text = '';
		global $gCms;
		$config = $gCms->GetConfig();
		$manager = $gCms->GetHierarchyManager();
		$node = $manager->sureGetNodeById($returnid);
		if (isset($node))
		{
			$content = $node->GetContent();

			if (isset($content))
			{
				if ($content->GetURL() != '')
				{
					if (!$onlyhref)
					{
						$text .= '<a href="';
					}
					$text .= $content->GetURL();

					$count = 0;
					foreach ($params as $key=>$value)
					{
					  $key = cms_htmlentities($key);
					  $value = cms_htmlentities($value);
						if ($count > 0)
						{
							if ($config["url_rewriting"] == 'mod_rewrite' && $rewrite == true)
								$text .= '?';
							else
								$text .= '&amp;';
						}
						else
						{
							$text .= '&amp;';
						}
						$text .= $id.$key.'='.$value;
						$count++;
					}
					if (!$onlyhref)
					{
						$text .= "\"";
						$text .= '>'.$contents.'</a>';
					}
				}
			}
		}

		return $text;
	}
}

# vim:ts=4 sw=4 noet
?>