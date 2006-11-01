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

	function ContentBase()
	{
		parent::__construct();
	}
	
	function before_load($type, $fields)
	{
		global $gCms;
		$ops =& $gCms->GetContentOperations();
		$ops->LoadContentType($type);
	}
	
	function after_load()
	{
		global $gCms;
		$content_property =& $gCms->GetOrmClass('content_property');
		$this->mProperties = $content_property->find_all_by_content_id($this->id);
	}
	
	function HasProperty($name)
	{
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
		foreach ($this->mProperties as $prop)
		{
			if ($prop->prop_name == $name)
			{
				return $prop->content;
			}
		}

		return '';
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
}

ContentBase::register_orm_class('ContentBase');

class ContentProperty extends CmsObjectRelationalMapping
{
	var $table = 'content_props';
	
	function ContentProperty()
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