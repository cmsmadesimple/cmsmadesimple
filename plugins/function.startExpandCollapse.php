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

function smarty_cms_function_startExpandCollapse($params, &$smarty)
{
       static $firstExpandCollapse = true;//only gets set one time per page

       if (!empty($params['id']) && !empty($params['title'])){
               $id = $params['id'];
               $title = $params['title'];
       }else{
	       echo 'Error: The expand/collapse plugin requires that both parameters (id,title) are used.';
               return;
       }

	if ($firstExpandCollapse) {
		echo '<script type="text/javascript" language="javascript" src="lib/helparea.js"></script>';
		$firstExpandCollapse = false;
	}

	echo '<a href="#'.$id.'" onClick="expandcontent(\''.$id.'\')" style="cursor:hand; cursor:pointer">'.$title.'</a><br>
	<div id="'.$id.'" class="expand">';
}

function smarty_cms_help_function_startExpandCollapse() {
	?>
	<h3>What does this do?</h3>
	<p>Enables content to be expandable and collapsable. Like the following:<br>
	<a href="#expand1" onClick="expandcontent('expand1')" style="cursor:hand; cursor:pointer">Click here for more info</a><span id="expand1" class="expand"><a name="help"></a> - Here is all the info you will ever need...</a></span></p>

	<h3>How do I use it?</h3>
	<p>Just insert the tag into your template/page like: <code>{startExpandCollapse id="name" title="Click Here"}</code>. Also, you must use the {stopExpandCollapse} at the end of the collapseable content. Here is an example:<br>
	<br>
	<code>{startExpandCollapse id="name" title="Click Here"}<br>
	This is all the content the user will see when they click the title "Click Here" above. It will display all the content that is between the {startExpandCollapse} and {stopExpandCollapse} when clicked.<br>
	{stopExpandCollapse}
	</code>
	<br>
	<br>
	Note: If you intend to use this multiple times on a single page each startExpandCollapse tag must have a unique id.</p>
	<h3>What if I want to change the look of the title?</h3>
	<p>The look of the title can be changed via css. The title is wrapped in a div with the id you specify.</p>

	<h3>What parameters does it take?</h3>
	<p>
	<i>startExpandCollapse takes the following parameters</i><br>
	&nbsp; &nbsp;id - A unique id for the expand/collapse section.<br>
	&nbsp; &nbsp;title - The text that will be displayed to expand/collapse the content.<br>
	<i>stopExpandCollapse takes no parameters</i><br>
	</p>
	<?php
}

function smarty_cms_about_function_startExpandCollapse() {
	?>
	<p>Author: Brett Batie&lt;brett-cms@classicwebdevelopment.com&gt;</p>
	<p>Version: 1.0</p>
	<p>
	Change History:<br/>
	None
	</p>
	<?php
}

?>
