<?php

namespace __appbase\tests;

class matchall_test extends test_base
{
    private $_children;

    public function __construct($name)
    {
        parent::__construct($name,'');
    }

    public function add_child(test_base $obj)
    {
        if( !is_array($this->_children) ) $this->_children = array();
        $this->_children[] = $obj;
    }


    public function __set($key,$value)
    {
        switch( $key ){
        case 'minimum':
        case 'maximum':
        case 'recommended':
        case 'success_key':
        case 'pass_key':
        case 'fail_key':
            $this->$key = $value;
            break;

        default:
            parent::__set($key,$value);
        }
    }

    public function execute()
    {
        $out = self::TEST_PASS;
        if( count($this->_children) ) {
            for( $i = 0; $i < count($this->_children); $i++ ) {
                $res = $this->_children[$i]->run();
                if( $res == self::TEST_FAIL ) {
                    // test failed.... if this test is not required, we can continue
                    if( $this->required ) return $res;
                    $out = self::TEST_WARN;
                }
            }
        }
        return $out;
    }

    public function msg()
    {
        switch( $this->status ) {
        case self::TEST_FAIL:
            for( $i = 0; $i < count($this->_children); $i++ ) {
                $obj = $this->_children[$i];
                if( $obj->status == self::TEST_FAIL ) {
                    if( $obj->fail_msg ) return $obj->fail_msg;
                    if( $obj->fail_key ) return \__appbase\lang($obj->fail_key);
                }
            }
            break;

        case self::TEST_WARN:
            for( $i = 0; $i < count($this->_children); $i++ ) {
                $obj = $this->_children[$i];
                if( $obj->status == self::TEST_FAIL ) {
                    if( $obj->warn_msg ) return $obj->warn_msg;
                    if( $obj->warn_key ) return \__appbase\lang($obj->warn_key);
                }
            }
        }

        return parent::msg();
    }
} // end of class

?>