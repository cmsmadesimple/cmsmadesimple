<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
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
#$Id: News.module.php 2114 2005-11-04 21:51:13Z wishy $

final class news_admin_ops
{
  protected function __construct() {}

  public static function delete_article($articleid)
  {
    $db = cmsms()->GetDb();

    //Now remove the article
    $query = "DELETE FROM ".cms_db_prefix()."module_news WHERE news_id = ?";
    $db->Execute($query, array($articleid));
    
    // Delete it from the custom fields
    $query = 'DELETE FROM '.cms_db_prefix().'module_news_fieldvals WHERE news_id = ?';
    $db->Execute($query, array($articleid));

    // delete any files...
    $config = cmsms()->GetConfig;
    $p = cms_join_path($config['uploads_path'],'news','id'.$articleid);
    if( is_dir($p) ) recursive_delete($p);

    news_admin_ops::delete_static_route($articleid);
    
    //Update search index
    $mod = cms_utils::get_module('News');
    $module = cms_utils::get_module('Search');
    if ($module != FALSE) $module->DeleteWords($mod->GetName(), $articleid, 'article');
    
    @$mod->SendEvent('NewsArticleDeleted', array('news_id' => $articleid));

    // put mention into the admin log
    audit($articleid, 'News: '.$articleid, 'Article deleted');
  }


  public static function handle_upload($itemid,$fieldname,&$error)
  {
    $config = cmsms()->GetConfig();
	  
    $mod = cms_utils::get_module('News');
    $p = cms_join_path($config['uploads_path'],'news');
    if (!is_dir($p)) {
      $res = @mkdir($p);
      if( $res === FALSE ) {
	$error = $mod->Lang('error_mkdir',$p);
	return FALSE;
      }
    }

    $p = cms_join_path($config['uploads_path'],'news','id'.$itemid);
    if (!is_dir($p)) {
      if( @mkdir($p) === FALSE ) {
	$error = $mod->Lang('error_mkdir',$p);
	return FALSE;
      }
    }

    if( $_FILES[$fieldname]['size'] > $config['max_upload_size'] ) {
      $error = $mod->Lang('error_filesize');
      return FALSE;
    }

    $filename = basename($_FILES[$fieldname]['name']);
    $dest = cms_join_path($config['uploads_path'],'news','id'.$itemid,$filename);

    // Get the files extension
    $ext = substr(strrchr($filename, '.'), 1);

    // compare it against the 'allowed extentions'
    $exts = explode(',',$mod->GetPreference('allowed_upload_types',''));
    if( !in_array( $ext, $exts ) )  {
      $error = $mod->Lang('error_invalidfiletype');
      return FALSE;
    }

    if( @cms_move_uploaded_file($_FILES[$fieldname]['tmp_name'], $dest) === FALSE ) {
      $error = $mod->Lang('error_movefile',$dest);
      return FALSE;
    }

    return $filename;
  }


  static public function UpdateHierarchyPositions()
  {
    $db = cmsms()->GetDb();

    $query = "SELECT news_category_id, news_category_name FROM ".cms_db_prefix()."module_news_categories";
    $dbresult = $db->Execute($query);
    while ($dbresult && $row = $dbresult->FetchRow()) {
      $current_hierarchy_position = "";
      $current_long_name = "";
      $content_id = $row['news_category_id'];
      $current_parent_id = $row['news_category_id'];
      $count = 0;

      while ($current_parent_id > -1) {
	$query = "SELECT news_category_id, news_category_name, parent_id FROM ".cms_db_prefix()."module_news_categories WHERE news_category_id = ?";
	$row2 = $db->GetRow($query, array($current_parent_id));
	if ($row2) {
	  $current_hierarchy_position = str_pad($row2['news_category_id'], 5, '0', STR_PAD_LEFT) . "." . $current_hierarchy_position;
	  $current_long_name = $row2['news_category_name'] . ' | ' . $current_long_name;
	  $current_parent_id = $row2['parent_id'];
	  $count++;
	}
	else {
	  $current_parent_id = 0;
	}
      }

      if (strlen($current_hierarchy_position) > 0) {
	$current_hierarchy_position = substr($current_hierarchy_position, 0, strlen($current_hierarchy_position) - 1);
      }

      if (strlen($current_long_name) > 0) {
	$current_long_name = substr($current_long_name, 0, strlen($current_long_name) - 3);
      }

      $query = "UPDATE ".cms_db_prefix()."module_news_categories SET hierarchy = ?, long_name = ? WHERE news_category_id = ?";
      $db->Execute($query, array($current_hierarchy_position, $current_long_name, $content_id));
    }
  }


  static public function delete_static_route($news_article_id)
  {
    return cms_route_manager::del_static('','News',$news_article_id);
  }

  static public function register_static_route($news_url,$news_article_id,$detailpage = '')
  {
    if( $detailpage <= 0 ) {
      $gCms = cmsms();
      $module = cms_utils::get_module('News');
      $detailpage = $module->GetPreference('detail_returnid',-1);
      if( $detailpage == -1 ) {
	$detailpage = $gCms->GetContentOperations()->GetDefaultContent();
      }
    }
    $parms = array('action'=>'detail','returnid'=>$detailpage,'articleid'=>$news_article_id);

    $route = CmsRoute::new_builder($news_url,'News',$news_article_id,$parms,TRUE);
    return cms_route_manager::add_static($route);
  }
} // end of class

#
# EOF
#
?>