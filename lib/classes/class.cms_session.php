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
 * Static methods for handling session creation.
 *
 * @author Ted Kulp
 * @since 2.0
 * @version $Revision$
 * @modifiedby $LastChangedBy$
 * @lastmodified $Date$
 * @license GPL
 **/
class CmsSession extends CmsObject
{
	function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Sets up the session properly for the system
	 *
	 * @return void
	 * @author Ted Kulp
	 **/
	public static function setup()
	{
		#Setup session with different id and start it
		@session_name('CMSSESSID' . CmsSession::generate_session_key());
		@ini_set('url_rewriter.tags', '');
		@ini_set('session.use_trans_sid', 0);
		
		if(!@session_id())
		{
		    #Trans SID sucks also...
		    @ini_set('url_rewriter.tags', '');
		    @ini_set('session.use_trans_sid', 0);
		    @session_start();
		}
		
		#Add users if they exist in the session
		$gCms = cmsms();

		$gCms->variables['user_id'] = '';
		if (isset($_SESSION['cmsms_user_id']))
		{
		    $gCms->variables['user_id'] = $_SESSION['cmsms_user_id'];
		}

		$gCms->variables['username'] = '';
		if (isset($_SESSION['cms_admin_username']))
		{
		    $gCms->variables['username'] = $_SESSION['cms_admin_username'];
		}
	}
	
	/**
	 * Generates a string that should be unique value depending on the directory
	 * that CMSMS resides in.  Allows us to have multiple CMSMS installs on the 
	 * same domain and not share sessions.
	 *
	 * @return string
	 * @author Ted Kulp
	 **/
	public static function generate_session_key()
	{
		return substr(md5(dirname(dirname(dirname(__FILE__)))), 0, 8);
	}
}

# vim:ts=4 sw=4 noet
?>