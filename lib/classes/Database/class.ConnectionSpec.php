<?php

namespace CMSMS\Database;

class ConnectionSpec
{
    private $_data = array('type'=>null,'host'=>null,'username'=>null,'password'=>null,
                           'dbname'=>null,'prefix'=>null,'port'=>null,'persistent'=>false,'debug'=>false);

    public function __get($key)
    {
        if( !array_key_exists($key,$this->_data) ) throw new \InvalidArgumentException("$key is not a valid member of ".__CLASS__);
        return $this->_data[$key];
    }

    public function __set($key,$val)
    {
        if( !array_key_exists($key,$this->_data) ) throw new \InvalidArgumentException("$key is not a valid member of ".__CLASS__);
        $this->_data[$key] = trim($val);
    }

    public function valid()
    {
        if( !$this->type || !$this->host || !$this->username || !$this->password || !$this->dbname ) return FALSE;
        return TRUE;
    }
}

class ConnectionSpecException extends \Exception {}

?>