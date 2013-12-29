<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (ted@cmsmadesimple.org)
#This project's homepage is: http://www.cmsmadesimple.org
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
check_login(); // admin only.... but any admin

$out = null;
$term = trim(get_parameter_value($_REQUEST,'term'));
debug_to_log('ajax_getpages');
debug_to_log('term is '.$term);
if( $term ) {
  $term = "%{$term}%";
  $query = 'SELECT content_id,content_name,menu_text,content_alias FROM '.cms_db_prefix().'content 
            WHERE (content_name LIKE ? OR menu_text LIKE ? OR content_alias LIKE ?)
              AND active = 1
            ORDER BY default_content DESC, hierarchy ASC';
  $dbr = $db->GetArray($query,array($term,$term,$term));
  debug_to_log($db->sql);
  debug_to_log($dbr);
  if( is_array($dbr) && count($dbr) ) {
    // found some pages to match
    $out = array();
    // load the content objects
    foreach( $dbr as $row ) {
      $out[] = array('label'=>$row['content_name'], 'value'=>$row['content_alias']);
    }
    debug_to_log($out);
    echo json_encode($out);
  }
}

exit;
#
# EOF
#
?>