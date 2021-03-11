<?php
#---------------------------------------------------------------------------
# CMS Made Simple - Power for the professional, Simplicity for the end user.
# (c) 2004 - 2011 by Ted Kulp
# (c) 2011 - 2018 by the CMS Made Simple Development Team
# (c) 2018 and beyond by the CMS Made Simple Foundation
# This project's homepage is: https://www.cmsmadesimple.org
#---------------------------------------------------------------------------
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
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
# Or read it online: http://www.gnu.org/licenses/licenses.html#GPL
#---------------------------------------------------------------------------

function smarty_function_menu_text($params, $smarty)
{
    $gCms = CmsApp::get_instance();
    $content_obj = $gCms->get_content_object();

	if (!is_object($content_obj) || $content_obj->Id() == -1) {
		// We've a custom error message...  set a message
		$result="404 Error";
    } else {
		$result = $content_obj->MenuText();
        $result = preg_replace("/\{\/?php\}/", "", $result);
	}

	if( isset($params['assign']) ){
		$smarty->assign(trim($params['assign']),$result);
		return;
	}

	return $result;
}

#
# EOF
#