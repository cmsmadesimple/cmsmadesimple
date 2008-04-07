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

ini_set('include_path', cms_join_path(ROOT_DIR, 'lib'));
include_once(cms_join_path(ROOT_DIR, 'lib', 'Zend', 'Search', 'Lucene.php'));

class CmsSearch extends CmsObject
{
	static private $instance = NULL;

	private $index_path = '';
	private $index = null;

	function __construct()
	{
		parent::__construct();
		$this->index_path = cms_join_path(ROOT_DIR, 'tmp', 'search');
		try
		{
			$this->index = Zend_Search_Lucene::open($this->index_path);
		}
		catch (Zend_Search_Lucene_Exception $e)
		{
			$this->index = Zend_Search_Lucene::create($this->index_path);
		}
	}
	
	/**
	 * Returns an instance of the CmsSearch singleton.
	 *
	 * @return CmsSearch The singleton CmsSearch instance
	 * @author Ted Kulp
	 **/
	static public function get_instance()
	{
		if (self::$instance == NULL)
		{
			self::$instance = new CmsSearch();
		}
		return self::$instance;
	}

	function add_content($module_name, $extra_attr, $object_id, $url = '', $title = '', $content = '', $lang = 'en_US', $teaser = '', $pretty_url = '')
	{
		$doc = new Zend_Search_Lucene_Document();

		$doc->addField(Zend_Search_Lucene_Field::UnIndexed('module_name', $module_name));
		$doc->addField(Zend_Search_Lucene_Field::UnIndexed('extra_attr', $extra_attr));
		$doc->addField(Zend_Search_Lucene_Field::UnIndexed('object_id', $object_id));
		$doc->addField(Zend_Search_Lucene_Field::UnIndexed('url', $url));
		$doc->addField(Zend_Search_Lucene_Field::UnIndexed('pretty_url', $pretty_url));
		$doc->addField(Zend_Search_Lucene_Field::UnIndexed('teaser', $teaser));
		$doc->addField(Zend_Search_Lucene_Field::UnIndexed('language', $lang));
		$doc->addField(Zend_Search_Lucene_Field::Text('title', $title));
		$doc->addField(Zend_Search_Lucene_Field::UnStored('content', $content));
		
		$this->index->addDocument($doc);
	}
	
	function add_document(Zend_Search_Lucene_Document $doc)
	{
		$this->index->addDocument($doc);
	}
	
	function remove_content($module_name, $extra_attr = '', $object_id = '')
	{
		$find_query = 'module_name:' . $module_name . ' ';
		if ($extra_attr != '')
			$find_query .= 'extra_attr:' . $extra_attr . ' ';
		if ($object_id != '')
			$find_query .= 'object_id:' . $object_id . ' ';

		$this->remove_document($find_query);
	}
	
	function remove_document($find_query = '')
	{
		$hits = $this->index->find(trim($find_str));
		foreach ($hits as $hit)
		{
		    $this->index->delete($hit->id);
		}
	}
	
	function commit()
	{
		$this->index->commit();
	}
	
	function index_count()
	{
		return $this->index->count();
	}
	
	function index_num_docs()
	{
		return $this->index->numDocs();
	}
	
	function optimize()
	{
		return $this->index->optimize();
	}
	
	function parse($find_query)
	{
		return $this->index->parse($find_query);
	}
	
	function find($find_query)
	{
		return $this->index->find($find_query);
	}
	
	function reindex()
	{
		CmsContentOperations::reindex_content();
		CmsEventOperations::send_event('Core', 'search_reindex');
	}
}

# vim:ts=4 sw=4 noet
?>