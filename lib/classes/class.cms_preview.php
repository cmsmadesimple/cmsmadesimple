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

class CmsPreview extends CmsObject
{
	function __construct()
	{
		parent::__construct();
	}
	
	public static function has_preview()
	{
		return (isset($_GET["tmpfile"]) && $_GET["tmpfile"] != "");
	}
	
	public static function get_preview()
	{
		if (CmsPreview::has_preview())
		{
			$fname = CmsPreview::clean_filename($_GET["tmpfile"]);

			if (is_writable($config["previews_path"]))
			{
				$fname = $config["previews_path"] . DIRECTORY_SEPARATOR . $fname;
			}
			else
			{
				$fname = TMP_CACHE_LOCATION . DIRECTORY_SEPARATOR . $fname;
			}
			$handle = fopen($fname, "r");
			$data = unserialize(fread($handle, filesize($fname)));
			fclose($handle);
		
			return $data;
		}

		return null;
	}
	
	public static function clear_preview()
	{
		$config = cms_config();

		//If we're in a preview, remove the file
		if (CmsPreview::has_preview())
		{
			$fname = CmsPreview::clean_filename($_GET["tmpfile"]);

			if (is_writable($config["previews_path"]))
			{
				$fname = $config["previews_path"] . DIRECTORY_SEPARATOR . $fname;
			}
			else
			{
				$fname = TMP_CACHE_LOCATION . DIRECTORY_SEPARATOR . $fname;
			}
			unlink($fname);
		}
	}
	
	public static function clean_filename($fname)
	{
		$fname = str_replace('.', '', $fname);
		$fname = str_replace("\\", '', $fname);
		$fname = str_replace('/', '', $fname);
		$fname = str_replace(DIRECTORY_SEPARATOR, '', $fname);
		$fname = basename($fname);
		return $fname;
	}
}

# vim:ts=4 sw=4 noet
?>