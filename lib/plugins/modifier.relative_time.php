<?php
#---------------------------------------------------------------------------
# CMS Made Simple - Power for the professional, Simplicity for the end user.
# (c) 2004 - 2011 by Ted Kulp
# (c) 2011 - 2018 by the CMS Made Simple Development Team
# (c) 2018 and beyond by the CMS Made Simple Foundation
# This project's homepage is: https://www.cmsmadesimple.org
#---------------------------------------------------------------------------
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
# Or read it online: http://www.gnu.org/licenses/licenses.html#GPL
#---------------------------------------------------------------------------

/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty relative date / time plugin
 *
 * Type:     modifier<br>
 * Name:     relative_datetime<br>
 * Date:     March 18, 2009
 * Purpose:  converts a date to a relative time
 * Input:    date to format
 * Example:  {$datetime|relative_datetime}
 * @author   Eric Lamb <eric@ericlamb.net>
 * @version 1.0
 * @param string
 * @return string
 */

/*
 * This modifier modified by calguy1000 to be compatible with CMSMS.
 */

function smarty_modifier_relative_time($timestamp)
{
    if(!$timestamp) return;

    if( !preg_match('/^[0-9]+$/',$timestamp) ) {
        $timestamp = (int) strtotime($timestamp);
    }
    $difference = time() - $timestamp;
    $periods = array("sec", "min", "hour", "day", "week","month", "year", "decade");
    $lengths = array("60","60","24","7","4.35","12","10");
    $total_lengths = count($lengths);

    if ($difference > 0) { // this was in the past
        $ending = lang('period_ago');
    } else { // this was in the future
        $difference = -$difference;
        $ending = lang('period_fromnow');
    }

    for( $j = 0; $j < $total_lengths && $difference > $lengths[$j]; $j++ ) {
         $difference /= $lengths[$j];
    }

    $period = $periods[$j];
    $difference = (int)round($difference);
    if($difference != 1) {
        $period.= "s";
    }

    $period = lang('period_'.$period);
    $text = lang('period_fmt',$difference,$period,$ending);

    return $text;
}

#
# EOF
#