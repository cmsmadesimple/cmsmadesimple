<?php
#BEGIN_LICENSE
#-------------------------------------------------------------------------
# Module: Content (c) 2013 by Robert Campbell
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
if( !isset($gCms) ) exit;
if( !$this->CheckPermission('Modify Site Preferences') ) return;

$page_prefs = CmsContentManagerUtils::get_pagedefaults();
$this->SetCurrentTab('pagedefaults');

if( isset($params['pagedefaults']) && isset($params['submit']) ) {
    // get settings from params
    $modified = 0;
    foreach( array_keys($page_prefs) as $fld ) {
        if( isset($params[$fld]) ) {
            $page_prefs[$fld] = $params[$fld];
            $modified++;
        }
    }
    if( $modified ) {
        // verify
        if( is_array($page_prefs['disallowed_types']) && in_array($page_prefs['contenttype'],$page_prefs['disallowed_types']) ) {
            $this->SetError($this->Lang('error_contenttype_disallowed'));
        }
        else {
            // save
            $this->SetPreference('page_prefs',serialize($page_prefs));
            $this->SetMessage($this->Lang('msg_prefs_saved'));
        }
    }
}

$this->RedirectToAdminTab('','','admin_settings');

#
# EOF
#
?>