<?php
#---------------------------------------------------------------------------
# CMS Made Simple - Power for the professional, Simplicity for the end user.
# (c) 2004 - 2011 by Ted Kulp
# (c) 2011 - 2018 by the CMS Made Simple Development Team
# (c) 2018 and beyond by the CMS Made Simple Foundation
# This project's homepage is: https://www.cmsmadesimple.org
#---------------------------------------------------------------------------
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
# Or read it online: http://www.gnu.org/licenses/licenses.html#GPL
#---------------------------------------------------------------------------

if( !isset($gCms) ) exit();
if( !$this->CheckPermission('Approve News') ) exit();

if( !isset($params['approve']) || !isset($params['articleid']) ) {
  die('missing parameter, this should not happen');
}

$this->SetCurrentTab('articles');
$articleid = (int)$params['articleid'];
$search = cms_utils::get_search_module();
$status = '';
$uquery = "UPDATE ".CMS_DB_PREFIX."module_news SET status = ?,modified_date = NOW() WHERE news_id = ?";
switch( $params['approve'] ) {
 case 0:
   $status = 'draft';
   break;
 case 1:
   $status = 'published';
   break;
 default:
   die('unknown value for approve parameter, I do not know what to do with this');
   break;
}

// Get the record
if( is_object($search) ) {
  if( $status == 'draft' ) {
    $search->DeleteWords($this->GetName(),$articleid,'article');
  }
  else if( $status == 'published' ) {
    $query = 'SELECT * FROM '.CMS_DB_PREFIX.'module_news WHERE news_id = ?';;
    $article = $db->GetRow($query,array($articleid));
    if( !$article ) return;

    $useexp = 0;
    $t_end = time() + 3600; // just for the math
    if( $article['end_time'] != "" ) {
      $useexp = 1;
      $t_end = $db->UnixTimeStamp($article['end_time']);
    }

    if( $t_end > time() || $this->GetPreference('expired_searchble',1) == 1 ) {
      $text = $article['news_data'] . ' ' . $article['summary'] . ' ' . $article['news_title'] . ' ' . $article['news_title'];
      $query = 'SELECT value FROM '.CMS_DB_PREFIX.'module_news_fieldvals WHERE news_id = ?';
      $flds = $db->GetArray($query,array($articleid));
      if( is_array($flds) ) {
	for( $i = 0; $i < count($flds); $i++ ) {
	  $text .= ' '.$flds[$i]['value'];
	}
      }

      $search->AddWords($this->GetName(), $articleid, 'article', $text,
			($useexp == 1 && $this->GetPreference('expired_searchable',0) == 0) ? $t_end : NULL);
    }
  }
}

$db->Execute($uquery,array($status,$articleid));
\CMSMS\HookManager::do_hook('News::NewsArticleEdited', [ 'news_id'=>$articleid, 'status'=>$status ] );
$this->SetMessage($this->Lang('msg_success'));
$this->RedirectToAdminTab();

#
# EOF
#