<?php
#BEGIN_LICENSE
#-------------------------------------------------------------------------
# Module: CmsLayoutTemplateType (c) 2013 by Robert Campbell
#         (calguy1000@cmsmadesimple.org)
#  A class to manage template types.
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
 * This file defines the TemplateTypeAssistant abstract class.
 *
 * @package CMS
 * @license GLP
 */
namespace CMSMS\Layout;

/**
 * An abstract class to define an assistant to the template type objects in the database.
 *
 * @package CMS
 * @license GPL
 * @since 2.2
 * @author Robert Campbell <calguy1000@gmail.com>
 */
abstract class TemplateTypeAssistant
{
    /**
     * Get the type object for the current assistant.
     *
     * @return CmsLayoutTemplateType
     */
    abstract public function &get_type();

    /**
     * Get a usage string for the current assistant.
     *
     * @param string $name The template name.
     * @return string
     */
    abstract public function get_usage_string($name);
}