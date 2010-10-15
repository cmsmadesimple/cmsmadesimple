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
	global $gCms;
	
	$template_id = '';

	if (isset($params["templateid"]) && $params["templateid"]!="")
	{
		$template_id = $params["templateid"];
	}
	else
	{
		$content_obj = &$gCms->variables['content_obj'];
		$template_id = $content_obj->TemplateId();
	}
	
	$config =& $gCms->config;
	$db =& $gCms->GetDb();

	$cache_dir = TMP_CACHE_LOCATION;
	$stylesheet = '';

	$qparms = array();
	$where = array();
	$query = '';
	$order = '';
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

	$conv_filename = array(' '=>'',':'=>'_');
	$res = $db->GetArray($query, $qparms);
	if( $res )
	{
		$fmt1 = '<link rel="stylesheet" type="text/css" media="%s" href="%s" />';
		$fmt2 = '<link rel="stylesheet" type="text/css" href="%s" />';
		foreach ($res as $one)
		{
			$media_type = str_replace(' ','',$one['media_type']);
			$filename = strtr($one['css_name'], $conv_filename).'_'.strtotime($one['modified_date']).'.css';
			if ( !file_exists(cms_join_path($cache_dir,$filename)) )
			{
				$smarty = $gCms->GetSmarty();
				$smarty->left_delimiter = '[[';
				$smarty->right_delimiter = ']]';
				$smarty->_compile_source('temporary stylesheet', $one['css_text'], $_compiled );
				@ob_start();
				$smarty->_eval('?>' . $_compiled);
				$_contents = @ob_get_contents();
				@ob_end_clean();
				$smarty->left_delimiter = '{';
				$smarty->right_delimiter = '}';
				$fname = cms_join_path($cache_dir,$filename);
				$fp = fopen($fname, 'w');
				//we convert CRLF to LF for unix compatibility
				fwrite($fp, str_replace("\r\n", "\n", $_contents));
				fclose($fp);
				//set the modified date to the template modified date
				//touch($fname, $db->UnixTimeStamp($one['modified_date']));
			}
			if ( empty($media_type) || isset($params['media']) )
			{
				$stylesheet .= '<link rel="stylesheet" type="text/css" href="'.$config['root_url'].'/tmp/cache/'.$filename.'"/>'."\n";
			}
			else
			{
				$stylesheet .= '<link rel="stylesheet" type="text/css" href="'.$config['root_url'].'/tmp/cache/'.$filename.'" media="'.$media_type.'"/>'."\n";
			}
		}
	}

	if (!(isset($config["use_smarty_php_tags"]) && $config["use_smarty_php_tags"] == true))
	{
		$stylesheet = preg_replace("/\{\/?php\}/", "", $stylesheet);
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