<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#CMS - CMS Made Simple
#(c)2004-2010 by Ted Kulp (ted@cmsmadesimple.org)
#This project's homepage is: http://cmsmadesimple.org
#
#This program is free software; you can redistribute it and/or modify
#it under the terms of the GNU General Public License as published by
#the Free Software Foundation; either version 2 of the License, or
#(at your option) any later version.
#
#This program is distributed in the hope that it will be useful,
#BUT withOUT ANY WARRANTY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
#You should have received a copy of the GNU General Public License
#along with this program; if not, write to the Free Software
#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

function smarty_cms_function_cms_stylesheet($params, &$smarty)
{

	//
	// begin
	//
	$gCms = cmsms();
	global $CMS_STYLESHEET;
	$CMS_STYLESHEET = 1;
	$template_id = '';
	$use_https = -1;

	if( isset($params['https']) ) $use_https = (bool)$params['https'];

	if (isset($params["templateid"]) && $params["templateid"]!="")
	{
		$template_id = $params["templateid"];
	}
	else
	{
		$content_obj = $gCms->variables['content_obj'];
		$template_id = $content_obj->TemplateId();
		if( $use_https == -1 )
		{
			$use_https = (bool)$content_obj->Secure();
		}	
	}
	
	if( $use_https < 0 ) $use_https = FALSE;
	$config = $gCms->config;
	$db = $gCms->GetDb();
	$root_url = $config['root_url'];
	if( $use_https && isset($config['ssl_url']) )
	{
		$root_url = $config['ssl_url'];
	}

	$cache_dir = TMP_CACHE_LOCATION;
	$stylesheet = '';

	$qparms = array();
	$where = array();
	$query = '';
	$order = '';
	$combine_stylesheets = FALSE;
	if( isset($params['media']) && strtolower($params['media']) != 'all' )
	{
		$where[] = '(media_type LIKE ? OR media_type LIKE ?)';
		$qparms[] = '%'.trim($params['media']).'%';
		$qparms[] = '%all%';
	}
	if (isset($params['name']) && $params['name'] != '')
	{
		$query = 'SELECT DISTINCT A.css_id,A.css_name,A.css_text,A.modified_date, A.media_type
                    FROM '.cms_db_prefix().'css A';
		$where[] = 'A.css_name = ?';
		$qparms[] = trim($params['name']);
	}
	else //No name?  Use the template_id instead
	{
		if( !isset($params['nocombine']) || $params['nocombine'] == 0 )
		{
			$combine_stylesheets = TRUE;
		}
  	    $query = 'SELECT DISTINCT A.css_id,A.css_name,A.css_text,A.modified_date,
               		              A.media_type,B.assoc_order 
   	                FROM '.cms_db_prefix().'css A 
                    LEFT JOIN '.cms_db_prefix().'css_assoc B ON A.css_id = B.assoc_css_id';
		$where[] = 'B.assoc_type = ?
		AND B.assoc_to_id = ?';
		$qparms = array('template', $template_id);
		$order = 'ORDER BY B.assoc_order';
	}
	$query .= " WHERE ".implode(' AND ',$where).' '.$order;

	if( isset($params['nocombine']) && $params['nocombine'] )
	{
		// forced not to combine.
		$combine_stylesheets = FALSE;
	}

	$fnsuffix = '';
	$trimbackground = FALSE;
	if( isset($params['adjustforeditor']) && $params['adjustforeditor'] )
	{
		$fnsuffix = '_e';
		$trimbackground = TRUE;
	}

	$res = $db->GetArray($query, $qparms);
	if( $res )
	{

		$modified_date = 0;
		$media_changed = false;
		$test_media = '';
		for( $i = 0; $i < count($res); $i++ )
		{
			$modified_date = max($modified_date,strtotime($res[$i]['modified_date']));
			if( $test_media == '' )
			{
				$test_media = $res[$i]['media_type'];
			}
			if( $res[$i]['media_type'] != $test_media )
			{
				$media_changed = TRUE;
			}
		}
		if( $media_changed ) $combine_stylesheets = FALSE;

		if( $combine_stylesheets && $template_id > 0 )
		{
			// combine all matches into one stylesheet.
			$filename = 'stylesheet_combined_'.$template_id.'_'.$modified_date.$fnsuffix.'.css';
			$fn = cms_join_path($cache_dir,$filename);
			if( !file_exists($fn) )
			{
				$text = '';
				for( $i = 0; $i < count($res); $i++ )
				{
					$one = $res[$i];
					$text .= '/* Stylesheet: '.$one['css_name'].' Modified On '.$one['modified_date']." */\n";
					$text .= $one['css_text'];
					if( !endswith($text,"\n") ) $text .= "\n";
				}

				$smarty = cmsms()->GetSmarty();
				$smarty->left_delimiter = '[[';
				$smarty->right_delimiter = ']]';
				$smarty->_compile_source('stylesheet:combined', $text, $_compiled );
				@ob_start();
				$smarty->_eval('?>' . $_compiled);
				$_contents = @ob_get_contents();
				@ob_end_clean();

				if( $trimbackground )
				{
					$_contents = preg_replace('/(\w*?background-color.*?\:\w*?).*?(;.*?)/', '\\1transparent\\2', $_contents);
					$_contents = preg_replace('/(\w*?background-image.*?\:\w*?).*?(;.*?)/', '', $_contents);
				}

				$fh = fopen($fn,'w');
				fwrite($fh,$_contents);
				fclose($fh);

				$smarty->left_delimiter = '{';
				$smarty->right_delimiter = '}';
			}

			if( isset($params['nolinks']) )
			{
				$stylesheet .= $root_url.'/tmp/cache/'.$filename;
			}
			else
			{
				$stylesheet .= '<link rel="stylesheet" type="text/css" href="'.$root_url.'/tmp/cache/'.$filename.'"/>'."\n";
			}
		}
		else
		{
			// separete stylesheet records.
			$fmt1 = '<link rel="stylesheet" type="text/css" media="%s" href="%s" />';
			$fmt2 = '<link rel="stylesheet" type="text/css" href="%s" />';
			foreach ($res as $one)
			{
				$media_type = str_replace(' ','',$one['media_type']);
				$filename = 'stylesheet_'.$one['css_id'].'_'.strtotime($one['modified_date']).$fnsuffix.'.css';
				if ( !file_exists(cms_join_path($cache_dir,$filename)) )
				{
					$smarty = $gCms->GetSmarty();
					$smarty->left_delimiter = '[[';
					$smarty->right_delimiter = ']]';
					$smarty->_compile_source('stylesheet:'.$one['css_name'], $one['css_text'], $_compiled );
					@ob_start();
					$smarty->_eval('?>' . $_compiled);
					$_contents = @ob_get_contents();
					@ob_end_clean();
					$smarty->left_delimiter = '{';
					$smarty->right_delimiter = '}';

					if( $trimbackground )
					{
						$_contents = preg_replace('/(\w*?background-color.*?\:\w*?).*?(;.*?)/', '\\1transparent\\2', $_contents);
						$_contents = preg_replace('/(\w*?background-image.*?\:\w*?).*?(;.*?)/', '', $_contents);
					}

					$fname = cms_join_path($cache_dir,$filename);
					$fp = fopen($fname, 'w');
					//we convert CRLF to LF for unix compatibility
					fwrite($fp, str_replace("\r\n", "\n", $_contents));
					fclose($fp);
					//set the modified date to the template modified date
					//touch($fname, $db->UnixTimeStamp($one['modified_date']));
				}

				if( isset($params['nolinks']) )
				{
					$stylesheet .= $root_url.'/tmp/cache/'.$filename.',';
				}
				else
				{	
					if ( empty($media_type) || isset($params['media']) )
						{
							$stylesheet .= '<link rel="stylesheet" type="text/css" href="'.$root_url.'/tmp/cache/'.$filename.'"/>'."\n";
						}
					else
						{
							$stylesheet .= '<link rel="stylesheet" type="text/css" href="'.$root_url.'/tmp/cache/'.$filename.'" media="'.$media_type.'"/>'."\n";
						}
				}
			}
		}
	}

	if (!(isset($config["use_smarty_php_tags"]) && $config["use_smarty_php_tags"] == true))
	{
		$stylesheet = preg_replace("/\{\/?php\}/", "", $stylesheet);
	}

	if( isset($params['nolinks']) && endswith($styleseet,',') )
	{
		$stylesheet = substr($stylesheet,0,strlen($stylesheet)-1);
	}

	$CMS_STYLESHEET = 0;
	unset($CMS_STYLESHEET);
	unset($_GLOBALS['CMS_STYLESHEET']);
	if( isset($params['assign']) ){
	    $smarty->assign(trim($params['assign']),$stylesheet);
	    return;
    }
	return $stylesheet;
}

function smarty_cms_help_function_cms_stylesheet()
{
	echo lang('help_function_cms_stylesheet');
}

function smarty_cms_about_function_cms_stylesheet()
{
	?>
	<p>Author: jeff&lt;jeff@ajprogramming.com&gt;</p>
	<p>Version: 0.6</p>
	<p>
	Change History:<br/>
	Rework from {stylesheet}
	</p>
	<?php
}

# vim:ts=4 sw=4 noet
?>