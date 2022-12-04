<?php
#CMS - CMS Made Simple
#(c)2013 by Robert Campbell (calguy1000@cmsmadesimple.org)
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

function smarty_function_cms_html_options($params, $smarty)
{
    $options = null;
    if( !isset($params['options']) ) {
        if( isset($params['value']) && isset($params['label']) ) {
            $opt = array();
            $opt['label'] = $params['label'];
            $opt['value'] = $params['value'];
            if( isset($params['title']) ) $opt['title'] = $params['title'];
            if( isset($params['class']) ) $opt['class'] = $params['class'];
            $options = $opt;
        }
        else {
            return;
        }
    }
    else {
        $options = $params['options'];
    }

    $out = null;
    if( is_array($options) && count($options) ) {
        $selected = null;
        if( isset($params['selected']) ) {
            $selected = $params['selected'];
            if( !is_array($selected) ) $selected = explode(',',$selected);
        }
        $out = CmsFormUtils::create_option($params['options'],$selected);
    }

    if( isset($params['assign']) ) {
        $smarty->assign($params['assign'],$out);
        return;
    }
    return $out;
}
