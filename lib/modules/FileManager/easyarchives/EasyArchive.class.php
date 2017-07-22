<?php
/**-------------------------------------------------
 | EasyArchive.class V0.8 -  by Alban LOPEZ
 | Copyright (c) 2007 Alban LOPEZ
 | Email bugs/suggestions to alban.lopez+eazyarchive@gmail.com
 +--------------------------------------------------
 | This file is part of EasyArchive.class V0.9.
 | EasyArchive is free software: you can redistribute it and/or modify
 | it under the terms of the GNU General Public License as published by
 | the Free Software Foundation, either version 3 of the License, or
 | (at your option) any later version.
 | EasyArchive is distributed in the hope that it will be useful,
 | but WITHOUT ANY WARRANTY; without even the implied warranty of
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 | See the GNU General Public License for more details on http://www.gnu.org/licenses/
 +-------------------------------------------------- **/
//www.phpclasses.org/browse/package/4239.html

$dirname=dirname(__FILE__);
require ($dirname.'/EasyZip.class.php');
require ($dirname.'/EasyTar.class.php');
require ($dirname.'/EasyGzip.class.php');
require ($dirname.'/EasyBzip2.class.php');
//echo "hi";die();

/**
// You can use this class like that.
$arch = new archive;
$arch->make('./', './archive.tar.gzip');
var_export($arch->infos('./toto.bzip2'));
$arch->extract('./toto.zip', './my_dir/');
$arch->download('./my_dir/');
**/

class EasyArchive
{
	var $WathArchive = array (
		'.zip'		=>'zip',
		'.tar'		=>'tar',
		'.gz'		=>'gz',
		'.gzip'		=>'gz',
		'.bzip'		=>'bz',
		'.bz'		=>'bz',
		'.bzip2'	=>'bz',
		'.bz2'		=>'bz',
		'.tgz'		=>'gz',
		'.tgzip'	=>'gz',
		'.tbzip'	=>'bz',
		'.tbz'		=>'bz',
		'.tbzip2'	=>'bz',
		'.tbz2'		=>'bz',
	);
	function download ($src, $name)
	{
		header('Content-Type: application/force-download');
		header("Content-Transfer-Encoding: binary");
		header("Cache-Control: no-cache, must-revalidate, max-age=60");
		header("Expires: Sat, 01 Jan 2000 12:00:00 GMT");
		header('Content-Disposition: attachment;filename="'.$name."\"\n");
		$data = $this->make($src, $name, false);
		header("Content-Length: ".strlen($data));
		print($data);
	}
	function make ($src, $name="Archive.tgz", $returnFile=true)
	{
		$ext = '.'.pathinfo ($name, PATHINFO_EXTENSION);
		foreach ($this->WathArchive as $key=>$val)
			if (stripos($ext, $key)!==false) $comp=$val;
		if ($comp == 'zip')
		{
			$zip = new zip;
			if ($returnFile)
				$result = $zip->makeZip($src, $name);
			else
			{
				$tmpZip = TMP_CACHE_LOCATION.'/'.md5(serialize($src)).'.zip';
				$result = $zip->makeZip($src, $tmpZip);
				$result = file_get_contents($tmpZip);
				unlink($tmpZip);
			}
			return $result;
		}
		elseif (strlen($comp)>1)
		{
			if (count($src)>1 || is_dir($src[0]) || $comp == 'tar')
			{
				$tar = new tar;
				$src = $tar->makeTar($src);
			}
			if ($comp == 'bz')
			{
				$bzip2 = new bzip2;
				$src = $bzip2->makeBzip2($src);
			}
			elseif ($comp == 'gz')
			{
				$gzip = new gzip;
				$src = $gzip->makeGzip($src);
			}
			if ($returnFile)
			{
				file_put_contents($name, $src);
				return $name;
			}
			return $src;
		}
		else return 'Specifie a valid format at the end of '.$name.' filename ! ';
	}
	function infos ($src, $data=false)
	{
		$ext = '.'.pathinfo ($src, PATHINFO_EXTENSION);
		foreach ($this->WathArchive as $key=>$val)
			if (stripos($ext, $key)!==false) $comp=$val;
		if ($comp == 'zip')
		{
			$zip = new zip;
			$zipresult = $zip->infosZip($src, $data);
			$result ['Items'] = count($zipresult);
			foreach($zipresult as $key=>$val)
				$result['UnCompSize'] += $zipresult[$key]['UnCompSize'];
			$result['Size']=filesize($src);
			$result['Ratio'] = $result['UnCompSize'] ? round(100 - $result['Size'] / $result['UnCompSize']*100, 1) : false;
		}
		elseif (strlen($comp)>1)
		{
			$tar = new tar;
			if ($comp == 'bz')
			{
				$bzip2 = new bzip2;
				$result = $bzip2->infosBzip2($src, true);
				$src=$result['Data'];
			}
			elseif ($comp == 'gz')
			{
				$gzip = new gzip;
				$result = $gzip->infosGzip($src, true);
				$src=$result['Data'];
			}
			if ($tar->is_tar($src) || is_file($src))
			{
				$tarresult = $tar->infosTar($src, false);
				$result ['Items'] = count($tarresult);
				$result ['UnCompSize'] = 0;
				if (empty($result['Size']))
					$result['Size']=is_file($src)?filesize($src):strlen($src);
				foreach($tarresult as $key=>$val)
					$result['UnCompSize'] += $tarresult[$key]['size'];
				$result['Ratio'] = $result['UnCompSize'] ? round(100 - $result['Size'] / $result['UnCompSize']*100, 1) : false;
				
			}
			if (!$data) unset($result['Data']);
		}
		else return false;
		return array('Items'=>$result['Items'], 'UnCompSize'=>$result['UnCompSize'], 'Size'=>$result['Size'], 'Ratio'=>$result['Ratio'],);
	}
	function extract ($src, $dest=false)
	{
		$path_parts = pathinfo ($src);
		if (!$dest)
			$dest = $path_parts['dirname'].'/';
		$ext = '.'.$path_parts['extension'];
		$name = $path_parts['filename'];
		foreach ($this->WathArchive as $key=>$val)
			if (stripos($ext, $key)!==false) $comp=$val;
		if ($comp == 'zip')
		{
			$zip = new zip;
			return $zip->extractZip($src, $dest);
		}
		elseif (strlen($comp)>1)
		{
			$tar = new tar;
			if ($comp == 'bz')
			{
				$bzip2 = new bzip2;
				$src = $bzip2->extractBzip2($src);
			}
			elseif ($comp == 'gz')
			{
				$gzip = new gzip;
				$src = $gzip->extractGzip($src);
			}
			if ($tar->is_tar($src) || is_file($src))
			{
				return $tar->extractTar($src, $dest);
			}
			else file_put_contents($dest.$name, $src);
			return $dest;
		}
		return false;
	}
}
?>