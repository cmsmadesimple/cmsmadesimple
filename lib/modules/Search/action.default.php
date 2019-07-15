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

$tpl_ob = $smarty->CreateTemplate($this->GetTemplateResource($template),null,null,$smarty);
$inline = (isset($params['inline'])) ? cms_to_bool(trim($params['inline'])) : false;
$origreturnid = $returnid;
if( isset( $params['resultpage'] ) ) {
    $manager = $gCms->GetHierarchyManager();
    $node = $manager->sureGetNodeByAlias($params['resultpage']);
    if (isset($node)) {
        $returnid = $node->getID();
    }
    else if( (int) $params['resultpage'] > 0 ) {
        $node = $manager->sureGetNodeById((int) $params['resultpage']);
        if (isset($node)) $returnid = $params['resultpage'];
    }
    if( !$node ) cms_warning('Could not resolve resultpage of '.$params['resultpage'].' to an id','Search');
}
//Pretty Urls Compatibility
$is_method = isset($params['search_method'])?'post':'get';

$submittext = (isset($params['submit'])) ? $params['submit'] : $this->Lang('searchsubmit');
$searchtext = (isset($params['searchtext'])) ? $params['searchtext'] : $this->GetPreference('searchtext',$this->Lang('search'));
$tpl_ob->assign('search_actionid',$id);
$tpl_ob->assign('searchtext',$searchtext);
$tpl_ob->assign('destpage',$returnid);
$tpl_ob->assign('form_method',$is_method);
$tpl_ob->assign('inline',$inline);
$tpl_ob->assign('searchprompt', $searchtext);
$tpl_ob->assign('submittext', $submittext);
$formparms = null;
if( $origreturnid != $returnid ) $formparms['origreturnid'] = $origreturnid;
if( isset($params['modules']) ) $formparms['modules'] = $params['modules'];
if( isset($params['detailpage']) ) $formparms['detailpage'] = $params['detaillpage'];
if( isset($params['use_like']) ) $formparms['use_like'] = $params['use_like'];

// for compatibility
$tpl_ob->assign('startform', $this->CreateFormStart($id, 'dosearch', $returnid, $is_method, $formparms, $inline, '', $formparms ));
$tpl_ob->assign('label', '<label for="'.$id.'searchinput">'.$this->Lang('search').'</label>');
$hogan = "onfocus=\"if(this.value==this.defaultValue) this.value='';\""." onblur=\"if(this.value=='') this.value=this.defaultValue;\"";
$tpl_ob->assign('hogan',$hogan);

$hidden = '';
/*
if( $origreturnid != $returnid ) $hidden .= $this->CreateInputHidden($id, 'origreturnid', $origreturnid);
if( isset( $params['modules'] ) ) $hidden .= $this->CreateInputHidden( $id, 'modules', trim($params['modules']) );
if( isset( $params['detailpage'] ) ) $hidden .= $this->CreateInputHidden( $id, 'detailpage', trim($params['detailpage']) );
if( isset( $params['uselike']) ) $hidden .= $this->CreateInputHidden($id, 'uselike', trim($params['uselike']))
*/
foreach( $params as $key => $value ) {
    if( preg_match( '/^passthru_/', $key ) > 0 ) $hidden .= $this->CreateInputHidden($id,$key,$value);
}

if( $hidden != '' ) $tpl_ob->assign('hidden',$hidden);
$tpl_ob->assign('endform', $this->CreateFormEnd());
$tpl_ob->assign('formparms',$formparms);
$tpl_ob->display();
