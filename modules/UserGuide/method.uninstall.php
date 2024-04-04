<?php
#---------------------------------------------------------------------------------------------------
# Module: UserGuide
# Author: Chris Taylor
# Copyright: (C) 2018 Chris Taylor, chris@binnovative.co.uk
# Licence: GNU General Public License version 3
#          see /UserGuide/lang/LICENCE.txt or <http://www.gnu.org/licenses/>
#---------------------------------------------------------------------------------------------------

if ( !defined('CMS_VERSION') ) exit;

$db = $this->GetDb();

// remove the database tables
$dict = NewDataDictionary( $db );
$sqlarray = $dict->DropTableSQL( CMS_DB_PREFIX.'module_userguide');
$dict->ExecuteSQLArray($sqlarray);

// remove the permissions
$this->RemovePermission(UserGuide::MANAGE_PERM);
$this->RemovePermission(UserGuide::USE_PERM);

// remove all preferences
$this->RemovePreference();


