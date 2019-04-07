<?php
namespace PressRoom\FieldTypes;
use PressRoom\FieldType;
use PressRoom\FieldDef;
use PressRoom;

class SeparatorFieldType extends FieldType
{

    private $mod;

    public function __construct( PressRoom $mod )
    {
        $this->mod = $mod;
    }

    public function getName() : string
    {
        return $this->mod->Lang('fldtype_separator');
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
        $tpl = $smarty->createTemplate( $this->mod->GetTemplateResource('SeparatorFieldArticleRender.tpl'));
        $tpl->assign('mod', $this->mod);
        $tpl->assign('def', $def);
        return $tpl->fetch();
    }

    public function handleForArticle( FieldDef $def, array $formdata )
    {
        // nothing here
    }
} // class
