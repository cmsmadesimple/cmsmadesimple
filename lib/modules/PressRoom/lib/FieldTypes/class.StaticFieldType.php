<?php
namespace PressRoom\FieldTypes;
use PressRoom\FieldType;
use PressRoom\FieldDef;
use PressRoom;

class StaticFieldType extends FieldType
{

    private $mod;

    public function __construct( PressRoom $mod )
    {
        $this->mod = $mod;
    }

    public function getName() : string
    {
        return $this->mod->Lang('fldtype_static');
    }

    public function renderForEditor( FieldDef $def )
    {
        $smarty = cmsms()->GetSmarty();
        $tpl = $smarty->createTemplate( $this->mod->GetTemplateResource('StaticFieldAdminEditor.tpl'));
        $tpl->assign('mod',$this->mod);
        $tpl->assign('def',$def);
        return $tpl->fetch();
    }

    public function handleEditorResponse( FieldDef $def, array $params ) : FieldDef
    {
        $text = strip_tags( get_parameter_value( $params, 'staticText') );
        $def->setExtra('staticText', $text);
        return $def;
    }

    public function renderForArticle( FieldDef $def, $value )
    {
        $smarty = cmsms()->GetSmarty();
        $tpl = $smarty->createTemplate( $this->mod->GetTemplateResource('StaticFieldArticleRender.tpl'));
        $tpl->assign('mod', $this->mod);
        $tpl->assign('def', $def);
        $tpl->assign('value', $def->getExtra('staticText') );
        return $tpl->fetch();
    }

    public function handleForArticle( FieldDef $def, array $formdata )
    {
        // nothing here
    }
} // class
