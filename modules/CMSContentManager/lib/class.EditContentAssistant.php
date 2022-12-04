<?php
#BEGIN_LICENSE
#-------------------------------------------------------------------------
# Module: CMSContentManager (c) 2013 by Robert Campbell 
#         (calguy1000@cmsmadesimple.org)
#  A module for managing content in CMSMS.
# 
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2004 by Ted Kulp (wishy@cmsmadesimple.org)
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
 * An abstract class for building edit content assistant objects.
 */
abstract class EditContentAssistant implements ContentAssistant
{
  private $_content_obj;

  /**
   * construct an EditContentAssistant object.
   *
   * @param ContentBase Specifyt he content object that we are building an assistant for.
   */
  public function __construct(Contentbase $content)
  {
    $this->_content_obj = $content;
  }

  /**
   * Get HTML (including javascript) that should go in the page content when eding this content object.
   * This could be used for outputting some javascript to enhance the functionality of some content fields.
   *
   * This function
   * @return string
   */
  abstract public function getExtraCode();
}

#
# EOF
#
?>