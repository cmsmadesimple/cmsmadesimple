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
 * This file defines the ConnectionSpec class.
 *
 * @package CMS
 */

namespace CMSMS\Database;

/**
 * A class defining all of the details needed to connect to a database.
 * Some database drivers may not require all of the parameters.
 *
 * @package CMS
 * @author Robert Campbell
 * @copyright Copyright (c) 2015, Robert Campbell <calguy1000@cmsmadesimple.org>
 * @since 2.2
 * @param string $type The database connection type.  Defaults to 'mysqli'.
 * @param string $host The hostname to connect to.
 * @param string $username The authentication username
 * @param string $password The authentication password
 * @param string $dbname The database name
 * @param string $prefix The table name prefix.
 * @param int    $port   The connection port
 * @param bool   $persistent Wether or not to use persistent connections.
 * @param bool   $debug  Enable debug mode.
 */
class ConnectionSpec
{
    /**
     * @ignore
     */
    private $_data = array('type'=>'mysqli','host'=>null,'username'=>null,'password'=>null,
                           'dbname'=>null,'prefix'=>null,'port'=>null,'persistent'=>false,'debug'=>false,
                           'auto_exec'=>null);

    /**
     * @ignore
     */
    public function __get($key)
    {
        if( !array_key_exists($key,$this->_data) ) throw new \InvalidArgumentException("$key is not a valid member of ".__CLASS__);
        return $this->_data[$key];
    }

    /**
     * @ignore
     */
    public function __set($key,$val)
    {
        if( !array_key_exists($key,$this->_data) ) throw new \InvalidArgumentException("$key is not a valid member of ".__CLASS__);
        $this->_data[$key] = trim($val);
    }

    /**
     * Test if this connectionspec is valid.
     * Returns true if there is enough information to connect to the database.
     *
     * @return bool
     */
    public function valid()
    {
        if( !$this->type || !$this->host || !$this->username || !$this->password || !$this->dbname ) return FALSE;
        return TRUE;
    }
}

/**
 * A special exception to indicate a problem with a ConnectionSpec
 */
class ConnectionSpecException extends \Exception {}

?>
