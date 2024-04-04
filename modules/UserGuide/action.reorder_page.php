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

if ( empty($params['pid']) || !isset($params['after']) || $params['after']<0 ) return;


// fix issue where new pages previously didn't have 'position' set
$sql = "SELECT * from ".CMS_DB_PREFIX."module_userguide
    WHERE position IS NULL OR position=0";
$res = $db->Execute($sql);
$errorCount = $res->RecordCount();
if ($errorCount>0) {
    $sql = "UPDATE ".CMS_DB_PREFIX."module_userguide SET position=id";
    $res = $db->Execute($sql);
    echo $this->Lang('order_error');
    return;
}



$page = new UserGuideItem();
if ($params['after']==0) {
    $afterPosition = 0;
} else {
    $page = UserGuideItem::load_by_id((int)$params['after']);
    $afterPosition = $page->__get('position');
}
$page = UserGuideItem::load_by_id((int)$params['pid']);
$fromPosition = $page->__get('position');

$db = \cms_utils::get_db();
if ($fromPosition>$afterPosition) {
    $sql = "UPDATE ".CMS_DB_PREFIX."module_userguide
        SET position=position+1
        WHERE position > '$afterPosition' AND position < '$fromPosition'";
    $res = $db->Execute($sql);

    $sql = "UPDATE ".CMS_DB_PREFIX."module_userguide
        SET position=$afterPosition+1
        WHERE id=".(int)$params['pid'];
    $res = $db->Execute($sql);

} else {
    $sql = "UPDATE ".CMS_DB_PREFIX."module_userguide
        SET position=position-1
        WHERE position > '$fromPosition' AND position <= '$afterPosition'";
    $res = $db->Execute($sql);

    $sql = "UPDATE ".CMS_DB_PREFIX."module_userguide
        SET position=$afterPosition
        WHERE id=".(int)$params['pid'];
    $res = $db->Execute($sql);
}


