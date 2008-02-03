<?php
#CMS - CMS Made Simple
#(c)2004-2008 by Ted Kulp (ted@cmsmadesimple.org)
#This project's homepage is: http://cmsmadesimple.sf.net
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

function smarty_cms_function_valid_xhtml($params, &$smarty)
{

    $link_url = 
	(isset($params['url']) && trim($params['url']) != '')
	? $params['url']
	: 'http://validator.w3.org/check/referer'
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
	: 'valid XHTML 1.0 Transitional'
    ;
    
    $use_image = ((! isset($params['image'])) || $params['image'] != 'false');
    
    $image_src = 
	(isset($params['src']) && trim($params['src']) != '') 
	? $params['src'] 
	: 'http://www.w3.org/Icons/valid-xhtml10'
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
    
    return $html;
}

function smarty_cms_help_function_valid_xhtml() 
{
?>
<h3>What does this do?</h3>
<p>Returns a link to the w3c HTML validator.</p>
<h3>How do I use it?</h3>
<p>Just insert the tag into your template/page like: <code>{valid_xhtml}</code></p>
<h3>What parameters does it take?</h3>
<p>
    <ul>
	<li><em>(optional)</em> url         (string)     - The URL used for validation, if none is given http://validator.w3.org/check/referer is used.</li>
	<li><em>(optional)</em> class       (string)     - If set, this will be used as class attribute for the link (a) element</li>
	<li><em>(optional)</em> target      (string)     - If set, this will be used as target attribute for the link (a) element</li>
	<li><em>(optional)</em> image       (true/false) - If set to false, a text link will be used instead of an image/icon.</li>
	<li><em>(optional)</em> text        (string)     - If set, this will be used for the link text or alternate text for the image. Default is 'valid XHTML 1.0 Transitional'.<br /> When an image is used, the given string will also be used for the image alt attribute (by default, this can be overridden by using the 'alt' parameter).</li>
	<li><em>(optional)</em> image_class (string)     - Only if 'image' is not set to false. If set, this will be used as class attribute for the image (img) element</li>
	<li><em>(optional)</em> src         (string)     - Only if 'image' is not set to false. The icon to show. Default is http://www.w3.org/Icons/valid-xhtml10</li>
	<li><em>(optional)</em> width       (string)     - Only if 'image' is not set to false. The image width. Default is 88 (width of http://www.w3.org/Icons/valid-xhtml10)</li>
	<li><em>(optional)</em> height      (string)     - Only if 'image' is not set to false. The image height. Default is 31 (height of http://www.w3.org/Icons/valid-xhtml10)</li>
	<li><em>(optional)</em> alt         (string)     - Only if 'image' is not set to false. The alternate text ('alt' attribute) for the image (element). If none is given the link text will be used.</li>
    </ul>
</p>
<?php
}

function smarty_cms_about_function_valid_xhtml() 
{
?>
<p>Author: Dick Ittmann&lt;dittmann2@users.sourceforge.net&gt;</p>
<p>Version: 1.0</p>
<p>
    Change History:<br/>
    None
</p>
<?php
}
?>
