<?php
#CMS - CMS Made Simple
#(c)2004-2015 by Ted Kulp (wishy@users.sf.net)
#Visit our homepage at: http://www.cmsmadesimple.org
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
#$Id: misc.functions.php 9861 2015-03-28 16:21:04Z calguy1000 $

/**
 * Miscellaneous support functions
 *
 * @package CMS
 * @license GPL
 */


if( !function_exists('gzopen') ) {
    /**
     * Wrapper for gzopen in case it does not exist.
     * Some installs of PHP (after PHP 5.3 use a different zlib library, and therefore gzopen is not defined.
     * This method works past that.
     *
     * @since 2.0
     * @ignore
     */
    function gzopen( $filename , $mode , $use_include_path = 0 ) {
        return gzopen64($filename, $mode, $use_include_path);
    }
}

#
# EOF
#
