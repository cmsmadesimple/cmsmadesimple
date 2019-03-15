<?php
#CMS - CMS Made Simple
#(c)2004-2013 by Ted Kulp (wishy@users.sf.net)
#(c)2011-2016 by The CMSMS Dev Team
#Visit our homepage at: http://www.cmsmadesimple.org
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

namespace CMSMS;

$gCms = cmsms();
global $CMS_INSTALL_PAGE;
if( !isset($CMS_INSTALL_PAGE) && !$gCms->is_frontend_request() ) {
    // admin requests only
    $hook_manager = $gCms->get_hook_manager();

    // add hooks for after a module install/uninstalled/reinstalled
    $clear_cache_fn = function( $parms ) use ($gCms) {
        $gCms->clear_cached_files();
    };

    $hook_manager->add_hook( 'Core::ModuleUpgraded', $clear_cache_fn );
    $hook_manager->add_hook( 'Core::ModuleInstalled', $clear_cache_fn );
    $hook_manager->add_hook( 'Core::ModuleUninstalled', $clear_cache_fn );
    $hook_manager->add_hook( 'Core::AfterModuleActivated', $clear_cache_fn );
}
