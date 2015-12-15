<?php
#-------------------------------------------------------------------------
# Module: DesignManager - A CMSMS addon module to provide template management.
# (c) 2014 by Robert Campbell <calguy1000@cmsmadesimple.org>
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
if( !isset($gCms) ) exit;
if( !$this->VisibleToAdminUser() ) exit;

$uid = get_userid(FALSE);
$type = (isset($params['type']) ) ? trim($params['type']) : 'template';
$is_admin = UserOperations::get_instance($uid,1);

$type = strtolower($type);
switch( $type ) {
case 'tpl':
case 'templates':
case 'template':
    $type = 'template';
    $this->SetCurrentTab('templates');
    break;
case 'css':
case 'stylesheets':
case 'stylesheet':
    $type = 'stylesheet';
    $this->SetCurrentTab('stylesheets');
    break;
default:
    $this->Redirect($id,'defaultadmin');
}

if( $is_admin ) {
    // clear all locks of type content
    $db = cmsms()->GetDb();
    $sql = 'DELETE FROM '.CMS_DB_PREFIX().CmsLock::LOCK_TABLE.' WHERE type = ?';
    $db->Execute($sql,array($type));
    audit('',$this->GetName(),'Cleared all content locks');
} else {
    // clear only my locks
    CmsLockOperations::delete_for_user($type);
    audit('',$this->GetName(),'Cleared his own content locks');
}

$this->SetMessage($this->Lang('msg_lockscleared'));
$this->RedirectToAdminTab();
