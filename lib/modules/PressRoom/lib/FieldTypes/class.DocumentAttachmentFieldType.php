<?php
namespace PressRoom\FieldTypes;
use PressRoom\FieldType;
use PressRoom\FieldDef;
use PressRoom;

class DocumentAttachmentFieldType extends FieldType
{

    private $mod;

    public function __construct( PressRoom $mod )
    {
        $this->mod = $mod;
    }

    public function getName() : string
    {
        return $this->mod->Lang('fldtype_documentattachment');
    }

    public function renderForEditor( FieldDef $def )
    {
        // nothing here
    }

    public function handleEditorResponse( FieldDef $def, array $params ) : FieldDef
    {
        return $def;
    }

    public function renderForArticle( FieldDef $def, $value )
    {
        $smarty = cmsms()->GetSmarty();
        $tpl = $smarty->createTemplate( $this->mod->GetTemplateResource('DocumentAttachmentFieldArticleRender.tpl'));
        $tpl->assign('mod',$this->mod);
        $tpl->assign('def',$def);
        $tpl->assign('value',$value);
        return $tpl->fetch();
    }

    public function handleForArticle( FieldDef $def, array $formdata )
    {
        // todo: clean this
        $val = get_parameter_value( $formdata, $def->name );
        return $val;
    }
} // class
