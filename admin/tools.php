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

$CMS_ADMIN_PAGE=1;

require_once("../include.php");

check_login($config);

include_once("header.php");

if ($_POST['action'] == "do_backup") {
    do_adodb_backup();
    return;
}
if ($_POST['action'] == "do_restore") {
    do_adodb_restore();
    return;
}

echo "<p>".$gettext->gettext("Click the Backup button to proceed with the database backup.")."</p>\n";
echo "<form method=\"POST\" action=\"".basename(__FILE__)."\">";
echo "<input type=hidden name=action value=\"do_backup\">\n";
echo "<input type=submit value=\"Backup\">\n";
echo "</form>\n";

echo "<p>".$gettext->gettext("Enter your backup sql filename and click Restore to wipe out your database and rebuild it.  Use with caution.")."</p>\n";
echo "<form method=\"POST\" action=\"".basename(__FILE__)."\">\n";
echo "<input type=hidden name=action value=\"do_restore\">\n";
echo "<p><input type=text length=50 maxlength=250 name=filename>\n";
echo "<input type=submit value=\"Restore\"></p>\n";
echo "</form>\n";

function do_adodb_backup() {

    global $gettext;
    global $dbnew;
    global $db;
    global $config;

    echo "<p>".$gettext->gettext("Working....")."<br />\n";

    ## open file for backup
    $date = date("mdy-Hi");
    $backup_file = $config->root_path."/backup.$date.sql";
    $file = @fopen($backup_file, "w");
    if ($file != 0) {
    } else {
        echo "Cannot create $backup, please change permissions to allow this\n";
        return;
    } ## if

    ## get an array of all the tables
    foreach ($dbnew->MetaTables('TABLES') as $table) {

        ## drop the table if it exists
        fwrite($file, "DROP TABLE IF EXISTS $table;\n");

        ## build a list of each column in $table
        $field_list = "";
        $create_syntax = "CREATE TABLE $table (";
        foreach ($dbnew->MetaColumns($table) as $column) {  ## name, type, max_length
            $field_list .= $column->name.", ";
            $create_syntax .= $column->name . " " . $column->type;
            if ($column->max_length <> -1) {
                $create_syntax .= "(".$column->max_length.")";
            } ## if
            $create_syntax .= ", ";
        } ## foreach

        $create_syntax = substr($create_syntax,0,strlen($create_syntax) -2);
        $create_syntax .= ");";

        ## recreate the table
        fwrite($file, $create_syntax."\n");

        $field_list = substr($field_list,0,strlen($field_list) -2);

        ## get all the data from the table
        $query = "select $field_list from $table";
        $result = &$dbnew->Execute($query);
    
        if ($result) {
            while ($row2 = $result->FetchRow()) {

                $data_line = "";
                foreach ($row2 as $data) {
                    $data_line .= $dbnew->qstr($data).",";
                } ## foreach

                $data_line = substr($data_line,0,strlen($data_line) -1);
                $insert_line = "INSERT INTO $table ($field_list) values ($data_line);\n";
##                 echo $insert_line."<br />";
                fwrite($file, $insert_line);
            } ## while
        } ## if
    
    } ## foreach
    fclose($file);

    #function audit(&$config, $userid, $username, $itemid, $itemname, $action) {
    audit($config, $_SESSION["cms_admin_user_id"], $_SESSION["cms_admin_username"], -1, $backup_file, 'Database backup');
    echo $gettext->gettext("Backup complete.  Wrriten to file:")." ".$backup_file."</p>\n";
}

function do_adodb_restore() {

    global $gettext;
    global $dbnew;
    global $db;
    global $config;

    $lines = explode("\n", file_get_contents($_POST["filename"]));

    foreach ($lines as $line) {
        $dbnew->Execute($line);
    }
    audit($config, $_SESSION["cms_admin_user_id"], $_SESSION["cms_admin_username"], -1, $_POST["filename"], 'Database restore');
    echo $gettext->gettext("Restore complete: ").$_POST["filename"]."</p>\n";
}
?>
