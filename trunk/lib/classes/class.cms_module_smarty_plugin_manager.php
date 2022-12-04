<?php
#BEGIN_LICENSE
#-------------------------------------------------------------------------
# Module: cms_content_tree (c) 2010 by Robert Campbell
#         (calguy1000@cmsmadesimple.org)
#
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2005 by Ted Kulp (wishy@cmsmadesimple.org)
# Visit our homepage at: http://www.cmsmadesimple.org
#
#-------------------------------------------------------------------------
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# However, as a special exception to the GPL, this software is distributed
# as an addon module to CMS Made Simple.  You may not use this software
# in any Non GPL version of CMS Made simple, or in any version of CMS
# Made simple that does not indicate clearly and obviously in its admin
# section that the site was built with CMS Made simple.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
# Or read it online: http://www.gnu.org/licenses/licenses.html#GPL
#
#-------------------------------------------------------------------------
#END_LICENSE


/**
 * Classes and functions to manage static smarty plugins
 * @package CMS
 * @license GPL
 */


/**
 * A singleton class to manage static smarty plugins registered by modules.
 *
 * @package CMS
 * @license GPL
 * @author Robert Campbell <calguy1000@cmsmadesimple.org>
 * @internal
 * @access private
 * @since  1.11
 */
final class cms_module_smarty_plugin_manager
{
	/**
	 * @ignore
	 */
	private static $_instance;

	/**
	 * @ignore
	 */
	private $_data;

	/**
	 * @ignore
	 */
	private $_loaded;

	/**
	 * @ignore
	 */
	private $_modified;

	/**
	 * A flag indicating that the plugin is intended to be available for the frontend
	 */
	const AVAIL_FRONTEND = 1;

	/**
	 * A flag indicating that the plugin is intended to be available for admin templates
	 */
	const AVAIL_ADMIN    = 2;

	/**
	 * @ignore
	 */
	protected function __construct() {}

	/**
	 * Get the single allowed instance of this class
	 */
	public static function &get_instance()
	{
		if( !self::$_instance ) self::$_instance = new cms_module_smarty_plugin_manager();
		return self::$_instance;
	}

	/**
	 * @ignore
	 */
	private function _load()
	{
		if( $this->_loaded == true ) return;

		$this->_loaded = TRUE;
		$this->_data = array();
		$db = CmsApp::get_instance()->GetDb();
		$query = 'SELECT * FROM '.CMS_DB_PREFIX.'module_smarty_plugins ORDER BY module';
		$tmp = $db->GetArray($query);
		if( is_array($tmp) ) {
			for( $i = 0, $n = count($tmp); $i < $n; $i++ ) {
				$row = $tmp[$i];
				$row['callback'] = unserialize($row['callback']);
				// todo, verify signature
				$this->_data[$row['sig']] = $row;
			}
		}
	}

	/**
	 * @ignore
	 */
	private function _save()
	{
		if( !is_array($this->_data) || count($this->_data) == 0 || $this->_modified == FALSE )
			return;

		$db = CmsApp::get_instance()->GetDb();
		$query = 'TRUNCATE TABLE '.CMS_DB_PREFIX.'module_smarty_plugins';
		$db->Execute($query);

		$query = 'INSERT INTO '.CMS_DB_PREFIX.'module_smarty_plugins (sig,name,module,type,callback,cachable,available) VALUES';
		$fmt = " ('%s','%s','%s','%s','%s',%d,%d),";
		foreach( $this->_data as $key => $row ) {
			$query .= sprintf($fmt,$row['sig'],$row['name'],$row['module'],$row['type'],serialize($row['callback']),$row['cachable'],$row['available']);
		}
		if( endswith($query,',') ) $query = substr($query,0,-1);
		$dbr = $db->Execute($query);
		if( !$dbr ) return FALSE;
		$this->_modified = FALSE;
		return TRUE;
	}

