<?php
#CMS - CMS Made Simple
#(c)2004-2010 by Ted Kulp (ted@cmsmadesimple.org)
#Visit our homepage at: http://cmsmadesimple.org
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

/**
 * Methods for modules to do template related functions
 *
 * @since		1.0
 * @package		CMS
 * @license GPL
 */

/**
 * @access private
 */
function cms_module_ListTemplates(&$modinstance, $modulename = '')
{
	$db = CmsApp::get_instance()->GetDb();
	$retresult = array();

	$query = 'SELECT * from '.CMS_DB_PREFIX.'module_templates WHERE module_name = ? ORDER BY template_name ASC';
	$result = $db->Execute($query, array($modulename != ''?$modulename:$modinstance->GetName()));

	while (isset($result) && !$result->EOF) {
		$retresult[] = $result->fields['template_name'];
		$result->MoveNext();
	}

	return $retresult;
}

/**
 * Returns a database saved template.  This should be used for admin functions only, as it doesn't
 * follow any smarty caching rules.
 * @access private
 */
function cms_module_GetTemplate(&$modinstance, $tpl_name, $modulename = '')
{
	$db = CmsApp::get_instance()->GetDb();

	$query = 'SELECT * from '.CMS_DB_PREFIX.'module_templates WHERE module_name = ? and template_name = ?';
	$result = $db->Execute($query, array($modulename != ''?$modulename:$modinstance->GetName(), $tpl_name));

	if ($result && $result->RecordCount() > 0) {
		$row = $result->FetchRow();
		return $row['content'];
	}

	return '';
}

/**
 * Returns contents of the template that resides in modules/ModuleName/templates/{template_name}.tpl
 * Code adapted from the Guestbook module
 * @access private
 */
function cms_module_GetTemplateFromFile(&$modinstance, $template_name)
{
	$ok = (strpos($template_name, '..') === false);
	if (!$ok) return;

	$tpl_base  = CMS_ROOT_PATH.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR;
	$tpl_base .= $modinstance->GetName().DIRECTORY_SEPARATOR.'templates';
	$template = $tpl_base.DIRECTORY_SEPARATOR.$template_name;
	if( !endswith($template,'.tpl') ) $template .= '.tpl';
	if (is_file($template)) {
		return file_get_contents($template);
	}
	else {
		return null;
	}
}

/**
 * @access private
 */
function cms_module_SetTemplate(&$modinstance, $tpl_name, $content, $modulename = '')
{
	$db = CmsApp::get_instance()->GetDb();

	$query = 'SELECT module_name FROM '.CMS_DB_PREFIX.'module_templates WHERE module_name = ? and template_name = ?';
	$result = $db->Execute($query, array($modulename != ''?$modulename:$modinstance->GetName(), $tpl_name));

	$time = $db->DBTimeStamp(time());
	if ($result && $result->RecordCount() < 1) {
		$query = 'INSERT INTO '.CMS_DB_PREFIX.'module_templates (module_name, template_name, content, create_date, modified_date) VALUES (?,?,?,'.$time.','.$time.')';
		$db->Execute($query, array($modulename != ''?$modulename:$modinstance->GetName(), $tpl_name, $content));
	}
	else {
		$query = 'UPDATE '.CMS_DB_PREFIX.'module_templates SET content = ?, modified_date = '.$time.' WHERE module_name = ? AND template_name = ?';
		$db->Execute($query, array($content, $modulename != ''?$modulename:$modinstance->GetName(), $tpl_name));
	}
}

/**
 * @access private
 */
function cms_module_DeleteTemplate(&$modinstance, $tpl_name = '', $modulename = '')
{
	$db = CmsApp::get_instance()->GetDb();

	$parms = array($modulename != ''?$modulename:$modinstance->GetName());
	$query = "DELETE FROM ".CMS_DB_PREFIX."module_templates WHERE module_name = ?";
	if( $tpl_name != '' ) {
		$query .= 'AND template_name = ?';
	    $parms[] = $tpl_name;
	}
	$result = $db->Execute($query, $parms);
	return ($result == false)?false:true;
}

/**
 * @access private
 */
function cms_module_ProcessTemplate(&$modinstance, $tpl_name, $designation = '', $cache = false, $cacheid = '')
{
	$ok = (strpos($tpl_name, '..') === false);
	if (!$ok) return;

    $smarty = $modinstance->GetActionTemplateObject();
    if( !$smarty ) $smarty = Smarty_CMS::get_instance();
	$oldcache = $smarty->caching;
	if( $smarty->caching != Smarty::CACHING_OFF ) {
		$smarty->caching = ($modinstance->can_cache_output())?Smarty::CACHING_LIFETIME_CURRENT:Smarty::CACHING_OFF;
	}
	$result = $smarty->fetch('module_file_tpl:'.$modinstance->GetName().';'.$tpl_name, $cacheid, ($designation != ''?$designation:$modinstance->GetName()));
	$smarty->caching = $oldcache;

	return $result;
}

/**
 * Given a template in a variable, this method processes it through smarty
 * note, there is no caching involved.
 * @access private
 */
function cms_module_ProcessTemplateFromData(&$modinstance, $data)
{
    $smarty = $modinstance->GetActionTemplateObject();
    if( !$smarty ) $smarty = Smarty_CMS::get_instance();
    $_contents = $smarty->fetch('string:'.$data);
    return $_contents;
}

/**
 * @access private
 */
function cms_module_ProcessTemplateFromDatabase(&$modinstance, $tpl_name, $designation = '', $cache = false, $modulename = '')
{
    $smarty = $modinstance->GetActionTemplateObject();
    if( !$smarty ) $smarty = Smarty_CMS::get_instance();
    if( $modulename == '' ) $modulename = $modinstance->GetName();

    $oldcache = $smarty->caching;
    if( $smarty->caching != Smarty::CACHING_OFF ) {
        $smarty->caching = ($modinstance->can_cache_output())?Smarty::CACHING_LIFETIME_CURRENT:Smarty::CACHING_OFF;
    }
    $result = $smarty->fetch('module_db_tpl:'.$modulename.';'.$tpl_name, '', ($designation != ''?$designation:$modulename));
    $smarty->caching = $oldcache;

    return $result;
}

?>
