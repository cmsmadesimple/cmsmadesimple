<?php
#Linkblog. A module for CMS - CMS Made Simple
#Copyright (c) 2004 by Greg Froese <heavy_g@users.sf.net>
#
#CMS- CMS Made Simple is Copyright (c) Ted Kulp (wishy@users.sf.net)
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

// Display a list of links from either today or in the past depending on the parameters passed
function linkblog_module_showLinks($cms, $id, $params, $return_id) {


    $allow_search = '';
    if(isset($params[$id.'allow_search']))
    {
        $allow_search = $params[$id.'allow_search'];
    }
    else if(isset($params['allow_search']))
    {
        $allow_search = $params['allow_search'];
    }

    if(isset($params[$id.'limit']))
    {
        $limit = $params[$id.'limit'];
    }
    else if(isset($params['limit']))
    {
        $limit = $params['limit'];
    } else {
		$limit = "20";
	}

    if(isset($params[$id.'category']))
    {
        $category = $params[$id.'category'];
    }
    else if(isset($params['category']))
    {
        $category = $params['category'];
    }

	if ($params[$id.'type'] == "rss") {

		if (isset($cms->config["linkblog_rss_limit"])) {
			$limit = $cms->config["linkblog_rss_limit"];
		} ## if 
		header('Content-type: text/xml');
		echo "<?xml version='1.0'?>\n";
		echo "<rss version='2.0'>\n";
		echo "	<channel>\n";
		echo "	<title>".$cms->config["linkblog_rss_title"]."</title>\n";
		echo "	<link>";
		echo cms_htmlentities($cms->config["linkblog_url"], ENT_NOQUOTES, get_encoding($encoding));
		echo "</link>\n";
		echo "	<description>Current linkblog entries</description>\n";
	} else if ($allow_search != '') {
		echo "<div class=\"modulelinkblogsearchform\">\n";
		echo cms_mapi_create_user_form_start("LinkBlog", $id, $return_id);
		?>
		<table>
			<tr>
				<td><input name="<?php echo $id?>search_text" type="text" value="" /></td>
				<td><input name="<?php echo $id?>submit_search" type="submit" value="Search" /><input type="hidden" name="<?php echo $id?>action" value="search_url" /></td>
			</tr>
		</table>
		<?php
		echo cms_mapi_create_user_form_end();
		echo "</div>\n";
	}

    $curr_date = date("Y-m-d");
    $old_date = "";
    if (isset($params[$id."old_date"])) {
		if ($params[$id."old_date"] == "curr_date") {
			$old_date = $curr_date;
		} else {
			$old_date = $params[$id."old_date"];
		} ## if
	} ## if

    $db = $cms->db;
    $query = "SELECT a.linkblog_id, a.linkblog_title, a.linkblog_url, a.linkblog_author, a.linkblog_type as type_id, a.create_date, count(b.linkblog_id) as total, a.linkblog_credit";
	$query .= ", c.linkblog_type as category";
	$query .= " FROM";
	$query .= " ".cms_db_prefix()."module_linkblog_types c,";
	$query .= " ".cms_db_prefix()."module_linkblog a LEFT OUTER JOIN ".cms_db_prefix()."module_linkblog_comments b ON a.linkblog_id=b.linkblog_id WHERE";
	$query .= " a.linkblog_type=c.linkblog_type_id";
	if ($category != "") { $query .= " and c.linkblog_type like \"%$category%\""; }
    if ($old_date != "") {
        $query .= " and a.create_date like '$old_date%'";
    } ## if
	$query .= " and a.status = '1'";
    $query .= " GROUP BY a.linkblog_id ORDER BY create_date DESC";
	$query .= " LIMIT $limit";

    $dbresult = $db->Execute($query);

	if ($params[$id.'type'] != "rss") {
		echo "<div class=\"modulelinkblogtitle\">Posted sites</div>";
		if (isset($params["make_rss_button"])) {
			echo "<div class=\"modulelinkblogrss\">\n";
			echo " ".cms_mapi_create_user_link('LinkBlog', $id, $cms->variables["page"], array('action'=>'viewoldlinks', 'type'=>'rss', 'showtemplate'=>'false'), "<img border=\"0\" src=\"images/cms/xml_rss.gif\" alt=\"RSS Linkblog Feed\" />");
			echo "</div>\n";
		} ## if
		echo "<div class=\"modulelinkblog\">\n";
	} ## if

    if ($dbresult && $dbresult->RowCount()) {
##         $last_date = "";
        while ($row = $dbresult->FetchRow()) {

			if ($params[$id.'type'] == "rss") {
				echo "	<item>\n";
				echo "		<title>".$row["linkblog_title"]."</title>\n";
				echo "		<link>";
				echo cms_htmlentities($row["linkblog_url"], ENT_NOQUOTES, get_encoding($encoding));
				echo "</link>\n";
				## this is just redundant echo "		<description>".$row["linkblog_title"]."</description>\n";
				## needs to be an email address echo "		<author>".$row["linkblog_author"]."</author>\n";
				echo "		<pubDate>".date("D, j M Y H:i:s T", $db->UnixTimeStamp($row['create_date']))."</pubDate>\n";
				echo "	</item>\n";
			} else {
				echo "<div class=\"modulelinkblogentry\">\n";
				$this_date = substr($row["create_date"],0,10);
				if ($last_date != $this_date) {
					echo "<div class=\"modulelinkblogentrydate\">\n";
					echo date("F j, Y", $db->UnixTimeStamp($row['create_date']))."<br />\n";
					echo "</div>\n";
				} ## if

				echo "<div class=\"modulelinkblogentrytime\">\n";
				echo "Posted at ".date("g:i a", $db->UnixTimeStamp($row['create_date']))." by ".$row['linkblog_author']."\n";
				echo "</div>\n";

                echo "<div class=\"modulelinkblogentrybody\">\n";
                echo cms_mapi_create_user_link("LinkBlog", $id, $cms->variables["page"], array('action'=>'viewoldlinks', 'category'=>$row["category"]), "<img src=\"modules/LinkBlog/images/type".$row["type_id"].".gif\" border=\"0\" alt=\"\" />");
				echo " (<a href=\"".$row["linkblog_url"]."\">Link</a>)\n";
                echo " ".$row["linkblog_title"]."\n";
				echo "</div>\n";

                echo "<div class=\"modulelinkbloglinks\">\n";
				echo " | \n";
				echo cms_mapi_create_user_link("LinkBlog", $id, $cms->variables["page"], array('action'=>'view_comments', 'linkblog_id'=>$row["linkblog_id"]), "Comments (".$row["total"].")");
				echo " | \n";
				echo cms_mapi_create_user_link("LinkBlog", $id, $cms->variables["page"], array('action'=>'view_comments', 'linkblog_id'=>$row["linkblog_id"]), "Permalink");
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

		if ($params[$id.'type'] == "rss") {
			echo "	</channel>\n";
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
			echo cms_mapi_create_user_link("LinkBlog", $id, $cms->variables["page"], array('action'=>'viewoldlinks', 'old_date'=>$curr_date, 'allow_search'=>'true', 'category'=>$category), "Today");
            echo "</li>\n";
            while ($row = $dbresult->FetchRow()) {
                if ($last_date != substr($row["create_date"],0,10) && substr($row["create_date"],0,10) != $curr_date) {
                    echo "<li>";
                    echo cms_mapi_create_user_link("LinkBlog", $id, $cms->variables["page"], array('action'=>'viewoldlinks', 'old_date'=>substr($row["create_date"],0,10), 'allow_search'=>'true', 'category'=>$category), substr($row["create_date"],0,10));
                    echo "</li>\n";
                }
                $last_date = substr($row["create_date"],0,10);
            }
            echo "</ul>\n";
        }
    } else {
		if ($params[$id.'type'] == "rss") {
            echo "	</channel>\n";
            echo "</rss>\n";
            return;
		} else {					
	        echo " - No linkblog posted - ";
		} ## if
    }
    echo "</div>\n";
    echo "<div class=\"modulelinkbloglink\">\n";
    echo cms_mapi_create_user_link("LinkBlog", $id, $cms->variables["page"], array('action'=>'create_add_link_form'), "Add a link");
    echo "</div>\n";
}

## This function controls allow user functions:
##	- posting comments
##	- viewing comments
##	- posting a link
##  - searching for a link
function linkblog_module_user_action($cms, $id, $return_id, $params) {

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

	$credit = "";
	if (isset($params[$id."credit"])) {
		$credit = $params[$id."credit"];
	}

    $comment = "";
    if (isset($params[$id."comment"])) {
        $comment = $params[$id."comment"];
    }

    switch ($action) {
    ## run this when a new link is submitted
    case "add_new_link":

        $validinfo = true;

        if ($author == "") {
            $validinfo = false;
            $errormsg .= "Enter an author<br />";
        }

        if ($title == "") {
            $validinfo = false;
            $errormsg .= "Enter a title so people know what you are talking about.<br />";
        }

        if ($type == "") {
            $validinfo = false;
            $errormsg .= "Enter a type<br />";
        }

        if ($url == "") {
            $validinfo = false;
            $errormsg .= "Enter a link, where are we supposed to go?<br />";
        }

        if ($validinfo) {
            $db = $cms->db;
            $new_id = $db->GenID(cms_db_prefix()."module_linkblog_seq");
            $query = "INSERT INTO ".cms_db_prefix()."module_linkblog (linkblog_id, linkblog_author, linkblog_title, linkblog_type, linkblog_url, create_date, modified_date, status, linkblog_credit) VALUES ($new_id, ".$db->qstr($author).", ".$db->qstr($title).",".$type.",".htmlentities($db->qstr($url)).",'".$db->DBTimeStamp(time())."','".$db->DBTimeStamp(time())."', '1', ".htmlentities($db->qstr($credit)).")";
            $dbresult = $db->Execute($query);
            cms_mapi_redirect_user_by_pageid($return_id);
            return;
        } ## if

    ## Display this when the Add Link link is clicked
    case "create_add_link_form":

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
        <p class="smalltitle">Add Link - <?php echo cms_mapi_create_content_link_by_page_id($return_id, "Back to LinkBlog"); ?></p>

        <table>
            <tr>
                <td>* Your name:</td>
                <td><input type="text" name="<?php echo $id?>author" value="<?php echo $author?>" size="20" maxlength="50" /></td>
            </tr>
            <tr>
                <td>* Title:</td>
                <td><input type="text" name="<?php echo $id?>title" value="<?php echo $title?>" size="100" maxlength="250" /></td>
            </tr>
            <tr>
                <td>* URL:</td>
                <td><input type="text" name="<?php echo $id?>url" value="<?php echo $url?>" size="100" maxlength="250" /></td>
            </tr>
            <tr>
                <td>* Category:</td>
                <td><?php echo $types?></td>
            </tr>
			<tr>
				<td>Source credit:</td>
				<td><input type="text" name="<?php echo $id?>credit" value="<?php echo $credit?>" size="100" maxlength="250" /></td>
			</tr>
            <tr>
                <td>&nbsp;<input type="hidden" name="<?php echo $id?>action" value="add_new_link" /></td>
                <td><input type="submit" name="<?php echo $id?>submitlink" value="Submit" /><input type="submit" name="<?php echo $id?>cancellink" value="Cancel" /></td>
            </tr>
        </table>
        <?php

        echo cms_mapi_create_user_form_end();
        break;


    ## this displays any existing comments for the chosen link and also displays a form to submit a comment
    case "view_comments":
	case "post_comment":

        $db = $cms->db;
        $query = "SELECT linkblog_id, linkblog_title, linkblog_url, linkblog_author, linkblog_type as type_id, create_date, status, linkblog_credit from ".cms_db_prefix()."module_linkblog WHERE linkblog_id=".$params[$id."linkblog_id"];
        $dbresult = $db->Execute($query);
        echo "<p class=\"modulelinkblogtitle\">Posted site - ";
        echo cms_mapi_create_content_link_by_page_id($return_id, "Back to LinkBlog");
        echo "</p>\n";
        echo "<div class=\"modulelinkblog\">\n";

        if ($dbresult && $dbresult->RowCount()) {
            while ($row = $dbresult->FetchRow()) {

				if ($row["status"] == "0") {
					echo "This link is no longer available<br />\n";
					return;
				} ## if
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
                echo cms_mapi_create_user_link("LinkBlog", $id, $cms->variables["page"], array('action'=>'view_comments', 'linkblog_id'=>$row["linkblog_id"]), "Permalink");
				echo " | \n";
                if (isset($row["linkblog_credit"]) && $row["linkblog_credit"] != "") {
                    echo "Source credit: <a href=\"".$row["linkblog_credit"]."\">".$row["linkblog_credit"]."</a>\n";
                    echo " | \n";
                } ## if
            } ## while
        } ## if

        echo "</div>\n";

        if ($action == "post_comment") {

            $validinfo = true;
            $errormsg = "<ul>\n";
		}

		if ($errormsg != "") { $errormsg .= "</ul>\n"; }

		if ($validinfo) {
			$new_id = $db->GenID(cms_db_prefix()."module_linkblog_comment_seq");
			$query = "INSERT INTO ".cms_db_prefix()."module_linkblog_comments (comment_id, linkblog_id, author, comment, create_date) VALUES ($new_id,".$params[$id."linkblog_id"].",".$db->qstr($params[$id."author"]).",".$db->qstr($params[$id."comment"]).", now())";
			$dbresult = $db->Execute($query);
		} else {
			echo $errormsg;
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
                <td><input type="text" name="<?php echo $id?>author" value="<?php echo $author?>" size="20" maxlength="50" /></td>
            </tr>
            <tr>
                <td>Comment:</td>
                <td><input type="text" name="<?php echo $id?>comment" value="" size="100" maxlength="250" /></td>
            </tr>
            <tr>
                <td>&nbsp;<input type="hidden" name="<?php echo $id?>action" value="post_comment" />
                <input type="hidden" name="<?php echo $id?>linkblog_id" value="<?php echo $params[$id."linkblog_id"]?>" /></td>
                <td><input type="submit" name="<?php echo $id?>submitlink" value="Submit" /><input type="submit" name="<?php echo $id?>cancellink" value="Cancel" /></td>
            </tr>
        </table>
        <?php

        echo cms_mapi_create_user_form_end();
        break;

	case "search_url":
		echo "<p class=\"smalltitle\">Search Results for (".$params[$id."search_text"].")";
		echo cms_mapi_create_content_link_by_page_id($return_id, "Back to LinkBlog");
		echo "</p>\n";
		echo "<div class=\"modulelinkblog\">\n";
        $db = $cms->db;
		$query = "SELECT a.linkblog_id, a.linkblog_title, a.linkblog_url, a.linkblog_author, a.linkblog_type, a.create_date, count(b.linkblog_id) as total FROM ".cms_db_prefix()."module_linkblog a LEFT OUTER JOIN ".cms_db_prefix()."module_linkblog_comments b ON a.linkblog_id=b.linkblog_id";
		$query .= " WHERE (a.linkblog_url like '%".$params[$id."search_text"]."%'";
		$query .= " OR a.linkblog_title like '%".$params[$id."search_text"]."%')";
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
				echo cms_mapi_create_user_link("LinkBlog", $id, $cms->variables["page"], array('action'=>'view_comments', 'linkblog_id'=>$row["linkblog_id"]), "Comments (".$row["total"].")");
				echo "\n</div>\n";
				echo "</div>\n";
			}
		}
		break;

	case "viewoldlinks":
		linkblog_module_showLinks($cms, $id, $params, $return_id);
		break;

	default:
		echo "No appropriate action found ($action)<br />\n";
    } //switch
}


# vim:ts=4 sw=4 noet
?>
