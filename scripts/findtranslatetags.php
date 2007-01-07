<?php

function searchdir ( $path , $maxdepth = -1 , $mode = "FULL" , $d = 0 )
{
   if ( substr ( $path , strlen ( $path ) - 1 ) != '/' ) { $path .= '/' ; }     
   $dirlist = array () ;
   if ( $mode != "FILES" ) { $dirlist[] = $path ; }
   if ( $handle = opendir ( $path ) )
   {
       while ( false !== ( $file = readdir ( $handle ) ) )
       {
           if ( $file != '.' && $file != '..' )
           {
               $file = $path . $file ;
               if ( ! is_dir ( $file ) ) { if ( $mode != "DIRS" ) { $dirlist[] = $file ; } }
               elseif ( $d >=0 && ($d < $maxdepth || $maxdepth < 0) )
               {
                   $result = searchdir ( $file . '/' , $maxdepth , $mode , $d + 1 ) ;
                   $dirlist = array_merge ( $dirlist , $result ) ;
               }
       }
       }
       closedir ( $handle ) ;
   }
   if ( $d == 0 ) { natcasesort ( $dirlist ) ; }
   return ( $dirlist ) ;
}

include('../lib/global.functions.php');

$dir = "../install/";
$arDirTree = searchdir($dir, -1, 'FILES');
$strings = array();
foreach ($arDirTree as $onefile)
{
	if (endswith($onefile, '.tpl') || endswith($onefile, '.php'))
	{
		$file = file_get_contents($onefile);
		preg_match_all("/_\(['\"]?(.+?)['\"]?\)/", $file, $matches);
		if (empty($matches[1]))
			preg_match_all("/\{translate\}(.+?)\{\/translate\}/", $file, $matches);

		if (isset($matches[1]) && is_array($matches[1]))
		{
			foreach ($matches[1] as $onestring)
			{
				if (!in_array($onestring, $strings, TRUE))
					$strings[] = $onestring;
			}
		}
	}
}

var_dump($strings);

?>
