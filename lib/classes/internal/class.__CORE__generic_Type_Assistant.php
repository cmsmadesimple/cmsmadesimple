<?php

/**
 * @ignore
 */
namespace CMSMS\internal;

/**
 * @since 2.2
 */
class __CORE__generic_Type_Assistant extends \CMSMS\Layout\TemplateTypeAssistant
{
    public function &get_type()
    {

    }

    public function get_usage_string($name)
    {
        $name = trim($name);
        if( !$name ) return;
        $pattern = '{include file=\'cms_template:%s\'}';
        return sprintf($pattern,$name);
    }
}
