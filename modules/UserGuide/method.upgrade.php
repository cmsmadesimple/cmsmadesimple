<?php
#-------------------------------------------------------------------------
# Module: UserGuide
# Author: Chris Taylor
# Copyright: (C) 2016 Chris Taylor, chris@binnovative.co.uk
# Licence: GNU General Public License version 3
#          see /UserGuide/lang/LICENCE.txt or <http://www.gnu.org/licenses/>
#-------------------------------------------------------------------------

if ( !defined('CMS_VERSION') ) exit;

$db = $this->GetDb();

if ( version_compare($oldversion, '1.1', '<') ) {
	$sql = 'SET @rownumber = 0';
	$db->Execute($sql);
	$sql = 'UPDATE '.CMS_DB_PREFIX.'module_userguide
		SET position = (@rownumber:=@rownumber+1)
		ORDER BY position, id';
	$db->Execute($sql);
}
