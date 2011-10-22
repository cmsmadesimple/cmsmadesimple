<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
#This project's homepage is: http://www.cmsmadesimple.org
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
#$Id: functions.php 253 2010-03-16 18:45:57Z calguy1000 $

function installerShowErrorPage( $error, $fragment='' )
{
	include cms_join_path(CMS_INSTALL_BASE, 'templates', 'installer_start.tpl');
	echo '<p class="error">';
	echo $error;
	if (!empty($fragment))
	{
		echo ' <a class="external" rel="external" href="'. CMS_INSTALL_HELP_URL.'#'.$fragment. '">?</a>';
	}
	echo '</p>';
	include cms_join_path(CMS_INSTALL_BASE, 'templates', 'installer_end.tpl');
	exit;
}
function installerHelpLanguage( $lang, $default_null=null )
{
	if( (!is_null($default_null)) && ($default_null == $lang) ) return '';
	return substr($lang, 0, 2);
}

function installer_done_failed( $msg, $error )
{
	$test = new StdClass();
	$test->error = $error;
	$test->messages = array();

	if(! $error)
	{
		$test->messages[] = $msg . lang('installer_done');
	}
	else
	{
		$test->messages[] = $msg . lang('installer_failed');
	}

	return $test;
}


function installer_create_tablesql( $dbdict, $table, $flds, $taboptarray )
{
	$_sqlarray = $dbdict->CreateTableSQL($table, $flds, $taboptarray);
	$_return = $dbdict->ExecuteSQLArray($_sqlarray);
	$_ado_ret = ($_return == 2) ? lang('done') : lang('failed');

	return lang('install_creating_table', $table, $_ado_ret);
}


function installer_create_indexsql( $dbdict, $table, $arr_index )
{
	if(! is_array($arr_index)) return 'Not array!';

	$_indexname = 'index_by_' . implode('_', $arr_index);
	$_indexflds = implode(',', $arr_index);

	$_sqlarray = $dbdict->CreateIndexSQL($_indexname, $table, $_indexflds);
	$_return = $dbdict->ExecuteSQLArray($_sqlarray);
	$_ado_ret = ($_return == 2) ? lang('done') : lang('failed');

	return lang('install_creating_index', $table, $_ado_ret);
}


function installer_create_permission( $permission_name, $permission_text )
{
	$gCms = cmsms();

	$test = new StdClass();
	$test->error = false;
	$test->messages = array();

	$_msg = lang('create_permission', $permission_text);

	$_return = cms_mapi_create_permission($gCms, $permission_name, $permission_text);
	if($_return)
	{
		$test->messages[] = $_msg . lang('installer_done');
	}
	else
	{
		$test->messages[] = $_msg . lang('installer_failed');
		$test->error = true;
	}

	return $test;
}






function upgrade_add_column_sql( $table, $schema )
{
	$gCms = cmsms();
	$db = $gCms->GetDB();
	$dbdict = NewDataDictionary($db);

	$test = new StdClass();
	$test->error = false;
	$test->messages = array();

	$_msg = lang('add_column_sql', $table);

	$_sqlarray = $dbdict->AddColumnSQL(cms_db_prefix().$table, $schema);
	$_return = $dbdict->ExecuteSQLArray($_sqlarray);
	if($_return == 2)
	{
		$test->messages[] = $_msg . lang('installer_done');
	}
	else
	{
		$test->messages[] = $_msg . lang('installer_failed');
		$test->error = true;
	}

	return $test;
}


function upgrade_installing_module( $module )
{
	$gCms = cmsms();

	$test = new StdClass();
	$test->error = false;
	$test->messages = array();

	$_msg = lang('installing_module', $module);

	$modops = $gCms->GetModuleOperations();
	$result = $modops->InstallModule($module, false);
	if($result[0] == false)
	{
		$test->messages[] = $_msg . lang('installer_failed');
		$test->error = true;
	}
	else
	{
		$test->messages[] = $_msg . lang('installer_done');
		$test->messages[] = $gCms->modules[$module]['object']->InstallPostMessage();
	}

	return $test;
}


function upgrade_schema_version( $version )
{
	$gCms = cmsms();
	$db = $gCms->GetDB();

	$test = new StdClass();
	$test->error = false;
	$test->messages = array();

	$_msg = lang('updating_schema_version', $version);

	$query = 'UPDATE '.cms_db_prefix(). 'version SET version = ?';
	$return = $db->Execute($query, $version);
	if($return)
	{
		$test->messages[] = $_msg . lang('installer_done');
	}
	else
	{
		$test->messages[] = $_msg . lang('installer_failed');
		$test->error = true;
	}

	return $test;
}

?>
