<?php
#BEGIN_LICENSE
#-------------------------------------------------------------------------
# Module: CMSContentManager (c) 2013 by Robert Campbell
#         (calguy1000@cmsmadesimple.org)
#  A module for managing content in CMSMS.
#
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2004 by Ted Kulp (wishy@cmsmadesimple.org)
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
    $pref = cms_userprefs::get_for_user($userid,'ce_navdisplay');
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
?>
