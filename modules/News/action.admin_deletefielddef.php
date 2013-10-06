<?php
#CMS - CMS Made Simple
#(c)2004-6 by Ted Kulp (ted@cmsmadesimple.org)
#This project's homepage is: http://cmsmadesimple.org
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
if (!isset($gCms)) exit;
if (!$this->CheckPermission('Modify Site Preferences')) return;

$fdid = '';
if (isset($params['fdid']))
{
	$fdid = $params['fdid'];
}

// Get the category details
$query = 'SELECT * FROM '.cms_db_prefix().'module_news_fielddefs WHERE id = ?';
$row = $db->GetRow($query, array($fdid));

//Now remove the category
$query = "DELETE FROM ".cms_db_prefix()."module_news_fielddefs WHERE id = ?";
$db->Execute($query, array($fdid));

//And remove it from any entries
$query = "DELETE FROM ".cms_db_prefix()."module_news_fieldvals WHERE fielddef_id = ?";
$db->Execute($query, array($fdid));

$db->Execute('UPDATE '.cms_db_prefix().'module_news_fielddefs SET item_order = (item_order - 1) WHERE item_order > ?', array($row['item_order']));

$params = array('tab_message'=> 'fielddefdeleted', 'active_tab' => 'customfields');
		// put mention into the admin log
		audit($name, 'News custom: '.$name, 'Field definition deleted');
$this->Redirect($id, 'defaultadmin', $returnid, $params);

?>