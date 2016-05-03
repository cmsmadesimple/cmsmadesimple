<?php
#BEGIN_LICENSE
#-------------------------------------------------------------------------
# (c) 2016 by Robert Campbell (calguy1000@cmsmadesimple.org)
#
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2005-2010 by Ted Kulp (wishy@cmsmadesimple.org)
# This projects homepage is: http://www.cmsmadesimple.org
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

namespace News;

class DraftMessageAlert extends \CMSMS\AdminAlerts\Alert
{
    private $_ndraft;

    public function __construct($count)
    {
        $this->_ndraft = (int) $count;
        $this->name = basename(__CLASS__);
        $mod = \cms_utils::get_module('News');
        $this->title = $mod->Lang('title_draft_entries');
        $this->priority = self::PRIORITY_LOW;
        $this->module = 'News';
    }

    public function __get($key)
    {
        switch( $key ) {
        case 'n_draft':
            return (int) $this->_ndraft;

        default:
            return parent::__get($key);
        }
    }

    public function __set($key,$val)
    {
        switch( $key ) {
        case 'n_draft':
            $this->_ndraft = max(0,(int) $val);
            break;

        default:
            return parent::__set($key,$val);
        }
    }

    protected function &get_module()
    {
        static $_mod;
        if( !$_mod ) $_mod = \cms_utils::get_module('News');
        return $_mod;
    }

    public function &load()
    {
        return self::load_by_name($this->get_prefname());
    }

    protected function is_for($uid)
    {
        return check_permission($uid,'Approve News');
    }

    public function get_icon()
    {
        // nothing here
    }

    public function get_message()
    {
        return $this->get_module()->Lang('notify_n_draft_items',(int) $this->_ndraft);
    }
}