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

function smarty_cms_function_anchor($params, &$smarty)
{
	global $gCms;
	
	if (isset($_SERVER['REQUEST_URI']))
	{
		$url = $_SERVER['REQUEST_URI'].'#'.$params['anchor'];
		if (isset($params['onlyhref']) && ($params['onlyhref'] == '1' || $params['onlyhref'] == 'true'))
			echo $url;
		else
			echo '<a href="'.$url.'">'.$params['text'].'</a>';
	}
}

function smarty_cms_help_function_anchor() {
	?>
	<h3>What does this do?</h3>
	<p>Makes a proper anchor link.</p>
	<h3>How do I use it?</h3>
	<p>Just insert the tag into your template/page like: <code>{anchor anchor='here' text='Scroll Down'}</code></p>
	<h3>What parameters does it take?</h3>
	<p>
	<ul>
	<li><tt>anchor</tt> - Where we are linking to.  The part after the #.</li>
	<li><tt>text</tt> - The text to display in the link.</li>
	<li><em>(optional)</em> <tt>onlyhref</tt> - Only display the href and not the entire link.</li>
	</ul>
	</p>
	<?php
}

function smarty_cms_about_function_anchor() {
	?>
	<p>Author: Ted Kulp&lt;tedkulp@users.sf.net&gt;</p>
	<p>Version: 1.0</p>
	<p>
	Change History:<br/>
	None
	</p>
	<?php
}
?>
