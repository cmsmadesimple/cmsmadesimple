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

function smarty_function_set_language($params, &$smarty) 
{
  if( isset($params['lang']) )
    {
      $str = trim($params['lang']);
      global $gCms;
      $gCms->variables['desired_language'] = $str;
    }
  else if( isset($params['language']) )
    {
      $str = trim($params['lang']);
      global $gCms;
      $gCms->variables['desired_language'] = $str;
    }
}

function smarty_help_function_set_language() {
  echo lang('help_function_contact_form');
}


?>
