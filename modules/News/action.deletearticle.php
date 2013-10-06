<?php
if (!isset($gCms)) exit;

if (!$this->CheckPermission('Delete News'))
  {
    echo $this->ShowErrors($this->Lang('needpermission', array('Modify News')));
    return;
  }

$articleid = '';
if (isset($params['articleid']))
  {
    $articleid = $params['articleid'];
  }

news_admin_ops::delete_article($articleid);

$params = array('tab_message'=> 'articledeleted', 'active_tab' => 'articles');
$this->Redirect($id, 'defaultadmin', $returnid, $params);
?>
