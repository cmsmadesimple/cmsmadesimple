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

function smarty_cms_function_anchor($params, &$smarty)
{
	global $gCms;
	#Added by Russ for class, tabindex and title for anchor 2006/07/19
	$class="";
	$title="";
	$tabindex="";
	$accesskey="";
	if (isset($params['class'])) $class = ' class="'.$params['class'].'"';
    if (isset($params['title'])) $title = ' title="'.$params['title'].'"';
    if (isset($params['tabindex'])) $tabindex = ' tabindex="'.$params['tabindex'].'"';
	if (isset($params['accesskey'])) $accesskey = ' accesskey="'.$params['accesskey'].'"';
	#End of first part added by Russ 2006/07/19
	
	if (isset($_SERVER['REQUEST_URI']))
	{
		$url = $_SERVER['REQUEST_URI'].'#'.$params['anchor'];
		$url = str_replace('&', '&amp;', $url);
		if (isset($params['onlyhref']) && ($params['onlyhref'] == '1' || $params['onlyhref'] == 'true'))
			#Note if you set 'onlyheref' that is what you get - no class or title or tabindex or text
			echo $url;
		else
			#Line replaced by Russ
			#	echo '<a href="'.$url.'">'.$params['text'].'</a>';
			#Line replaced with -  by Russ to reflect class and title for anchor 2006/07/19
			echo '<a href="'.$url.'"'.$class.$title.$tabindex.$accesskey.'>'.$params['text'].'</a>';
			#End of second part added by Russ 2006/07/19
	}
}
	#Ammended by Russ for class, tabindex and title for anchor 2006/07/19
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
	<li><tt>class</tt> - The class for the link, if any</li>
	<li><tt>title</tt> - The title to display for the link, if any.</li>
	<li><tt>tabindex</tt> - The numeric tabindex for the link, if any.</li>
	<li><tt>accesskey</tt> - The accesskey for the link, if any.</li>
	<li><em>(optional)</em> <tt>onlyhref</tt> - Only display the href and not the entire link. No other options will work</li>
	</ul>
	</p>
	<?php
}
	#Amended by Russ for class, tabindex and title for anchor 2006/07/19
function smarty_cms_about_function_anchor() {
	?>
	<p>Author: Ted Kulp&lt;tedkulp@users.sf.net&gt;</p>
	<p>Version: 1.1</p>
	<p>
	Change History:<br/>
	<strong>Update to version 1.1 from 1.0</strong> <em>2006/07/19</em><br/>
	Russ added the means to insert a title, a tabindex and a class for the anchor link. Westis added accesskey and changed parameter names to not include 'anchorlink'.<br/>
	</hr>
	</p>
	<?php
}
?>
