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

if (!isset($gCms)) exit;

//
// initialization
//
$query = null;
$article = null;
$preview = FALSE;
$articleid = (isset($params['articleid']))?$params['articleid']:-1;
$cache_id = 'nd'.md5(serialize($params));
$compile_id = 'nd'.$articleid;

$template = null;
if (isset($params['detailtemplate'])) {
    $template = trim($params['detailtemplate']);
}
else {
    $tpl = CmsLayoutTemplate::load_dflt_by_type('News::detail');
    if( !is_object($tpl) ) {
        audit('',$this->GetName(),'No default summary template found');
        return;
    }
    $template = $tpl->get_name();
}

if( $id == '_preview_' && isset($_SESSION['news_preview']) && isset($params['preview']) ) {
    // see if our data matches.
    if( md5(serialize($_SESSION['news_preview'])) == $params['preview'] ) {
        $fname = TMP_CACHE_LOCATION.'/'.$_SESSION['news_preview']['fname'];
        if( file_exists($fname) && (md5_file($fname) == $_SESSION['news_preview']['checksum']) ) {
            $data = unserialize(file_get_contents($fname));
            if( is_array($data) ) {
                // get passed data into a standard format.
                $article = new news_article;
                $article->set_linkdata($id,$params);
                news_ops::fill_article_from_formparams($article,$data,FALSE,FALSE);
                $compile_id = 'news_preview_'.time();
                $preview = TRUE;
            }
        }
    }
}

$tpl_ob = $smarty->CreateTemplate($this->GetTemplateResource($template),$cache_id,$compile_id,$smarty);
if( $preview || !$tpl_ob->IsCached() ) {
    // not cached... have to do to the work.
    if( isset($params['articleid']) && $params['articleid'] == -1 ) {
        $article = news_ops::get_latest_article();
    }
    else if( isset($params['articleid']) && (int)$params['articleid'] > 0 ) {
        $show_expired = $this->GetPreference('expired_viewable',1);
        if( isset($params['showall']) ) $show_expired = 1;
        $article = news_ops::get_article_by_id((int)$params['articleid'],TRUE,$show_expired);
    }
    if( !$article ) {
        throw new CmsError404Exception('Article '.(int)$params['articleid'].' not found, or otherwise unavailable');
        return;
    }
    $article->set_linkdata($id,$params);

    $return_url = $this->CreateReturnLink($id, isset($params['origid'])?(int)$params['origid']:$returnid, $this->lang('news_return'));
    $tpl_ob->assign('return_url', $return_url);
    $tpl_ob->assign('entry', $article);

    $catName = '';
    if (isset($params['category_id'])) {
        $catName = $db->GetOne('SELECT news_category_name FROM '.CMS_DB_PREFIX . 'module_news_categories where news_category_id=?',array((int)$params['category_id']));
    }
    $tpl_ob->assign('category_name',$catName);
    unset($params['article_id']);
    $tpl_ob->assign('category_link',$this->CreateLink($id, 'default', $returnid, $catName, $params));

    $tpl_ob->assign('category_label', $this->Lang('category_label'));
    $tpl_ob->assign('author_label', $this->Lang('author_label'));
    $tpl_ob->assign('extra_label', $this->Lang('extra_label'));
}

//Display template
$tpl_ob->display();

#
# EOF
#