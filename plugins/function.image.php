<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
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

function smarty_cms_function_image($params, &$smarty)
{
	global $gCms;

	$text = '';
	$imgstart = "<img src=";
	$imgend = "/>";
	if( !empty($params['src'] ) )
	{
		$text = $imgstart .= '"'.$gCms->config['image_uploads_url'].'/'.$params['src'].'"';
        }
	if( !empty($params['width'] ) )
	{
		$text .= ' width="'.$params['width'].'"';
	}
	if( !empty($params['height'] ) )
	{
		$text .= ' height="'.$params['height'].'"';
	}
	if( !empty($params['addtext'] ) )
	{
	        $text .= ' ' . $params['addtext'];
	}
	if( !empty($text) )
	{
		$text .= $imgend;
	}
	else
	{
		$text = '<!-- empty results from image plugin -->';
	}
	return $text."</img>";	
}


function smarty_cms_help_function_image()
{
  ?>
  <h3>What does this do?</h3>
  <p>Creates an image tag to an image stored within your images directory</p>
  <h3>How do I use it?</h3>
  <p>Just insert the tag into your template/page like: <code>{image src="something.jpg"}</code></p>
  <h3>What parameters does it take?</h3>
  <p>
  <ul>
     <li><em>(required)</em>  <tt>src</tt> - Image filename within your images directory.</li>
     <li><em>(optional)</em>  <tt>width</tt> - Width of the image within the page</li>
     <li><em>(optional)</em>  <tt>height</tt> - Height of the image within the page</li>
     <li><em>(optional)</em>  <tt>addtext</tt> - Additional text to put into the tag</li>
  </ul>
  </p>
  <?php
}


function smarty_cms_about_function_image() 
{
?>
  <p>Author:  Robert Campbell &lt;calguy1000@hotmail.com&gt;</p>
  <p>Version 1.0</p>
  <p>Change History<br/>
     1.0 - Initial release<br/>
  </p>
<?php
}

?>
