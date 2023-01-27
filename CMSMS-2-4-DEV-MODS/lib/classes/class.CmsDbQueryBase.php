<?php
#CMS - CMS Made Simple
#(c)2004-2012 by Ted Kulp (ted@cmsmadesimple.org)
#Visit our homepage at: http://cmsmadesimple.org
#
#This program is free software; you can redistribute it and/or modify
#it under the terms of the GNU General Public License as published by
#the Free Software Foundation; either version 2 of the License, or
#(at your option) any later version.
#
#This program is distributed in the hope that it will be useful,
#but WITHOUT ANY WARRANTY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
#You should have received a copy of the GNU General Public License
#along with this program; if not, write to the Free Software
#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
#$Id: class.global.inc.php 6939 2011-03-06 00:12:54Z calguy1000 $

/**
 * An abstract class for building database queries and managing results.
 *
 * @since 2.0
 * @package CMS
 * @license GPL
 */

/**
 * An abstract class for building queries and managing results.
 *
 * This class is capable of managing a resultset, and encapsulates conversionof database rows into
 * application objects.
 *
 * @since 2.0
 * @package CMS
 * @license GPL
 * @author Robert Campbell <calguy1000@gmail.com>
 * @property-read array $fields Associative array of the current row of the resultset (read only)
 * @property-read boolean $EOF  Indicates wether the resultset is past the last element (read only)
 * @property-read int $limit The maximum number of rows to return in one resultset (read only)
 * @property-read int $offset The start offset of rows to return when executing the query (read only)
 * @property-read int $totalrows The total number of rows in the database that match the query (read only)
 * @property-read int $numpages The total number of pages of rows that match the query (read only)
 */
abstract class CmsDbQueryBase
{
	/**
	 * The total number of rows matching the query.
	 * This value is populated after execute() is called.
	 *
	 * @see execute()
	 */
    protected $_totalmatchingrows = null;

	/**
	 * The current (integer) offset in the list of results
	 */
    protected $_offset = 0;

	/**
	 * The (integer) page limit.
	 */
    protected $_limit = 1000;

	/**
	 * This member stores the raw database resultset object.
	 */
    protected $_rs = null;

	/**
	 * This member stores the original arguments passed to the constructor and used when generating
	 * the query.
	 */
    protected $_args = array();

	/**
	 * Constructor
	 *
	 * @param mixed $args Accepts an associative array (key=>value) with arguments for the query, or a comma separarated string of arguments.
	 */
    public function __construct($args = '')
    {
        if( empty($args) ) return;

        if( is_array($args) ) {
            $this->_args = $args;
        }
        else if( is_string($args) ) {
            $this->_args = explode(',',$args);
        }
    }

	/**
	 * Execute the query.
	 *
	 * This method should read the parameters, build and execute the database query and populate
	 * the $_totalmatchingrows and $_rs members.
	 *
	 * This method should be smart enough to not execute the database query more than once
	 * independent of how many times it is called.
	 */
    abstract public function execute();

    /**
     * Return the total number of matching records that match the current query
     *
	 * If execute has not already been called, this method will call it.
	 *
	 * @return int
	 */
    public function TotalMatches()
    {
        $this->execute();
        if( $this->_rs ) return $this->_totalmatchingrows;
    }

	/**
	 * Return the number of records that match the the current query
	 * subject to page limits, this method will return either the pagelimit or a lesser value.
	 *
	 * If execute has not already been called, this method will call it.
	 *
	 * @return int
	 */
    public function RecordCount()
    {
        $this->execute();
        if( $this->_rs ) return $this->_rs->RecordCount();
    }


	/**
	 * Modify the resultset object and point to the next record of the matched rows.
	 *
	 * If execute has not been called yet, this method will call it.
	 */
    public function MoveNext()
    {
        $this->execute();
        if( $this->_rs ) return $this->_rs->MoveNext();
    }

	/**
	 * Modify the resultset object and point to the first record of the matched rows.
	 *
	 * If execute has not been called yet, this method will call it.
	 */
    public function MoveFirst()
    {
        $this->execute();
        if( $this->_rs ) return $this->_rs->MoveFirst();
    }

	/**
	 * Modify the resultset object and point to the first record of the matched rows.
	 * This is a synonym for MoveFirst()
	 *
	 * If execute has not been called yet, this method will call it.
	 *
	 * @see MoveFirst()
	 */
    public function Rewind()
    {
        $this->execute();
        if( $this->_rs ) return $this->_rs->MoveFirst();
    }

	/**
	 * Modify the resultset object and point to the last record of the matched rows.
	 *
	 * If execute has not been called yet, this method will call it.
	 */
    public function MoveLast()
    {
        $this->execute();
        if( $this->_rs ) return $this->_rs->MoveLast();
    }

	/**
	 * Test if the resultset is pointing past the last record in the returned set
	 *
	 * @return bool
	 */
    public function EOF()
    {
        $this->execute();
        if( $this->_rs ) return $this->_rs->EOF();
        return TRUE;
    }

	/**
	 * Close the resultset and free any resources it may have claimed.
	 */
    public function Close()
    {
        $this->execute();
        if( $this->_rs ) return $this->_rs->Close();
        return TRUE;
    }

    /**
     * Get the object for the current matching database row.
     *
     * @see $this->fields
     * @return mixed
     */
    abstract public function &GetObject();

	/**
	 * Return an array of matched objects.
     *
     * This method will iterate through all of the rows of the resultset, and convert each resulting
     * row into an object.
	 *
	 * The output of this method depends on the derived class.
	 *
     * @see GetObject()
	 * @return array|null
	 */
    public function GetMatches()
    {
        $this->MoveFirst();
        $out = array();
        while( !$this->EOF() ) {
            $out[] = $this->GetObject();
            $this->MoveNext();
        }
        if( count($out) ) return $out;
    }

	/**
	 * @ignore
	 */
    public function __get($key)
    {
        $this->execute();
        if( $key == 'fields' && $this->_rs && !$this->_rs->EOF() ) return $this->_rs->fields;
        if( $key == 'EOF' ) return $this->_rs->EOF();
        if( $key == 'limit' ) return $this->_limit;
        if( $key == 'offset' ) return $this->_offset;
        if( $key == 'totalrows' ) return $this->_totalmatchingrows;
        if( $key == 'numpages' ) return ceil($this->_totalmatchingrows / $this->_limit);
    }

} // end of class

#
# EOF
#
?>