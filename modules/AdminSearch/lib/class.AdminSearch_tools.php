<?php
#---------------------------------------------------------------------------
# CMS Made Simple - Power for the professional, Simplicity for the end user.
# (c) 2004 - 2011 by Ted Kulp
# (c) 2011 - 2018 by the CMS Made Simple Development Team
# (c) 2018 and beyond by the CMS Made Simple Foundation
# This project's homepage is: https://www.cmsmadesimple.org
#---------------------------------------------------------------------------
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
# Or read it online: http://www.gnu.org/licenses/licenses.html#GPL
#---------------------------------------------------------------------------

final class AdminSearch_tools 
{
  private function __construct() {}

  public static function get_slave_classes()
  {
    $key = __CLASS__.'slaves'.get_userid(FALSE);
    $results = null;
    $data =  cms_cache_handler::get_instance()->get($key);
    if( !$data ) {
      // cache needs refreshing.    
      $results = array();

      // get module results.
      $mod = cms_utils::get_module('AdminSearch');
      $modulelist = $mod->GetModulesWithCapability('AdminSearch');
      if( is_array($modulelist) && count($modulelist) ) {
	foreach( $modulelist as $module_name ) {
	  $mod = cms_utils::get_module($module_name);
	  if( !is_object($mod) ) continue;
	  if( !method_exists($mod,'get_adminsearch_slaves') ) continue;

	  $classlist = $mod->get_adminsearch_slaves();
	  if( is_array($classlist) && count($classlist) ) {
	    foreach( $classlist as $class_name ) {
	      if( !class_exists($class_name) ) continue;
	      if( !is_subclass_of($class_name,'AdminSearch_slave') ) continue;
	      $obj = new $class_name;
	      if( !is_object($obj) ) continue;

	      $tmp = array();
	      $tmp['module'] = $module_name;
	      $tmp['class'] = $class_name;
	      $name = $tmp['name'] = $obj->get_name();
	      $tmp['description'] = $obj->get_description();
	      $tmp['section_description'] = $obj->get_section_description();
	      if( !$name ) continue;
	      if( isset($results[$name]) ) continue;

	      $results[$name] = $tmp;
	    }
	  }
	}
      }

      // store the results into the cache.
      cms_cache_handler::get_instance()->set($key,serialize($results));
    }
    else {
      $results = unserialize($data);
    }

    return $results;
  }

  public static function summarize($text,$len = 255)
  {
    $text = strip_tags($text);
    return substr($text,0,$len);
  }

} // end of class

#
# EOF
#