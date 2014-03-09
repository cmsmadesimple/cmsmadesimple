<?php
if (!isset($gCms)) exit;

$template = null;
if (isset($params['formtemplate'])) {
  $template = trim($params['formtemplate']);
}
else {
  $tpl = CmsLayoutTemplate::load_dflt_by_type('Search::searchform');
  if( !is_object($tpl) ) {
    audit('',$this->GetName(),'No default summary template found');
    return;
  }
  $template = $tpl->get_name();
}

$cache_id = '|sr'.md5(serialize($params));
$compile_id = '';

if( !$smarty->isCached($this->GetDatabaseResource($template),$cache_id,$compile_id) ) {
  $inline = false;
  if( isset( $params['inline'] ) ) {
    $txt = strtolower(trim($params['inline']));
    if( $txt == 'true' || $txt == '1' || $txt == 'yes' ) {
      $inline = true;
    }
  }
  $origreturnid = $returnid;
  if( isset( $params['resultpage'] ) ) {
    $manager = $gCms->GetHierarchyManager();
    $node = $manager->sureGetNodeByAlias($params['resultpage']);
    if (isset($node)) {
      $returnid = $node->getID();
    }
    else {
      $node = $manager->sureGetNodeById($params['resultpage']);
      if (isset($node)) {
	$returnid = $params['resultpage'];
      }
    }
  }
  //Pretty Urls Compatibility
  $is_method = isset($params['search_method'])?'post':'get';
  // donot remove this line (calguy1000)
  if( !$inline ) $id = 'cntnt01';

  // Variable named hogan in honor of moorezilla's Rhodesian Ridgeback :) http://forum.cmsmadesimple.org/index.php/topic,9580.0.html
  $submittext = (isset($params['submit'])) ? $params['submit'] : $this->Lang('searchsubmit');
  $searchtext = (isset($params['searchtext'])) ? $params['searchtext'] : $this->GetPreference('searchtext','');
  $smarty->assign('search_actionid',$id);
  $smarty->assign('searchtext',$searchtext);
  $smarty->assign('startform', 
		  $this->CreateFormStart($id, 'dosearch', $returnid, $is_method, '', $inline ));
  $smarty->assign('label', '<label for="'.$id.'searchinput">'.$this->Lang('search').'</label>');
  $smarty->assign('searchprompt',$this->Lang('search'));
  //$smarty->assign('inputbox', $this->CreateInputText($id, 'searchinput', $searchtext, 20, 50, $hogan));
  //$smarty->assign('submitbutton', $this->CreateInputSubmit($id, 'submit', $submittext));
  $smarty->assign('submittext', $submittext);

  $hidden = '';
  if( $origreturnid != $returnid ) {
    $hidden .= $this->CreateInputHidden($id, 'origreturnid', $origreturnid);
  }
  if( isset( $params['modules'] ) ) {
    $hidden .= $this->CreateInputHidden( $id, 'modules', trim($params['modules']) );
  }
  if( isset( $params['detailpage'] ) ) {
    $hidden .= $this->CreateInputHidden( $id, 'detailpage', trim($params['detailpage']) );
  }
  foreach( $params as $key => $value ) {
    if( preg_match( '/^passthru_/', $key ) > 0 ) {
      $hidden .= $this->CreateInputHidden($id,$key,$value);
    }
  }

  if( $hidden != '' ) {
    $smarty->assign('hidden',$hidden);
  }
  $smarty->assign('endform', $this->CreateFormEnd());
}
echo $smarty->fetch($this->GetDatabaseResource($template),$cache_id,$compile_id);
?>