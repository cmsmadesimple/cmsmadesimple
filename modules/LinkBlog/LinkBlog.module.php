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
#
#$Id$

class LinkBlog extends CMSModule
{
	function GetName()
	{
		return 'LinkBlog';
	}

	function IsPluginModule()
	{
		return true;
	}

	function HasAdmin()
	{
		return true;
	}

	function GetVersion()
	{
		return '1.6';
	}

	function Install()
	{
		$db = $this->cms->db;
		$dict = NewDataDictionary($db);

## Create the actual linkblog table
		$flds = "
			linkblog_id I KEY,
				    linkblog_title C(255),
				    linkblog_url C(255),
				    linkblog_author C(255),
				    linkblog_type I,
				    create_date T,
				    modified_date T,
				    status C(10)
					    ";
		$taboptarray = array('mysql' => 'TYPE=MyISAM');
		$sqlarray = $dict->CreateTableSQL(cms_db_prefix()."module_linkblog", $flds, $taboptarray);
		$dict->ExecuteSQLArray($sqlarray);

		$db->CreateSequence(cms_db_prefix()."module_linkblog_seq");

## Create the linkblog_types table and put in the initial data
		$flds = "
			linkblog_type_id I,
					 linkblog_type C(255)
						 ";
		$sqlarray = $dict->CreateTableSQL(cms_db_prefix()."module_linkblog_types", $flds, $taboptarray);
		$dict->ExecuteSQLArray($sqlarray);

		$new_id_type = $db->GenID(cms_db_prefix()."module_linkblog_type_seq");
		$query = "INSERT INTO ".cms_db_prefix()."module_linkblog_types (linkblog_type_id, linkblog_type) VALUES ";
		$query.= "($new_id_type, ".$db->qstr("News").")";
		$dbresult = $db->Execute($query);

		$new_id_type = $db->GenID(cms_db_prefix()."module_linkblog_type_seq");
		$query = "INSERT INTO ".cms_db_prefix()."module_linkblog_types (linkblog_type_id, linkblog_type) VALUES ";
		$query.= "($new_id_type, ".$db->qstr("Sports").")";
		$dbresult = $db->Execute($query);

		$new_id_type = $db->GenID(cms_db_prefix()."module_linkblog_type_seq");
		$query = "INSERT INTO ".cms_db_prefix()."module_linkblog_types (linkblog_type_id, linkblog_type) VALUES ";
		$query.= "($new_id_type, ".$db->qstr("Silly").")";
		$dbresult = $db->Execute($query);

		$new_id_type = $db->GenID(cms_db_prefix()."module_linkblog_type_seq");
		$query = "INSERT INTO ".cms_db_prefix()."module_linkblog_types (linkblog_type_id, linkblog_type) VALUES ";
		$query.= "($new_id_type, ".$db->qstr("Funny").")";
		$dbresult = $db->Execute($query);

## Create the comments table
		$flds = "
			comment_id I KEY,
				   linkblog_id I,
				   author C(255),
				   comment C(255),
				   create_date T
					   ";
		$sqlarray = $dict->CreateTableSQL(cms_db_prefix()."module_linkblog_comments", $flds, $taboptarray);
		$dict->ExecuteSQLArray($sqlarray);

		$db->CreateSequence(cms_db_prefix()."module_linkblog_comment_seq");
		cms_mapi_create_permission( $cms, 'Modify Linkblog', 'Modify Linkblog');

	}

	function Upgrade($oldversion, $newversion)
	{
		$db = $this->cms->db;
		switch ($oldversion) {
			case "1.0":
				case "1.1":
				cms_mapi_create_permission( $cms, 'Modify Linkblog', 'Modify Linkblog');
			case "1.2":
				$dict = NewDataDictionary($db);
				$sqlarray = $dict->AddColumnSQL(cms_db_prefix()."module_linkblog", "status C(10)");
				$dict->ExecuteSQLArray($sqlarray);
				$update = "UPDATE ".cms_db_prefix()."module_linkblog SET status='1'";
				$db->Execute($update);
			case "1.3":
			case "1.4":
				$dict = NewDataDictionary($db);
				$sqlarray = $dict->AddColumnSQL(cms_db_prefix()."module_linkblog", "linkblog_credit C(255)");
				$dict->ExecuteSQLArray($sqlarray);
			case "1.5":
				$dict = NewDataDictionary($db);
				$sqlarray = $dict->AddColumnSQL(cms_db_prefix()."module_linkblog", "linkblog_hits I");
				$dict->ExecuteSQLArray($sqlarray);
				$update = "UPDATE ".cms_db_prefix()."module_linkblog SET linkblog_hits=0";
				$db->Execute($update);
		} ## switch
	} ## Upgrade

	function Uninstall()
	{
		$db = $this->cms->db;
		$dict = NewDataDictionary($db);
		$sqlarray = $dict->DropTableSQL(cms_db_prefix()."module_linkblog");
		$dict->ExecuteSQLArray($sqlarray);

		$sqlarray = $dict->DropTableSQL(cms_db_prefix()."module_linkblog_types");
		$dict->ExecuteSQLArray($sqlarray);

		$sqlarray = $dict->DropTableSQL(cms_db_prefix()."module_linkblog_comments");
		$dict->ExecuteSQLArray($sqlarray);

		$db->DropSequence(cms_db_prefix()."module_linkblog_seq");
		$db->DropSequence(cms_db_prefix()."module_linkblog_type_seq");
		$db->DropSequence(cms_db_prefix()."module_linkblog_comment_seq");
	} ## Uninstall

