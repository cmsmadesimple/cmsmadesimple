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

function smarty_function_cms_module_hint($params, $smarty)
{
    if( !isset($params['module']) ) return;

    $module = trim($params['module']);
    $modobj = cms_utils::get_module($module);
    if( !is_object($modobj) ) return;

    $data = cms_utils::get_app_data('__CMS_MODULE_HINT__'.$module);
    if( !$data ) $data = array();

    // warning, no check here if the module understands the parameter.
    foreach( $params as $key => $value ) {
      if( $key == 'module' ) continue;
      $data[$key] = $value;
    }

    cms_utils::set_app_data('__CMS_MODULE_HINT__'.$module,$data);
}

#
# EOF
#