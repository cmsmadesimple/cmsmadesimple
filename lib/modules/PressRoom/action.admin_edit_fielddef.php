<?php
namespace PressRoom;
use PressRoom;

if( !isset($gCms) ) exit;
if( !$this->CheckPermission('Modify Site Preferences') ) exit;

try {
    $fdtype_class = $fielddef = null;
    $fdm = $this->fielddefManager();
    $fdid = get_parameter_value( $params, 'fdid' );
    if( $fdid > 0 ) {
        $fielddef = $fdm->loadByID( $fdid );
        $fdtype_class = $fielddef->type;
    }
    $fdtype_class = get_parameter_value( $_REQUEST, 'fldtype', $fdtype_class );
    $fdtype_class = get_parameter_value( $params, 'fldtype', $fdtype_class );
    if( !$fdtype_class ) throw new \LogicException( 'Missing or invalid parameter (fldtype)');
    $fldtype = $this->fieldTypeManager()->getByClass( $fdtype_class );
    if( !$fldtype ) throw new \LogicException( "Unknown field type class $fldtype_class" );
    if( $fdid < 1 ) {
        $fielddef = $fdm->createNewOfType( $fldtype );
    }
    else {
        $fielddef = $fdm->loadById( $fdid );
    }

    if( isset($_POST['cancel']) ) {
        $this->SetMessage( $this->Lang('msg_cancelled') );
        $this->RedirectToAdminTab( 'fielddefs', null, 'admin_settings' );
    }
    else if( isset( $_POST['submit']) ) {
        try {
            $fielddef->name = filter_var( $_POST['name'], FILTER_SANITIZE_STRING );
            $fielddef->label = filter_var( $_POST['label'], FILTER_SANITIZE_STRING );
            $fielddef = $fldtype->handleEditorResponse( $fielddef, $_POST );
            /*
            $fielddef->type = filter_var( $_POST['type'], FILTER_SANITIZE_STRING );
            $fielddef->optionsText = filter_var( $_POST['optionsText'], FILTER_SANITIZE_STRING );
            */
            if( !preg_match( '/^[a-zA-Z_][a-zA-Z0-9_]*$/', $fielddef->name) ) {
                throw new \RuntimeException( $this->Lang('err_fieldname') );
            }
            $hm = $this->cms->get_hook_manager();
            $hm->emit( 'PressRoom::beforeEditFielddef', $fielddef );
            $fdm->save( $fielddef );
            $fielddef = $hm->emit( 'PressRoom::afterEditFielddef', $fielddef );
            $this->SetMessage( $this->Lang('msg_saved') );
            if( $fielddef->id ) {
                audit($fielddef->id, $this->GetName(), 'fielddef '.$fielddef->name.' updated');
            } else {
                audit($this->GetName(), $this->GetName(), 'fielddef '.$fielddef->name.' created');
            }
            $this->RedirectToAdminTab('fielddefs',null,'admin_settings');
        }
        catch( \Exception $e ) {
            echo $this->ShowErrors( $e->GetMessage() );
        }
    }


    $tpl = $smarty->CreateTemplate( $this->GetTemplateResource('admin_edit_fielddef.tpl'), null, null, $smarty );
    $tpl->assign('type',$fldtype);
    $tpl->assign('obj',$fielddef);
    $tpl->display();
}
catch( \Exception $e ) {
    $this->SetError( $e->GetMessage() );
    $this->RedirectToAdminTab( 'fielddefs', null, 'admin_settings' );
}