	/**
	 * Attempt to load a specific plugin
	 * This is called by the smarty class when looking for an unknwon plugin.
	 *
	 * @internal
	 */
	public static function load_plugin($name,$type)
	{
		$row = self::get_instance()->find($name,$type);
		if( !is_array($row) ) return;

		// load the module
		$module = cms_utils::get_module($row['module']);
		if( $module ) {
			// fix the callback, incase somebody used 'this' in the string.
			if( is_array($row['callback']) ) {
				// its an array
				if( count($row['callback']) == 2 ) {
					// first element is some kind of string... do some magic to point to the module object
					if( !is_string($row['callback'][0]) || strtolower($row['callback'][0]) == 'this') $row['callback'][0] = $row['module'];
				}
				else {
					// an array with only one item?
					audit('','cms_module_smarty_plugin_manager','Cannot load plugin '.$row['name'].' from module '.$row['module'].' because of errors in the callback');
					return;
				}
			}
			else if( startswith($row['callback'],'::') ) {
				// ::method syntax (implies module name)
				$row['callback'] = array($row['module'],substr($row['callback'],2));
			}
			else {
				// assume it's just a method name
				$row['callback'] = array($row['module'],$row['callback']);
			}
		}
		if( !is_callable($row['callback']) ) {
			// it's in the db... but not callable.
			audit('','cms_module_smarty_plugin_manager','Cannot load plugin '.$row['name'].' from module '.$row['module'].' because callback not callable (module disabled?)');
			$row['callback'] = array($row['module'],'function_plugin');
			return $row;
		}
		return $row;
	}

	/**
	 * Find the details for a specific plugin
	 *
	 * @param string $name
	 * @param string $type
	 * @return array
	 */
	public function find($name,$type)
	{
		$this->_load();
		if( is_array($this->_data) && count($this->_data) ) {
			foreach( $this->_data as $key => $row ) {
				if( $row['name'] == $name && $row['type'] == $type ) return $row;
			}
		}
	}

	/**
	 * Add information about a plugin to the database
	 * This method is normally called from within a module's installation directory.
	 *
	 * @param string $module_name The module name
	 * @param string $name  The plugin name
	 * @param string $type  The plugin type (function,block,modifier)
	 * @param callable $callback A static function to call.
	 * @param bool $cachable Whether the plugin is cachable
	 * @param int  $available Flags indicating the availability of the plugin.   See AVAil_ADMIN AND AVAIL_FRONTEND
	 */
	public static function addStatic($module_name,$name,$type,$callback,$cachable = TRUE,$available = 0)
	{
		return self::get_instance()->add($module_name,$name,$type,$callback,$cachable,$available);
	}


	/**
	 * add information about a plugin to the database.
	 * This method is normally called from within a module's installation directory.
	 *
	 * @param string $module_name The module name
	 * @param string $name  The plugin name
	 * @param string $type  The plugin type (function,block,modifier)
	 * @param callable $callback A static function to call.
	 * @param bool $cachable Whether the plugin is cachable
	 * @param int  $available Flags indicating the availability of the plugin.   See AVAil_ADMIN AND AVAIL_FRONTEND
	 */
	public function add($module_name,$name,$type,$callback,$cachable = TRUE,$available = 0)
	{
		$this->_load();
		if( !is_array($this->_data) ) $this->_data = array();

		// todo... check valid input

		$sig = md5($name.$module_name.serialize($callback));
		if( !isset($this->_data[$sig]) ) {
			if( $available == 0 ) $available = self::AVAIL_FRONTEND;
			$this->_data[$name] =
				array('sig'=>$sig, 'module'=>$module_name, 'name'=>$name, 'type'=>$type,
					  'callback'=>$callback, 'cachable'=>(int)$cachable, 'available'=>$available);
			$this->_modified = TRUE;
			return $this->_save();
		}
		return TRUE;
	}

	/**
	 * Remove all of the plugins for a module
	 *
	 * @param string $module_name
	 */
	public static function remove_by_module($module_name)
	{
		self::get_instance()->_remove_by_module($module_name);
	}

	/**
	 * Remove all of the plugins for a module
	 *
	 * @param string $module_name
	 */
	public function _remove_by_module($module_name)
	{
		$this->_load();
		if( is_array($this->_data) && count($this->_data) ) {
			$new = array();
			foreach( $this->_data as $key => $row ) {
				if( $row['module'] != $module_name ) $new[$key] = $row;
			}
			$this->_data = $new;
			$this->_modified = true;
			$this->_save();
		}
	}

	/**
	 * Remove the plugin by it's name and type
	 *
	 * @param string $name
	 * @param string $type (function,block,modifier)
	 */
	public static function remove_by_name($name)
	{
		self::get_instance()->_remove_by_name($name);
	}

	/**
	 * Remove the plugin by it's name and type
	 *
	 * @param string $name
	 * @param string $type (function,block,modifier)
	 */
	public function _remove_by_name($name)
	{
		$this->_load();
		if( is_array($this->_data) && count($this->_data) ) {
			$new = array();
			foreach( $this->_data as $key => $row ) {
				if( $name != $row['name'] ) $new[$key] = $row;
			}
			$this->_data = $new;
			$this->_modified = true;
			$this->_save();
		}
	}

} // end of class

#
# EOF
#
?>