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
	if ($allow_search != '') {
		echo cms_mapi_create_user_form_start("LinkBlog", $id, $return_id);
		?>
		<table>
			<tr>
				<td><input name="<?php echo $id?>search_text" type=text value=""></td>
				<td><input name="<?php echo $id?>submit_search" type=submit value=Search><input type=hidden name="<?php echo $id?>action" value="search_url"></td>
			</tr>
		</table>
		<?php
		echo cms_mapi_create_user_form_end();
	}

    $old_date = "";
    if (isset($params[$id."old_date"])) {
        $old_date = $params[$id."old_date"];
    }

    $db = $cms->db;
    $curr_date = date("Y-m-d");
    $query = "SELECT a.linkblog_id, a.linkblog_title, a.linkblog_url, a.linkblog_author, a.linkblog_type, a.create_date, count(b.linkblog_id) as total FROM ".cms_db_prefix()."module_linkblog a LEFT OUTER JOIN ".cms_db_prefix()."module_linkblog_comments b ON a.linkblog_id=b.linkblog_id WHERE";
    if ($old_date != "") {
        $query .= " a.create_date like '$old_date%' and ";
    } ## if
	$query .= " a.status = '1'";
    $query .= " GROUP BY a.linkblog_id ORDER BY create_date DESC";
    $dbresult = $db->Execute($query);
    echo "<p class=\"smalltitle\">Posted sites</p>\n";
    echo "<div class=\"modulelinkblog\">\n";

    if ($dbresult && $dbresult->RowCount()) {
        $last_date = "";
        while ($row = $dbresult->FetchRow()) {

            #echo "last_date: ($last_date) create_date: ".$row["create_date"].")<br/>";
            if ($last_date == substr($row["create_date"],0,10) || $last_date == "") {
                echo "<div class=\"modulelinkblogentry\">\n";
                echo "<div class=\"modulelinkblogentryheader\">\n";
                if ($last_date == "") {
                    echo date("F j, Y", $db->UnixTimeStamp($row['create_date']))."<br />\n";
                }
                echo "Posted at ".date("g:i a", $db->UnixTimeStamp($row['create_date']))." by ".$row['linkblog_author']."\n</div>\n";
                echo "<div class=\"modulelinkblogentrybody\">\n<a href=\"".$row["linkblog_url"]."\"><img src=\"modules/LinkBlog/images/type".$row["linkblog_type"].".png\" border=\"0\" alt=\"\" /> ".$row["linkblog_title"]."</a>\n";
                echo "</div>\n";

                echo "<div class=\"modulelinkblogentrycommentlink\">\n";
                echo cms_mapi_create_user_link("LinkBlog", $id, $cms->variables["page"], array('action'=>'view_comments', 'linkblog_id'=>$row["linkblog_id"]), "Comments (".$row["total"].")");
                echo "\n</div>\n";
                echo "</div>\n";
            } else {
                break;
            }
            $last_date = substr($row["create_date"],0,10);
        }

        // show a list of the last 5 days with links
        $query = "SELECT create_date from ".cms_db_prefix()."module_linkblog GROUP BY DATE_FORMAT(create_date, '%d %m %Y') ORDER BY create_date DESC LIMIT 5";
        $dbresult = $db->Execute($query);

        if ($dbresult && $dbresult->RowCount()) {
            $last_date = "";
            echo "<p>Recent links:</p><ul>\n";
            echo "<li>";
            echo cms_mapi_create_user_link("LinkBlog", $id, $cms->variables["page"], array('action'=>'viewoldlinks', 'old_date'=>$curr_date, 'allow_search'=>'true'), "Today");
            echo "</li>\n";
            while ($row = $dbresult->FetchRow()) {
                if ($last_date != substr($row["create_date"],0,10) && substr($row["create_date"],0,10) != $curr_date) {
                    echo "<li>";
                    echo cms_mapi_create_user_link("LinkBlog", $id, $cms->variables["page"], array('action'=>'viewoldlinks', 'old_date'=>substr($row["create_date"],0,10), 'allow_search'=>'true'), substr($row["create_date"],0,10));
                    echo "</li>\n";
                }
                $last_date = substr($row["create_date"],0,10);
            }
            echo "</ul>\n";
        }
    } else {
        echo " - No linkblog posted - ";
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

    $comment = "";
    if (isset($params[$id."comment"])) {
        $comment = $params[$id."comment"];
    }

##     if ($action == "viewoldlinks") {
##         linkblog_module_showLinks($cms, $id, $params, $return_id);
##     }

    switch ($action) {
    ## run this when a new link is submitted
    case "add_new_link":

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
            $query = "INSERT INTO ".cms_db_prefix()."module_linkblog (linkblog_id, linkblog_author, linkblog_title, linkblog_type, linkblog_url, create_date, modified_date, status) VALUES ($new_id, ".$db->qstr($author).", ".$db->qstr($title).",".$type.",".$db->qstr($url).",".$db->DBTimeStamp(time()).",".$db->DBTimeStamp(time()).", '1')";
            $dbresult = $db->Execute($query);
            cms_mapi_redirect_user_by_pageid($return_id);
            return;
        } else {
            echo "Error: $errormsg<br />\n";
        }
        break;

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
                <td>Your name:</td>
                <td><input type="text" name="<?php echo $id?>author" value="<?php echo $author?>" size="20" maxlength="50" /></td>
            </tr>
            <tr>
                <td>Title:</td>
                <td><input type="text" name="<?php echo $id?>title" value="<?php echo $title?>" size="100" maxlength="250" /></td>
            </tr>
            <tr>
                <td>URL:</td>
                <td><input type="text" name="<?php echo $id?>url" value="<?php echo $url?>" size="100" maxlength="250" /></td>
            </tr>
            <tr>
                <td>Type:</td>
                <td><?php echo $types?></td>
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
        $query = "SELECT linkblog_id, linkblog_title, linkblog_url, linkblog_author, linkblog_type, create_date from ".cms_db_prefix()."module_linkblog WHERE linkblog_id=".$params[$id."linkblog_id"];
        $dbresult = $db->Execute($query);
        echo "<p class=\"smalltitle\">Posted site - ";
        echo cms_mapi_create_content_link_by_page_id($return_id, "Back to LinkBlog");
        echo "</p>\n";
        echo "<div class=\"modulelinkblog\">\n";

        if ($dbresult && $dbresult->RowCount()) {
            while ($row = $dbresult->FetchRow()) {

                echo "<div class=\"modulelinkblogentry\">\n";
                ## echo "<div class=\"modulelinkblogentryheader\">\nPosted at ".date("F j, Y, g:i a", $db->UnixTimeStamp($row['create_date']))." by ".$row['linkblog_author']."\n</div>\n";
                echo "<div class=\"modulelinkblogentryheader\">\nPosted at ".date("g:i a", $db->UnixTimeStamp($row['create_date']))." by ".$row['linkblog_author']."\n</div>\n";
                echo "<div class=\"modulelinkblogentrybody\">\n<a href=\"".$row["linkblog_url"]."\"><img src=\"modules/LinkBlog/images/type".$row["linkblog_type"].".png\" border=\"0\" alt=\"\" /> ".$row["linkblog_title"]."</a>\n";
                echo "</div>\n";

                echo "</div>\n";
            }
        }
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
				echo "<div class=\"modulelinkblogentryheader\">\n";
				if ($last_date == "") {
					echo date("F j, Y", $db->UnixTimeStamp($row['create_date']))."<br />\n";
				}
				echo "Posted at ".date("g:i a", $db->UnixTimeStamp($row['create_date']))." by ".$row['linkblog_author']."\n</div>\n";
				echo "<div class=\"modulelinkblogentrybody\">\n<a href=\"".$row["linkblog_url"]."\"><img src=\"modules/LinkBlog/images/type".$row["linkblog_type"].".png\" border=\"0\" alt=\"\" /> ".$row["linkblog_title"]."</a>\n";
#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
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
