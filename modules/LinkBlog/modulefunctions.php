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

function linkblog_module_install($cms) {
	//This function should install the database functions and do other basic init stuff for first time use.
	$db = $cms->db;
	$dict = NewDataDictionary($db);

	## Create the actual linkblog table
	$flds = "
		linkblog_id I KEY,
		linkblog_title C(255),
		linkblog_url C(255),
		linkblog_author C(255),
		linkblog_type I,
		create_date T,
		modified_date T
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
}

function linkblog_module_uninstall($cms) {
	//This function should uninstall database tables and generally cleanup.
	$db = $cms->db;
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

}

function linkblog_module_execute($cms, $id, $params) {
	//This is the entryway into the module.  All requests from CMS will come through here.
	$db = $cms->db;
	$query = "SELECT a.linkblog_id, a.linkblog_title, a.linkblog_url, a.linkblog_author, a.linkblog_type, a.create_date, count(b.linkblog_id) as total FROM ".cms_db_prefix()."module_linkblog a LEFT OUTER JOIN ".cms_db_prefix()."module_linkblog_comments b ON a.linkblog_id=b.linkblog_id GROUP BY a.linkblog_id ORDER BY create_date desc";
	$dbresult = $db->Execute($query);
	echo "<p class=\"smalltitle\">Posted sites</p>\n";
	echo "<div class=\"modulelinkblog\">\n";
	if ($dbresult && $dbresult->RowCount()) {
		while ($row = $dbresult->FetchRow()) {
			echo "<div class=\"modulelinkblogentry\">\n";
			echo "<div class=\"modulelinkblogentryheader\">\nPosted at ".date("F j, Y, g:i a", $db->UnixTimeStamp($row['create_date']))." by ".$row['linkblog_author']."\n</div>\n";
			echo "<div class=\"modulelinkblogentrybody\">\n<a href=\"".$row["linkblog_url"]."\"><img src=modules/LinkBlog/images/type".$row["linkblog_type"].".png border=\"0\"> ".$row["linkblog_title"]."</a>\n";
			echo "</div>\n";

			echo "<div class=\"modulelinkblogentrycommentlink\">\n";
			echo cms_mapi_create_user_link("LinkBlog", $id, $cms->variables["page"], array('action'=>'viewcomments', 'linkblog_id'=>$row["linkblog_id"]), "Comments (".$row["total"].")");
			echo "\n</div>\n";
			echo "</div>\n";
		}
	} else {
		echo " - No linkblog posted - ";
	}
	echo "</div>\n";
	echo "<div class=\"modulelinkbloglink\">\n";
	echo cms_mapi_create_user_link("LinkBlog", $id, $cms->variables["page"], array('action'=>'addlink'), "Add a link");
	echo "</div>\n";
}

