<?php

namespace CMSMS\Database;

abstract class Resultset
{
    public function __destruct()
    {
        $this->Close();
    }

    abstract public function MoveFirst();
    abstract public function MoveNext();
    abstract protected function Move($idx);

    public function GetArray()
    {
        $results = array();
        while( !$this->EOF() ) {
            $results[] = $this->fields();
            $this->MoveNext();
        }
        return $results;
    }

    public function GetRows() { return $this->GetArray(); }
    public function GetAll() { return $this->GetArray(); }
    public function GetAssoc() { return $this->GetArray(); }

    abstract public function EOF();
    abstract public function Close();
    abstract public function RecordCount();
    abstract public function Fields( $field = null );
    public function FetchRow() {
        if( $this->EOF() ) return false;
        $out = $this->fields();
        $this->MoveNext();
        return $out;
    }

    abstract protected function fetch_row();

    public function __get($key)
    {
        if( $key == 'EOF' ) return $this->EOF();
        if( $key == 'fields' ) return $this->Fields();
    }

}