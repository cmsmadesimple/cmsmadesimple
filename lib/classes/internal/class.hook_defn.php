<?php
namespace CMSMS\internal;

class hook_defn
{

    /**
     * @ignore
     */
    public $name;

    /**
     * @ignore
     */
    public $handlers = [];

    /**
     * @ignore
     */
    public $sorted;

    /**
     * @ignore
     */
    public function __construct($name)
    {
        $this->name = $name;
    }
} // class
