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
            case 'search_casesensitive':
            case 'show_snippets':
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

    protected function search_casesensitive()
    {
        if( isset($this->_params['search_casesensitive']) ) return cms_to_bool($this->_params['search_casesensitive']);
        return FALSE;
    }

    protected function show_snippets()
    {
        if( isset($this->_params['show_snippets']) ) return cms_to_bool($this->_params['show_snippets']);
        return FALSE;
    }

    protected function generate_snippets($content) {
        $search_term = $this->get_text();
        $positions = array();
        $lastPos = 0;

        $strposFunctionName = $this->search_casesensitive() ? 'strpos' : 'stripos';

        while (($lastPos = $strposFunctionName($content, $search_term, $lastPos))!== false) {
                $positions[] = $lastPos;
                $lastPos = $lastPos + strlen($search_term);
        }
         
        $tmp = array();
        foreach ($positions as $pos) {
            $start = max(0,$pos - 50);
            $end = min(strlen($content),$pos+50);
            $text = substr($content,$start,$end-$start);
            $tmp[] = htmlentities($text);
        }

        return $tmp;
    }

    protected function get_resultset($title, $description = '', $edit_url = ''){
        $obj = New StdClass;
        $obj->title = $title;
        $obj->description = $description;
        $obj->edit_url = $edit_url;
        $obj->count = 0;
        $obj->snippets = array();
        $obj->locations = array();
        $obj->search_text = htmlentities($this->get_text());
        $obj->casesensitive = $this->search_casesensitive();

        return $obj;
    }
    
    protected function get_number_of_occurrences($content) {
        if ($this->search_casesensitive()) {
            return substr_count($content,$this->get_text());
        } else {
            return substr_count(strtolower($content),strtolower($this->get_text()));
        }
    }

    protected function process_query_string(&$qry) {
        #check if we need a case sensitive query string
        if (!$this->search_casesensitive()) return;
        #make it happen
        $qry = str_replace('LIKE','COLLATE utf8_bin LIKE', $qry);
    }

    abstract public function check_permission();
    abstract public function get_name();
    abstract public function get_description();
    abstract public function get_matches();
    public function get_section_description() {}
}

?>