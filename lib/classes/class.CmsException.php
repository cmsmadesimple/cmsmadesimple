<?php
#BEGIN_LICENSE
#-------------------------------------------------------------------------
# CmsException (c) 2012 by Robert Campbell 
# (calguy1000@cmsmadesimple.org)
# A collection of CMSMS Exception classes
# 
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2005 by Ted Kulp (wishy@cmsmadesimple.org)
# This project's homepage is: http://www.cmsmadesimple.org
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
 * @package CMS
 */

/**
 * A base CMSMS Exception
 *
 * This exception can accept an integer 'code' for an exception or a language key.
 * if the string passed in contains a space it is not translated.
 * 
 * @package CMS
 * @author Robert Campbell (calguy1000@cmsmadesimple.org)
 * @since 1.10
 */
class CmsException extends Exception 
{
  public function __construct($msg,$code = 0, Exception $prev = null ) {
    if( is_int($msg) ) {
      $msg = 'CMSEX_'.$msg;
    }
    if( startswith($msg,'CMSEX_') && !CmsLangOperations::key_exists($msg) ) {
      $msg = 'MISSING TRANSLATION FOR '.$msg;
    }
    else if( strpos($msg,' ') === FALSE && CmsLangOperations::key_exists($msg) ) {
      $msg = CmsLangOperations::lang($msg);
    }
    parent::__construct($msg,$code,$prev);
  }
}

/**
 * A base CMSMS Logic Exception
 * 
 * @package CMS
 * @author Robert Campbell (calguy1000@cmsmadesimple.org)
 * @since 1.10
 */
class CmsLogicException extends CmsException {}

/**
 * A base CMSMS Privacy Exception
 * 
 * @package CMS
 * @author Robert Campbell (calguy1000@cmsmadesimple.org)
 * @since 1.10
 */
class CmsPrivacyException extends CmsException {}

/**
 * A base CMSMS Singleton Exception
 * 
 * @package CMS
 * @author Robert Campbell (calguy1000@cmsmadesimple.org)
 * @since 1.10
 */
class CmsSingletonException extends CmsException {}

/**
 * An exception indicating invalid data was supplied to a function or class.
 * 
 * @package CMS
 * @author Robert Campbell (calguy1000@cmsmadesimple.org)
 * @since 1.10
 */
class CmsInvalidDataException extends CmsException {}

/**
 * An exception indicating that the requested data could not be found.
 * 
 * @package CMS
 * @author Robert Campbell (calguy1000@cmsmadesimple.org)
 * @since 1.10
 */
class CmsDataNotFoundException extends CmsException {}

/**
 * A special exception indicating that a 404 error should be supplied.
 *
 * @package CMS
 * @author Robert Campbell (calguy1000@cmsmadesimple.org)
 * @since 1.11
 */
class CmsError404Exception extends CmsException {}

/**
 * A special exception indicating an error when editing content.
 *
 * @package CMS
 * @author Robert Campbell (calguy1000@cmsmadesimple.org)
 * @since 1.11
 */
class CmsEditContentException extends CmsException {}

/**
 * A special exception indicating an SQL Error.
 *
 * @package CMS
 * @author Robert Campbell (calguy1000@cmsmadesimple.org)
 * @since 1.12
 */
class CmsSQLErrorException extends CmsException {}


/**
 * A special exception indicating an XML Error.
 *
 * @package CMS
 * @author Robert Campbell (calguy1000@cmsmadesimple.org)
 * @since 1.12
 */
class CmsXMLErrorException extends CmsException {}

/**
 * A special exception indicating a problem with a file, directory, or filesystem
 *
 * @package CMS
 * @author Robert Campbell (calguy1000@cmsmadesimple.org)
 * @since 1.12
 */
class CmsFileSystemException extends CmsException {}

?>
