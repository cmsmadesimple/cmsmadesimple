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


function linkblog_module_about() {
    ?>
    <p>Author: Greg Froese &lt;heavy_g@users.sf.net&gt;<br />
    Please send all bug requests through <a href="http://bugs.cmsmadesimple.org">here</a></p>
    <p>Version: 1.3</p>
    <p>
    <b>Change History:</b><br/>
    Version 1.4 - 2005.01.03
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

function linkblog_module_executeadmin($cms, $module_id) {

    $action = "";
    ## i know this isn't the right way to do it, but couldn't get anything  else working
    if (isset($_POST[$module_id."action"])) {
        $action = $_POST[$module_id."action"];
    } elseif (isset($_GET[$module_id."action"])) {
        $action = $_GET[$module_id."action"];
    } ## if

    $access = cms_mapi_check_permission($cms, "Modify Linkblog");
    if (!$access) {
        echo "<p class=\"error\">You need the 'Modify Linkblog' permission to perform that function.</p>";
        return;
    } ## if

    ## create mini menu
    echo cms_mapi_create_admin_link("LinkBlog", $module_id, array('action'=>'add_category_form'), "Add Category");
    echo " | " . cms_mapi_create_admin_link("LinkBlog", $module_id, array('action'=>'manage_categories'), "Manage Categories");
    echo " | " . cms_mapi_create_admin_link("LinkBlog", $module_id, array('action'=>'manage_links'), "Manage Links");

##     echo "action: ($action)\n";
    switch ($action) {
        case "add_category_form":
            linkblog_module_add_category_form($cms, $module_id);
            break;
        case "add_category_to_db":
            linkblog_module_add_category_to_db($cms, $module_id);
            linkblog_module_manage_categories($cms, $module_id);
            break;
        case "update_category":
            linkblog_module_update_category($cms, $module_id);
        case "manage_categories":
            linkblog_module_manage_categories($cms, $module_id);
            break;
        case "manage_links":
            linkblog_module_manage_links($cms, $module_id);
            break;
        case "edit_category":
            linkblog_module_edit_category($cms, $module_id);
            break;
        case "deactivate":
            linkblog_module_deactivate_link($cms, $module_id);
            linkblog_module_manage_links($cms, $module_id);
            break;
        case "activate":
            linkblog_module_activate_link($cms, $module_id);
            linkblog_module_manage_links($cms, $module_id);
            break;
        default:
            break;
    } ## switch
} ## module_linkblog_executeadmin

function linkblog_module_add_category_form($cms, $module_id) {

##     echo "<table cellspacing=5 border=0>\n";
    echo "<h4 class=admintitle>Add Category</h4>\n";
    echo cms_mapi_create_admin_form_start("LinkBlog", $module_id);
    echo "Cagegory name: <input name=".$module_id."category_name length=25 maxlength=100 />\n";
    echo "<input type=hidden name=".$module_id."action value=add_category_to_db />\n";
    echo "<input type=submit value=\"Add Category\" />\n";
    echo cms_mapi_create_admin_form_end();

} ## linkblog_module_add_category

function linkblog_module_add_category_to_db($cms, $module_id) {

    $db = $cms->db;
    $category_id = $db->GenID(cms_db_prefix()."module_linkblog_type_seq");
    $category_name = $_POST[$module_id."category_name"];
    $insert = "INSERT INTO ".cms_db_prefix()."module_linkblog_types (linkblog_type_id, linkblog_type) VALUES ($category_id, \"$category_name\")";
    $dbresult = $db->Execute($insert);
    ## TODO: need error checking
    echo "<h4 class=admintitle>Category $category_name successfully added.</h4>\n";

} ## linkblog_module_add_category_to_db

function linkblog_module_manage_categories($cms, $module_id) {

    $db = $cms->db;
    echo "<h4 class=admintitle>Manage categories</h4><br />\n";
    echo "<table cellspacing=0 class='admintable'>\n";
    echo "<tr><th>Name</th><th>Category ID</th><th>Graphics file</th></tr>\n";

    $categories = linkblog_module_get_categories($db);
    
    $records = count($categories);

    for ($i = 0; $i < $records; $i++) {
        if ($categories[$i]["linkblog_type"] != "") {
            echo "<tr><td>".cms_mapi_create_admin_link('LinkBlog', $module_id, array('action'=>'edit_category', 'category_id'=>$categories[$i]['linkblog_type_id'], 'category_name'=>$categories[$i]['linkblog_type']), $categories[$i]['linkblog_type']) . "</td>\n";
            echo "<td>".$categories[$i]['linkblog_type_id']."</td>\n";
            echo "<td>type".$categories[$i]['linkblog_type_id'].".gif</td></tr>\n";
        } ## if
    } ## for
    echo "</table>\n";

    echo "<p>Note: Graphics files are stored in the <i>images</i> directory of the Linkblog module.</p>\n";
} ## linkblog_module_manage_categories

function linkblog_module_edit_category($cms, $module_id) {

    $category_id = "";
    if (isset($_REQUEST[$module_id."category_id"])) {
        $category_id = $_REQUEST[$module_id."category_id"];
    } ## if
    $category_name = "";
    if (isset($_REQUEST[$module_id."category_name"])) {
        $category_name = $_REQUEST[$module_id."category_name"];
    } ## if

    echo "<p>\n";
    echo cms_mapi_create_admin_form_start("LinkBlog", $module_id);
    echo "Category name: $category_name<br />\n";
    echo "<input name=".$module_id."category_name type=text length=20 maxlength=40 value=\"$category_name\" />\n";
    echo "<input name=".$module_id."action type=hidden value=update_category />\n";
    echo "<input name=".$module_id."category_id type=hidden value=$category_id />\n";
    echo "<input type=submit value=Update />\n";
    echo cms_mapi_create_admin_form_end();
    echo "</p>\n";
} ## linkblog_module_edit_category

function linkblog_module_update_category($cms, $module_id) {

    $db = $cms->db;
    $category_name = $_POST[$module_id."category_name"];
    $category_id = $_POST[$module_id."category_id"];
##     echo "category_name: ($category_name)<br />\n";
    $update = "UPDATE ".cms_db_prefix()."module_linkblog_types SET linkblog_type='$category_name' WHERE linkblog_type_id=$category_id";
    $dbresult = $db->Execute($update);
    ## TODO: add error checking
    echo "<h4 class=admintitle>Category $category_name successfully updated.</h4>\n";

} ## linkblog_module_update_category

function linkblog_module_manage_links($cms, $module_id) {

    $keywords = '';
    if (isset($_POST[$module_id."keywords"])) {
        $keywords = $_POST[$module_id."keywords"];
    } elseif (isset($_GET[$module_id."keywords"])) {
        $keywords = $_GET[$module_id."keywords"];
    } ## if

    echo "<h4 class=admintitle>Manage Links</h4><br />\n";
    echo "Note: Only the 20 most recent links are shown. Use the search box to find others<br />\n";
    echo cms_mapi_create_admin_form_start("LinkBlog", $module_id);
    echo "<input type=text name=".$module_id."keywords length=25 maxlength=50 value=\"$keywords\" />\n";
    echo "<input type=hidden name=".$module_id."action value=manage_links />\n";
    echo "<input type=submit value=\"Search\" />\n"; 
    echo cms_mapi_create_admin_form_end();

    echo "<table class=admintable cellspacing=5 border=0>\n";
    echo "<tr><th>Name</th><th>Category</th><th>Author</th><th>Post Date</th><th>Status</th></tr>\n";

    $db = $cms->db;
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
                echo cms_mapi_create_admin_link("LinkBlog", $module_id, array('action'=>'deactivate', 'linkblog_id'=>$row["linkblog_id"], 'keywords'=>$keywords), "Active"); 
                echo "</td>\n";
            } else {
                echo "<td>";
                echo cms_mapi_create_admin_link("LinkBlog", $module_id, array('action'=>'activate', 'linkblog_id'=>$row["linkblog_id"], 'keywords'=>$keywords), "Inactive"); 
                echo "</td>\n";
            } ## if
            echo "</tr>\n";
        } ## while
    } ## if
    echo "</table>\n";

} ## linkblog_module_manage_links

