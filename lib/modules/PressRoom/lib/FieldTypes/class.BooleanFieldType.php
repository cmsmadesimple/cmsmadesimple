<?php
namespace PressRoom\FieldTypes;
use PressRoom\FieldType;
use PressRoom\FieldDef;
use PressRoom;

class BooleanFieldType extends FieldType
{

    private $mod;

    public function __construct( PressRoom $mod )
    {
        $this->mod = $mod;
    }

    public function getName() : string
    {
        return $this->mod->Lang('fldtype_bool');
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
        $tpl = $smarty->createTemplate( $this->mod->GetTemplateResource('BooleanFieldArticleRender.tpl'));
        $tpl->assign('mod',$this->mod);
        $tpl->assign('def',$def);
        $tpl->assign('value',$value);
        return $tpl->fetch();
    }

    public function handleForArticle( FieldDef $def, array $formdata )
    {
        $val = get_parameter_value( $formdata, $def->name );
        $val = cms_to_bool($val);
        return $val;
    }
} // class
