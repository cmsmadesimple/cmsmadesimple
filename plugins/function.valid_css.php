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

function smarty_function_valid_css($params, &$template)
{
  $smarty = $template->smarty;
    $link_url = 
	(isset($params['url']) && trim($params['url']) != '')
	? $params['url']
        : 'http://jigsaw.w3.org/css-validator/check/referer'
    ;
    
    $link_target =
	(isset($params['target']) && trim($params['target']) != '')
	? $params['target']
	: ''
    ;
    $link_target_html = $link_target != '' ? ' target="' . $link_target . '"' : '';
    
    $link_class =
	(isset($params['class']) && trim($params['class']) != '')
	? $params['class']
	: ''
    ;
    $link_class_html  = $link_class  != '' ? ' class="'  . $link_class  . '"' : '';
    
    $link_text = 
	(isset($params['text']) && trim($params['text']) != '')
	? $params['text']
	: 'valid CSS 2.1'
    ;
    
    $use_image = ((! isset($params['image'])) || $params['image'] != 'false');
    
    $image_src = 
	(isset($params['src']) && trim($params['src']) != '') 
	? $params['src'] 
	: 'http://jigsaw.w3.org/css-validator/images/vcss'
    ;
    
    $image_alt = isset($params['alt']) ? $params['alt'] : $link_text;
    
    $image_width = 
	(isset($params['width']) && trim($params['width']) != '') 
	? $params['width'] 
	: '88'
    ;
    $image_height = 
	(isset($params['height']) && trim($params['height']) != '') 
	? $params['height'] 
	: '31'
    ;
    $image_size_html = ' width="' . $image_width . '" height="' . $image_height . '"';
    
    $image_class =
	(isset($params['image_class']) && trim($params['image_class']) != '')
	? $params['image_class']
	: ''
    ;
    $image_class_html  = $image_class  != '' ? ' class="'  . $image_class  . '"' : '';
    
    $html = '<a href="' . $link_url . '"' . $link_class_html . $link_target_html . '>';
    $html .= 
	$use_image
	? '<img src="' . $image_src . '" alt="' . $image_alt . '"' . $image_size_html . $image_class_html . ' border="0" />' 
	: $link_text;
    $html .= '</a>';
	if( isset($params['assign']) ){
		$smarty->assign(trim($params['assign']),$html);
		return;
	}
    return $html;
}

function smarty_cms_help_function_valid_css() 
{
  echo lang('help_function_valid_css');
}

function smarty_cms_about_function_valid_css() 
{
?>
<p>Author: Tatu Wikman&lt;tatu.wikman[at]gmail.com&gt;</p>
<p>Version: 1.0</p>
<p>
    Change History:<br/>
    None
</p>
<p>Thanks go to Dick Ittmann for valid_xhtml tag which is used as a base for this tag</p>
<?php
}
?>
