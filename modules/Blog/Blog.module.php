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

class Blog extends CmsModuleBase
{
	function __construct()
	{
		parent::__construct();
	}
	
	function get_admin_description()
	{
		return $this->Lang('description');
	}
	
	function setup()
	{
		$this->register_data_object('BlogPost');
		$this->register_data_object('BlogCategory');
		
		//$this->register_module_plugin('blog');

		$this->register_route('/blog\/rss\.xml$/', array('action' => 'default', 'rss' => true, 'showtemplate' => false));
		$this->register_route('/blog\/category\/(?P<category>[a-zA-Z\-_\ ]+)\.xml$/', array('action' => 'list_by_category', 'rss' => true, 'showtemplate' => false));
		$this->register_route('/blog\/category\/(?P<category>[a-zA-Z\-_\ ]+)$/', array('action' => 'list_by_category'));
		$this->register_route('/blog\/(?P<url>[0-9]{4}\/[0-9]{2}\/[0-9]{2}\/.*?)$/', array('action' => 'detail'));
		$this->register_route('/blog\/(?P<year>[0-9]{4})$/', array('action' => 'filter_list', 'month' => '-1', 'day' => '-1'));
		$this->register_route('/blog\/(?P<year>[0-9]{4})\/(?P<month>[0-9]{2})$/', array('action' => 'filter_list', 'day' => '-1'));
		$this->register_route('/blog\/(?P<year>[0-9]{4})\/(?P<month>[0-9]{2})\/(?P<day>[0-9]{2})$/', array('action' => 'filter_list'));
		
		//$this->add_xmlrpc_method('getRecentPosts', 'metaWeblog');
		//$this->add_xmlrpc_method('getCategories', 'metaWeblog');
		//$this->add_xmlrpc_method('getPost', 'metaWeblog');
		//$this->add_xmlrpc_method('newPost', 'metaWeblog');
		//$this->add_xmlrpc_method('editPost', 'metaWeblog');
		
		//$this->add_temp_event_handler('Core', 'HeaderTagRender', 'handle_header_callback');
		//$this->add_temp_event_handler('Core', 'SearchReindex', 'reindex');
	}
	
	public function handle_header_callback($modulename, $eventname, &$params)
	{
		//We only add this link if another action hasn't done so already -- view by category does it's own thing, for example.
		if (!$this->has_added_header_text())
			$params['content'] .= '<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="' . $this->generate_rss_link() . '" />' . "\n";
	}
	
	protected function generate_rss_link($params = array(), $category = '')
	{
		$pretty_url = 'blog/rss.xml';
		if ($category != '')
		{
			$pretty_url = 'blog/category/' . $category . '.xml';
			if (!array_key_exists('category', $params))
				$params['category'] = $category;
		}
		return $this->create_link('cntnt01', 'rss', $this->get_default_return_id(), '', $params, '', true, false, '', false, $pretty_url);
	}
	
	private function get_default_return_id()
	{
		return cmsms()->get('pageinfo')->return_id;
	}
	
	public function getRecentPosts($blog_id, $username, $password, $number_of_posts)
	{
		$posts = cms_orm('BlogPost')->find_all(array('limit' => array(0, $number_of_posts), 'order' => 'id DESC'));
		$result = array();
		foreach ($posts as $post)
		{
			$result[] = $post->create_xmlrpc_array();
		}
		return $result;
	}
	
	public function getPost($post_id, $username, $password)
	{
		$post = cms_orm('BlogPost')->find_by_id($post_id);
		if ($post != null)
			return $post->create_xmlrpc_array();
		return null;
	}
	
	public function getCategories($blog_id, $username, $password)
	{
		return array_values($this->get_categories());
	}
	
	public function newPost($blog_id, $username, $password, $struct, $publish)
	{
		$post = new BlogPost();
		$post->title = $struct['title'];
		$post->content = $struct['description'];
		$post->status = ($publish ? 'publish' : 'draft');
		if ($post->save())
		{
			if (isset($struct['categories']))
			{
				foreach ($struct['categories'] as $one_cat)
				{
					$post->set_category_by_name($one_cat);
				}
			}
			return $post->id;
		}
		return null;
	}
	
