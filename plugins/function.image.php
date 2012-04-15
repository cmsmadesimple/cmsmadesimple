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

function smarty_function_image($params, &$template)
{
  $smarty = $template->smarty;
	$gCms = cmsms();

	$text = '';
	$imgstart = '<img src=';
	$imgend = '/>';
	if( !empty($params['src'] ) )
	{
		$text = $imgstart .= '"'.$gCms->config['image_uploads_url'].'/'.$params['src'].'"';
		$size = @getimagesize($gCms->config['image_uploads_path'].'/'.$params['src']);
		
				if( !empty($params['width'] ) ) {
					$text .= ' width="'.$params['width'].'"';
				} elseif ($size[0] > 0) {
					$text .= ' width="'.$size[0].'"';
				}
				
				if( !empty($params['height'] ) ) {
					$text .= ' height="'.$params['height'].'"';
				} elseif ($size[1] > 0) {
					$text .= ' height="'.$size[1].'"';
				}
				if( !empty($params['alt'] ) )
				{
					$alt = $params['alt'];
				} else {
					$alt = '['.$params['src'].']';
				}
				$text .= ' alt="'.$alt.'"';
				if( !empty($params['title'] ) )
				{
					$text .= ' title="'.$params['title'].'"';
				} else {
					$text .= ' title="'.$alt.'"';
				}
				if( !empty($params['class'] ) )
				{
					$text .= ' class="'.$params['class'].'"';
				}
				
				if( !empty($params['addtext'] ) )
				{
						$text .= ' ' . $params['addtext'];
				}
		$text .= $imgend;
	} else {
		$text = '<!-- empty results from image plugin -->';
	}
	if( isset($params['assign']) ){
		$smarty->assign(trim($params['assign']),$text);
		return;
	}
	return $text;	
}


function smarty_cms_help_function_image()
{
  echo lang('help_function_image');
}


function smarty_cms_about_function_image() 
{
?>
  <p>Author:  Robert Campbell &lt;calguy1000@hotmail.com&gt;,</p>
  <p>Version 1.2</p>
  <p>Change History<br/>
     1.2 - Added default width, height and alt <small>(contributed by Walter Wlodarski)</small><br />
     1.1 - Added alt param and removed the </img><br />
     1.0 - Initial release<br/>
  </p>
<?php
}

?>