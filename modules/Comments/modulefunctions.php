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

function comments_module_install($cms) {
	//This function should install the database functions and do other basic init stuff for first time use.
	$db = $cms->db;
	$dict = NewDataDictionary($db);
	$flds = "
		comment_id I KEY,
		comment_data X,
		comment_date T,
		comment_author C(255),
		page_id C(255),
		create_date T,
		modified_date T
	";
	$taboptarray = array('mysql' => 'TYPE=MyISAM');
	$sqlarray = $dict->CreateTableSQL(cms_db_prefix()."module_comments", $flds, $taboptarray);
	$dict->ExecuteSQLArray($sqlarray);

	$db->CreateSequence(cms_db_prefix()."module_comments_seq");

	cms_mapi_create_permission($cms, 'Comments Admin', 'Comments Admin');
}

function comments_module_uninstall($cms) {
	//This function should uninstall database tables and generally cleanup.
	$db = $cms->db;
	$dict = NewDataDictionary($db);
	$sqlarray = $dict->DropTableSQL(cms_db_prefix()."module_comments");
	$dict->ExecuteSQLArray($sqlarray);

	$db->DropSequence(cms_db_prefix()."module_comments_seq");

	cms_mapi_remove_permission($cms, 'Comments Admin');
}

function comments_module_execute($cms, $id, $params) {
	//This is the entryway into the module.  All requests from CMS will come through here.
	$db = $cms->db;
	$query = "SELECT comment_id, comment_author, comment_data, comment_date FROM ".cms_db_prefix()."module_comments WHERE page_id = ".$cms->page." ORDER BY comment_date desc";
	$dbresult = $db->Execute($query);
	echo "<div class=\"modulecomments\">Page Comments";
	if ($dbresult && $dbresult->RowCount()) {
		while ($row = $dbresult->FetchRow()) {
			echo "<div class=\"modulecommentsentry\">";
			echo "<div class=\"modulecommentsentryheader\">Posted at ".date("F j, Y, g:i a", $db->UnixTimeStamp($row['comment_date']))." by ".$row['comment_author']."</div>";
			echo "<div class=\"modulecommentsentrybody\">".$row["comment_data"]."</div>";
			echo "</div>";
		}
	} else {
		echo "No comments posted";
	}
	echo cms_mapi_create_user_link("Comments", $id, $cms->page, array('action'=>'addcomment'), "Add a comment");
}

function comments_module_executeuser($cms, $id, $return_id, $params) {

	if (isset($params[$id."cancelcomment"])) {
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
	$content = "";
	if (isset($params[$id."content"])) {
		$content = $params[$id."content"];
	}

	if ($action == "contadd") {

		$validinfo = true;

		if ($author == "") {
			$validinfo = false;
			$errormsg .= "<li>Enter an author</li>";
		}
	
		if ($content == "") {
			$validinfo = false;
			$errormsg .= "<li>Enter a comment...  isn't that the point?</li>";
		} else if (strlen($content) < 32) {
			$validinfo = false;
			$errormsg .= "<li>Please, no test comments.  It's works.</li>";
		}

		if ($validinfo) {
			$db = $cms->db;
			$new_id = $db->GenID(cms_db_prefix()."module_comments_seq");
			$query = "INSERT INTO ".cms_db_prefix()."module_comments (comment_id, page_id, comment_author, comment_data, comment_date, create_date) VALUES ($new_id, $return_id, ".$db->qstr($author).", ".$db->qstr($content).",".$db->DBTimeStamp(time()).",".$db->DBTimeStamp(time()).")";
			$dbresult = $db->Execute($query);
			cms_mapi_redirect_user_by_pageid($return_id);
			return;
		}
	}

	if (strlen($errormsg) > 0) {
		echo "<p>Error:</p><ul>".$errormsg."</ul>";
	}
	
	echo cms_mapi_create_user_form_start("Comments", $id, $return_id);

	?>
	<h3>Add Comment</h3>

	<table>
		<tr>
			<td>Your name:</td>
			<td><input type="text" name="<?=$id?>author" value="<?=$author?>" /></td>
		</tr>
		<tr>
			<td>Comment:</td>
			<td><textarea rows="6" cols="40" name="<?=$id?>content"><?=$content?></textarea></td>
		</tr>
		<tr>
			<td>&nbsp;<input type="hidden" name="<?=$id?>action" value="contadd" /></td>
			<td><input type="submit" name="<?=$id?>submitcomment" value="Submit" /><input type="submit" name="<?=$id?>cancelcomment" value="Cancel" /></td>
		</tr>
	</table>
	<?

	echo cms_mapi_create_user_form_end();

}

# vim:ts=4 sw=4 noet
?>
