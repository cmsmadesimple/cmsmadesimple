<?php
#CMS - CMS Made Simple
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

function smarty_cms_function_cms_yesno($params, $smarty)
{
    $opts = array(0=>lang('no'),1=>lang('yes'));

    $out = '';
    foreach( $opts as $k => $v ) {
        $out .= '<option value="'.$k.'"';
        if( isset($params['selected']) && $k == $params['selected'] ) $out .= ' selected="selected"';
        $out .= '>'.$v.'</option>';
    }
    $out .= "\n";

    if( isset($params['assign']) ) {
        $smarty->assign(trim($params['assign']),$out);
        return;
    }
    return $out;
}

#
# EOF
#
?>