	function DoAction($action, $id, $params, $return_id = '')
	{
		debug_buffer($return_id);
		$errormsg = "";
		if (!isset($params["sort_order"]))
		{
			$params["sort_order"] = "default";
		}

		switch($action) {
			case "default":
			{
				$break = false;
				if ($params["allow_search"]) {
					echo "<div class=\"modulelinkblogsearchform\">\n";
					echo $this->CreateFormStart($id, "search_url", $this->cms->variables["page"]);
				?>
					<table>
					<tr>
					<td><?php echo $this->CreateInputText($id, "search_text", "", '20', '50'); ?></td>
					<td><?php echo $this->CreateInputSubmit($id, 'submit', 'Submit') ?></td>
					</tr>
					</table>
					<?php
					echo $this->CreateFormEnd();
					echo "</div>\n";
					$break = true;
				} ## if
				if ($params["view_popular"]) {
					echo "<div class=\"modulelinkblogpopular\">";
					echo $this->CreateLink($id, "viewoldlinks", $this->cms->variables["page"], "View popular", array('sort_order'=>'popular'));
					echo "</div>\n";
					$break = true;
				}
				if (isset($params["make_rss_button"])) {
					echo "<div class=\"modulelinkblogrss\">\n";
					echo $this->CreateLink($id, 'viewoldlinks', $this->cms->variables['page'], "<img border=\"0\" src=\"images/cms/xml_rss.gif\" alt=\"RSS Linkblog Feed\" />", array('type'=>'rss', 'showtemplate'=>'false'));
					echo "</div>\n";
					$break = true;
				} ## if
 ## if

				if ($break) { break; }
				$this->linkblog_module_showLinks($id, $params, $return_id);
				break;
			}

			case "defaultadmin":
			{
				$this->admin_menu($id, $params);
				break;
			}

			case "viewoldlinks":
			{
				$this->linkblog_module_showLinks($id, $params, $return_id);
				break;
			}

			case "add_new_link":
			{
				$this->add_new_link($id, $params, $return_id);
				break;
			}

			case "create_add_link_form":
			{
				$this->create_add_new_link_form($id, $params, $return_id);
				break;
			}

			case "view_comments":
				case "post_comment":
				{
					$this->view_linkblog_id($id, $params, $return_id);
					break;
				}

			case "search_url":
			{
				$this->search_url($id, $params, $return_id);
				break;
			}

			case "redirect":
			{
				$this->redirect_url($id, $params, $return_id);
				break;
			}

			case "admin_add_category_form":
			{
				$this->admin_menu($id, $params);
				$this->linkblog_module_add_category_form($id, $params);
				break;
			}

			case "admin_add_category_to_db":
			{
				$this->admin_menu($id, $params);
				$this->linkblog_module_add_category_to_db($id, $params);
				$this->linkblog_module_manage_categories($id, $params);
				break;
                        }

			case "admin_manage_categories":
			{
				$this->admin_menu($id, $params);
				$this->linkblog_module_manage_categories($id, $params);
				break;
			}

			case "admin_edit_category":
			{
				$this->admin_menu($id, $params);
				$this->linkblog_module_edit_category($id, $params);
				break;
			}

			case "admin_update_category":
			{
				$this->admin_menu($id, $params);
				$this->linkblog_module_update_category($id, $params);
				$this->linkblog_module_manage_categories($id, $params);
				break;
			}

			case "admin_manage_links":
			{
				$this->admin_menu($id, $params);
				$this->linkblog_module_manage_links($id, $params);
				break;
			}

			case "admin_activate":
			{
				$this->admin_menu($id, $params);
				$this->linkblog_module_activate_link($id, $params);
				$this->linkblog_module_manage_links($id, $params);
				break;
			}
			
			case "admin_deactivate":
			{
				$this->admin_menu($id, $params);
				$this->linkblog_module_deactivate_link($id, $params);
				$this->linkblog_module_manage_links($id, $params);
				break;
			}

			case "admin_preferences":
			{
				$this->admin_menu($id, $params);
				$this->linkblog_module_view_preferences($id, $params);
				break;
			}

			case "admin_set_preferences":
			{
				$this->admin_menu($id, $params);
				$this->linkblog_module_set_preferences($id, $params);
				$this->linkblog_module_view_preferences($id, $params);
				break;
			}
				
		} ## switch

	} ## DoAction


	function GetAuthor()
	{
		return 'Greg Froese';
	}

	function GetAuthorEmail()
	{
		return 'greg.froese@gmail.com';
	}

