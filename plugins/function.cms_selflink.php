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

function smarty_cms_function_cms_selflink($params, &$smarty) {

	global $gCms;
	$db = $gCms->db;
	$config = $gCms->config;

	if (isset($params['page']))
	{
		$page = $params['page'];
		$name = $params['page']; //mbv - 21-06-2005

		# check if the page exists in the db
		$content = ContentManager::LoadContentFromAlias($page);
		if ($content !== FALSE)
		{
			$pageid = $content->Id();
			$alias = $content->Alias();
			$name = $content->Name(); //mbv - 21-06-2005 
			$url = $content->GetUrl();
		}
			$Prev_label = "";
			$Next_label = "";
	}
	elseif (isset($params['dir'])) // this part is by mbv built on a proposal by 100rk
	{
		if (isset($params['lang'])){
			switch (strtolower($params['lang']))
			{
				case 'dk':
				case 'da':
					$Prev_label = "Forrige side: ";
					$Next_label = "N&aelig;ste side: ";
					break;
				case 'en':
					$Prev_label = "Previous page: ";
					$Next_label = "Next page: ";
					break;
				case '0':
					$Prev_label = "";
					$Next_label = "";
					break;
				default:
					$Prev_label = "Previous page: ";
					$Next_label = "Next page: ";
			}
		}
		else
		{
			$Prev_label = "Previous page: ";
			$Next_label = "Next page: ";
		}
		$condition = $order_by = false;
		switch (strtolower($params['dir']))
		{
			case 'next':
				$condition = '>';
				$order_by = 'hierarchy';
				$label=$Next_label;
				break;
			case 'prev':
			case 'previous':
				$condition = '<';
				$order_by = 'hierarchy DESC';
				$label=$Prev_label;
				break;
		}
		if ($condition && $order_by)
		{
			$query  = "SELECT DISTINCT t1.content_id, t1.content_alias, t1.content_name, t1.menu_text ";
			$query .= "FROM ".cms_db_prefix()."content AS t1, ".cms_db_prefix()."content AS t2 ";
			$query .= "WHERE t1.active = 1 ";
			$query .= "AND t1.type = 'content' "; // Added by Teemu Koistinen
			$query .= "AND t2.content_id = ".$gCms->variables['content_id']." ";
			$query .= "AND t1.hierarchy ".$condition." t2.hierarchy ";
			$query .= "ORDER BY t1.".$order_by." LIMIT 1";
			$dbresult = $db->Execute($query);
			if ($dbresult && $dbresult->RowCount() > 0)
			{
				while ($row = $dbresult->FetchRow())
				{
					$content = new content();
					$pageid = $content->mId = $row['content_id'];
					$alias = $content->mAlias = $row['content_alias'];
					$name = $content->mName = $row['content_name'];
					$menu_text = $content->mMenu_text = $row['menu_text'];
					$url = $content->GetUrl();
					unset($content);
				}
			}
		}
		unset($condition);
		unset($order_by);
	} // end of next-prev code
	if (isset($alias) && $alias != "") {
		if ($config["assume_mod_rewrite"]) 
			$url = $config["root_url"]."/".$alias.$config['page_extension'];
		else 
			$url = $config["root_url"]."/index.php?".$config["query_var"]."=".$alias;
	} else if (isset ($pageid)) {
		if ($config["assume_mod_rewrite"])
			$url = $config["root_url"]."/".$pageid.$config['page_extension'];
		else 
			$url = $config["root_url"]."/index.php?".$config["query_var"]."=".$pageid;
	} else {
		$url="";
	}

	$result = "";

	if ($url != "") {
		$result .= $label.'<a href="'.$url.'"';

		if (isset($params['target'])) {
			$result .= ' target="'.$params['target'].'"';
		}
		
		if (isset($params['id'])) {
			$result .= ' id="'.$params['id'].'"';
		}

		if (isset($params['class'])) {
			$result .= ' class="'.$params['class'].'"';
		}

		if (isset($params['more'])) {
			$result .= ' '.$params['more'];
		}
		$result .= '>';
		
		if (isset($params['text']))	{
			$result .= $params['text'];
		} elseif (isset($params['menu']) && $params['menu'] == "1")	{ // mbv 
			$result .= $menu_text;
		} else {
			$result .= $name; // mbv - 21-06-2005 
		}

		$result .= '</a>';
	}
	else {
		$result .= "<!-- Not a valid cms_selflink -->".$params['text'];
	}
	
	return $result;
}

function smarty_cms_help_function_cms_selflink() {
	?>
	<h3>What does this do?</h3>
	<p>Creates a link to another cms content page inside your template or content.</p>
	<h3>How do I use it?</h3>
	<p>Just insert the tag into your template/page like: <code>{cms_selflink page="1"}</code></p>
	<h3>What parameters does it take?</h3>
	<p>
	<ul>
		<li><em>(optional)</em> <tt>page</tt> - Page ID or alias to link to.</li>
		<li><em>(optional)</em> <tt>dir next/prev (previous)</tt> - If this is used <tt>page</tt> should not be set.</li> <!-- mbv - 21-06-2005 -->
			<B>Note!</B> Only one of the above may be used in the same cms_selflink statement!!
		<li><em>(optional)</em> <tt>text</tt> - Text to show for the link.  If not given, the Page Name is used instead.</li>
		<li><em>(optional)</em> <tt>menu 1/0</tt> - If 1 the Menu Text is used for the link text in stead of the Page Name</li> <!-- mbv - 21-06-2005 -->
		<li><em>(optional)</em> <tt>target</tt> - Optional target for the a link to point to.  Useful for frame and javascript situations.</li>
		<li><em>(optional)</em> <tt>class</tt> - Class for the &lt;a&gt; link.  Useful for styling the link.</li> <!-- mbv - 21-06-2005 -->
		<li><em>(optional)</em> <tt>lang</tt> - Display link-labels  ("Next Page"/"Previous Page") in different languages (0 for no label.) Danish (dk) or English (en), for now.</li> <!-- mbv - 21-06-2005 -->
		<li><em>(optional)</em> <tt>id</tt> - Optional css_id for the &lt;a&gt; link.</li> <!-- mbv - 29-06-2005 -->
		<li><em>(optional)</em> <tt>more</tt> - place additional options inside the &lt;a&gt; link.</li> <!-- mbv - 29-06-2005 -->
	</ul>
	</p>

	<?php
}

function smarty_cms_about_function_cms_selflink() {
	?>
	<p>Author: Ted Kulp &lt;tedkulp@users.sf.net&gt;</p>
	<p>Version: 1.1</p>
	<p>Modified: Martin B. Vestergaard &lt;mbv@nospam.dk&gt;</p>
	<p>Version: 1.4</p>
	<p>
	Change History:<br/>
	1.4 - fixed bug next/prev linking to non-content pages. (Thanks Teemu Koistinen for this fix)
	1.3 - added option "more"<br />
	1.2 - by Martin B. Vestergaard
	<ul>
		<li>changed default text to Page Name (was Page Alias)</li>
		<li>added option dir=next/prev to display next or previous item in the hirachy - thanks to 100rk</li>
		<li>added option class to add a class= statement to the a-tag.</li>
		<li>added option menu to display menu-text in sted of Page Name</li>
		<li>added option lang to display link-labels in different languages</li>
	</ul>
	1.1 - Changed to new content system<br />
	1.0 - Initial release
	</p>
	<?php
}

# vim:ts=4 sw=4 noet
?>
