<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#CMS - CMS Made Simple
#(c)2004-2007 by Ted Kulp (ted@cmsmadesimple.org)
#This project's homepage is: http://cmsmadesimple.org
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
 * Wraps the DateTime class in PHP 5.2+.  It allows us to have a consistent
 * object for handling dates and is especially useful when handing dates over
 * to the ORM system.  Includes methods for manipulating the stored date and
 * time.
 *
 * @since 2.0
 * @author Ted Kulp
 **/
class CmsDateTime extends CmsObject
{
	private $datetime = null;

	function __construct($curtime = 'now')
	{
		if (is_int($curtime))
			$curtime = strftime('%x %X', $curtime);

		try
		{
			$this->datetime = date_create($curtime);
		}
		catch (Exception $e)
		{
			$this->datetime = date_create('now');
		}
	}
	
	function __toString()
	{
		return $this->to_format_string();
	}
	
	function modify($modify)
	{
		date_modify($this->datetime, $modify);
	}
	
	function format($format)
	{
		try
		{
			return @date_format($this->datetime, $format);
		}
		catch (Exception $e)
		{
			return '';
		}
	}
	
	function strftime($format)
	{
		try
		{
			return @strftime($format, $this->datetime->format('U'));
		}
		catch (Exception $e)
		{
			return '';
		}
	}
	
	function timestamp()
	{
		return $this->format('U');
	}

	/**
	 * Returns a formatted string based on the individual user's settings.
	 * If no one is logged in, then a default based on locale is used.
	 *
	 * @return String The formatted datetime string
	 * @author Ted Kulp
	 **/
	function to_format_string()
	{
		$format = '%x %X';

		$user = CmsLogin::get_current_user();
		if ($user != null)
			$format = get_preference($user->id, 'date_format_string', $format);

		return $this->strftime($format);
	}
	
	/**
	 * Returns a formating string based on the current database connection.
	 *
	 * @return String the formatted datetime string
	 * @author Ted Kulp
	 */
	function to_sql_string()
	{
		return cms_db()->DBTimeStamp($this->timestamp());
	}
}

# vim:ts=4 sw=4 noet
?>