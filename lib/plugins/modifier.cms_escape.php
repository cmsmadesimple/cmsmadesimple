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

/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty escape modifier plugin
 *
 * Type:     modifier<br>
 * Name:     escape<br>
 * Purpose:  Escape the string according to escapement type
 *
 * @link     http://smarty.php.net/manual/en/language.modifier.escape.php
 *          escape (Smarty online manual)
 * @author   Monte Ohrt <monte at ohrt dot com>
 *
 * @param string $string html|htmlall|url|quotes|hex|hexentity|javascript
 * @param string $esc_type
 * @param string $char_set
 *
 * @return string
 *
 * calguy1000: change default char-set to utf-8
 */
 
function smarty_modifier_cms_escape($string, $esc_type = 'html', $char_set = 'utf-8') : string
{
  $string = (string) $string;
	$esc_type = strtolower($esc_type);
    switch ($esc_type) {
        case 'html':
            return htmlspecialchars($string, ENT_QUOTES, $char_set);

        case 'htmlall':
            return htmlentities($string, ENT_QUOTES, $char_set);

        case 'url':
            return rawurlencode($string);

        case 'urlpathinfo':
            return str_replace('%2F','/',rawurlencode($string));
            
        case 'quotes':
            // escape unescaped single quotes
            return preg_replace("%(?<!\\\\)'%", "\\'", $string);

        case 'hex':
            // escape every character into hex
            $return = '';
            for ($x=0, $xMax = strlen($string); $x < $xMax; $x++) {
                $return .= '%' . bin2hex($string[$x]);
            }
            return $return;
            
        case 'hexentity':
            $return = '';
            for ($x=0, $xMax = strlen($string); $x < $xMax; $x++) {
                $return .= '&#x' . bin2hex($string[$x]) . ';';
            }
            return $return;

        case 'decentity':
            $return = '';
            for ($x=0, $xMax = strlen($string); $x < $xMax; $x++) {
                $return .= '&#' . ord($string[$x]) . ';';
            }
            return $return;

        case 'javascript':
            // escape quotes and backslashes, newlines, etc.
            return strtr($string, array('\\'=>'\\\\',"'"=>"\\'",'"'=>'\\"',"\r"=>'\\r',"\n"=>'\\n','</'=>'<\/'));
            
        case 'mail':
            // safe way to display e-mail address on a web page
            return str_replace(array('@', '.'),array(' [AT] ', ' [DOT] '), $string);
            
        case 'nonstd':
           // escape non-standard chars, such as ms document quotes
           $_res = '';
           for($_i = 0, $_len = strlen($string); $_i < $_len; $_i++) {
               $_ord = ord(substr($string, $_i, 1));
               // non-standard char, escape it
               if($_ord >= 126){
                   $_res .= '&#' . $_ord . ';';
               }
               else {
                   $_res .= substr($string, $_i, 1);
               }
           }
           return $_res;

		case 'htmltiny':
			return str_replace("<", "&lt;", $string);
					
        default:
            return $string;
    }
}
?>