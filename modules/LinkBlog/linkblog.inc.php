<?php

// Display a list of links from either today or in the past depending on the parameters passed
function linkblog_module_showLinks($cms, $id, $params) {

    $old_date = "";
    if (isset($params[$id."old_date"])) {
        $old_date = $params[$id."old_date"];
    }

    $db = $cms->db;
    $curr_date = date("Y-m-d");
    $query = "SELECT a.linkblog_id, a.linkblog_title, a.linkblog_url, a.linkblog_author, a.linkblog_type, a.create_date, count(b.linkblog_id) as total FROM ".cms_db_prefix()."module_linkblog a LEFT OUTER JOIN ".cms_db_prefix()."module_linkblog_comments b ON a.linkblog_id=b.linkblog_id";
    if ($old_date != "") {
        $query .= " WHERE a.create_date like '$old_date%'";
    }
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
                echo "<div class=\"modulelinkblogentrybody\">\n<a href=\"".$row["linkblog_url"]."\"><img src=modules/LinkBlog/images/type".$row["linkblog_type"].".png border=\"0\"> ".$row["linkblog_title"]."</a>\n";
                echo "</div>\n";

                echo "<div class=\"modulelinkblogentrycommentlink\">\n";
                echo cms_mapi_create_user_link("LinkBlog", $id, $cms->variables["page"], array('action'=>'viewcomments', 'linkblog_id'=>$row["linkblog_id"]), "Comments (".$row["total"].")");
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
            echo cms_mapi_create_user_link("LinkBlog", $id, $cms->variables["page"], array('action'=>'viewoldlinks', 'old_date'=>$curr_date), "Today");
            echo "</li>\n";
            while ($row = $dbresult->FetchRow()) {
                if ($last_date != substr($row["create_date"],0,10) && substr($row["create_date"],0,10) != $curr_date) {
                    echo "<li>";
                    echo cms_mapi_create_user_link("LinkBlog", $id, $cms->variables["page"], array('action'=>'viewoldlinks', 'old_date'=>substr($row["create_date"],0,10)), substr($row["create_date"],0,10));
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
    echo cms_mapi_create_user_link("LinkBlog", $id, $cms->variables["page"], array('action'=>'addlink'), "Add a link");
    echo "</div>\n";
}

# vim:ts=4 sw=4 noet


?>