function linkblog_module_executeuser($cms, $id, $return_id, $params) {

	if (isset($params[$id."cancellink"])) {
		cms_mapi_redirect_user_by_pageid($return_id);
		return;
	}

	$errormsg = "";

	$action = "";
	if (isset($params[$id."action"])) {
		$action = $params[$id."action"];
	}
	$author = "";
	if (isset($params[$id."author"])) {
		$author = $params[$id."author"];
	}
	$title = "";
	if (isset($params[$id."title"])) {
		$title = $params[$id."title"];
	}

	$type = "";
	if (isset($params[$id."type"])) {
		$type = $params[$id."type"];
	}

	$url = "";
	if (isset($params[$id."url"])) {
		$url = $params[$id."url"];
	}

	$comment = "";
	if (isset($params[$id."comment"])) {
		$comment = $params[$id."comment"];
	}

	
	## run this when a new link is submitted
	if ($action == "linkadd") {
		echo "here we are<br/>\n";

		$validinfo = true;

		if ($author == "") {
			$validinfo = false;
			$errormsg .= "<li>Enter an author</li>";
		}
	
		if ($title == "") {
			$validinfo = false;
			$errormsg .= "<li>Enter a title so people know what you are talking about.</li>";
		}

		if ($type == "") {
			$validinfo = false;
			$errormsg .= "<li>Enter a type</li>";
		}

		if ($url == "") {
			$validinfo = false;
			$errormsg .= "<li>Enter a link, where are we supposed to go?</li>";
		}
		
		if ($validinfo) {
			echo "validinfo: true<br/>\n";
		} else {
			echo "validinfo: false<br/>\n";
		}

		if ($validinfo) {
			$db = $cms->db;
			$new_id = $db->GenID(cms_db_prefix()."module_linkblog_seq");
			$query = "INSERT INTO ".cms_db_prefix()."module_linkblog (linkblog_id, linkblog_author, linkblog_title, linkblog_type, linkblog_url, create_date, modified_date) VALUES ($new_id, ".$db->qstr($author).", ".$db->qstr($title).",".$type.",".$db->qstr($url).",".$db->DBTimeStamp(time()).",".$db->DBTimeStamp(time()).")";
			echo "query: $query<br/>\n";
			$dbresult = $db->Execute($query);
			cms_mapi_redirect_user_by_pageid($return_id);
			return;
		} else {
			echo $errormsg;
		}
	}

	## Display this when the Add Link link is clicked
	if ($action == "addlink") {

		if (strlen($errormsg) > 0) {
			echo "<p>Error:</p><ul>".$errormsg."</ul>";
		}
		
		$types = "<select name=\"".$id."type\">\n";
		$db = $cms->db;
		$query = "SELECT linkblog_type_id, linkblog_type from ".cms_db_prefix()."module_linkblog_types ORDER BY linkblog_type";
		$dbresult = $db->Execute($query);
		if ($dbresult && $dbresult->RowCount()) {
			while ($row = $dbresult->FetchRow()) {
				$types .= "<option value=\"".$row['linkblog_type_id']."\">".$row['linkblog_type']."</option>\n";
			}
		}
		$types .= "</select>\n";

		echo cms_mapi_create_user_form_start("LinkBlog", $id, $return_id);

		?>
		<p class="smalltitle">Add Link - <? echo cms_mapi_create_content_link_by_page_id($return_id, "Back to LinkBlog"); ?></p>

		<table>
			<tr>
				<td>Your name:</td>
				<td><input type="text" name="<?=$id?>author" value="<?=$author?>" size=20 maxlength=50 /></td>
			</tr>
			<tr>
				<td>Title:</td>
				<td><input type="text" name="<?=$id?>title" value="<?=$title?>" size=100 maxlength=250/></td>
			</tr>
			<tr>
				<td>URL:</td>
				<td><input type="text" name="<?=$id?>url" value="<?=$url?>" size=100 maxlength=250 /></td>
			</tr>
			<tr>
				<td>Type:</td>
				<td><?=$types?></td>
			</tr>
			<tr>
				<td>&nbsp;<input type="hidden" name="<?=$id?>action" value="linkadd" /></td>
				<td><input type="submit" name="<?=$id?>submitlink" value="Submit" /><input type="submit" name="<?=$id?>cancellink" value="Cancel" /></td>
			</tr>
		</table>
		<?

		echo cms_mapi_create_user_form_end();
	}

	## this displays any existing comments for the chosen link and also displays a form to submit a comment
	if ($action == "viewcomments" || $action == "postcomment") {

		$db = $cms->db;
		$query = "SELECT linkblog_id, linkblog_title, linkblog_url, linkblog_author, linkblog_type, create_date from ".cms_db_prefix()."module_linkblog WHERE linkblog_id=".$params[$id."linkblog_id"];
		$dbresult = $db->Execute($query);
		echo "<p class=\"smalltitle\">Posted site - ";
		echo cms_mapi_create_content_link_by_page_id($return_id, "Back to LinkBlog");
		echo "</p>\n";
		echo "<div class=\"modulelinkblog\">\n";

		if ($dbresult && $dbresult->RowCount()) {
			while ($row = $dbresult->FetchRow()) {
				
				echo "<div class=\"modulelinkblogentry\">\n";
				echo "<div class=\"modulelinkblogentryheader\">\nPosted at ".date("F j, Y, g:i a", $db->UnixTimeStamp($row['create_date']))." by ".$row['linkblog_author']."\n</div>\n";
				echo "<div class=\"modulelinkblogentrybody\">\n<a href=\"".$row["linkblog_url"]."\"><img src=modules/LinkBlog/images/type".$row["linkblog_type"].".png border=\"0\"> ".$row["linkblog_title"]."</a>\n";
				echo "</div>\n";

				echo "</div>\n";
			}
		}
		echo "</div>\n";

		if ($action == "postcomment") {

			$validinfo = true;
			$errormsg = "<ul>\n";

			if ($comment == "") {
				$validinfo = false;
				$errormsg .= "<li>You must be saying something to post a comment</li>\n";
			}

			$errormsg .= "</ul>\n";

			if ($validinfo) {
				$new_id = $db->GenID(cms_db_prefix()."module_linkblog_comment_seq");
				$query = "INSERT INTO ".cms_db_prefix()."module_linkblog_comments (comment_id, linkblog_id, author, comment, create_date) VALUES ($new_id,".$params[$id."linkblog_id"].",".$db->qstr($params[$id."author"]).",".$db->qstr($params[$id."comment"]).", now())";
				$dbresult = $db->Execute($query);
			} else {
				echo $errormsg;
			}
		}

		$query = "SELECT author, comment, create_date FROM ".cms_db_prefix()."module_linkblog_comments WHERE linkblog_id=".$params[$id."linkblog_id"]." ORDER BY create_date desc";
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

		echo cms_mapi_create_user_form_start("LinkBlog", $id, $return_id);
		?>
		<h3>Add a comment</h3>

		<table>
			<tr>
				<td>Your name:</td>
				<td><input type="text" name="<?=$id?>author" value="<?=$author?>" size=20 maxlength=50 /></td>
			</tr>
			<tr>
				<td>Comment:</td>
				<td><input type="text" name="<?=$id?>comment" value="" size=100 maxlength=250/></td>
			</tr>
			<tr>
				<td>&nbsp;<input type="hidden" name="<?=$id?>action" value="postcomment" />
				<input type="hidden" name="<?=$id?>linkblog_id" value="<?=$params[$id."linkblog_id"]?>" /></td>
				<td><input type="submit" name="<?=$id?>submitlink" value="Submit" /><input type="submit" name="<?=$id?>cancellink" value="Cancel" /></td>
			</tr>
		</table>
		<?

		echo cms_mapi_create_user_form_end();
	}

}

# vim:ts=4 sw=4 noet
?>
