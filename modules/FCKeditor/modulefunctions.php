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

function fckeditor_module_header_function($cms)
{
	?>

	<script type="text/javascript" src="http://cms.wishy.org/modules/FCKeditor/fckeditor.js"></script>

	<?php
}

function fckeditor_module_textbox_function($cms, $name='textbox', $columns='80', $rows='8', $encoding='', $content='')
{
	require_once(dirname(__FILE__)."/fckeditor.php");
	$neweditor = new FCKeditor($name);
	$neweditor->BasePath = 'http://cms.wishy.org/modules/FCKeditor/';
	$neweditor->Width = '100%';
	$neweditor->Value = $content;
	#$neweditor->ToolbarSet = 'Basic';
	$neweditor->Create();
}

function fckeditor_module_help($cms)
{
	//Text to show for the help box...
	?>

	<h3>What does this do?</h3>
	<p>Enalbes FCKeditor to be used as a WYSIWYG.</p>
	<h3>How do I use it?</h3>
	<p>Install it, then go to User Preferences and Set FCKeditor to be your wysiwyg of choice.</p>

	<?php
}

function fckeditor_module_about()
{
	?>
	<p>Author: Ted Kulp &lt;wishy@cmsmadesimple.org&gt;</p>
	<p>Version: 1.0</p>
	<p>
	Change History:<br/>
	None
	</p>
	<?php
}

# vim:ts=4 sw=4 noet
?>
