<?php

abstract class AdminSearch_slave
{
    private $_params = array();

    public function set_text($text) {
        $this->set_params(array('search_text'=>$text));
    }

    protected function get_text() {
        if( isset($this->_params['search_text']) ) return $this->_params['search_text'];
    }

    public function set_params($params)
    {
        foreach( $params as $key => $value ) {
            switch( $key ) {
            case 'search_text':
            case 'search_descriptions':
                // valid keys
                break;

            default:
                throw new CmsException('Invalid parameter '.$key.' in search params');
            }
        }

        $this->_params = $params;
    }

    protected function search_descriptions()
    {
        if( isset($this->_params['search_descriptions']) ) return cms_to_bool($this->_params['search_descriptions']);
        return FALSE;
    }

    abstract public function check_permission();
    abstract public function get_name();
    abstract public function get_description();
    abstract public function get_matches();
    public function get_section_description() {}
}

?>