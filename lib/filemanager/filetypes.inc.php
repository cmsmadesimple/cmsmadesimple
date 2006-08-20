<?php

/*
 General filetype format to allow for easy extension of file handlers

 	item format:

	    'extention'  =>Array('img'=>'imagename','desc'=>'File description',
			'link'=>Array(
				'handler name1'=>'link1',
				'handler name2'=>'link2'
			),
			"alias"=>Array("ext1","ext2")			
		), 

 note that :'extention' is without leading '.'
           :'imagename' is without trailing '.png'
           :'link' is parsed: {file} denotes the file name with relative directory,
                              {dir_file} denotes the file name with full directory,
                              {url_dir_file} denotes the file name wit full directory and URL,
*/
	
	$filetype = Array(

	    'html'  =>Array(
		    "img"=>"fhtml","desc"=>"Web page",
			"link"=>Array(
				"view"=>'{url_dir_file}',
				"edit"=>'../filehandlers/htmledit?file={file}'				
			),
			"alias"=>Array("htm","shtm","shtml","hta","xhtml")			
		), 
	    "unknown"  =>Array("img"=>"ffile","desc"=>"Unknown file format",
			"link"=>Array(
				"view"=>'{url_dir_file}'
			)
		), 
	    "zip"  =>Array("img"=>"fzip","desc"=>"Zip archive",
			"link"=>Array(
				"view"=>'{url_dir_file}'
			),
			"alias"=>Array("tar","gz","rar","arc")	
		), 
	    "tar.gz"  =>Array("img"=>"fzip","desc"=>"Zipped tar archive",
			"link"=>Array(
				"view"=>'{url_dir_file}'
			)
		), 
	    "exe"  =>Array("img"=>"fexe","desc"=>"Executable",
			"link"=>Array(
				"view"=>'{url_dir_file}'
			),
			"alias"=>Array("com","pif","so","dll")	
		), 
	    "doc"  =>Array("img"=>"fdoc","desc"=>"Document",
			"link"=>Array(
				"view"=>'{url_dir_file}'
			),
			"alias"=>Array("rtf","dot")	
		),
		"txt"  =>Array("img"=>"ftxt","desc"=>"Text document",
			"link"=>Array(
				"view"=>'{url_dir_file}'
			)
		),
		"jpg"  =>Array("img"=>"fpaint","desc"=>"Image file",
			"link"=>Array(
				"view"=>'{url_dir_file}'
			),
			"alias"=>Array("gif","png","jpeg")	
		),		 
	    "psd"  =>Array("img"=>"fpsd","desc"=>"Photoshop",
			"link"=>Array(
				"view"=>'{url_dir_file}'
			),
			"alias"=>Array("pdd")	
		)
		
	); 
	
/*
 File filter following simple shell patterns 
*/	
	$excludefilters = Array("editor_*.*","thumb_*.*");

?>
