<?php

/**
 * A file to describe an empty recordset
 *
 * @package CMS
 * @ignore
 */
namespace CMSMS\Database;

/**
 * A final class to describe a special (empty) recordset.
 *
 * @package CMS
 * @ignore
 */
final class EmptyRecordset extends Resultset
{
    /**
     * @ignore
     */
    public function MoveFirst() {
    }
    /**
     * @ignore
     */
    public function MoveNext() {
    }

    /**
     * @ignore
     */
    public function GetArray() {
    }
    /**
     * @ignore
     */
    public function GetRows() {
    }
    /**
     * @ignore
     */
    public function GetAll() {
    }
    /**
     * @ignore
     */
    public function GetAssoc() {
    }

    /**
     * @ignore
     */
    public function EOF() { return TRUE;
    }
    /**
     * @ignore
     */
    public function Close() {
    }
    /**
     * @ignore
     */
    public function RecordCount() { return 0;
    }

    /**
     * @ignore
     */
    public function fields() {
    }
} // end of class
