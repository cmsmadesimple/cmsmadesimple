<?php

if (!isset($gCms)) exit;

$items = news_ops::get_categories($id,$params,$returnid);

#Display template
$smarty->assign('count', count($items));
$smarty->assign('cats', $items);

$template = 'browsecat'.$this->GetPreference('current_browsecat_template');
if (isset($params['browsecattemplate']))
  {
    $template = 'browsecat'.$params['browsecattemplate'];
  }
echo $this->ProcessTemplateFromDatabase($template);


?>
