<?php
namespace News2\FieldTypes;
use News2\FieldType;
use News2\FieldDef;
use News2;

class TextFieldType extends FieldType
{

    private $mod;

    public function __construct( News2 $mod )
    {
        $this->mod = $mod;
    }

    public function getName() : string
    {
        return $this->mod->Lang('fldtype_text');
    }

    public function renderForEditor( FieldDef $def )
    {
        $smarty = cmsms()->GetSmarty();
        $tpl = $smarty->createTemplate( $this->mod->GetTemplateResource('TextFieldAdminEditor.tpl'));
        $tpl->assign('mod',$this->mod);
        $tpl->assign('def',$def);
        return $tpl->fetch();
    }

    public function handleEditorResponse( FieldDef $def, array $params ) : FieldDef
    {
        $maxlen = trim( get_parameter_value( $params, 'text_maxlen' ) );
        if( (int) $maxlen < 1 ) $maxlen = null;

        $def->setExtra( 'maxlen', $maxlen );
        return $def;
    }

    public function renderForArticle( FieldDef $def, $value )
    {
        $smarty = cmsms()->GetSmarty();
        $tpl = $smarty->createTemplate( $this->mod->GetTemplateResource('TextFieldArticleRender.tpl'));
        $tpl->assign('mod',$this->mod);
        $tpl->assign('def',$def);
        $tpl->assign('maxlen', $def->getExtra( 'maxlen', 0));
        $tpl->assign('value',$value);
        return $tpl->fetch();
    }

    public function handleForArticle( FieldDef $def, array $formdata )
    {
        $val = get_parameter_value( $formdata, $def->name );
        if( $val ) $val = filter_var( $val, FILTER_SANITIZE_STRING );
        return $val;
    }
} // class
