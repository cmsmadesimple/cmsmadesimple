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

function smarty_function_anchor($params, $smarty)
{
    $content = cms_utils::get_current_content();
    if( !is_object($content) ) return;

    $class="";
    $title="";
    $tabindex="";
    $accesskey="";
    if (isset($params['class'])) $class = ' class="'.$params['class'].'"';
    if (isset($params['title'])) $title = ' title="'.$params['title'].'"';
    if (isset($params['tabindex'])) $tabindex = ' tabindex="'.$params['tabindex'].'"';
    if (isset($params['accesskey'])) $accesskey = ' accesskey="'.$params['accesskey'].'"';

    $url = $content->GetURL().'#'.trim($params['anchor']);
    $url = str_replace('&amp;','***',$url);
    $url = str_replace('&', '&amp;', $url);
    $url = str_replace('***','&amp;',$url);
    if (isset($params['onlyhref']) && ($params['onlyhref'] == '1' || $params['onlyhref'] == 'true')) {
        $tmp =  $url;
    }
    else {
	$text = get_parameter_value( $params, 'text','<!-- anchor tag: no text provided -->anchor');
        $tmp = '<a href="'.$url.'"'.$class.$title.$tabindex.$accesskey.'>'.$text.'</a>';
    }

    if( isset($params['assign']) ){
        $smarty->assign(trim($params['assign']),$tmp);
        return;
    }
    echo $tmp;
}

#
# EOF
#