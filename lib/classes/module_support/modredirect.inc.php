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
 * Methods for modules to do redirection related functions
 *
 * @since		1.0
 * @package		CMS
 * @license GPL
 */

/**
 * @access private
 */
function cms_module_RedirectToAdmin(&$modinstance, $page, $params=array())
{
  $urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];
  $url = $page.$urlext;
  if( count($params) )
    {
      foreach ($params as $key=>$value)
      {
        $url .= '&'.$key.'='.rawurlencode($value);
      }
    }
  redirect($url);
}

/**
 * @access private
 */
function cms_module_Redirect(&$modinstance, $id, $action, $returnid='', $params=array(), $inline=false)
{
	$gCms = cmsms();
	$config = $gCms->config;

	$name = $modinstance->GetName();

	#Suggestion by Calguy to make sure 2 actions don't get sent
	if (isset($params['action']))
	{
		unset($params['action']);
	}
	if (isset($params['id']))
	{
		unset($params['id']);
	}
	if (isset($params['module']))
	{
		unset($params['module']);
	}

	if (!$inline && $returnid != '')
		$id = 'cntnt01';

	$text = '';
	if ($returnid != '')
	{
		// gotta get the URL for this page.
		$contentops = $gCms->GetContentOperations();
		$content = $contentops->LoadContentFromId($returnid);
		if( !is_object($content) )
		{
			// no destination content object
			return;
		}
		$text .= $content->GetURL();
		//$text .= 'index.php';

		$parts = parse_url($text);
		if( isset($parts['query']) && $parts['query'] != '?' )
		{
			$text .= '&';
		}
		else
		{
			$text .= '?';
		}		
	}
	else
	{
		$text .= 'moduleinterface.php?';
	}

	$text .= 'mact='.$name.','.$id.','.$action.','.($inline == true?1:0);
	if ($returnid != '')
	{
		$text .= '&'.$id.'returnid='.$returnid;
	}
	else
	{
	    $text .= '&'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];
	}

	foreach ($params as $key=>$value)
	{
		if( $key !== '' && $value !== '' )
		{
			$text .= '&'.$id.$key.'='.rawurlencode($value);
		}
	}
	redirect($text);
}

?>
