<?php

# CMS - CMS Made Simple
# (c)2004 by Ted Kulp (tedkulp@users.sf.net)
# This project's homepage is: http://cmsmadesimple.org
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# BUT withOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
#$Id$

/**
 * Generic content class.
 *
 * As for some treatment we don't need the extra properties of the content
 * we load them only when required. However, each function which makes use
 * of extra properties should first test if the properties object exist.
 *
 * @since		0.8
 * @package		CMS
 */

class ContentBase extends CmsObjectRelationalMapping
{
	var $table = 'content';
	var $field_maps = array('content_name' => 'name', 'content_alias' => 'alias', 'titleattribute' => 'title_attribute', 'accesskey' => 'access_key', 
	'tabindex' => 'tab_index');

     var $mProperties = array();

	function __construct()
	{
		parent::__construct();
	}
	
	function before_load($type, $fields)
	{
		global $gCms;
		$ops =& $gCms->GetContentOperations();
		$ops->LoadContentType($type);
	}
	
	function LoadProperties()
	{
		global $gCms;
		$content_property =& $gCms->GetOrmClass('content_property');
		$this->mProperties = $content_property->find_all_by_content_id($this->id);
	}
	
    /**
     * Does this have children?
     */
	function HasChildren()
	{
		global $gCms;
		$content_base =& $gCms->GetOrmClass('content_base');
		$count = $content_base->find_count_by_parent_id($this->id);
		
		if (isset($count) && $count > 0)
			return true;
		
		return false;
	}
	
	function HasProperty($name)
	{
		$this->LoadProperties();
		foreach ($this->mProperties as $prop)
		{
			if ($prop->prop_name == $name)
			{
				return true;
			}
		}

		return false;
	}
	
	function GetPropertyValue($name)
	{
		$this->LoadProperties();
		foreach ($this->mProperties as $prop)
		{
			if ($prop->prop_name == $name)
			{
				return $prop->content;
			}
		}

		return '';
	}
	
    /**
     * Returns the Hierarchy
     */
    function Hierarchy()
    {
		global $gCms;
		$contentops =& $gCms->GetContentOperations();
		return $contentops->CreateFriendlyHierarchyPosition($this->hierarchy);
    }
	
    function GetURL($rewrite = true)
    {
		global $gCms;
		$config = &$gCms->GetConfig();
		$url = "";
		$alias = ($this->mAlias != ''?$this->mAlias:$this->mId);
		if ($config["assume_mod_rewrite"] && $rewrite == true)
		{
		    if ($config['use_hierarchy'] == true)
		    {
				$url = $config['root_url']. '/' . $this->HierarchyPath() . (isset($config['page_extension'])?$config['page_extension']:'.html');
		    }
		    else
		    {
				$url = $config['root_url']. '/' . $alias . (isset($config['page_extension'])?$config['page_extension']:'.html');
		    }
		}
		else
		{
		    if (isset($_SERVER['PHP_SELF']) && $config['internal_pretty_urls'] == true)
		    {
				if ($config['use_hierarchy'] == true)
				{
				    $url = $config['root_url'] . '/index.php/' . $this->HierarchyPath() . (isset($config['page_extension'])?$config['page_extension']:'.html');
				}
				else
				{
				    $url = $config['root_url'] . '/index.php/' . $alias . (isset($config['page_extension'])?$config['page_extension']:'.html');
				}
		    }
		    else
		    {
				$url = $config['root_url'] . '/index.php?' . $config['query_var'] . '=' . $alias;
		    }
		}
		return $url;
    }

	function SetAlias($alias)
	{
		global $gCms;
		$config =& $gCms->GetConfig();

		$tolower = false;

		if ($alias == '' && $config['auto_alias_content'] == true)
		{
			$alias = trim($this->mMenuText);
			if ($alias == '')
			{
			    $alias = trim($this->mName);
			}
			
			$tolower = true;
			$alias = munge_string_to_url($alias, $tolower);
			// Make sure auto-generated new alias is not already in use on a different page, if it does, add "-2" to the alias
			$contentops =& $gCms->GetContentOperations();
			$error = $contentops->CheckAliasError($alias);
			if ($error !== FALSE)
			{
				if (FALSE == empty($alias))
				{
					$alias_num_add = 2;
					// If a '-2' version of the alias already exists
					// Check the '-3' version etc.
					while ($contentops->CheckAliasError($alias.'-'.$alias_num_add) !== FALSE)
					{
						$alias_num_add++;
					}
					$alias .= '-'.$alias_num_add;
				}
				else
				{
					$alias = '';
				}
			}
		}

		$this->alias = munge_string_to_url($alias, $tolower);
	}

    /**
     * Function content types to use to say whether or not they should show
     * up in lists where parents of content are set.  This will default to true,
     * but should be used in cases like Separator where you don't want it to 
     * have any children.
     * 
     * @since 0.11
     */
    function WantsChildren()
    {
		return true;
    }

    /**
     * Should this link be used in various places where a link is the only
     * useful output?  (Like next/previous links in cms_selflink, for example)
     */
    function HasUsableLink()
    {
		return true;
    }
}

ContentBase::register_orm_class('ContentBase');

class ContentProperty extends CmsObjectRelationalMapping
{
	var $table = 'content_props';
	
	function __construct()
	{
		parent::__construct();
	}
}

ContentProperty::register_orm_class('ContentProperty');

/**
 * Class that module defined content types must extend.
 *
 * @since		0.9
 * @package		CMS
 */
class CMSModuleContentType extends ContentBase
{
	function __construct()
	{
		parent::__construct();
	}

	//What module do I belong to?  (needed for things like Lang to work right)
	function ModuleName()
	{
		return '';
	}

	function Lang($name, $params=array())
	{
		global $gCms;
		$cmsmodules = &$gCms->modules;
		if (array_key_exists($this->ModuleName(), $cmsmodules))
		{
			return $cmsmodules[$this->ModuleName()]['object']->Lang($name, $params);
		}
		else
		{
			return 'ModuleName() not defined properly';
		}
	}

	/*
	* Returns the instance of the module this content type belongs to
	*
	*/
	function GetModuleInstance() 
	{
		global $gCms;
		$cmsmodules = &$gCms->modules;
		if (array_key_exists($this->ModuleName(), $cmsmodules))
		{
			return $cmsmodules[$this->ModuleName()]['object'];
		}
		else
		{
			return 'ModuleName() not defined properly';
		}
	}
}

?>