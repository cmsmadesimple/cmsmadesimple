<?php
#BEGIN_LICENSE
#-------------------------------------------------------------------------
# Module: \CMSMS\Database\Connection (c) 2016 by Robert Campbell
#         (calguy1000@cmsmadesimple.org)
#  A class to define interaction with a database.
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
 * This file defines the primary functionality for a cron job.
 *
 * @package CMS
 */

namespace CMSMS\Async;

/**
 * A Trait to define functionality for recurring cron jobs.
 *
 * @package CMS
 * @author Robert Campbell
 * @copyright Copyright (c) 2015, Robert Campbell <calguy1000@cmsmadesimple.org>
 * @since 2.2
 * @property string $frequency The frequency of the cron job
 * @property int $start The minimum start time of the cron job.  This property is adjusted after each and every execution of the cron job.
 * @property int $until A unix timestamp representing when the cron job should stop recurring.
 */
trait CronJobTrait
{
    /**
     * @ignore
     */
    private $_data = [ 'start'=>null, 'frequency' => self::RECUR_NONE, 'until'=>null  ];

    /**
     * @ignore
     */
    public function __get($key)
    {
        switch( $key ) {
        case 'frequency':
            return trim($this->_data[$key]);

        case 'start':
        case 'until':
            return (int) $this->_data[$key];

        default:
            return parent::__get($key);
        }
    }

    /**
     * @ignore
     */
    public function __set($key,$val)
    {
        switch( $key ) {
        case 'frequency':
            switch( $val ) {
            case self::RECUR_NONE:
            case self::RECUR_15M:
            case self::RECUR_30M:
            case self::RECUR_HOURLY:
            case self::RECUR_2H:
            case self::RECUR_3H:
            case self::RECUR_12H:
            case self::RECUR_DAILY:
            case self::RECUR_WEEKLY:
            case self::RECUR_MONTHLY:
                $this->_data[$key] = $val;
                break;
            default:
                throw new \LogicException("$val is an invalid value for $key");
            }
            break;

        case 'force_start':
            // internal use only.
            $this->_data['start'] = (int) $val;
            break;

        case 'start':
            // this start overrides the one in the base class.
            $val = (int) $val;
            if( $val < time() - 60 ) throw new \LogicException('Cannot adjust a start value to the past');
            // fall through.

        case 'until':
            $this->_data[$key] = (int) $val;
            break;

        default:
            return parent::__set($key,$val);
        }
    }
}