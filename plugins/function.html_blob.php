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

require dirname(dirname(__FILE__)) . "/lib/classes/class.htmlblob.inc.php";

function smarty_cms_function_html_blob($params, &$smarty) {

	$blobname = $params['name'];

	$oneblob = HtmlBlobOperations::LoadHtmlBlobByName($blobname);
	if ($oneblob)
	{
		echo $oneblob->content;
	}
	else
	{
		echo "<!-- Html blob '" . $blobname . "' does not exist  -->";
	}
}

function smarty_cms_help_function_html_blob() {
	?>
	<h3>What does this do?</h3>
	<p></p>
	<h3>How do I use it?</h3>
	<p>It's just a basic tag plugin.  You would insert it into your template or page like so: <code>{html_blob name="someblobname"}</code>
	<h3>What parameters does it take?</h3>
	<p>There is only one required parameter.  All other parameters are passed on to the module.
	<ul>
		<li>name - Name of the blob to insert.  Case Sensitivity is dependant on the database used (mysql is not case sensitive).</li>
	</ul>
	</p>
	<?php
}

function smarty_cms_about_function_html_blob() {
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
