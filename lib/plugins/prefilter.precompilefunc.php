<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
#Visit our homepage at: http://www.cmsmadesimple.org
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

use \CMSMS\HookManager;

function smarty_prefilter_precompilefunc($tpl_output, $smarty)
{
	$result = explode(':', $smarty->_current_file);

	if (count($result) > 1)	{
		if( startswith($result[0],'tmp_') ) $result[0] = 'template';

		switch ($result[0]) {
        case 'cms_stylesheet':
        case 'stylesheet':
            HookManager::do_hook('Core::StylesheetPreCompile',array('stylesheet'=>&$tpl_output));
            break;

        case "content":
            HookManager::do_hook('Core::ContentPreCompile', array('content' => &$tpl_output));
            break;

        case "cms_template":
        case 'tpl_top':
        case 'tpl_body':
        case 'tpl_head':
        case "template":
            HookManager::do_hook('Core::TemplatePreCompile', array('template' => &$tpl_output,'type'=>$result[0]));
        break;

        default:
            break;
		}
	}

    HookManager::do_hook('Core::SmartyPreCompile', array('content' => &$tpl_output));

	return $tpl_output;
}
