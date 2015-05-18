<?php

namespace CMSMS\internal;

class global_cachable
{
    private $_name;
    private $_fetchcb;

    public function __construct($name,callable $fetch_fn)
    {
        $this->_name = trim($name);
        $this->_fetchcb = $fetch_fn;
    }

    public function get_name()
    {
        return $this->_name;
    }

    public function fetch()
    {
        $fn = $this->_fetchcb;
        return $fn();
    }

} // end of class

?>