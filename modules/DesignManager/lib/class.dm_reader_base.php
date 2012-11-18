<?php // -*- mode:php; tab-width:2; indent-tabs-mode:t; c-basic-offset:2; -*-
#-------------------------------------------------------------------------
# Module: AdminSearch - A CMSMS addon module to provide template management.
# (c) 2012 by Robert Campbell <calguy1000@cmsmadesimple.org>
#
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
#
#-------------------------------------------------------------------------

abstract class dm_reader_base 
{
  private $_suggested_name;
  private $_filename;

  public function __construct($filename)
  {
    $this->_filename = $filename;
  }

  public function set_suggested_name($name)
  {
    if( $name ) $this->_suggested_name = $name;
  }

  public function get_filename()
  {
    return $_filename;
  }

  public function get_suggested_name()
  {
    return $this->_suggested_name;
  }

  abstract public function validate();

  abstract public function get_design_info();

  abstract public function get_template_list();

  abstract public function get_stylesheet_list();

  /**
   * Actually do the importing..
   * can throw exceptions.
   */
  abstract public function import();

  /**
   * Get the destination directory for this designs files.
   *
   * Directory should be created, and checked for writable...
   * throw exception on failure.
   */
  abstract protected function get_destination_dir();

  /**
   * Get a finalized new name for this new design.
   *
   * Use the suggested name if possible, check for duplicate names
   * throw exception on failure.
   */
  abstract protected function get_new_name();
}
#
# EOF
#
?>