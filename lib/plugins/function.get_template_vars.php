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

if( !function_exists('__cms_function_output_var') ) {
    // because of stupid php 5.3
    function __cms_function_output_accessor($ptype,$key,$depth)
    {
        // $ptype is the parent type
        // $key is the current key we are trying to output
        if( $depth == 0 ) return "\${$key}";
        switch( strtolower($ptype) ) {
        case 'object':
            return "-&gt;{$key}";

        case 'array':
            if( is_numeric($key) ) return "[{$key}]";
            if( strpos($key,' ') !== FALSE ) return "['{$key}']";
            return ".{$key}";

        default:
            // should not get here....
            throw new \LogicException('Invalid accessor type');
        }
    }

    function __cms_function_output_var($key,$val,$ptype = null,$depth = 0) {
        // this outputs something similar to json, but with type information, and indentation
        $type = gettype($val);
        $out = null;
        $depth_str = '&nbsp;&nbsp;&nbsp;';
        $acc = __cms_function_output_accessor($ptype,$key,$depth);
        if( is_object($val) ) {
            $o_items = get_object_vars($val);

            $out .= str_repeat($depth_str,$depth);
            $out .= "{$acc} <em>(object of type: ".get_class($val).")</em> = {";
            if( count($o_items) ) $out .= '<br/>';
            foreach( $o_items as $o_key => $o_val ) {
                $out .= __cms_function_output_var($o_key,$o_val,$type,$depth+1);
            }
            $out .= str_repeat($depth_str,$depth)."}<br/>";
        }
        else if( is_array($val) ) {
            $out .= str_repeat($depth_str,$depth);
            $out .= "{$acc} <em>($type)</em> = [<br/>";
            foreach( $val as $a_key => $a_val ) {
                $out .= __cms_function_output_var($a_key,$a_val,$type,$depth+1);
            }
            $out .= str_repeat($depth_str,$depth)."]<br/>";
        }
        else if( is_callable($val) ) {
            $out .= str_repeat($depth_str,$depth)."{$acc} <em>($type)</em> = callable<br/>";
        }
        else {
            $out .= str_repeat($depth_str,$depth);
            if( $depth == 0 ) {
                $out .= '$'.$key;
            }
            else {
                $out .= '.'.$key;
            }
            $out .= " <em>($type)</em> = $val<br/>";
        }
        return $out;
    }
}

function smarty_cms_function_get_template_vars($params, &$smarty)
{
	$tpl_vars = $smarty->getTemplateVars();
	$str = '<pre>';
	foreach( $tpl_vars as $key => $value ) {
        $str .= __cms_function_output_var($key,$value);
    }
    $str .= '</pre>';
	if( isset($params['assign']) ){
	    $smarty->assign(trim($params['assign']),$str);
	    return;
    }
	return $str;
}

#
# EOF
#