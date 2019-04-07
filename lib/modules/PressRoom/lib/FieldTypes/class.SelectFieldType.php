<?php
namespace PressRoom\FieldTypes;
use PressRoom\FieldType;
use PressRoom\FieldDef;
use PressRoom;

class SelectFieldType extends FieldType
{

    protected $mod;

    public function __construct( PressRoom $mod )
    {
        $this->mod = $mod;
    }

    public function getName() : string
    {
        return $this->mod->Lang('fldtype_select');
    }

    public function handleEditorResponse( FieldDef $def, array $data ) : FieldDef
    {
        $def->setExtra('optionsText',get_parameter_value( $data, 'optionsText'));
        return $def;
    }

    public function renderForEditor( FieldDef $def )
    {
        $smarty = cmsms()->GetSmarty();
        $tpl = $smarty->createTemplate( $this->mod->GetTemplateResource('SelectFieldAdminEditor.tpl'));
        $tpl->assign('mod',$this->mod);
        $tpl->assign('def',$def);
        $txt = $tpl->fetch();
        return $txt;
    }

    protected function getArrayFromText( $str )
    {
        $out = null;
        $lines = explode( "\n", $str );
        foreach( $lines as $line ) {
            $line = trim($line);
            if( !$line ) continue;
            list($key,$val) = explode('=',$line,2);
            $key = trim($key);
            $val = trim($val);
            if( !$key && !$val ) continue;
            if( !$key ) $key = $val;
            else if( !$val ) $val = $key;
            $out[$key] = $val;
        }
        return $out;
    }

    public function renderForArticle( FieldDef $def, $value )
    {
        $smarty = cmsms()->GetSmarty();
        $tpl = $smarty->createTemplate( $this->mod->GetTemplateResource('SelectFieldArticleRender.tpl'));
        $tpl->assign('mod',$this->mod);
        $tpl->assign('def',$def);
        $tpl->assign('options',$this->getArrayFromText( $def->getExtra('optionsText') ) );
        $tpl->assign('value',$value);
        return $tpl->fetch();
    }

    public function handleForArticle( FieldDef $def, array $formdata )
    {
        $options = $this->getArrayFromText( $def->getExtra('optionsText') );
        $val = get_parameter_value( $formdata, $def->name );
        if( isset( $options[$val]) ) return $val;
    }
} // class
