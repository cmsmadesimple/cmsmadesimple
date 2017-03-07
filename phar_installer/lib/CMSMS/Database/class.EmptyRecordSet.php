<?php

namespace CMSMS\Database;

class EmptyResultset extends Resultset
{
    public function MoveFirst() {}
    public function MoveNext() {}

    public function GetArray() {}
    public function GetRows() {}
    public function GetAll() {}
    public function GetAssoc() {}

    public function EOF() { return TRUE; }
    public function Close() {}
    public function RecordCount() { return 0; }

    public function fields()
}

?>