<?php
#BEGIN_LICENSE
#-------------------------------------------------------------------------
# CmsException (c) 2012 by Robert Campbell
# (calguy1000@cmsmadesimple.org)
# A collection of CMSMS Exception classes
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
 * Contains definitions for the various CMSMS exception classes.
 * @package CMS
 * @license GPL
 */

/**
 * A basic exception class that holds on to extended information.
 *
 * @package CMS
 * @license GPL
 * @since 2.0
 */
abstract class CmsExtraDataException extends Exception
{
  /**
   * @ignore
   */
  private $_extra;

  /**
   * Constructor
   * This method accepts variable arguments
   *
   * i.e:  throw new CmsExtraDataException($msg_str,$msg_code,$prev)
   * i.e:  throw new CmsExtraDataException($msg_str,$msg_code,$extra,$prev)
   *
   * @see \Exception
   */
  public function __construct(/* var args */)
  {
      $args = $msg = $prev = NULL;
      $code = 0;
      $args = func_get_args();
      if( is_array($args) && count($args) == 1 ) $args = $args[0];
      for( $i = 0; $i < count($args); $i++ ) {
          switch( $i ) {
          case 0:
              $msg = $args[$i];
              break;

          case 1:
              $code = (int)$args[$i];
              break;

          case 2:
              if( is_object($args[$i]) ) {
                  $prev = $args[$i];
              }
              else {
                  $this->_extra = $args[$i];
              }
              break;

          case 3:
              if( $prev == null && is_object($args[$i]) ) $prev = $args[$i];
              break;
          }
      }
      parent::__construct($msg,$code,$prev);
  }

  /**
   * Return extra data associated with the exception
   * @return mixed
   */
  public function GetExtraData()
  {
      return $this->_extra;
  }
}

/**
 * A base CMSMS Exception
 *
 * This exception can accept an integer 'code' for an exception or a language key.
 * if the string passed in contains a space it is not translated.
 *
 * @package CMS
 * @license GPL
 * @author Robert Campbell (calguy1000@cmsmadesimple.org)
 * @since 1.10
 */
class CmsException extends CmsExtraDataException
{
    /**
     * Constructor
     * This method accepts variable arguments.
     *
     * i.e:  throw new CmsExtraDataException($msg_str,$msg_code,$prev)
     * i.e:  throw new CmsExtraDataException($msg_str,$msg_code,$extra,$prev)
     *
   * @see \Exception
     */
    public function __construct(/* var args */) {
        $args = func_get_args();
        parent::__construct($args);
        if( is_int($this->message) ) $this->messsage = 'CMSEX_'.$msg;
        if( startswith($this->message,'CMSEX_') && !CmsLangOperations::key_exists($this->message) ) {
            $this->message = 'MISSING TRANSLATION FOR '.$this->message;
        }
        else if( strpos($this->message,' ') === FALSE && CmsLangOperations::key_exists($this->message) ) {
            $this->message = CmsLangOperations::lang($this->message);
        }
    }
}

/**
 * A base CMSMS Logic Exception
 *
 * @package CMS
 * @license GPL
 * @author Robert Campbell (calguy1000@cmsmadesimple.org)
 * @since 1.10
 */
class CmsLogicException extends CmsException {}

/**
 * A base CMSMS Communications Exception
 *
 * @package CMS
 * @license GPL
 * @author Robert Campbell (calguy1000@cmsmadesimple.org)
 * @since 1.10
 */
class CmsCommunicationException extends CmsException {}

/**
 * A base CMSMS Privacy Exception
 *
 * @package CMS
 * @license GPL
 * @author Robert Campbell (calguy1000@cmsmadesimple.org)
 * @since 1.10
 */
class CmsPrivacyException extends CmsException {}

/**
 * A base CMSMS Singleton Exception
 *
 * @package CMS
 * @license GPL
 * @author Robert Campbell (calguy1000@cmsmadesimple.org)
 * @since 1.10
 */
class CmsSingletonException extends CmsException {}

/**
 * An exception indicating invalid data was supplied to a function or class.
 *
 * @package CMS
 * @license GPL
 * @author Robert Campbell (calguy1000@cmsmadesimple.org)
 * @since 1.10
 */
class CmsInvalidDataException extends CmsLogicException {}

/**
 * An exception indicating that the requested data could not be found.
 *
 * @package CMS
 * @license GPL
 * @author Robert Campbell (calguy1000@cmsmadesimple.org)
 * @since 1.10
 */
class CmsDataNotFoundException extends CmsException {}

/**
 * A special exception indicating that a 404 error should be supplied.
 *
 * @package CMS
 * @license GPL
 * @author Robert Campbell (calguy1000@cmsmadesimple.org)
 * @since 1.11
 */
class CmsError404Exception extends CmsException {}

/**
 * A special exception indicating that a 403 error should be supplied.
 *
 * @package CMS
 * @author Robert Campbell (calguy1000@cmsmadesimple.org)
 * @since 1.12
 */
class CmsError403Exception extends CmsException {}

/**
 * A special exception indicating an error with a content object
 *
 * @package CMS
 * @license GPL
 * @author Robert Campbell (calguy1000@cmsmadesimple.org)
 * @since 2.0
 */
class CmsContentException extends CmsException {}

/**
 * A special exception indicating an error when editing content.
 *
 * @package CMS
 * @license GPL
 * @author Robert Campbell (calguy1000@cmsmadesimple.org)
 * @since 1.11
 */
class CmsEditContentException extends CmsContentException {}

/**
 * A special exception indicating an SQL Error.
 *
 * @package CMS
 * @license GPL
 * @author Robert Campbell (calguy1000@cmsmadesimple.org)
 * @since 2.0
 */
class CmsSQLErrorException extends CmsException {}


/**
 * A special exception indicating an XML Error.
 *
 * @package CMS
 * @license GPL
 * @author Robert Campbell (calguy1000@cmsmadesimple.org)
 * @since 2.0
 */
class CmsXMLErrorException extends CmsException {}

/**
 * A special exception indicating a problem with a file, directory, or filesystem
 *
 * @package CMS
 * @license GPL
 * @author Robert Campbell (calguy1000@cmsmadesimple.org)
 * @since 2.0
 */
class CmsFileSystemException extends CmsException {}

?>