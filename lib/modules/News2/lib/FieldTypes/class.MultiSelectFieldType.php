<?php
namespace News2\FieldTypes;
use News2\FieldType;
use News2\FieldDef;
use News2;

class MultiSelectFieldType extends SelectFieldType
{
    public function getName() : string
    {
        return $this->mod->Lang('fldtype_multiselect');
    }

    public function render( FieldDef $def, $value )
    {
        die(__METHOD__);
    }

    public function renderForArticle( FieldDef $def, $value )
    {
        $options = $this->getArrayFromText( $def->getExtra('optionsText'));
        $size = 3;
        if( !empty($options) ) $size = min($size,max(20,count($options)));
        $smarty = cmsms()->GetSmarty();
        $tpl = $smarty->createTemplate( $this->mod->GetTemplateResource('MultiSelectFieldArticleRender.tpl'));
        $tpl->assign('mod',$this->mod);
        $tpl->assign('def',$def);
        $tpl->assign('options',$options);
        $tpl->assign('value',$value);
        $tpl->assign('size',$size);
        return $tpl->fetch();
    }

    public function handleForArticle( FieldDef $def, array $formdata )
    {
        $options = $this->getArrayFromText( $def->getExtra('optionsText') );
        $val = get_parameter_value( $formdata, $def->name );
        if( !is_array($val) ) $val = [ $val ];
        $out = null;
        foreach( $val as $one ) {
            if( isset($options[$one]) ) $out[] = $one;
        }
        return $out;
    }
} // class