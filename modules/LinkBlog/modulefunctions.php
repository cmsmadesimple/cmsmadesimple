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

}

function linkblog_module_uninstall($cms) {
	//This function should uninstall database tables and generally cleanup.
	$db = $cms->db;
	$dict = NewDataDictionary($db);
	$sqlarray = $dict->DropTableSQL(cms_db_prefix()."module_linkblog");
	$dict->ExecuteSQLArray($sqlarray);

	$sqlarray = $dict->DropTableSQL(cms_db_prefix()."module_linkblog_types");
	$dict->ExecuteSQLArray($sqlarray);

	$db->DropSequence(cms_db_prefix()."module_linkblog_seq");
	$db->DropSequence(cms_db_prefix()."module_linkblog_type_seq");

}

function linkblog_module_execute($cms, $id, $params) {
	//This is the entryway into the module.  All requests from CMS will come through here.
	$db = $cms->db;
	$query = "SELECT linkblog_id, linkblog_title, linkblog_url, linkblog_author, linkblog_type, create_date FROM ".cms_db_prefix()."module_linkblog ORDER BY create_date desc";
	$dbresult = $db->Execute($query);
	echo "<p class=\"smalltitle\">Posted sites</p>\n";
	echo "<div class=\"modulelinkblog\">";
	if ($dbresult && $dbresult->RowCount()) {
		while ($row = $dbresult->FetchRow()) {
			echo "<div class=\"modulelinkblogentry\">\n";
			echo "<div class=\"modulelinkblogentryheader\">Posted at ".date("F j, Y, g:i a", $db->UnixTimeStamp($row['create_date']))." by ".$row['linkblog_author']."</div>\n";
			echo "<div class=\"modulelinkblogentrybody\"><a href=\"".$row["linkblog_url"]."\">".$row["linkblog_title"]."</a></div>\n";
			echo "</div>\n";
		}
	} else {
		echo " - No linkblog posted - ";
	}
	echo "</div>\n";
	echo cms_mapi_create_user_link("LinkBlog", $id, $cms->variables["page"], array('action'=>'addlink'), "Add a link");
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
		$type = $params[$id."title"];
	}

	$url = "";
	if (isset($params[$id."url"])) {
		$url = $params[$id."url"];
	}

	if ($action == "linkadd") {

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
			$db = $cms->db;
			$new_id = $db->GenID(cms_db_prefix()."module_linkblog_seq");
			$query = "INSERT INTO ".cms_db_prefix()."module_linkblog (linkblog_id, linkblog_author, linkblog_title, linkblog_type, linkblog_url, create_date, modified_date) VALUES ($new_id, ".$db->qstr($author).", ".$db->qstr($title).",".$db->qstr($type).",".$db->qstr($url).",".$db->DBTimeStamp(time()).",".$db->DBTimeStamp(time()).")";
			$dbresult = $db->Execute($query);
			cms_mapi_redirect_user_by_pageid($return_id);
			return;
		}
	}

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
	<h3>Add Link</h3>

	<table>
		<tr>
			<td>Your name:</td>
			<td><input type="text" name="<?=$id?>author" value="<?=$author?>" /></td>
		</tr>
		<tr>
			<td>Title:</td>
			<td><input type="text" name="<?=$id?>title" value="<?=$title?>" /></td>
		</tr>
		<tr>
			<td>URL:</td>
			<td><input type="text" name="<?=$id?>url" value="<?=$url?>" /></td>
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

# vim:ts=4 sw=4 noet
?>
