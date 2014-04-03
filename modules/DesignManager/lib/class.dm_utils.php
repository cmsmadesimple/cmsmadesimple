<?php
#-------------------------------------------------------------------------
# Module: AdminSearch - A CMSMS addon module to provide template management.
# (c) 2012 by Robert Campbell <calguy1000@cmsmadesimple.org>
#
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
#
#-------------------------------------------------------------------------

final class dm_utils
{
    public function __construct() {}

    public static function locking_enabled()
    {
		$mod = cms_utils::get_module('DesignManager');
        $timeout = $mod->GetPreference('lock_timeout');
        if( $timeout > 0 ) return TRUE;
        return FALSE;
    }

	public static function get_template_locks()
	{
		static $_locks = null;
		static $_locks_loaded = FALSE;
		if( !$locks_loaded ) {
			$_locks_loaded = TRUE;
			$tmp = CmsLockOperations::get_locks('template');
			if( is_array($tmp) && count($tmp) ) {
				$_locks = array();
				foreach( $tmp as $lock_obj ) {
					$_locks[$lock_obj['oid']] = $lock_obj;
				}
			}
		}
		return $_locks;
	}

	public static function get_css_locks()
	{
		static $_locks = null;
		static $_locks_loaded = FALSE;
		if( !$locks_loaded ) {
			$_locks_loaded = TRUE;
			$tmp = CmsLockOperations::get_locks('stylesheet');
			if( is_array($tmp) && count($tmp) ) {
				$_locks = array();
				foreach( $tmp as $lock_obj ) {
					$_locks[$lock_obj['oid']] = $lock_obj;
				}
			}
		}
		return $_locks;
	}

} // end of class

#
# EOF
#
?>