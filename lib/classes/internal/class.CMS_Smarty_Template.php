<?php

class CMS_Smarty_Template extends Smarty_Internal_Template
{
    public function fetch()
    {
        $args = func_get_args();
        $class = get_class($this);
        if( count($args) ) {
            return call_user_func_array(array($this->smarty,'fetch'),$args);
        }
        return parent::fetch();
    }
}
?>