function linkblog_module_activate_link($cms, $module_id) {

    $db = $cms->db;
    $linkblog_id = $_GET[$module_id."linkblog_id"];
    $update = "UPDATE ".cms_db_prefix()."module_linkblog SET status='1' WHERE linkblog_id=$linkblog_id";
    $db->Execute($update);

} ## linkblog_module_deactivate_link

function linkblog_module_deactivate_link($cms, $module_id) {

    $db = $cms->db;
    $linkblog_id = $_GET[$module_id."linkblog_id"];
    $update = "UPDATE ".cms_db_prefix()."module_linkblog SET status='0' WHERE linkblog_id=$linkblog_id";
    $db->Execute($update);

} ## linkblog_module_deactivate_link

function linkblog_module_upgrade($cms, $oldversion, $newversion)
{
    switch ($oldversion) {
        case "1.0":
        case "1.1":
            cms_mapi_create_permission( $cms, 'Modify Linkblog', 'Modify Linkblog');
        case "1.2":
            $db = $cms->db;
            $dict = NewDataDictionary($db);
            $sqlarray = $dict->AddColumnSQL(cms_db_prefix()."module_linkblog", "status C(10)");
            $dict->ExecuteSQLArray($sqlarray);
            $update = "UPDATE ".cms_db_prefix()."module_linkblog SET status='1'";
            $db->Execute($update);
        case "1.3":
    } ## switch

} ## linkblog_module_upgrade

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
}


?>
