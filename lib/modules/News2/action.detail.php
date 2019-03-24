<?php
namespace News2;
if( !isset($gCms) ) exit;
$artm = $this->articleManager();
$fielddefs = $this->fielddefManager()->loadAllAsHash();

$template = get_parameter_value($params,'detailtemplate');
$template = $this->ResolveTemplate('detail',$template,'detail.tpl');
$article = null;

$preview_key = get_parameter_value($params,'preview_key');
if( $preview_key ) {
    if( !isset($_SESSION[$preview_key]) ) throw new Exception('Preview key not found 2',400);
    $sig = sha1('news2_preview_'.$_SESSION[$preview_key]);
    if( $preview_key != $sig ) throw new Exception('Preview key invalid',400);
    $article = unserialize($_SESSION[$preview_key]);
    if( ! $article instanceof Article ) throw new Exception('Preview data not available',400);
    unset($_SESSION[$preview_key]);
}
else {
    $article_id = (int) get_parameter_value($params,'article');
    if( $article_id < 0 ) {
        // get latest valid article
        $article = $artm->loadLatestValid();
    } else if( $article_id == 0 ) {
        // invalid param
    } else {
        // get specified article
        $article = $artm->loadByID( $article_id );
    }
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
