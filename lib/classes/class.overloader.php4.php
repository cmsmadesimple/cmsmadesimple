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

class Overloader extends Object
{
	// Getter
	function __get($n,&$r)
	{
		$this->_get($n, $r);
		return true;
	}

	// Setter
	function __set($n, $val)
	{
		$this->_set($n, $val);
		return true;
	}

    function __call($method, $args, &$r) 
    {
		if (method_exists($this, "_call"))
		{
        	$this->_call($method, $args, $r);
        	return true;
		}
		return false;
    }
}

if (function_exists("overload") && phpversion() < 5)
{
   overload("Overloader");
}

?>