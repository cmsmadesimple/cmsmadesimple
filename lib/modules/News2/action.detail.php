<?php
namespace News2;
if( !isset($gCms) ) exit;
$artm = $this->articleManager();
$fielddefs = $this->fielddefManager()->loadAllAsHash();

$template = get_parameter_value($params,'detailtemplate','detail.tpl');
$article_id = (int) get_parameter_value($params,'article');

$article = null;
if( $article_id < 0 ) {
    // get latest valid article
    $article = $artm->loadLatestValid();
} else if( $article_id == 0 ) {
    // invalid param
} else {
    // get specified article
    $article = $artm->loadByID( $article_id );
}

if( !$article ) throw new \CmsError404Exception('Article '.$articleid.' not found, or is otherwise unavailable');
if( $article->end_time && $article->end_time < time() && !$this->settings()->detail_show_expired ) {
    throw new \CmsError404Exception('Article '.$articleid.' not found, or is otherwise unavailable');
}

$category = null;
if( $article->category_id > 0 ) $category = $this->categoriesManager()->loadByID( $article->category_id );
$tpl = $smarty->CreateTemplate( $this->GetTemplateResource( $template ), null, null, $smarty );
$tpl->assign('article',$article);
$tpl->assign('fielddefs',$fielddefs);
$tpl->assign('category',$category);
$tpl->assign('fieldtypes',$this->fieldTypeManager()->getAll());
$tpl->display();
