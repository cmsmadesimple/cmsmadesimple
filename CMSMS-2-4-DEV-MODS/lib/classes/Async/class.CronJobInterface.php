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
 * This file defines the protocol for a recurring job.
 *
 * @package CMS
 */

namespace CMSMS\Async;

/**
 * A simple interface to define the functions and constants needed for a cron job.
 *
 * @package CMS
 * @author Robert Campbell
 * @copyright Copyright (c) 2015, Robert Campbell <calguy1000@cmsmadesimple.org>
 * @since 2.2
 */
interface CronJobInterface
{
    /**
     * Constant indicating that this job does not recur (empty string is also used).
     */
    const RECUR_NONE = '_none';

    /**
     * Constant indicating that ths job should recur every 15 minutes.
     */
    const RECUR_15M = '_15m';

    /**
     * Constant indicating that ths job should recur every 30 minutes.
     */
    const RECUR_30M = '_30m';

    /**
     * Constant indicating that ths job should recur every hour.
     */
    const RECUR_HOURLY = '_hourly';

    /**
     * Constant indicating that ths job should recur every 2 hours.
     */
    const RECUR_120M = '_120m';
    const RECUR_2H = '_120m';

    /**
     * Constant indicating that ths job should recur every 3 hours.
     */
    const RECUR_180M = '_180m';
    const RECUR_3H = '_180m';

    /**
     * Constant indicating that ths job should recur every 12 hours..
     */
    const RECUR_12H = '_720m';

    /**
     * Constant indicating that ths job should recur daily.
     */
    const RECUR_DAILY = '_daily';

    /**
     * Constant indicating that ths job should recur weekly.
     */
    const RECUR_WEEKLY = '_weekly';

    /**
     * Constant indicating that ths job should recur monthly.
     */
    const RECUR_MONTHLY = '_monthly';
}