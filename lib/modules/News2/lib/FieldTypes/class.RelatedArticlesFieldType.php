<?php
namespace News2\FieldTypes;
use News2\FieldType;
use News2\FieldDef;
use News2;

class RelatedArticlesFieldType extends FieldType
{

    private $mod;

    public function __construct( News2 $mod )
    {
        $this->mod = $mod;
    }

    public function getName() : string
    {
        return $this->mod->Lang('fldtype_relatedarticles');
    }

    public function renderForEditor( FieldDef $def )
    {
        $smarty = cmsms()->GetSmarty();
        $tpl = $smarty->createTemplate( $this->mod->GetTemplateResource('RelatedArticlesFieldAdminEditor.tpl'));
        $tpl->assign('mod',$this->mod);
        $tpl->assign('def',$def);
        return $tpl->fetch();
    }

    public function handleEditorResponse( FieldDef $def, array $params ) : FieldDef
    {
        $maxlen = trim( get_parameter_value( $params, 'text_maxsize' ) );
        if( (int) $maxlen < 1 ) $maxlen = null;

        $def->setExtra( 'maxsize', $maxlen );
        return $def;
    }

    public function renderForArticle( FieldDef $def, $value )
    {
        $smarty = cmsms()->GetSmarty();
        $tpl = $smarty->createTemplate( $this->mod->GetTemplateResource('RelatedArticlesArticleRender.tpl'));
        $tpl->assign('mod',$this->mod);
        $tpl->assign('def',$def);
        $tpl->assign('maxsize', $def->getExtra( 'maxsize', 0));
        $tpl->assign('value',$value);
        return $tpl->fetch();
    }

    public function handleForArticle( FieldDef $def, array $formdata )
    {
        return $formdata[$def->name] ?? null;
    }
} // class
