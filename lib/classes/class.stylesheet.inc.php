<?php
#CMS - CMS Made Simple
#(c)2004-6 by Ted Kulp (ted@cmsmadesimple.org)
#This project's homepage is: http://cmsmadesimple.org
#
#This program is free software; you can redistribute it and/or modify
#it under the terms of the GNU General Public License as published by
#the Free Software Foundation; either version 2 of the License, or
#(at your option) any later version.
#
#This program is distributed in the hope that it will be useful,
#BUT withOUT ANY WARRANTY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
#You should have received a copy of the GNU General Public License
#along with this program; if not, write to the Free Software
#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
#$Id$

/**
 * Generic stylesheet class. This can be used for any logged in stylesheet or stylesheet related function.
 *
 * @since		0.11
 * @package		CMS
 */
class Stylesheet extends CmsObjectRelationalMappting
{
	var $params = array('id' => -1, 'name' => '', 'value' => '', 'media_type' => '');
	var $field_maps = array('css_id' => 'id', 'css_name' => 'name', 'css_text' => 'value');
	var $table = 'css';
	var $sequence = 'css_seq';
}

if (function_exists("overload") && phpversion() < 5)
{
   overload("Stylesheet");
}
Stylesheet::register_orm_class('Stylesheet');

# vim:ts=4 sw=4 noet
?>