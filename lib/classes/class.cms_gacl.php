<?php
#CMS - CMS Made Simple
#(c)2004-2006 by Ted Kulp (ted@cmsmadesimple.org)
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

require_once(cms_join_path(ROOT_DIR,'lib','phpgacl','gacl.class.php'));

class CmsGacl extends gacl
{	
	static private $instance = NULL;

	function __construct()
	{
		//parent::__construct();
		
		$options = array();

		$available_options = array('db','debug','items_per_page','max_select_box_items','max_search_return_items','db_table_prefix','db_type','db_host','db_user','db_password','db_name','caching','force_cache_expire','cache_dir','cache_expire_time');

		//Values supplied in $options array overwrite those in the config file.
		$config_file = cms_join_path(ROOT_DIR,'lib','phpgacl','gacl.ini.php');
		if (file_exists($config_file))
		{
			$config = parse_ini_file($config_file);

			if ( is_array($config) )
			{
				$gacl_options = array_merge($config, $options);
			}

			unset($config);
		}

		if (is_array($options))
		{
			foreach ($options as $key => $value)
			{
				$this->debug_text("Option: $key");

				if (in_array($key, $available_options) )
				{
					$this->debug_text("Valid Config options: $key");
					$property = '_'.$key;
					$this->$property = $value;
				}
				else
				{
					$this->debug_text("ERROR: Config option: $key is not a valid option");
				}
			}
		}

		$this->db = cms_db();
		$this->_db_table_prefix = 'gacl';
		$this->_cache_dir = TMP_CACHE_LOCATION;
		$this->_caching = !CmsConfig::get('debug');

		if ( $this->_caching )
		{
			if (!class_exists('Hashed_Cache_Lite'))
			{
				require_once(cms_join_path(dirname(dirname(__FILE__)),'pear','cache','lite','Hashed.php'));
			}

			/**
			 * Cache options. We default to the highest performance. If you run in to cache corruption problems,
			 * Change all the 'false' to 'true', this will slow things down slightly however.
			 */
			$cache_options = array(
				'caching' => $this->_caching,
				'cacheDir' => $this->_cache_dir.'/',
				'lifeTime' => $this->_cache_expire_time,
				'fileLocking' => TRUE,
				'writeControl' => FALSE,
				'readControl' => FALSE,
				'memoryCaching' => TRUE,
				'automaticSerialization' => FALSE				
				);

			$this->Cache_Lite = new Hashed_Cache_Lite($cache_options);
		}

		//return true;
	}
	
	/**
	 * Returns an instnace of the CmsGalc singleton.
	 *
	 * @return CmsGacl The singleton CmsGacl instance
	 * @author Ted Kulp
	 **/
	static public function get_instance()
	{
		if (self::$instance == NULL)
		{
			self::$instance = new CmsGacl();
		}
		return self::$instance;
	}
	
}

# vim:ts=4 sw=4 noet
?>