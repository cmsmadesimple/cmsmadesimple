<?php
#BEGIN_LICENSE
#-------------------------------------------------------------------------
# Module: \CMSMS\Database\ConnectionSpec (c) 2015 by Robert Campbell
#         (calguy1000@cmsmadesimple.org)
#  A class to define how to connect to a database.
#
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2005 by Ted Kulp (wishy@cmsmadesimple.org)
# Visit our homepage at: http://www.cmsmadesimple.org
#
#-------------------------------------------------------------------------
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# However, as a special exception to the GPL, this software is distributed
# as an addon module to CMS Made Simple.  You may not use this software
# in any Non GPL version of CMS Made simple, or in any version of CMS
# Made simple that does not indicate clearly and obviously in its admin
# section that the site was built with CMS Made simple.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
# Or read it online: http://www.gnu.org/licenses/licenses.html#GPL
#
#-------------------------------------------------------------------------
#END_LICENSE

/**
 * This file defines the base ResultSet class.
 *
 * @package CMS
 */

namespace CMSMS\Database;

/**
 * A class defining a resultset and how to interact with results from a database query.
 *
 * @package CMS
 * @author Robert Campbell
 * @copyright Copyright (c) 2015, Robert Campbell <calguy1000@cmsmadesimple.org>
 * @since 2.2
 * @property-read bool $EOF Test if we are at the end of the current resultset.
 * @property-read array $fields Return the current row of the resultset.
 */
abstract class Resultset
{
    /**
     * @ignore
     */
    public function __destruct()
    {
        $this->Close();
    }

    /**
     * Move to the first row in a resultset.
     */
    abstract public function MoveFirst();

    /**
     * Move to the next row of a resultset.
     */
    abstract public function MoveNext();

    /**
     * Move to a specified index of the resultset.
     *
     * @param int $idx
     */
    abstract protected function Move($idx);

    /**
     * Get all remaining results in this resultset as an array of records.
     *
     * @return array
     */
    public function GetArray()
    {
        $results = array();
        while( !$this->EOF() ) {
            $results[] = $this->fields();
            $this->MoveNext();
        }
        return $results;
    }

    /**
     * An alias for the GetArray method.
     *
     * @see GetArray()
     * @return array
     * @deprecated
     */
    public function GetRows() { return $this->GetArray(); }

    /**
     * An alias for the GetArray method.
     * @see GetArray()
     * @return array
     * @deprecated
     */
    public function GetAll() { return $this->GetArray(); }

    /**
     * Get an associative array from a resultset.
     *
     * If only two columns are returned in the resultset, the keys of the returned associative array
     * will be the value of the first column, and the value of each key will be the value from the second column.
     *
     * If more than 2 columns are returned, then the key of the returned associative array will be the
     * value from the first column, and the value of each key will be an associative array of the remaining columns.
     * This is known as array behavior.
     *
     * @deprecated
     * @param boolean force_array Force array behavior, even if there are only two columns in the resulting SQL.
     * @param boolean first2cols The opposite of force_array.  Only output the data from the first 2 columns as an associative array.
     * @return array
     */
    public function GetAssoc($force_array = false, $first2cols = false)
    {
        $data = null;
        $first_row = $this->Fields();
        if( count($first_row) < 2 ) return $data;

        $data = [];
        $keys = array_keys($first_row);
        $numeric_index = isset($row[0]);
        if( !$first2cols && (count($keys) > 2 || $force_array) ) {
            // output key is first column
            // other columns as assoc
            $first_key = $keys[0];
            while( !$this->EOF() ) {
                $row = $this->Fields();
                $data[trim($row[$first_key])] = array_slice($row,1);
                $this->MoveNext();
            }
        } else {
            // only 2 columns... output a single associative
            while( !$this->EOF() ) {
                $row = $this->Fields();
                $data[trim($row[$keys[0]])] = $row[$keys[1]];
                $this->MoveNext();
            }
        }
        return $data;
    }

    /**
     * Test if we are at the end of a resultset, and there are no further matches.
     *
     * @return bool
     */
    abstract public function EOF();

    /**
     * Close the current resultset.
     */
    abstract public function Close();

    /**
     * Return the number of rows in the current resultset.
     *
     * @return int
     */
    abstract public function RecordCount();

    /**
     * Alias for the RecordCount() method.
     *
     * @see RecordCount();
     * @return int
     */
    public function NumRows() { return $this->RecordCount(); }

    /**
     * Return the fields of the current resultset, or a single field of it.
     *
     * @param string $field An optional field name, if not specified, the entire row will be returned.
     * @return mixed|array Either a single value, or an array
     */
    abstract public function Fields( $field = null );

    /**
     * Fetch the current row, and move to the next row.
     *
     * @return array
     */
    public function FetchRow() {
        if( $this->EOF() ) return false;
        $out = $this->fields();
        $this->MoveNext();
        return $out;
    }

    /**
     * @internal
     */
    abstract protected function fetch_row();

    /**
     * @ignore
     */
    public function __get($key)
    {
        if( $key == 'EOF' ) return $this->EOF();
        if( $key == 'fields' ) return $this->Fields();
    }

}
