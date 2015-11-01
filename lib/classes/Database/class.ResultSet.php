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
    abstract public function MoveLast();

    abstract public function GetArray();
    public function GetRows() { return $this->GetArray(); }
    public function GetAll() { return $this->GetArray(); }
    public function GetAssoc() { return $this->GetArray(); }

    abstract public function EOF();
    abstract public function Close();
    abstract public function RecordCount();
    abstract public function fields( $field = null );
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
        if( $key == 'fields' ) return $this->fields();
    }
}