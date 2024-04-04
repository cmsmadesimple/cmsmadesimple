<?php
#---------------------------------------------------------------------------------------------------
# Module: UserGuide
# Author: Chris Taylor
# Copyright: (C) 2018 Chris Taylor, chris@binnovative.co.uk
# Licence: GNU General Public License version 3
#          see /UserGuide/lang/LICENCE.txt or <http://www.gnu.org/licenses/>
#---------------------------------------------------------------------------------------------------

if ( !defined('CMS_VERSION') ) exit;

if ( !$this->CheckPermission(UserGuide::MANAGE_PERM) ) return;

$page = new UserGuideItem();
$isNewPage = true;
if ( isset($params['pid']) && $params['pid'] > 0 ) {
    $page = UserGuideItem::load_by_id((int)$params['pid']);
    $isNewPage = false;
}
if ( isset($params['cancel']) ) {
    $this->RedirectToAdminTab('UGPage'.$page->id);

} elseif ( isset($params['submit']) || isset($params['apply']) ) {

    $errors = [];
    $page->title = trim($params['title']);
    $page->active = isset($params['active']) ? true : false;
    $page->admin = isset($params['admin']) ? true : false;
    $page->content = trim($params['content']);
    $page->embed_type = trim($params['embed_type']);
    $page->embed_code = trim($params['embed_code']);
    $page->embed_first = isset($params['embed_first']) ? true : false;

    // check title
    if ( $page->title=='' ) {
        $errors[] = $this->Lang('error_title_empty');
    }

    // set position to last position for new Pages
    if ($isNewPage) {
        $sql = "SELECT MAX(position) AS maxposition FROM ".CMS_DB_PREFIX."module_userguide";
        $maxposition = $db->GetOne($sql);
        if ($maxposition) {
            $page->position = $maxposition + 1;
        } else {
            $page->position = 1;
        }
    }

    // either display errors or save
    $formattedErrors = '';
    if ( !empty($errors) ) {
        foreach ($errors as $error) {
            $formattedErrors .= '<li>'.$error.'</li>';
        }
        $this->ShowErrors('<ul>'.$formattedErrors.'</ul>');

    } else {
        $isSaved = $page->save();
        if ( !$isSaved ) {
            $this->SetError( $this->Lang('item_notsaved') );

        } else { // saved
            $isNewPage = false;
            if ($isNewPage) {
                $query = new UserGuideQuery;
                $updated = $query->updatePositions();
            }
            if ( isset($params['submit']) ) {
                $this->SetMessage( $this->Lang('item_saved') );
                $this->RedirectToAdminTab('UGPage'.$page->id);
            } else { // apply
                $this->ShowMessage( $this->Lang('item_saved') );
            }
        }
    }

}



// get defaults
$active = is_null($page->active) ? 1 : $page->active;   // default to active
$admin = is_null($page->admin) ? 0 : $page->admin;      // default to not admin
$wysiwyg = 1;   // default to wysiwyg - not even sure how this got here!
$embed_type = is_null($page->embed_type) ? $this::EMBED_TYPES[0] : $page->embed_type;
foreach ($this::EMBED_TYPES as $type) {
    $embed_options[$type] = $this->Lang('embed_type_'.$type);
}


// smarty processing to display admin page
$tpl = $smarty->CreateTemplate($this->GetTemplateResource('admin_edit_page.tpl'), null, null, $smarty);
$tpl->assign('page',$page);
$tpl->assign('input_active', $this->CreateInputcheckbox($id, 'active', 1, $active));
$tpl->assign('isNewPage',$isNewPage);
$tpl->assign('input_content', $this->CreateTextArea(($wysiwyg), $id, $page->content, 'content'));
$tpl->assign('embed_type', $embed_type);
$tpl->assign('embed_options', $embed_options);

$tpl->display();
