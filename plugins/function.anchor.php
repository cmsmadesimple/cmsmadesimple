<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
#This project's homepage is: http://www.cmsmadesimple.org
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

function smarty_function_anchor($params, &$template)
{
	$smarty = $template->smarty;
	
	$class					= isset($params['class']) ? $class = ' class="'.$params['class'].'"' : '';
    $title 					= isset($params['title']) ? $title = ' title="'.$params['title'].'"' : '';
    $tabindex 				= isset($params['tabindex']) ? $tabindex = ' tabindex="'.$params['tabindex'].'"' : '';
	$accesskey 				= isset($params['accesskey']) ? $accesskey = ' accesskey="'.$params['accesskey'].'"' : '';
	
	$pageURL = 'http';
	if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
	$pageURL .= "://";
	if ($_SERVER["SERVER_PORT"] != "80") {
	  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	} 
	else {
		   $str = $_SERVER['REQUEST_URI'];
		   $pos = strpos($str,'?');
	           if( $pos !== FALSE )
			     {
			       $str = substr($str,0,$pos);
			     }
        $pageURL .= $_SERVER["SERVER_NAME"].$str;
        }
  
	if (isset($pageURL))
	{
		$url = cms_htmlentities($_SERVER['REQUEST_URI']).'#'.$params['anchor'];
		$url = str_replace('&amp;','***',$url);
		$url = str_replace('&', '&amp;', $url);
		$url = str_replace('***','&amp;',$url);
		if (isset($params['onlyhref']) && ($params['onlyhref'] == '1' || $params['onlyhref'] == 'true'))
			$tmp =  $url;
		else
			$tmp = '<a href="'.$url.'"'.$class.$title.$tabindex.$accesskey.'>'.$params['text'].'</a>';
	}

	if( isset($params['assign']) ) {
	    $smarty->assign(trim($params['assign']),$tmp);
	    return;
        }
	echo $tmp;
}

function smarty_cms_help_function_anchor() {
	echo lang('help_function_anchor');
}

function smarty_cms_about_function_anchor() {
?>
	<p>Author: Ted Kulp&lt;tedkulp@users.sf.net&gt;</p>

	<p>Change History:</p>
	<ul>
		<li>2006/07/19 - Russ added the means to insert a title, a tabindex and a class for the anchor link.<br />
		Westis added accesskey and changed parameter names to not include 'anchorlink'.</li>
	</ul>
<?php
}
?>