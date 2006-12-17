<?php
#CMS - CMS Made Simple
#(c)2004-2006 by Ted Kulp (ted@cmsmadesimple.org)
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
#
#File apapted from (do not remove the copyright below!): 
#
#@version profiler.php 5270 2006-10-02 03:30:29Z webImagery
#@package Joomla
#@copyright Copyright (C) 2005 - 2006 Open Source Matters. All rights reserved.
#@license GNU/GPL, see LICENSE.php
#Joomla! is free software. This version may have been modified pursuant
#to the GNU General Public License, and as distributed it includes or
#is derivative of works licensed under the GNU General Public License or
#other free or open source software licenses.
#See COPYRIGHT.php for copyright notices and details.

class CmsProfiler extends CmsObject
{
	/**
	 * Constructor
	 *
	 * @access protected
	 * @param string Prefix for mark messages
	 **/
	function __construct( $prefix = '', $start_time = null )
	{
		$this->_start = ($start_time == null ? $this->get_microtime() : $start_time);
		$this->_prefix = $prefix;
		$this->_buffer = array();
	}
	
	/**
	 * Returns a reference to the global Profiler object, only creating it
	 * if it doesn't already exist.
	 *
	 * This method must be invoked as:
	 *              <pre>  $browser = CmsProfiler::getInstance([$prefix]);</pre>
	 *
	 * @access public
	 * @return CmsProfiler  The Profiler object.
	 **/
	public static function get_instance($prefix = '', $start_time = null)
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		if (empty($instances[$prefix]))
		{
			$instances[$prefix] = new CmsProfiler($prefix, $start_time);
		}

		return $instances[$prefix];
	}

	/**
	 * Output a time mark
	 *
	 * @access public
	 * @var string A label for the time mark
	 **/
	function mark( $label )
	{
		$mark = sprintf ( "\n<div class=\"profiler\">$this->_prefix %.4f $label</div>", $this->get_microtime() - $this->_start );
		$this->_buffer[] = $mark;
		return $mark;
	}
	
	function get_time()
	{
		return sprintf("%.4f", $this->get_microtime() - $this->_start);
	}
	
	/**
	 * Reports on the buffered marks
	 *
	 * @access public
	 * @param string Glue string
	 **/
	function report( $memory = true, $database = true, $glue='' )
	{
		$db = db();

		echo implode( $glue, $this->_buffer );
		if ($memory)
		{
			echo '<br />';
			echo $this->get_memory();
		}
		if ($database)
		{
			/*
			echo '<br />';
			echo $db->_ticker . ' queries executed';
			echo '<pre>';
			foreach ($db->_log as $k=>$sql) {
				echo $k+1 . "\n" . $sql . '<hr />';
			}
			echo '</pre>';
			*/
		}
	}
	
	/**
	 *
	 * @access public
	 * @return float The current time
	 **/
	public static function get_microtime()
	{
		list( $usec, $sec ) = explode( ' ', microtime() );
		return ((float)$usec + (float)$sec);
	}
	
	/**
	 *
	 * @access public
	 * @return int The memory usage
	 **/
	public static function get_memory()
	{
		static $isWin;

		if (function_exists( 'memory_get_usage' ))
		{
			return memory_get_usage();
		}
		else
		{
			if (is_null( $isWin ))
			{
				$isWin = (substr(PHP_OS, 0, 3) == 'WIN');
			}
			if ($isWin)
			{
				// Windows workaround
				$output = array();
				$pid = getmypid();
				exec( 'tasklist /FI "PID eq ' . $pid . '" /FO LIST', $output );
				if (!isset($output[5]))
				{
					$output[5] = null;
				}
				return substr( $output[5], strpos( $output[5], ':' ) + 1 );
			}
			else
			{
				return 0;
			}
		}
	}
}

# vim:ts=4 sw=4 noet
?>