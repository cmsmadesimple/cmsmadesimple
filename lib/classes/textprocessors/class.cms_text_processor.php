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

class CmsTextProcessor extends CmsObject
{
	static private $instance = NULL;

	function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Returns an instance of the CmsTextProcessor singleton.
	 *
	 * @return CmsTextProcessor The singleton CmsTextProcessor instance
	 * @author Ted Kulp
	 **/
	static public function get_instance()
	{
		if (self::$instance == NULL)
		{
			self::$instance = new CmsTextProcessor();
		}
		return self::$instance;
	}
	
	static public function list_processors()
	{
		return array('none', 'simple', 'markdown');
	}
	
	static public function list_processors_for_dropdown()
	{
		$result = array();

		foreach (self::list_processors() as $one_item)
		{
			$result[$one_item] = ucwords($one_item);
		}
		
		return $result;
	}
	
	static public function process($text, $processor = 'none')
	{
		switch ($processor)
		{
			case 'markdown':
				include_once(cms_join_path(ROOT_DIR,'lib','smarty','plugins','modifier.markdown.php'));
				return Markdown($text);
				break;

			case 'nl2br':
			case 'simple':
				return nl2br($text);
				break;

			default:
				return $text;
		}
	}
}

# vim:ts=4 sw=4 noet
?>