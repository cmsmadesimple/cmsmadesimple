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
 * Methods for modules to do miscellaneous functions
 *
 * @since		1.0
 * @package		CMS
 * @license GPL
 */

/**
 * @access private
 */
function cms_module_GetAbout(&$modinstance)
{
	$str = '';
	if ($modinstance->GetAuthor() != '') {
		$str .= "<br />".lang('author').": " . $modinstance->GetAuthor();
		if ($modinstance->GetAuthorEmail() != '') $str .= ' &lt;' . $modinstance->GetAuthorEmail() . '&gt;';
		$str .= "<br />";
	}
	$str .= "<br />".lang('version').": " .$modinstance->GetVersion() . "<br />";

	if ($modinstance->GetChangeLog() != '') {
		$str .= "<br />".lang('changehistory').":<br />";
		$str .= $modinstance->GetChangeLog() . '<br />';
	}
	return $str;
}

/**
 * @access private
 */
function cms_module_GetHelpPage(&$modinstance)
{
	$str = '';
	@ob_start();
	echo $modinstance->GetHelp();
	$str .= @ob_get_contents();
	@ob_end_clean();
	$dependencies = $modinstance->GetDependencies();
	if (count($dependencies) > 0 ) {
		$str .= '<h3>'.lang('dependencies').'</h3>';
		$str .= '<ul>';
		foreach( $dependencies as $dep => $ver ) {
			$str .= '<li>';
			$str .= $dep.' =&gt; '.$ver;
			$str .= '</li>';
		}
		$str .= '</ul>';
	}
	$paramarray = $modinstance->GetParameters();
	if (count($paramarray) > 0) {
		$str .= '<h3>'.lang('parameters').'</h3>';
		$str .= '<ul>';
		foreach ($paramarray as $oneparam) {
			$str .= '<li>';
			$help = '';
			if ($oneparam['optional'] == true) $str .= '<em>(optional)</em> ';
			if( isset($oneparam['help']) ) $help = $oneparam['help'];
			$str .= $oneparam['name'].'="'.$oneparam['default'].'" - '.$help.'</li>';
		}
		$str .= '</ul>';
	}
	return $str;
}

?>