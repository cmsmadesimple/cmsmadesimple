<?php
namespace PressRoom\FieldTypes;
use PressRoom\FieldType;
use PressRoom\FieldDef;
use PressRoom;

class NumberFieldType extends FieldType
{

    private $mod;

    public function __construct( PressRoom $mod )
    {
        $this->mod = $mod;
    }

    public function getName() : string
    {
        return $this->mod->Lang('fldtype_number');
    }

    public function handleEditorResponse( FieldDef $def, array $data ) : FieldDef
    {
        $get_num_or_null = function( $data, $key ) {
            $tmp = trim(get_parameter_value( $data, $key ));
            if( strlen($tmp) == 0 ) return;
            return (float) $tmp;
        };

        $def->setExtra('minval',$get_num_or_null($data,'num_minval'));
        $def->setExtra('maxval',$get_num_or_null($data,'num_maxval'));
        $def->setExtra('stepval',$get_num_or_null($data,'num_stepval'));
        return $def;
    }

    public function renderForEditor( FieldDef $def )
    {
        $smarty = cmsms()->GetSmarty();
        $tpl = $smarty->createTemplate( $this->mod->GetTemplateResource('NumberFieldAdminEditor.tpl'));
        $tpl->assign('mod',$this->mod);
        $tpl->assign('def',$def);
        return $tpl->fetch();
    }

    public function renderForArticle( FieldDef $def, $value )
    {
        $smarty = cmsms()->GetSmarty();
        $tpl = $smarty->createTemplate( $this->mod->GetTemplateResource('NumberFieldArticleRender.tpl'));
        $tpl->assign('mod',$this->mod);
        $tpl->assign('def',$def);
        $tpl->assign('minval', $def->getExtra( 'minval', ''));
        $tpl->assign('maxval', $def->getExtra( 'maxval', ''));
        $tpl->assign('stepval', $def->getExtra( 'stepval', ''));
        $tpl->assign('value',$value);
        return $tpl->fetch();
    }

    public function handleForArticle( FieldDef $def, array $formdata )
    {
        $val = get_parameter_value( $formdata, $def->name );
        if( $val ) $val = filter_var( $val, FILTER_SANITIZE_STRING );
        return (float) $val;
    }
} // class
