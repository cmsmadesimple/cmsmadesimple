<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#CMS - CMS Made Simple
#(c)2004-2008 by Ted Kulp (ted@cmsmadesimple.org)
#This project's homepage is: http://cmsmadesimple.org
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
#$Id: class.cms_content_base.php 5976 2009-09-07 14:23:29Z wishy $

require_once(dirname(dirname(__FILE__)).'/http/class.http.php');

class CmsHttpClient extends Http
{

  public function __construct()
  {
    $config = cms_config();
    $this->user_agent = 'CMS Made Simple';
    $this->referrer   = get_current_url();
    $this->cookiePath = $config['root_path'].'/tmp/cache/cookies.dat';
  }

  public function do_post($url,$data)
  {
    $result = $this->execute($url,'','POST',$data);
    return $result;
  }

  public function do_get($url,$params)
  {
    $result = $this->execute($url,'','GET',$data);
    return $result;
  }
} // end of class

#
# EOF
#
?>