<?php
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

final class dm_reader_factory
{
  private function __construct() {}

  public static function &get_reader($xmlfile)
  {
    $mod = cms_utils::get_module('DesignManager');
    if( !is_readable($xmlfile) ) throw new CmsFileSystemException($mod->Lang('error_filenotfound',$xmlfile));
    $fh = fopen($xmlfile,'r');
    if( !$fh ) throw new CmsException($this->Lang('error_fileopen',$xmlfile));
    $str = fread($fh,200);
    fclose($fh);
    if( strpos($str,'<!DOCTYPE') === FALSE ) throw new CmsException($mod->Lang('error_readxml'));

    // get the first element
    $x = '<!ELEMENT ';
    $p = strpos($str,$x);
    if( $p === FALSE ) throw new CmsException($this->Lang('error_readxml'));
    $str = substr($str,$p+strlen($x));
    $p = strpos($str,' ');
    if( $p === FALSE ) throw new CmsException($this->Lang('error_readxml'));  // highly unlikely.
    $word = substr($str,0,$p);

		$ob = null;
    switch( $word ) {
    case 'theme':
      $ob = new dm_theme_reader($xmlfile);
      break;

    case 'design':
      $ob = new dm_design_reader($xmlfile);
      break;
    }
		return $ob;
  }
} // end of class

#
# EOF
#
?>