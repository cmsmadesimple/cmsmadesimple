<?php
#---------------------------------------------------------------------------------------------------
# Module: UserGuide
# Author: Chris Taylor
# Copyright: (C) 2018 Chris Taylor, chris@binnovative.co.uk
# Licence: GNU General Public License version 3
#          see /UserGuide/lang/LICENCE.txt or <http://www.gnu.org/licenses/>
#---------------------------------------------------------------------------------------------------

if ( !defined('CMS_VERSION') ) exit;
if ( !$this->CheckPermission(UserGuide::MANAGE_PERM) ) return;

if ( isset($params['pid']) && $params['pid'] > 0) {
    $page = UserGuideItem::load_by_id( (int)$params['pid'] );
    $page->toggle_admin_only();
    $this->RedirectToAdminTab('manage');
}