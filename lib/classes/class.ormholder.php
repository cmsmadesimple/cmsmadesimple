<?php
#(c)2004 by Ted Kulp (wishy@users.sf.net)
#This project's homepage is: http://cmsmadesimple.sf.net
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

class OrmHolder extends Overloader
{
	var $params = array();
	
	// Getter
	function _get($n, &$v)
	{
		$v = $this->params[$n];
		return true;
	}

	// Setter
	function _set($n, $val)
	{
		$this->params[$n] = $val;
		return true;
	}
	
	function _call($function, $arguments, &$return)
	{
		return true;
	}
}

if (function_exists("overload") && phpversion() < 5)
{
   overload("OrmHolder");
}

?>