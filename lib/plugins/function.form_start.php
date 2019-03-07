<?php
// CMS - CMS Made Simple
// (c)2004 by Ted Kulp (wishy@users.sf.net)
// Visit our homepage at: http://www.cmsmadesimple.org
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

function smarty_function_form_start($params, &$smarty)
{
    $gCms = cmsms();
    $tagparms = $mactparms = [];
    if( !get_parameter_value($params,'module') ) $params['module'] = $smarty->getTemplateVars('_module');
    if( !get_parameter_value($params,'mid') ) $params['mid'] = $smarty->getTemplateVars('actionid');
    if( !get_parameter_value($params,'returnid') ) $params['returnid'] = $smarty->getTemplateVars('returnid');
    if( !get_parameter_value($params,'action') ) $params['action'] = $smarty->getTemplateVars('_action');
    if( !isset($params['inline']) ) $params['inline'] = 0;

    $out = CmsFormUtils::create_form_start($params);
    if(isset($params['assign']) ) {
        $smarty->assign($params['assign'], $out);
        return;
    }
    return $out;
}
