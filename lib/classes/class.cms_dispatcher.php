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

class CmsDispatcher extends CmsObject
{
	static private $instance = NULL;
	
	function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Returns an instnace of the CmsDispatcher singleton.
	 *
	 * @return CmsDispatcher The singleton CmsDispatcher instance
	 * @author Ted Kulp
	 **/
	static public function get_instance()
	{
		if (self::$instance == NULL)
		{
			self::$instance = new CmsDispatcher();
		}
		return self::$instance;
	}
	
	public function run()
	{
		//Start up a profiler for getting render times for this page.
		$profiler = CmsProfiler::get_instance('');

		//Can we find a page somewhere in the request?
		$page = CmsRequest::calculate_page_from_request();
		
		//If we need the xmlrpc server, then jump out
		if ($page == 'cmsms/xmlrpc.php')
		{
			CmsXmlrpc::handle_requests();
			exit;
		}

		//Are we using full page caching?  Now is a good time to check and 
		//output any cached data.
		if (CmsConfig::get('full_page_caching'))
		{
			if (cms_request('request:mact') != null && cms_request('request:id') != null && $data = CmsCache::get_instance('page')->get($page))
			{
				cmsms()->response->body($data);
				$endtime = $profiler->get_time();
				$memory = $profiler->get_memory();
				$result = $endtime . " / " . $memory . ' / ' . CmsDatabase::query_count() . " queries (cached)";
				cmsms()->response->body("<!-- {$result} -->\n");
				//if (CmsConfig::get('debug'))
				//{
					cmsms()->response->body($result . "\n");
					cmsms()->response->body(CmsProfiler::get_instance()->report());
				//}
				cmsms()->response->render();
				exit;
			}
		}

		//See if our page matches any predefined routes.  If so,
		//the updated $page will be returned. (No point in matching
		//if we have no url to match).
		if ($page != '')
			$page = CmsRoute::match_route($page);

		//Last ditch effort.  If we still have no page, then
		//grab the default.
		if ($page == '')
			$page = CmsContentOperations::get_default_page_id();
		
		//Ok, we should have SOMETHING at this point.  Grab it's info
		//from the database.
		$page_obj = null;
		if (is_numeric($page) && strpos($page, '.') === FALSE && strpos($page, ',') === FALSE) //Fix for postgres
		{
			$page_obj = CmsPageTree::get_instance()->get_node_by_id($page);
		}
		
		if ($page_obj == null)
		{
			$page_obj = CmsPageTree::get_instance()->get_node_by_alias($page);
		}

		//No info?  Look for a 404 page.  If none, then generate a generic
		//404 error...
		if ($page_obj == null)
		{
			cmsms()->response->send_error_404();
		}
		
		//Backwards compat stuff
		$page_obj->content_id = $page_obj->id;
		cmsms()->current_page = $page_obj;
		
		//Put current page into a CmsApplication variable
		cmsms()->pageinfo = $page_obj;
		
		//Setup variables
		//Some for backwards compat
		cmsms()->content_id = $page_obj->id;
		cmsms()->page = $page;
		cmsms()->page_id = $page;
		cmsms()->page_name = $page_obj->alias;
		cmsms()->position = $page_obj->hierarchy;
		cmsms()->friendly_position = $page_obj->friendly_hierarchy();

		cms_smarty()->assign('content_id', $page_obj->id);
		cms_smarty()->assign('page', $page);
		cms_smarty()->assign('page_id', $page);
		cms_smarty()->assign('page_name', $page_obj->alias);
		cms_smarty()->assign('page_alias', $page_obj->alias);
		cms_smarty()->assign('position', $page_obj->hierarchy);
		cms_smarty()->assign('friendly_position', cmsms()->friendly_position);

		//Render the page object
		$page_output = $page_obj->render();

		//Send any headers.  After the render?  Sure, because modules that
		//have been processed could have changed what values should be in the
		//headers.  Plus, it's output buffered, so the content isn't actually
		//getting sent until the ob_flush below this.
		//echo $pageinfo->send_headers();

		//Calculate our profiler data
		$endtime = $profiler->get_time();
		$memory = $profiler->get_memory();

		//Flush the buffer out to the browser
		//If caching is on, save the data to the cache
		//as well.
		//If not, then just flush it out and save the
		//memory of putting it into a variable.
		if (CmsConfig::get('full_page_caching'))
		{
			CmsCache::get_instance('page')->save($page_output);
		}
		
		//Send the output to the response object
		cmsms()->response->body($page_output);

		$result = $endtime . " / " . $memory . ' / ' . CmsDatabase::query_count() . " queries";
		cmsms()->response->body("<!-- {$result} -->\n");

		if (CmsConfig::get('debug') || !CmsConfig::get('debug'))
		{
			cmsms()->response->body(CmsProfiler::get_instance()->report());
		}
		
		//Finally send it to the browser
		cmsms()->response->render();

		if (CmsApplication::get_preference('enablesitedownmessage') == "1" || CmsConfig::get('debug') == true)
		{
			cms_smarty()->clear_compiled_tpl();
		}

		//Clear out any previews that may be going on
		//CmsPreview::clear_preview();

		if( $page == '__CMS_PREVIEW_PAGE__' && isset($_SESSION['cms_preview']) ) // temporary
		{
			unset($_SESSION['cms_preview']);
		}
	}
}

# vim:ts=4 sw=4 noet
?>