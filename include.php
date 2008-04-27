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

//Load necessary global functions
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'cmsms.api.php');

//Where are we?
$dirname = ROOT_DIR;

//Load stuff that hasn't been moved to static methods yet
require_once(cms_join_path($dirname,'lib','misc.functions.php'));
require_once(cms_join_path($dirname,'lib','page.functions.php'));

//Setup the session
CmsSession::setup();

//Do any necessary stuff to the actual request
CmsRequest::setup();

//Setup a global $gCms...  this needs to die, though
$gCms = cmsms();

#define timezone
if (function_exists('date_default_timezone_set') && CmsConfig::exists('timezone') && CmsConfig::get('timezone') != '') 
{
    date_default_timezone_set(CmsConfig::get('timezone'));
}

#Preload content types (TODO: Cache me!)
CmsContentOperations::find_content_types();

#And block types (TODO: Cache me!)
CmsContentOperations::find_block_types();

#Set a umask
$global_umask = CmsApplication::get_preference('global_umask','');
if( $global_umask != '' )
{
	@umask( octdec($global_umask) );
}

#Set the locale if it's set
#either in the config, or as a site preference.
$frontendlang = CmsApplication::get_preference('frontendlang','');
if (CmsConfig::exists('locale') && CmsConfig::get('locale') != '')
{
    $frontendlang = CmsConfig::get('locale');
}
if ($frontendlang != '')
{
    @setlocale(LC_ALL, $frontendlang);
}

cms_smarty()->assign('lang', $frontendlang);
cms_smarty()->assign('encoding', 'UTF-8');

#Load all installed module code
CmsModuleLoader::load_modules(isset($LOAD_ALL_MODULES), !isset($CMS_ADMIN_PAGE));

# vim:ts=4 sw=4 noet
?>
