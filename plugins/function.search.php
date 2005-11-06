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

function smarty_cms_function_search($params, &$smarty) {
	$domain = $_SERVER['SERVER_NAME'];
	$buttonText = 'Search Site';
	if (!empty($params['domain']))
		$domain = $params['domain'];
	if (!empty($params['buttonText'])) 
		$buttonText = $params['buttonText'];

	return '<form method="get" action="http://www.google.com/search">
	<input type="hidden" name="ie" value="utf-8" />
	<input type="hidden" name="oe" value="utf-8" />
	<input type="hidden" name="sitesearch" value="'.$domain.'" />
	<input type="text" id="textSearch" name="q" maxlength="255" value="" />
	<input type="submit" id="buttonSearch" value="'.$buttonText.'" />
	</form>';

}

function smarty_cms_help_function_search() {
	?>
	<h3>What does this do?</h3>
	<p>Search's your website using Google's search engine.</p>
	<h3>How do I use it?</h3>
	<p>Just insert the tag into your template/page like: <code>{search}</code><br>
	<br>
	Note: Google needs to have your website indexed for this to work. You can submit your website to google <a href="http://www.google.com/addurl.html">here</a>.</p>
	<h3>What if I want to change the look of the textbox or button?</h3>
	<p>The look of the textbox and button can be changed via css. The textbox is given an id of textSearch and the button is given an id of buttonSearch.</p>

	<h3>What parameters does it take?</h3>
	<ul>
		<li><em>(optional)</em> domain - This tells google the website domain to search. This script tries to determine this automatically.</li>
		<li><em>(optional)</em> buttonText - The text you want to display on the search button. The default is "Search Site".</li>
	</ul>
	</p>
	<?php
}

function smarty_cms_about_function_search() {
	?>
	<p>Author: Brett Batie&lt;brett-cms@provisiontech.net&gt;</p>
	<p>Version: 1.0</p>
	<p>
	Change History:<br/>
	None
	</p>
	<?php
}

?>
