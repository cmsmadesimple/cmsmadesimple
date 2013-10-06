<?php
if (!isset($gCms)) exit;

#
#The tab headers
#
echo $this->StartTabHeaders();
if (FALSE == empty($params['active_tab'])) {
  $tab = $params['active_tab'];
 } else {
  $tab = '';
}
if ($this->CheckPermission('Modify News') || $this->CheckPermission('Approve News') ) {
  echo $this->SetTabHeader('articles',$this->Lang('articles'), ('articles' == $tab)?true:false);
}
if( $this->CheckPermission('Modify Site Preferences') ) {
  echo $this->SetTabHeader('categories',$this->Lang('categories'), ('categories' == $tab)?true:false);
  echo $this->SetTabHeader('customfields',$this->Lang('customfields'),
			   ('customfields'==$tab)?true:false);

  echo $this->SetTabHeader('options',$this->Lang('options'), ('options' == $tab)?true:false);
}
echo $this->EndTabHeaders();

#
#The content of the tabs
#
echo $this->StartTabContent();
if ($this->CheckPermission('Modify News') || $this->CheckPermission('Approve News') ) {
  echo $this->StartTab('articles', $params);
  include(dirname(__FILE__).'/function.admin_articlestab.php');
  echo $this->EndTab();
}
if ($this->CheckPermission('Modify Site Preferences')) {
  echo $this->StartTab('categories', $params);
  include(dirname(__FILE__).'/function.admin_categoriestab.php');
  echo $this->EndTab();

  echo $this->StartTab('customfields', $params);
  include(dirname(__FILE__).'/function.admin_customfieldstab.php');
  echo $this->EndTab();

  echo $this->StartTab('options', $params);
  include(dirname(__FILE__).'/function.admin_optionstab.php');
  echo $this->EndTab();
}

echo $this->EndTabContent();

# vim:ts=4 sw=4 noet
?>
