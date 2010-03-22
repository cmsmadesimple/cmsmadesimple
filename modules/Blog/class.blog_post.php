<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#CMS - CMS Made Simple
#(c)2004-2008 by Ted Kulp (ted@cmsmadesimple.org)
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
#
#$Id$

class BlogPost extends CmsModuleOrm
{
	var $table = 'blog_posts';
	var $module_name = 'Blog';

	function __construct()
	{
		parent::__construct();
		$this->post_date = new CmsDateTime();
	}
	
	function setup()
	{
		$this->create_belongs_to_association('author', 'CmsUser', 'author_id');
		$this->create_has_and_belongs_to_many_association('categories', 'BlogCategory', 'blog_post_categories', 'category_id', 'post_id', array('order' => 'name ASC'));
	}
	
	function split_content()
	{
		return preg_split("/<!--\ ?more\ ?-->/i", $this->content);
	}
	
	function has_more()
	{
		return $this->params['summary'] != '' || count($this->split_content()) > 1;
	}
	
	function get_summary_for_frontend()
	{
		$result = '';

		if ($this->params['summary'] != '')
		{
			$result = $this->params['summary'];
		}
		else
		{
			$parts = $this->split_content();
			$result = $parts[0];
		}
		
		//return CmsTextProcessor::process($result, $this->get_text_processor());
		return $result;
	}
	
	function get_details_for_frontend()
	{
		//return CmsTextProcessor::process($this->content, $this->get_text_processor());
		return $this->content;
	}
	
	/*
	function get_text_processor()
	{
		if ($this->processor !== null)
		{
			return $this->processor;
		}
		return 'markdown';
	}
	*/
	
	function get_url()
	{
		$smarty = cms_smarty();
		$module = $smarty->get_template_vars('cms_mapi_module');
		if ($module != null && $module instanceof Blog)
		{
			$id = $smarty->get_template_vars('cms_mapi_id');
			$return_id = $smarty->get_template_vars('cms_mapi_return_id');
			return $module->create_link($id, 'detail', $return_id, '', array('post_id' => $this->id), '', true, false, '', false, 'blog/' . $this->params['url']);
		}
		else
		{
			return $this->params['url'];
		}
	}
	
	function in_category($id)
	{
		foreach ($this->categories as $one_category)
		{
			if ($one_category->id == $id)
			{
				return true;
			}
		}
		
		return false;
	}
	
	function set_category($id)
	{
		if (!$this->in_category($id))
		{
			$date = cms_db()->DBTimeStamp(time());
			cms_db()->Execute("INSERT INTO " . CMS_DB_PREFIX . "blog_post_categories (category_id, post_id, create_date, modified_date) VALUES (?, ?, {$date}, {$date})", array($id, $this->id));
			unset($this->associations['categories']);
		}
	}
	
	function set_category_by_name($name)
	{
		$cat = cms_orm('BlogCategory')->find_by_name($name);
		if ($cat != null)
		{
			$this->set_category($cat->id);
		}
	}
	
	function clear_categories()
	{
		if ($this->id > 0)
		{
			cms_db()->Execute("DELETE FROM " . CMS_DB_PREFIX . 'blog_post_categories WHERE post_id = ?', array($this->id));
			unset($this->associations['categories']);
		}
	}
	
	function validate()
	{
		$this->validate_not_blank('title', lang('nofieldgiven',array(lang('title'))));
		$this->validate_not_blank('content', lang('nofieldgiven',array(lang('content'))));
		if ($this->title != '')
			$this->validate_not_blank('slug', lang('nofieldgiven',array('slug')));
	}
	
	function before_validation()
	{
		//if this is the first save of this post, generate a decent slug
		if ($this->slug == '' || $this->url == '')
		{
			$this->slug = munge_string_to_url($this->title, true);
			$this->url = $this->post_date->format('Y/m/d/') . $this->slug;
		}
	}
	
	function before_save()
	{
		//Make sure the date is split out properly
		$this->post_year = $this->post_date->format('Y');
		$this->post_month = $this->post_date->format('m');
		$this->post_day = $this->post_date->format('d');
	}
	
	function after_save()
	{
		if ($this->status == 'publish')
			$this->index();
		else
			$this->remove_index(); //In case the status was changed from publish to sommething else
	}
	
	function after_delete()
	{
		$this->remove_index();
	}
	
	function create_xmlrpc_array()
	{
		$result = array();
		
		$result['title'] = $this->title;
		$result['link'] = $this->get_url();
		$result['permaLink'] = $this->get_url();
		$result['userid'] = $this->author_id;
		$result['description'] = $this->content;
		$result['postid'] = $this->id;
		
		$categories = array();
		foreach ($this->categories as $one_cat)
		{
			$categories[] = $one_cat->name;
		}
		if (count($categories) > 0)
			$result['categories'] = $categories;
		
		return $result;
	}
	
	public function index()
	{
		//CmsSearch::get_instance()->add_content('Blog', 'BlogPost', $this->id, $this->get_url(), $this->title, $this->content, 'en_US', $this->get_summary_for_frontend());
	}
	
	public function remove_index()
	{
		//CmsSearch::get_instance()->remove_content('Blog', 'BlogPost', $this->id);
	}
}

# vim:ts=4 sw=4 noet
?>