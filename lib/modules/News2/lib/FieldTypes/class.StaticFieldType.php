<?php
namespace News2\FieldTypes;
use News2\FieldType;
use News2\FieldDef;
use News2;

class StaticFieldType extends FieldType
{

    private $mod;

    public function __construct( News2 $mod )
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
