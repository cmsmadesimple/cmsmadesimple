<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#CMS - CMS Made Simple
#(c)2004-2008 by Ted Kulp (ted@cmsmadesimple.org)
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

class CmsInstallPage extends CmsObject
{
	function __construct()
	{
		parent::__construct();
	}

	static function process_content(&$smarty, $debug)
	{
		$errors = array();

		if(CmsRequest::get('check'))
		{
			$thisError = '';

			$test = testFileUploads('cksumdat');
			if(isset($test->error))
			{
				$thisError = $test->error;
			}
			elseif(count($test->files) > 1)
			{
				$thisError = lang('upload_file_multiple');
			}
			else
			{
				if(isset($test->files[0]['error_string']))
				{
					$thisError = $test->files[0]['error_string'];
				}
				else
				{
					$checksum_file = $test->files[0]['tmp_name'];
					if($debug) $handle = fopen($checksum_file, 'rb');
					else       $handle = @fopen($checksum_file, 'rb');
					if(! $handle)
					{
						$thisError = lang('upload_file_no_readable');
					}
				}
			}

			if(empty($thisError))
			{
				$results = array();
				while (!feof($handle))
				{
					$line = @fgets($handle, 4096);
					$line = trim($line); // clean

					if(empty($line)) continue; // skip empty line

					$pos = strpos($line, '#');
					if($pos) $line = substr($line, 0, $pos); // strip out comments

					list($md5sum, $file) = explode(' *./', $line, 2); // split it into fields
					$md5sum = trim($md5sum);
					$file = trim($file);
					$file = str_replace('/', DS, $file); // avoid windows suck

					$test_file = CMS_BASE . DS . $file;
					$test = testFileChecksum(0, '', $test_file, $md5sum, '', lang('format_datetime'), $debug);
					if($test->res == 'green') continue; // ok, skip

					$results[] = $test;
				}
				@fclose($handle);

				if(count($results) > 0) $this->smarty->assign_by_ref('results', $results);
				$smarty->assign('try_test', true);
			}
			else
			{
				$errors[] = $thisError;
			}
		}

		return $errors;
	}
}
# vim:ts=4 sw=4 noet
?>