	public function editPost($post_id, $username, $password, $struct, $publish)
	{
		$post = cms_orm('BlogPost')->find_by_id($post_id);
		if ($post != null)
		{
			$post->title = $struct['title'];
			$post->content = $struct['description'];
			$post->status = ($publish ? 'publish' : 'draft');
			if ($post->save())
			{
				if (isset($struct['categories']))
				{
					$post->clear_categories();
					foreach ($struct['categories'] as $one_cat)
					{
						$post->set_category_by_name($one_cat);
					}
				}
				return true;
			}
		}
		return null;
	}
	
	public function get_categories($add_any = false)
	{
		$result = array();
		if ($add_any)
		{
			$result[''] = $this->lang('any');
		}
		$categories = cms_orm('BlogCategory')->find_all(array('order' => 'name ASC'));
		if ($categories != null)
		{
			foreach ($categories as $one_category)
			{
				$result[$one_category->id] = $one_category->name;
			}
		}
		return $result;
	}
	
	public function get_statuses($add_any = false)
	{
		$result = array();
		if ($add_any)
		{
			$result[''] = $this->lang('any');
		}
		$result['draft'] = $this->lang('draft');
		$result['publish'] = $this->lang('published');
		return $result;
	}
	
	public function reindex()
	{
		$posts = cms_orm('BlogPost')->find_all_by_status('publish');
		foreach ($posts as $one_post)
		{
			$one_post->index();
		}
	}
	
	public function get_default_summary_template()
	{
		return '{foreach from=$posts item=entry}
<h3><a href="{$entry->url}">{$entry->title}</a></h3>
<small>
  {$entry->post_date} 
  {if $entry->author ne null}
    {mod_lang string=by} {$entry->author->full_name()}
  {/if}
</small>

<div>
{$entry->get_summary_for_frontend()}
</div>

{if $entry->has_more() eq true}
  <a href="{$entry->url}">{mod_lang string=hasmore} &gt;&gt;</a>
{/if}

{/foreach}';
	}
	
	public function get_default_detail_template()
	{
		return '{if $post ne null}
<h3>{$post->title}</h3>
<small>
  {$post->post_date} 
  {if $post->author ne null}
    {mod_lang string=by} {$post->author->full_name()}
  {/if}
</small>

<div>
{$post->content}
</div>

<hr />

<p>
{cms_module module="comments" module_name="blog" content_id=$post->id}
</p>

{else}
{mod_lang string=postnotfound}
{/if}';
	}
	
	public function get_default_rss_template()
	{
		return '<?xml version="1.0"?>
<rss version="2.0">
	<channel>
		<title>{$sitename|escape}</title>
		<link>{root_url}</link>
		{foreach from=$posts item=entry}
		<item>
			<title><![CDATA[{$entry->title}]]></title>
			<link>{$entry->url}</link>
			<guid>{$entry->url}</guid>
			<pubDate>{$entry->post_date}</pubDate>
			<category><![CDATA[]]></category>
			<description><![CDATA[{$entry->get_summary_for_frontend()}]]></description>
		</item>
		{/foreach}
	</channel>
</rss> 
';
	}
	
	function get_notification_output($priority = 2)
	{
		// if this user has permission to change News articles from
		// Draft to published, and there are draft news articles
		// then display a nice message.
		// this is a priority 2 item.
		if( $priority >= 2 )
		{
			$output = array();

			// dummy priority 3 object
			$obj = new StdClass();
			$obj->priority = 2;
			$obj->html = 'This is a dummy priority 2 notification';
			$output[] = $obj;

			// dummy priority 3 object
			$obj = new StdClass();
			$obj->priority = 3;
			$obj->html = 'This is a dummy priority 3 notification';
			$output[] = $obj;
		}

		return $output;
	}
	
	function GetNotificationOutput($priority = 2)
	{
		return $this->get_notification_output($priority);
	}
}

# vim:ts=4 sw=4 noet
?>