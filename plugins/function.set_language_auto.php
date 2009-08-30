<?php

# CMSMS - CMS Made Simple
#
# (c)2004 by Ted Kulp (wishy@users.sf.net)
#
# This project's homepage is: http://cmsmadesimple.org
#
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
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

function smarty_cms_function_set_language_auto($params, &$smarty) 
{
  $lang = '';

  // first see if accept_lang is set
  if( isset($_REQUEST['lang']) )
    {
      $lang = trim($_REQUEST['lang']);
      $lang = CmsNls::to_lang($lang);
    }
  else if( isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) )
    {
      $lang = CmsNls::to_lang($_SERVER['HTTP_ACCEPT_LANGUAGE']);
    }

  if( isset($params['allow']) )
    {
      $allow = explode(',',trim($params['allow']));
      if( !in_array($lang,$allow) )
	{
	  // not an allowed language.
	  return;
	}
    }

  if( $lang )
    {
      global $gCms;
      $gCms->variables['desired_language'] = $lang;
    }
}

function smarty_cms_help_function_set_language_auto() {
  echo lang('help_function_set_language_auto');
}


?>
