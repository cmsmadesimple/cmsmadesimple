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

if (!isset($gCms)) exit;
if (!$this->CheckPermission('Modify Site Preferences')) return;

$fdid = '';
if (isset($params['fdid']))	$fdid = $params['fdid'];

// Get the category details
$query = 'SELECT * FROM '.CMS_DB_PREFIX.'module_news_fielddefs WHERE id = ?';
$row = $db->GetRow($query, array($fdid));

//Now remove the category
$query = "DELETE FROM ".CMS_DB_PREFIX."module_news_fielddefs WHERE id = ?";
$db->Execute($query, array($fdid));

//And remove it from any entries
$query = "DELETE FROM ".CMS_DB_PREFIX."module_news_fieldvals WHERE fielddef_id = ?";
$db->Execute($query, array($fdid));

$db->Execute('UPDATE '.CMS_DB_PREFIX.'module_news_fielddefs SET item_order = (item_order - 1) WHERE item_order > ?', array($row['item_order']));

$params = array('tab_message'=> 'fielddefdeleted', 'active_tab' => 'customfields');
// put mention into the admin log
audit('','News custom: '.$name, 'Field definition deleted');
$this->Setmessage($this->Lang('fielddefdeleted'));
$this->RedirectToAdminTab('customfields','','admin_settings');

#
# EOF
#