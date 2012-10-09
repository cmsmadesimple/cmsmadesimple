<?php

function smarty_cms_function_form_start($params, &$template)
{
  $smarty   = $template->smarty;
  $tagparms = array();
  $mactparms = array();
  $tmp = $smarty->get_template_vars('actionparams');
  if( is_array($tmp) && isset($tmp['action']) ) {
    $mactparms['action'] = $tmp['action'];
  }
  $mactparms['module'] = $smarty->get_template_vars('actionmodule');
  $mactparms['mid'] = $smarty->get_template_vars('actionid');
  $mactparms['returnid'] = $smarty->get_template_vars('returnid');
  $mactparms['inline'] = 0;
  $tagparms['method'] = 'POST';
  $tagparms['enctype'] = 'multipart/form-data';
  $tagparms['action'] = 'moduleinterface.php';
  if( cmsms()->test_state(CmsApp::STATE_ADMIN_PAGE) ) {
    if( !isset($mactparms['action']) ) {
      $mactparms['action'] = 'defaultadmin';
    }
    $mactparms['returnid'] = '';
  }
  else if( cmsms()->is_frontend_request() ) {
    if( !isset($mactparms['action']) ) {
      $mactparms['action'] = 'default';
    }
    $mactparms['mid'] = 'cntnt01';
  }

  if( $mactparms['returnid'] != '' ) {
    $hm = $gCms->GetHierarchyManager();
    $node = $hm->sureGetNodeById($returnid);
    if( $node ) {
      $content_obj = $node->getContent();
      if( $content_obj ) $tagparms['action'] = $content_obj->GetURL();
    }
  }

  $parms = array();
  foreach( $params as $key => $value ) {
    switch( $key ) {
    case 'module':
    case 'action':
    case 'mid':
    case 'returnid':
    case 'inline':
      $mactparms[$key] = trim($value);
      break;

    case 'method':
    case 'enctype':
    case 'id':
    case 'class':
      $tagparms[$key] = trim($value);
      break;

    case 'extraparms':
      if( is_array($value) && count($value) ) {
	foreach( $value as $key=>$value2 ) {
	  $parms[$key] = $value2;
	}
      }
      break;
    case 'assign':
      break;

    default:
      $parms[$key] = $value;
      break;
    }
  }

  $out = '<form';
  foreach( $tagparms as $key => $value ) {
    if( $value ) {
      $out .= " $key=\"$value\"";
    }
  }
  $out .= '><div class="hidden">';
  $mact = $mactparms['module'].','.$mactparms['mid'].','.$mactparms['action'].','.$mactparms['inline'];
  $out .= '<input type="hidden" name="mact" value="'.$mact.'"/>';
  if( $mactparms['returnid'] != '' ) {
    $out .= '<input type="hidden" name="'.$mactparms['mid'].'returnid" value="'.$mactparms['returnid'].'"/>';
  } else {
    $out .= '<input type="hidden" name="'.CMS_SECURE_PARAM_NAME.'" value="'.$_SESSION[CMS_USER_KEY].'"/>';
  }
  foreach( $parms as $key => $value ) {
    $out .= '<input type="hidden" name="'.$mactparms['mid'].$key.'" value="'.$value.'"/>';
  }
  $out .= '</div>';

  if( isset($params['assign']) ) {
    $smarty->assign($params['assign'],$out);
    return;
  }
  return $out;
}

?>