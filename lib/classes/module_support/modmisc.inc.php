<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#CMS - CMS Made Simple
#(c)2004-2010 by Ted Kulp (ted@cmsmadesimple.org)
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
	if ($modinstance->GetAuthor() != '')
	{
		$str .= "<br />".lang('author').": " . $modinstance->GetAuthor();
		if ($modinstance->GetAuthorEmail() != '')
		{
			$str .= ' &lt;' . $modinstance->GetAuthorEmail() . '&gt;';
		}
		$str .= "<br />";
	}
	$str .= "<br />".lang('version').": " .$modinstance->GetVersion() . "<br />";

	if ($modinstance->GetChangeLog() != '')
	{
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
	if (count($dependencies) > 0 )
	{
		$str .= '<h3>'.lang('dependencies').'</h3>';
		$str .= '<ul>';
		foreach( $dependencies as $dep => $ver )
		{
			$str .= '<li>';
			$str .= $dep.' =&gt; '.$ver;
			$str .= '</li>';
		}
		$str .= '</ul>';
	}
	$paramarray = $modinstance->GetParameters();
	if (count($paramarray) > 0)
	{
		$str .= '<h3>'.lang('parameters').'</h3>';
		$str .= '<ul>';
		foreach ($paramarray as $oneparam)
		{
			$str .= '<li>';
			if ($oneparam['optional'] == true)
			{
				$str .= '<em>(optional)</em> ';
			}
			$help = '';
			if( isset($oneparam['help']) ) {
				$help = $oneparam['help'];
			} else if( $oneparam['name'] == 'lang' ) {
				$help = lang('module_param_lang');
			}
			$str .= $oneparam['name'].'="'.$oneparam['default'].'" - '.$help.'</li>';
		}
		$str .= '</ul>';
	}
	return $str;
}

/**
 * @access private
 */
function cms_module_CreatePagination(&$modinstance, $id, $action, $returnid, $page, $totalrows, $limit, $inline=false)
{
	$config = cmsms()->GetConfig();

	$goto = 'index.php';
	if ($returnid == '')
	{
	  $urlext = '?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];
	  $goto = 'moduleinterface.php'.$urlext;
	}
	$link = '<a href="'.$goto.'&amp;module='.$modinstance->GetName().'&amp;'.$id.'returnid='.$id.$returnid.'&amp;'.$id.'action=' . $action .'&amp;'.$id.'page=';
	if ($inline)
	{
		$link .= '&amp;'.$config['query_var'].'='.$returnid;
	}
	$page_string = "";
	$from = ($page * $limit) - $limit;
	$numofpages = floor($totalrows / $limit);
	if ($numofpages * $limit < $totalrows)
	{
		$numofpages++;
	}

	if ($numofpages > 1)
	{
		if($page != 1)
		{
			$pageprev = $page-1;
			$page_string .= $link.$pageprev."\">".lang('previous')."</a>&nbsp;";
		}
		else
		{
			$page_string .= lang('previous')." ";
		}

		for($i = 1; $i <= $numofpages; $i++)
		{
			if($i == $page)
			{
				 $page_string .= $i."&nbsp;";
			}
			else
			{
				 $page_string .= $link.$i."\">$i</a>&nbsp;";
			}
		}

		if (($totalrows - ($limit * $page)) > 0)
		{
			$pagenext = $page+1;
			$page_string .= $link.$pagenext."\">".lang('next')."</a>";
		}
		else
		{
			$page_string .= lang('next')." ";
		}
	}

	return $page_string;
}

?>