<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (ted@cmsmadesimple.org)
#Visit our homepage at: http://www.cmsmadesimple.org
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
if( !isset($gCms) ) exit;
if( !check_login(FALSE) ) exit; // admin only.... but any admin

$handlers = ob_list_handlers();
for ($cnt = 0; $cnt < sizeof($handlers); $cnt++) { ob_end_clean(); }

$out = null;
$term = trim(strip_tags(get_parameter_value($_REQUEST,'term')));
$alias = trim(strip_tags(get_parameter_value($_REQUEST,'alias')));

if( $alias ) {
    $query = 'SELECT content_id,content_name,menu_text,content_alias,id_hierarchy FROM '.CMS_DB_PREFIX.'content
              WHERE content_alias = ? AND active = 1';
    $dbr = $db->GetRow($query,array($alias));
    if( is_array($dbr) && count($dbr) ) {
        $lbl = "{$dbr['content_name']} ({$dbr['id_hierarchy']})";
        $out = array('label'=>$lbl, 'value'=>$dbr['content_alias']);
        echo json_encode($out);
    }
}
else if( $term ) {
    $term = "%{$term}%";
    $query = 'SELECT content_id,content_name,menu_text,content_alias,id_hierarchy FROM '.CMS_DB_PREFIX.'content
            WHERE (content_name LIKE ? OR menu_text LIKE ? OR content_alias LIKE ?)
              AND active = 1
            ORDER BY default_content DESC, hierarchy ASC';
    $dbr = $db->GetArray($query,array($term,$term,$term));
    if( is_array($dbr) && count($dbr) ) {
        // found some pages to match
        $out = array();
        // load the content objects
        foreach( $dbr as $row ) {
            $lbl = "{$row['content_name']} ({$row['id_hierarchy']})";
            $out[] = array('label'=>$lbl, 'value'=>$row['content_alias']);
        }
        echo json_encode($out);
    }
}

exit;
#
# EOF
#
?>
