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

namespace FilePicker;

// store profiles temporarily in the session... uses uniqueid
// may pollute the session, but meh... we can clean it up after some time.
// note: cwd is stored separately for each instance,  as the profile won't change as we modify directories
class TemporaryProfileStorage
{
    private function __construct() {}

    public static function set(\CMSMS\FilePickerProfile $profile)
    {
        $key = md5(__FILE__);
        $sig = md5(__FILE__.serialize($profile).microtime(TRUE).'1');
        $_SESSION[$key][$sig] = serialize($profile);
        return $sig;
    }

    public static function get($sig)
    {
        $key = md5(__FILE__);
        if( isset($_SESSION[$key][$sig]) ) return unserialize($_SESSION[$key][$sig]);
    }

    public static function clear($sig)
    {
        $key = md5(__FILE__);
        if( isset($_SESSION[$key][$sig]) ) unset($_SESSION[$key][$sig]);
    }

    public static function reset()
    {
        $key = md5(__FILE__);
        if( isset($_SESSION[$key]) ) unset($_SESSION[$key]);
    }

} // end of class

#
# EOF
#