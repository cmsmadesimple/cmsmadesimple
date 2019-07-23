<?php
#CMS - CMS Made Simple
#(c)2004-2012 by Ted Kulp (wishy@users.sf.net)
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
#
#$Id$

/**
 * Translation functions/classes
 * @package CMS
 * @license GPL
 */


/**
 * @package CMS
 */

/**
 * Retrieve a translation for a specific string in a specific realm.
 * Called with the realm first, followed by the key, this method will attempt
 * to load the specific realm data if necessary before doing translation.
 *
 * This method accepts a variable number of arguments.  Any arguments after
 * the realm and the key are passed to the key via vsprintf
 *
 * i.e: lang_by_realm('tasks','my_string');
 *
 * @since 1.8
 * @param string $realm The realm
 * @param string $key   The lang key and any vspring arguments.
 * @return string
 */
function lang_by_realm($realm,$key)
{
    $args = func_get_args();
    return CmsLangOperations::lang_from_realm($args);
}


/**
 * Temporarily allow accessing admin realm from within a frontend action.
 *
 * @internal
 * @ignore
 */
function allow_admin_lang($flag = TRUE)
{
    return CmsLangOperations::allow_nonadmin_lang($flag);
}


/**
 * Return a translated string for the default 'admin' realm.
 * This function is merely a wrapper around the lang_by_realm function
 * that assumes the realm is 'admin'.
 *
 * This method will throw a notice if it is called from a frontend request
 *
 * i.e: lang('title');
 *
 * @param string $key The key to translate and then any vsprintf arguments for the key.
 * @see lang_by_realm
 * @return string
 */
function lang($key)
{
    $args = func_get_args();
    return CmsLangOperations::lang($args);
}

/**
 * Retrieve a list of installed languages that is suitable for use in a dropdown.
 *
 * @param bool $allow_none Optionally adds 'none' (translated to current language) to the top of the list.
 * @return associative array of lang keys and display strings.
 * @internal
 * @deeprecated
 */
function get_language_list($allow_none = true)
{
    $tmp = array();
    if( $allow_none ) $tmp = array(''=>lang('nodefault'));

    $langs = CmsNlsOperations::get_installed_languages();
    asort($langs);
    foreach( $langs as $key  ) {
        $obj = CmsNlsOperations::get_language_info($key);
        $value = $obj->display();
        if( $obj->fullname() ) $value .= ' ('.$obj->fullname().')';
        $tmp[$key] = $value;
    }

    return $tmp;
}
