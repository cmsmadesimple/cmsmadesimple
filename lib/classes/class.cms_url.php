<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#BEGIN_LICENSE
#-------------------------------------------------------------------------
# Module: cms_content_tree (c) 2010 by Robert Campbell 
#         (calguy1000@cmsmadesimple.org)
#  A caching tree for CMSMS content objects.
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
 * A class for interacting with a URL.
 *
 * @package CMS
 * @author  Robert Campbell
 * @copyright Copyright (c) 2010, Robert Campbell <calguy1000@cmsmadesimple.org>
 * @since 1.9
 */
class cms_url
{
  /**
   * @ignore
   */
  private $_orig;

  /**
   * @ignore
   */
  private $_parts;

  /**
   * @ignore
   */
  private $_query = array();

  /**
   * Constructor
   * 
   * @param string the url to work with
   */
  public function __construct($url)
  {
    $this->_orig = $url;
    $this->_parts = parse_url($url);
    if( ($str = $this->get_query()) )
      {
	$this->_query = parse_str($str);
      }
  }

  /**
   * @ignore
   */
  private function _get_part($key)
  {
    if( isset($this->_parts[$key]) ) 
      {
	return $this->_parts[$key];
      }
  }

  /**
   * @ignore
   */
  private function _set_part($key,$value)
  {
    if( !$value && isset($this->_parts[$key]) )
      {
	unset($this->_parts[$key]);
      }
    else
      {
	$this->_parts[$key] = $value;
      }
  }

  /**
   * Return the original url
   *
   * @return string
   */
  public function get_orig()
  {
    return $this->_orig;
  }
  
  /**
   * Return the URL scheme.  i.e: HTTP, HTTPS, ftp etc.
   *
   * @return string (may be empty)
   */
  public function get_scheme()
  {
    return $this->_get_part('scheme');
  }


  /**
   * Set the URL scheme
   *
   * @param string The url scheme.
   */
  public function set_scheme($val)
  {
    return $this->_set_part('scheme',$val);
  }

  /**
   * Return the host part of the URL
   * may return an empty string if the input url does not have a host part.
   *
   * @return string (may be empty)
   */
  public function get_host()
  {
    return $this->_get_part('host');
  }


  /**
   * Set the URL host
   *
   * @param string The url hostname.
   */
  public function set_host($val)
  {
    $this->_set_part('host',$val);
  }


  /**
   * Return the port part of the URL
   * may return an empty string if the input url does not have a port portion.
   *
   * @return integer (may be empty)
   */
  public function get_port()
  {
    return $this->_get_part('port');
  }

  /**
   * Set the URL port
   *
   * @param integer the URL port number.
   */
  public function set_port($val)
  {
    return $this->_set_part('port',(int)$val);
  }

  /**
   * Return the user part of the URL, if any.
   * may return an empty string if the input url does not have a username portion.
   *
   * @return string (may be empty)
   */
  public function get_user()
  {
    return $this->_get_part('user');
  }

  /**
   * Set the user portion of the URL. An empty string is accepted
   * Note: usually one must set the password if setting the username.
   *
   * @param string The username
   */
  public function set_user($val)
  {
    return $this->_set_part('user',$val);
  }


  /**
   * Retrieve the password portion of the URL, if any.
   *
   * @return string (may be empty)
   */
  public function get_pass()
  {
    return $this->_get_part('pass');
  }

  /**
   * Set the password portion of the URL.  Empty string is accepted
   * Usually when setting the password, the username portion is also required on a URL.
   *
   * @param string The password
   */
  public function set_pass($val)
  {
    return $this->_set_part('pass',$val);
  }

  /**
   * Return the path portion of the URL.  This may be empty
   *
   * @return string
   */
  public function get_path()
  {
    return $this->_get_part('path');
  }

  /**
   * Set the path portion of the URL.  An empty string is accepted.
   *
   * @param string (may be empty)
   */
  public function set_path($val)
  {
    return $this->_set_part('path',$val);
  }

  /**
   * Return the the query portion of the URL, if any is set.
   *
   * @return string (may be empty)
   */
  public function get_query()
  {
    return $this->_get_part('query');
  }

  /**
   * Set the query portion of the URL.  An empty string is accepted
   *
   * @param string (may be empty)
   */
  public function set_query($val)
  {
    return $this->_set_part('query',$val);
    if( $val )
      {
	$this->_query = parse_str($val);
      }
  }

  public function get_fragment()
  {
    return $this->_get_part('fragment');
  }


  public function set_fragment($val)
  {
    return $this->_set_part('fragment',$val);
  }


  public function queryvar_exists($key)
  {
    return ($key && isset($this->_query[$key]));
  }


  public function get_queryvar($key)
  {
    if( $key && isset($this->_query[$key]) )
      {
	return $this->_query[$key];
      }
  }

  public function set_queryvar($key,$value)
  {
    if( $key && $value )
      {
	$this->_query[$key] = $value;
      }
  }


  public function __toString()
  {
    // build the query array back into a string.
    if( count($this->_query) )
      {
	$tmp = array();
	foreach( $this->_query as $key => $val )
	  {
	    $tmp[] = $key.'='.$val;
	  }

	$str = '?';
	$str = implode('&',$tmp);
	$this->_parts['query'] = $str;
      }

    $parts = $this->_parts;
    $url = ((isset($parts['scheme'])) ? $parts['scheme'] . '://' : '')
      .((isset($parts['user'])) ? $parts['user'] . ((isset($parts['pass'])) ? ':' . $parts['pass'] : '') .'@' : '')
      .((isset($parts['host'])) ? $parts['host'] : '')
      .((isset($parts['port'])) ? ':' . $parts['port'] : '')
      .((isset($parts['path'])) ? $parts['path'] : '')
      .((isset($parts['query'])) ? '?' . $parts['query'] : '')
      .((isset($parts['fragment'])) ? '#' . $parts['fragment'] : '');
    return $url;
  }
} // end of class

#
# EOF
#
?>
