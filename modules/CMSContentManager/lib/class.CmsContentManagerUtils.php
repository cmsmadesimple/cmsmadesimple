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

/**
 * @package CMS
 */

/**
 * A set of utility methods for the CmsContentManager module.
 *
 * This is an internal class.  Use of this class in third party modules will not be supported.
 *
 * @package CMS
 * @internal
 * @ignore
 * @author Robert Campbell
 * @copyright Copyright (c) 2013, Robert Campbell <calguy1000@cmsmadesimple.org>
 */
final class CmsContentManagerUtils
{
  private function __construct() {}

  public static function get_pagedefaults()
  {
      $tpl_id = null;
      try {
          $tpl = CmsLayoutTemplate::load_dflt_by_type(CmsLayoutTemplateType::CORE.'::page');
          $tpl_id = $tpl->get_id();
      }
      catch( \CmsDataNotFoundException $e ) {
          $type = CmsLayoutTemplateType::load(CmsLayoutTemplateType::CORE.'::page');
          $list = CmsLayoutTemplate::load_all_by_type($type);
          $tpl = $list[0];
          $tpl_id = $tpl->get_id();
      }

      $page_prefs = array('contenttype'=>'content', // string
                          'disallowed_types'=>'', // array of strings
                          'design_id'=>CmsLayoutCollection::load_default()->get_id(), // int
                          'template_id'=>$tpl_id,
                          'parent_id'=>-2, // int
                          'secure'=>0, // boolean
                          'cachable'=>1, // boolean
                          'active'=>1, // boolean
                          'showinmenu'=>1, // boolean
                          'metadata'=>'', // string
                          'content'=>'', // string
                          'searchable'=>1, // boolean
                          'addteditors'=>array(), // array of ints.
                          'extra1'=>'', // string
                          'extra2'=>'', // string
                          'extra3'=>''); // string
      $mod = cms_utils::get_module('CMSContentManager');
      $tmp = $mod->GetPreference('page_prefs');
      if( $tmp ) $page_prefs = unserialize($tmp);

      return $page_prefs;
  }

  public static function locking_enabled()
  {
      $mod = cms_utils::get_module('CMSContentManager');
      $timeout = (int) $mod->GetPreference('locktimeout');
      if( $timeout > 0 ) return TRUE;
      return FALSE;
  }

  public static function get_pagenav_display()
  {
    $userid = get_userid(FALSE);
    $pref = cms_userprefs::get($userid,'ce_navdisplay');
    if( !$pref ) {
        $mod = cms_utils::get_module('CMSContentManager');
        $pref = $mod->GetPreference('list_namecolumn');
        if( !$pref ) $pref = 'title';
    }
    return $pref;
  }
  
} // end of class

#
# EOF
#