	function GetHelp()
	{
		return "
		<h3>What does this do?</h3>
		<p>LinkBlog is a module which allows users to post links and comments about the links to a page within your site.  It can be inserted into a template or content page as a module, however, it makes more sense as a content page because it does produce a complete page.</p>
		<h3>Anything else I should know?</h3>
		<p>You should know that if you uninstall this module it will delete all your links and comments.  If you do not wish to use it for a temporary time period, just deactivate it.</p>
		<h3>How do I use it?</h3>
		<p>As this is just a tag module, it's inserted into your page or template by using the cms_module tag.  Example syntax would be: <br /><code>{cms_module module=\"LinkBlog\" allow_search=\"true\"}</code></p>
		<h3>What parameters are there?</h3>
		<p>category - set category=\"NAME\" where NAME is a category name.  Only links in that category will be visible.  This includes the links generated to previously posted links.</p>
		<i>If any of the following options are set, only the chosen option will appear, the default linkblog page will not.  For example: you would have to use a second <code>{cms_module module=\"LinkBlog\"}</code> line to show both the search form and the default page.</i>
		<p>allow_search - set allow_search=\"true\" to show a search form - off by default</p>
		<p>make_rss_button - creates an image link to the rss feed for the linkblog.<br />
		The RSS feed title, linkblog URL, and number of links returned in the rss feed are all set in the linkblog admin preferences.<br />
		Email notification preferences are also set in the linkblog admin preferences.</p>
		<p>view_popular - shows the top 20 most clicked on links (view_popluar=\"true\")</p>
		";
	}
	
	function GetChangeLog()
	{
		?>
			<p>Author: Greg Froese &lt;heavy_g@users.sf.net&gt;<br />
			Please send all bug requests through <a href="http://bugs.cmsmadesimple.org">here</a></p>
			<b>Change History:</b><br/>
			<p>Version: 1.5</p>
			<p>Version 1.4 - 2005.01.03</p>
			<ul>
			<li>Added an rss feed</li>
			</ul>
			Version 1.3 - 2004.12.20
			<ul>
			<li>Added admin functions for managing categories and links</li>
			<li>Added a status code to each link to allow the administrator to hide selected links</li>
			<li>Version 1.2 got lost on the way to 1.3</li>
			<li>Added ability to filter links by category</li>
			<li>Added the To Do section below</li>
			</ul>
			Version 1.1 - 2004.12.01
			<ul>
			<li>Added search functionality</li>
			</ul>
			Version 1.0
			<ul>
			<li>Initial release</li>
			</ul>
			</p>
			<p>
			To do:
			<ul>
			<li>Allow a link's category to be changed from the admin section</li>
			<li>Password protect submissions</li>
			<li>Spam protection for submissions</li>
			</ul>

			<?php

	}

	function redirect_url($id, $params, $return_id)
	{
		$db = $this->cms->db;
		$update = "UPDATE ".cms_db_prefix()."module_linkblog SET linkblog_hits=linkblog_hits+1 WHERE linkblog_id=".$params["linkblog_id"];
		$dbresult = $db->Execute($update);
		$select = "SELECT linkblog_url FROM ".cms_db_prefix()."module_linkblog WHERE linkblog_id=".$params["linkblog_id"];
		$dbresult = $db->Execute($select);

		if ($dbresult && $dbresult->RowCount()) {
			while ($row = $dbresult->FetchRow()) {
				$url = html_entity_decode($row["linkblog_url"], ENT_NOQUOTES, get_encoding($encoding));
			}
		}
		redirect($url);
	}
	
	function linkblog_module_showLinks($id, $params, $return_id) {

		$db = $this->cms->db;
		$allow_search = '';
		if(isset($params['allow_search']))
		{
			$allow_search = $params['allow_search'];
		}

		if(isset($params['limit']))
		{
			$limit = $params['limit'];
		} else {
			$limit = "20";
		}

		if(isset($params['category']))
		{
			$category = $params['category'];
		}

		if ($params['type'] == "rss") {

			$limit = $this->GetPreference("rss_limit", "10");
			if (!isset($limit)) { $limit = 10; } ## if
				header('Content-type: text/xml');
			echo "<?xml version='1.0'?>\n";
			echo "<rss version='2.0'>\n";
			echo "  <channel>\n";
			echo "  <title>".$this->GetPreference("rss_title", "LinkBlog")."</title>\n";
			echo "  <link>";
			echo cms_htmlentities($this->GetPreference("linkblog_url", ""), ENT_NOQUOTES, get_encoding($encoding));
			echo "</link>\n";
			echo "  <description>Current linkblog entries</description>\n";
		} else if ($allow_search != '') {
			echo "<div class=\"modulelinkblogsearchform\">\n";
			echo $this->CreateFormStart($id, "search_url", $this->cms->variables["page"]);
			?>
				<table>
				<tr>
				<td><?php echo $this->CreateInputText($id, "search_text", "", '20', '50'); ?></td>
				<td><?php echo $this->CreateInputSubmit($id, 'submit', 'Submit') ?></td>
				</tr>
				</table>
				<?php
				echo $this->CreateFormEnd();
			echo "</div>\n";
		}

		$curr_date = date("Y-m-d");
		$old_date = "";
		if (isset($params["old_date"])) {
			if ($params["old_date"] == "curr_date") {
				$old_date = $curr_date;
			} else {
				$old_date = $params["old_date"];
			} ## if
		} ## if

		$query = "SELECT a.linkblog_id, a.linkblog_title, a.linkblog_url, a.linkblog_author, a.linkblog_type as type_id, a.create_date, count(b.linkblog_id) as total, a.linkblog_credit";
		$query .= ", c.linkblog_type as category";
		$query .= " FROM";
		$query .= " ".cms_db_prefix()."module_linkblog_types c,";
		$query .= " ".cms_db_prefix()."module_linkblog a LEFT OUTER JOIN ".cms_db_prefix()."module_linkblog_comments b ON a.linkblog_id=b.linkblog_id WHERE";
		$query .= " a.linkblog_type=c.linkblog_type_id";
		$query .= " and a.status = '1'";
		if ($category != "") { $query .= " and c.linkblog_type like \"%$category%\""; }
		if ($params["sort_order"] == "default") {
			if ($old_date != "") {
				$query .= " and a.create_date like '$old_date%'";
			} ## if
			$query .= " GROUP BY a.linkblog_id ORDER BY create_date DESC";
		} else if ($params["sort_order"] == "popular") {
			$query .= " GROUP BY a.linkblog_id";
			$query .= " ORDER by linkblog_hits DESC, create_date DESC";
		}

		$query .= " LIMIT $limit";

		$dbresult = $db->Execute($query);

		if ($params['type'] != "rss") {
			echo "<div class=\"modulelinkblogtitle\">Posted sites</div>";
			echo "<div class=\"modulelinkblog\">\n";
		} ## if

		if ($dbresult && $dbresult->RowCount()) {
##         $last_date = "";
			while ($row = $dbresult->FetchRow()) {

				if ($params['type'] == "rss") {
					echo "  <item>\n";
					echo "          <title>".$row["linkblog_title"]."</title>\n";
					echo "          <link>";
					echo cms_htmlentities($row["linkblog_url"], ENT_NOQUOTES, get_encoding($encoding));
					echo "</link>\n";
## this is just redundant echo "                <description>".$row["linkblog_title"]."</description>\n";
## needs to be an email address echo "          <author>".$row["linkblog_author"]."</author>\n";
					echo "          <pubDate>".date("D, j M Y H:i:s T", $db->UnixTimeStamp($row['create_date']))."</pubDate>\n";

					echo "  </item>\n";
				} else {
					echo "<div class=\"modulelinkblogentry\">\n";
					$this_date = substr($row["create_date"],0,10);
					if ($last_date != $this_date || $params["sort_order"] != "default") {
						echo "<div class=\"modulelinkblogentrydate\">\n";
						echo date("F j, Y", $db->UnixTimeStamp($row['create_date']))."<br />\n";
						echo "</div>\n";
					} ## if

					echo "<div class=\"modulelinkblogentrytime\">\n";
					echo "Posted at ".date("g:i a", $db->UnixTimeStamp($row['create_date']))." by ".$row['linkblog_author']."\n";
					echo "</div>\n";

					echo "<div class=\"modulelinkblogentrybody\">\n";
					echo $this->CreateLink($id, 'viewoldlinks', $this->cms->variables['page'], "<img src=\"modules/LinkBlog/images/type".$row["type_id"].".gif\" border=\"0\" alt=\"\" />", array('category'=>$row["category"]));
##" (<a href=\"".$row["linkblog_url"]."\">Link</a>)\n";
					echo "(";
					echo $this->CreateLink($id, 'redirect', $this->cms->variables['page'], "Link", array('linkblog_id'=>$row["linkblog_id"], 'showtemplate'=>'false'));
					echo ")\n";
					echo " ".$row["linkblog_title"]."\n";
					echo "</div>\n";

					echo "<div class=\"modulelinkbloglinks\">\n";
					echo " | \n";
					echo $this->CreateLink($id, 'view_comments', $this->cms->variables['page'], 'Comments ('.$row["total"].")", array('linkblog_id'=>$row["linkblog_id"]));
					echo " | \n";
					echo $this->CreateLink($id, 'view_comments', $this->cms->variables['page'], 'Permalink', array('linkblog_id'=>$row["linkblog_id"]));
					echo " | \n";
					if (isset($row["linkblog_credit"]) && $row["linkblog_credit"] != "") {
						echo "Source credit: <a href=\"".$row["linkblog_credit"]."\">".$row["linkblog_credit"]."</a>\n";
						echo " | \n";
					} ## if
					echo "\n</div>\n";
					echo "</div>\n";
				} ## if

					$last_date = substr($row["create_date"],0,10);
			} ## while

					if ($params['type'] == "rss") {
						echo "  </channel>\n";
						echo "</rss>\n";
						return;
					} ## if

					// show a list of the last 5 days with links
					$query = "SELECT create_date from ".cms_db_prefix()."module_linkblog GROUP BY DATE_FORMAT(create_date, '%d %m %Y') ORDER BY create_date DESC LIMIT 5";
					$dbresult = $db->Execute($query);

					if ($dbresult && $dbresult->RowCount()) {
						$last_date = "";
						echo "<p>Recent links:</p><ul>\n";
						echo "<li>";
						echo $this->CreateLink($id, 'viewoldlinks', $this->cms->variables['page'], 'Today', array('old_date'=>$curr_date, 'allow_search'=>'true', 'category'=>$category));
						echo "</li>\n";

						while ($row = $dbresult->FetchRow()) {
							if ($last_date != substr($row["create_date"],0,10) && substr($row["create_date"],0,10) != $curr_date) {
								echo "<li>";
								echo $this->CreateLink($id, 'viewoldlinks', $this->cms->variables['page'], substr($row["create_date"],0,10), array('old_date'=>substr($row["create_date"],0,10), 'allow_search'=>'true', 'category'=>$category));
								echo "</li>\n";
							}
							$last_date = substr($row["create_date"],0,10);
						}
						echo "</ul>\n";
					}
		} else {
			if ($params[$id.'type'] == "rss") {
				echo "      </channel>\n";
				echo "</rss>\n";
				return;
			} else {
				echo " - No linkblog posted - ";
			} ## if
		}
		echo "</div>\n";
		echo "<div class=\"modulelinkbloglink\">\n";
#echo $this->CreateLink($id, 'create_add_link_form', $return_id, 'Add a Link', array());
		echo $this->CreateLink($id, 'create_add_link_form', $this->cms->variables['page'], 'Add a Link', array());
		echo "</div>\n";
	} ## showlinks

	function add_new_link($id, $params, $return_id)
	{
		$validinfo = true;

		if (isset($params["rememberme"])) {
			$_SESSION["linkblog_author"] = $params["author"];
		} else {
			unset($_SESSION["linkblog_author"]);
		}

		if (isset($params["author"])) {
			$author = $params["author"];
		} else {
			$validinfo = false;
			$errormsg .= "Enter an author<br />";
		}	
		if ($author == "") {
			$author = $_SESSION["linkblog_author"];
		}

		if (!isset($params["title"])) {
			$validinfo = false;
			$errormsg .= "Enter a title so people know what you are talking about.<br />";
		}

		if (!isset($params["type"])) {
			$validinfo = false;
			$errormsg .= "Enter a type<br />";
		}

		if (!isset($params["url"])) {
			$validinfo = false;
			$errormsg .= "Enter a link, where are we supposed to go?<br />";
		}

		$credit = "";
		if (isset($params["credit"])) {
			$credit = $params["credit"];
		}

		if ($validinfo) {
			$db = $this->cms->db;
			$new_id = $db->GenID(cms_db_prefix()."module_linkblog_seq");
			$query = "INSERT INTO ".cms_db_prefix()."module_linkblog (linkblog_id, linkblog_author, linkblog_title, linkblog_type, linkblog_url, create_date, modified_date, status, linkblog_credit) VALUES ($new_id, ".$db->qstr($params["author"]).", ".$db->qstr($params["title"]).",".$params["type"].",".htmlentities($db->qstr($params["url"])).",'".$db->DBTimeStamp(time())."','".$db->DBTimeStamp(time())."', '2', ".htmlentities($db->qstr($credit)).")";
			$dbresult = $db->Execute($query);

			if ($this->GetPreference("email_notify", "0") == "1") {
				$subject = 'New Linkblog submission';
				$body = "A new link for your linkblog has been submitted.\n\n";
				$body .= "Title: {".$db->qstr($title)."}\n";
				$body .= "Url: {".htmlentities($db->qstr($url))."}\n";
				$body .= "Author: {".$db->qstr($author)."}\n";

				$headers = "From: \"CMS Linkblog Module\" <".$this->GetPreference("email_from", "").">\r\n"
					."Return-Path: \"CMS Linkblog Module\" <".$this->GetPreference("email_from", "").">\r\n"
					."X-Mailer: PHP/" . phpversion();
				$result = @mail($this->GetPreference("email_to", ""), $subject, $body, $headers);
			} ## if

			cms_mapi_redirect_user_by_pageid($return_id);
			return;
		} else {
			echo "Error: $errormsg<br />\n";
			return;
		} ## if

		cms_mapi_redirect_user_by_pageid($return_id);
		return;

	}

	function create_add_new_link_form($id, $params, $return_id)
	{

		if (strlen($errormsg) > 0) {
			echo "<p>Error:</p><ul>".$errormsg."</ul>";
		}

		$types = "<select name=\"".$id."type\">\n";
		$db = $this->cms->db;
		$query = "SELECT linkblog_type_id, linkblog_type from ".cms_db_prefix()."module_linkblog_types ORDER BY linkblog_type";
		$dbresult = $db->Execute($query);
		if ($dbresult && $dbresult->RowCount()) {
			while ($row = $dbresult->FetchRow()) {
				$types .= "<option value=\"".$row['linkblog_type_id']."\">".$row['linkblog_type']."</option>\n";
			}
		}
		$types .= "</select>\n";

		echo $this->CreateFormStart($id, "add_new_link", $this->cms->variables["page"]);

		?>
			<p class="smalltitle">Add Link - <?php echo $this->CreateLink($id, 'default', $this->cms->variables['page'], "FIXME: Back to LinkBlog", array()); ?></p>

			<table>
			<tr>
			<td>* Your name:</td>
			<td><?php echo $this->CreateInputText($id, "author", $author, '20', '50'); ?></td>
			</tr>
			<tr>
			<td>* Title:</td>
			<td><?php echo $this->CreateInputText($id, "title", $title, '50', '250'); ?></td>
			</tr>
			<tr>
			<td>* URL:</td>
			<td><?php echo $this->CreateInputText($id, "url", $url, '50', '250'); ?></td>
			</tr>
			<tr>
			<td>* Category:</td>
			<td><?php echo $types?></td>
			</tr>
			<tr>
			<td>Source credit:</td>
			<td><?php echo $this->CreateInputText($id, "credit", $credit, '50', '250'); ?></td>
			</tr>
			<tr>
			<td></td>
			<td><input type="checkbox" name="<?php echo $id?>rememberme" <?php if(isset($_SESSION["linkblog_author"])) { echo "CHECKED"; }?> /> Remember me</td>
			</tr>
			<tr>
			<td></td>
			<td><?php echo $this->CreateInputSubmit($id, 'submit', 'Submit') ?>
			<?php echo $this->CreateInputSubmit($id, 'cancelsubmit', 'Cancel') ?>
			* Note: Links are subject to moderation prior to showing up on the main page.</td>
			</tr>
			</table>

			<?php echo $this->CreateFormEnd()?>
			<?php
	} ## create_add_new_link_form

	function view_linkblog_id($id, $params, $return_id)
	{
		$db = $this->cms->db;
		$query = "SELECT linkblog_id, linkblog_title, linkblog_url, linkblog_author, linkblog_type as type_id, create_date, status, linkblog_credit from ".cms_db_prefix()."module_linkblog WHERE linkblog_id=".$params["linkblog_id"];
		$dbresult = $db->Execute($query);
		echo "<p class=\"modulelinkblogtitle\">Posted site - ";
		echo cms_mapi_create_content_link_by_page_id($return_id, "Back to LinkBlog");
		echo "</p>\n";
		echo "<div class=\"modulelinkblog\">\n";

		if ($dbresult && $dbresult->RowCount()) {
			while ($row = $dbresult->FetchRow()) {

				if ($row["status"] == "0") {
					echo "This link is no longer available<br />\n";
					echo "</div>\n";
					return;
				} ## if
				echo "<div class=\"modulelinkblogentrydate\">\n";
				echo date("F j, Y", $db->UnixTimeStamp($row['create_date']))."<br />\n";
				echo "</div>\n";
				echo "<div class=\"modulelinkblogentrytime\">\n";
				echo "Posted at ".date("g:i a", $db->UnixTimeStamp($row['create_date']))." by ".$row['linkblog_author']."\n";
				echo "</div>\n";

				echo "<div class=\"modulelinkblogentrybody\">\n";
				echo "<img src=\"modules/LinkBlog/images/type".$row["type_id"].".gif\" border=\"0\" alt=\"\" />";
				echo " (<a href=\"".$row["linkblog_url"]."\">Link</a>)\n";
				echo " ".$row["linkblog_title"]."\n";
				echo "</div>\n";

				echo "<div class=\"modulelinkbloglinks\">\n";
				echo " | \n";
				echo $this->CreateLink($id, 'view_comments', $this->cms->variables['page'], 'Permalink', array('linkblog_id'=>$row["linkblog_id"]));
				echo " | \n";
				if (isset($row["linkblog_credit"]) && $row["linkblog_credit"] != "") {
					echo "Source credit: <a href=\"".$row["linkblog_credit"]."\">".$row["linkblog_credit"]."</a>\n";
					echo " | \n";
				} ## if
			} ## while
		} else {
			echo "There is no link available for id: ".$params["linkblog_id"];
			echo "</div>\n";
			return;
		} ## if

		echo "</div>\n";

		if ($params["action"] == "post_comment") {

			$validinfo = true;
			$errormsg = "<ul>\n";
		}

		if ($errormsg != "") { $errormsg .= "</ul>\n"; }

		if ($validinfo) {
			$new_id = $db->GenID(cms_db_prefix()."module_linkblog_comment_seq");
			$query = "INSERT INTO ".cms_db_prefix()."module_linkblog_comments (comment_id, linkblog_id, author, comment, create_date) VALUES ($new_id,".$params["linkblog_id"].",".$db->qstr($params["author"]).",".$db->qstr($params["comment"]).", now())";
			$dbresult = $db->Execute($query);
		} else {
			echo $errormsg;
		}

		$query = "SELECT author, comment, create_date FROM ".cms_db_prefix()."module_linkblog_comments WHERE linkblog_id=".$params["linkblog_id"]." ORDER BY create_date desc";
		$dbresult = $db->Execute($query);
		echo "<p class=\"smalltitle\">Comments</p>\n";
		echo "<div class=\"modulelinkblog\">\n";
		if ($dbresult && $dbresult->RowCount()) {
			while ($row = $dbresult->FetchRow()) {
				echo "<div class=\"modulelinkblogauthor\">".$row["author"]." - ".date("F j, Y, g:i a", $db->UnixTimeStamp($row['create_date']))."</div>\n";
				echo "<div class=\"modulelinkblogcomment\">".$row["comment"]."</div>\n";
			}
		}
		echo "</div>\n";

		echo $this->CreateFormStart($id, "post_comment", $this->cms->variables["page"]);
		?>
			<h3>Add a comment</h3>


			<table>
			<tr>
			<td>Your name:</td>
			<td><?php echo $this->CreateInputText($id, "author", $author, '20', '50'); ?></td>
			</tr>
			<tr>
			<td>Comment:</td>
			<td><?php echo $this->CreateInputText($id, "comment", "", '100', '250'); ?></td>
			</tr>
			<tr>
			<td><?php echo $this->CreateInputHidden($id, 'linkblog_id', $params["linkblog_id"]);?></td>
			<td>
			<?php echo $this->CreateInputSubmit($id, 'submit', 'Submit') ?>
			<?php echo $this->CreateInputSubmit($id, 'cancelsubmit', 'Cancel') ?>
			</td>
			</tr>
			</table>
			<?php

			echo $this->CreateFormEnd();


	} ## view_linkblog_id


	function search_url($id, $params, $return_id)
	{
		echo "<p class=\"smalltitle\">Search Results for (".$params["search_text"].")";
		echo cms_mapi_create_content_link_by_page_id($return_id, "Back to LinkBlog");
		echo "</p>\n";
		echo "<div class=\"modulelinkblog\">\n";
		$db = $this->cms->db;
		$query = "SELECT a.linkblog_id, a.linkblog_title, a.linkblog_url, a.linkblog_author, a.linkblog_type, a.create_date, count(b.linkblog_id) as total FROM ".cms_db_prefix()."module_linkblog a LEFT OUTER JOIN ".cms_db_prefix()."module_linkblog_comments b ON a.linkblog_id=b.linkblog_id";
		$query .= " WHERE (a.linkblog_url like '%".$params["search_text"]."%'";
		$query .= " OR a.linkblog_title like '%".$params["search_text"]."%')";
		$query .= " AND a.status = '1'";
		$query .= " GROUP BY a.linkblog_id ORDER BY create_date DESC";
		$dbresult = $db->Execute($query);
		if ($dbresult && $dbresult->RowCount()) {
			while ($row = $dbresult->FetchRow()) {

				echo "<div class=\"modulelinkblogentry\">\n";
				if ($last_date == "") {
					echo "<div class=\"modulelinkblogentrydate\">\n";
					echo date("F j, Y", $db->UnixTimeStamp($row['create_date']))."<br />\n";
					echo "</div>\n";
				}
				echo "<div class=\"modulelinkblogentrytime\">\n";
				echo "Posted at ".date("g:i a", $db->UnixTimeStamp($row['create_date']))." by ".$row['linkblog_author']."\n</div>\n";
				echo "<div class=\"modulelinkblogentrybody\">\n<a href=\"".$row["linkblog_url"]."\"><img src=\"modules/LinkBlog/images/type".$row["linkblog_type"].".gif\" border=\"0\" alt=\"\" /> ".$row["linkblog_title"]."</a>\n";
				echo "</div>\n";

				echo "<div class=\"modulelinkblogentrycommentlink\">\n";
				echo $this->CreateLink($id, 'view_comments', $this->cms->variables['page'], 'Comments ('.$row["total"].")", array('linkblog_id'=>$row["linkblog_id"]));
				echo "\n</div>\n";
				echo "</div>\n";
			} ## while
		} ## if

	} ## search_url

	function admin_menu($id, $params)
	{
		$access = cms_mapi_check_permission($cms, "Modify Linkblog");
		if (!$access) {
		echo "<p class=\"error\">You need the 'Modify Linkblog' permission to perform that function.</p>";
		return;
		} ## if

		## create mini menu
		echo $this->CreateLink($id, 'admin_add_category_form', $this->cms->variables['page'], 'Add Category', array());
		echo " | " . $this->CreateLink($id, 'admin_manage_categories', $this->cms->variables['page'], 'Manage Categories', array());
		echo " | " . $this->CreateLink($id, 'admin_manage_links', $this->cms->variables['page'], 'Manage Links', array());
		echo " | " . $this->CreateLink($id, 'admin_preferences', $this->cms->variables['page'], 'Preferences', array());

	} ## admin_menu

	function linkblog_module_add_category_form($id) {

		echo "<h4 class=admintitle>Add Category</h4>\n";
		echo $this->CreateFormStart($id, "admin_add_category_to_db");
		echo "Cagegory name: \n";
		echo $this->CreateInputText($id, "category_name", "", '25', '100');
		echo $this->CreateInputSubmit($id, 'submit', 'Submit');
		echo $this->CreateFormEnd();

	} ## linkblog_module_add_category

	function linkblog_module_add_category_to_db($id, $params) {

		$db = $this->cms->db;
		$category_id = $db->GenID(cms_db_prefix()."module_linkblog_type_seq");
		$category_name = $params["category_name"];
		$insert = "INSERT INTO ".cms_db_prefix()."module_linkblog_types (linkblog_type_id, linkblog_type) VALUES ($category_id, \"$category_name\")";
		$dbresult = $db->Execute($insert);
## TODO: need error checking
		echo "<h4 class=admintitle>Category $category_name successfully added.</h4>\n";

	} ## linkblog_module_add_category_to_db

	function linkblog_module_manage_categories($id, $params) {

		$db = $this->cms->db;
		echo "<h4 class=admintitle>Manage categories</h4><br />\n";
		echo "<table cellspacing=0 class='admintable'>\n";
		echo "<tr><th>Name</th><th>Category ID</th><th>Graphics file</th></tr>\n";

		$categories = $this->linkblog_module_get_categories($db);

		$records = count($categories);

		for ($i = 0; $i < $records; $i++) {
			if ($categories[$i]["linkblog_type"] != "") {
				echo "<tr><td>";
				echo $this->CreateLink($id, 'admin_edit_category', $this->cms->variables['page'], $categories[$i]['linkblog_type'], array('category_id'=>$categories[$i]['linkblog_type_id'], 'category_name'=>$categories[$i]['linkblog_type']));
				echo "</td>\n";
				echo "<td>".$categories[$i]['linkblog_type_id']."</td>\n";
				echo "<td>type".$categories[$i]['linkblog_type_id'].".gif</td></tr>\n";
			} ## if
		} ## for
		echo "</table>\n";

		echo "<p>Note: Graphics files are stored in the <i>images</i> directory of the Linkblog module.</p>\n";
	} ## linkblog_module_manage_categories

	function linkblog_module_edit_category($id, $params) {

		$category_id = "";
		if (isset($params["category_id"])) {
			$category_id = $params["category_id"];
		} ## if
		$category_name = "";
		if (isset($params["category_name"])) {
			$category_name = $params["category_name"];
		} ## if

		echo "<p>\n";
		echo $this->CreateFormStart($id, "admin_update_category", $this->cms->variables["page"]);
		echo "Category name: $category_name<br />\n";
		echo $this->CreateInputText($id, "category_name", "$category_name", '20', '40');
		echo $this->CreateInputHidden($id, 'category_id', $category_id);
		echo $this->CreateInputSubmit($id, 'submit', 'Update');
		echo $this->CreateFormEnd();
		echo "</p>\n";
	} ## linkblog_module_edit_category

	function linkblog_module_update_category($id, $params) {

		$db = $this->cms->db;
		$category_name = $params["category_name"];
		$category_id = $params["category_id"];
##     echo "category_name: ($category_name)<br />\n";
		$update = "UPDATE ".cms_db_prefix()."module_linkblog_types SET linkblog_type='$category_name' WHERE linkblog_type_id=$category_id";
		$dbresult = $db->Execute($update);
## TODO: add error checking
		echo "<h4 class=admintitle>Category $category_name successfully updated.</h4>\n";

	} ## linkblog_module_update_category

	function linkblog_module_manage_links($id, $params) {

		$keywords = '';
		if (isset($params["keywords"])) {
			$keywords = $params["keywords"];
		} elseif (isset($params["keywords"])) {
			$keywords = $params["keywords"];
		} ## if

		echo "<h4 class=admintitle>Manage Links</h4><br />\n";
		echo "Note: Only the 20 most recent links are shown. Use the search box to find others<br />\n";
		echo $this->CreateFormStart($id, "admin_manage_links");
		echo $this->CreateInputText($id, "keywords", "$keywords", '25', '50');
		echo $this->CreateInputSubmit($id, 'submit', 'Search');
		echo $this->CreateFormEnd();

		echo "<table class=admintable cellspacing=5 border=0>\n";
		echo "<tr><th>Name</th><th>Category</th><th>Author</th><th>Post Date</th><th>Status</th></tr>\n";

		$db = $this->cms->db;
		$query = "SELECT a.linkblog_id, a.linkblog_title, a.linkblog_url, a.linkblog_author, a.create_date, a.status, b.linkblog_type from ".cms_db_prefix()."module_linkblog a, ".cms_db_prefix()."module_linkblog_types b where a.linkblog_type = b.linkblog_type_id";
		if ($keywords != "") {
			$query .= " and (a.linkblog_title like \"%$keywords%\" or a.linkblog_url like \"$keywords\")";
		} ## if
		$query .= " ORDER BY  a.create_date DESC LIMIT 20";
		$dbresult = $db->Execute($query);
		if ($dbresult && $dbresult->RowCount()) {
			while ($row = $dbresult->FetchRow()) {
				echo "<tr>\n";
				echo "<td>".$row["linkblog_title"]." <a href=\"".$row["linkblog_url"]."\" target=_blank>Link</a></td>\n";
				echo "<td>".$row["linkblog_type"]."</td>\n";
				echo "<td>".$row["linkblog_author"]."</td>\n";
				echo "<td>".date($row["create_date"], "Y-m-d")."</td>\n";
				if ($row["status"] == "1") {
					echo "<td>";
					echo $this->CreateLink($id, 'admin_deactivate', $this->cms->variables['page'], 'Deactivate', array('linkblog_id'=>$row["linkblog_id"], 'keywords'=>$keywords));
					echo "</td>\n";
				} elseif ($row["status"] == "0") {
					echo "<td>";
					echo $this->CreateLink($id, 'admin_activate', $this->cms->variables['page'], 'Activate', array('linkblog_id'=>$row["linkblog_id"], 'linkblog_title'=>$row["linkblog_title"], 'keywords'=>$keywords));
					echo "</td>\n";
				} ## if
				if ($row["status"] == "2") {
					echo "<td>";
					echo $this->CreateLink($id, 'admin_activate', $this->cms->variables['page'], 'Approve', array('linkblog_id'=>$row["linkblog_id"], 'linkblog_title'=>$row["linkblog_title"], 'keywords'=>$keywords));
					echo "</td>\n";
				} ## if
				echo "</tr>\n";
			} ## while
		} ## if
		echo "</table>\n";

	} ## linkblog_module_manage_links

	function linkblog_module_activate_link($id, $params) {

		$db = $this->cms->db;
		$linkblog_id = $params["linkblog_id"];
		$update = "UPDATE ".cms_db_prefix()."module_linkblog SET status='1' WHERE linkblog_id=$linkblog_id";
		$db->Execute($update);
		
		if ($this->GetPreference("ping", "0") == "1") {
			require_once dirname(dirname(dirname(__FILE__))).'/lib/xmlrpc.functions.php';
			if (XMLRPC_request('rpc.pingomatic.com', '/RPC2', 'weblogUpdates.ping', array($params["linkblog_title"], $this->GetPreference("linkblog_url", "")))) {}
			if (XMLRPC_request('rpc.weblogs.com', '/RPC2', 'weblogUpdates.ping', array($params["linkblog_title"], $this->GetPreference("linkblog_url", "")))) {}
		} ## if

	} ## linkblog_module_activate_link

	function linkblog_module_deactivate_link($id, $params) {

		$db = $this->cms->db;
		$linkblog_id = $params["linkblog_id"];
		$update = "UPDATE ".cms_db_prefix()."module_linkblog SET status='0' WHERE linkblog_id=$linkblog_id";
		$db->Execute($update);

	} ## linkblog_module_deactivate_link

	function linkblog_module_view_preferences($id, $params) {

		echo "<br />\n";
		echo $this->CreateFormStart($id, "admin_set_preferences");
		?>
			<table>
			<tr>
			<td>Send email notification:</td><td><input type="checkbox" value="1" name="<?php echo $id;?>email_notify" <?php if ($this->GetPreference("email_notify", "0") == "1") { echo "CHECKED"; }?>/></td>
			</tr>
			<tr>
			<td>Send email to:</td>
			<td><?php echo $this->CreateInputText($id, "email_to", $this->GetPreference("email_to", ""), '30', '80');?></td>
			</tr>
			<tr>
			<td>Send email from:</td>
			<td><?php echo $this->CreateInputText($id, "email_from", $this->GetPreference("email_from", ""), '30', '80');?></td>
			
			</tr>
			<tr>
			<td>Linkblog URL:</td>
			<td><?php echo $this->CreateInputText($id, "linkblog_url", $this->GetPreference("linkblog_url", ""), '30', '80');?></td>
			</tr>
			<tr>
			<td>Linkblog RSS Title:</td>
			<td><?php echo $this->CreateInputText($id, "rss_title", $this->GetPreference("rss_title", ""), '30', '80');?></td>
			</tr>
			<tr>
			<td>Linkblog RSS limit:</td>
			<td><?php echo $this->CreateInputText($id, "rss_limit", $this->GetPreference("rss_limit", ""), '30', '80');?></td>
			</tr>
			<tr>
			<td>Ping pingomatic.com and weblogs.com for each approved link:</td>
			<td><input type="checkbox" value="1" name="<?php echo $id;?>ping" <?php if ($this->GetPreference("ping", "0") == "1") {echo "CHECKED";}?></td>
			<tr>
			<td></td>
			<td><?php echo $this->CreateInputSubmit($id, 'submit', 'Save Changes');?></td>
			</tr>
			</table>
			<?php

			echo $this->CreateFormEnd();
	} ## linkblog_module_view_preferences

	function linkblog_module_set_preferences($id, $params) {

		## retrieve any posted settings
		if (isset($params["email_notify"])) { $email_notify = "1"; } else { $email_notify = "0"; }
		if (isset($params["email_to"])) { $email_to = $params["email_to"]; } else { $email_to = ""; }
		if (isset($params["email_from"])) { $email_from = $params["email_from"]; } else { $email_from = ""; }
		if (isset($params["linkblog_url"])) { $linkblog_url = $params["linkblog_url"]; } else { $linkblog_url = ""; }
		if (isset($params["rss_title"])) { $rss_title = $params["rss_title"]; } else { $rss_title = ""; }
		if (isset($params["rss_limit"])) { $rss_limit = $params["rss_limit"]; } else { $rss_limit = ""; }
		if (isset($params["ping"])) { $ping = "1"; } else { $ping = "0"; }

		$this->SetPreference("email_notify", $email_notify);
		$this->SetPreference("email_to", $email_to);
		$this->SetPreference("email_from", $email_from);
		$this->SetPreference("linkblog_url", $linkblog_url);
		$this->SetPreference("rss_title", $rss_title);
		$this->SetPreference("rss_limit", $rss_limit);
		$this->SetPreference("ping", $ping);

	} ## linkblog_module_set_preferences

	function linkblog_module_get_categories($db, $order_by='linkblog_type')
	{
		$categories_table_name = cms_db_prefix().'module_linkblog_types';
		$sql = "SELECT * FROM $categories_table_name";
		if($order_by != '')
		{
			$sql .= " ORDER BY $order_by";
		}

		$result = array();
		$rs = $db->Execute($sql);
		if($rs && $rs->RowCount() > 0)
			$result = $rs->GetArray();
		return $result;
	} ## linkblog_module_get_categories



} ## class
