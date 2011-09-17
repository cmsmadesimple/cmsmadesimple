<?php
#BEGIN_LICENSE
#-------------------------------------------------------------------------
# Module: ModuleManager (c) 2011 by Robert Campbell 
#         (calguy1000@cmsmadesimple.org)
#  An addon module for CMS Made Simple to allow browsing remotely stored
#  modules, viewing information about them, and downloading or upgrading
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

final class modmgr_cached_request
{
  private $_status;
  private $_result;
  private $_timeout;
  private $_signature;

  private function _getCacheFile()
  {
    if( $this->_signature )
      {
	$fn = TMP_CACHE_LOCATION.'/modmgr_'.$this->_signature.'.dat';
	return $fn;
      }
  }

  public function execute($target = '',$data = array(), $age = '')
  {
    $mod = cms_utils::get_module('ModuleManager');
    if( !$age ) $age = get_site_preference('browser_cache_expiry',60);
    if( $age ) $age = max(1,(int)$age);

    // build a signature
    $this->_signature = md5(serialize(array($target,$data)));
    $fn = $this->_getCacheFile();
    if( !$fn ) return;

    // check for the cached file
    $atime = time() - ($age * 60);
    $status = '';
    $resutl = '';
    if( $mod->GetPreference('disable_caching',0) || !file_exists($fn) || filemtime($fn) <= $atime )
    {
      // execute the request
      $req = new cms_http_request();
      if( $this->_timeout ) $req->setTimeout($this->_timeout);
      $req->execute($target,'','POST',$data);
      $this->_status = $req->getStatus();
      $this->_result = $req->getResult();

      @unlink($fn);
      if( $this->_status == 200 )
	{
	  // create a cache file
	  $fh = fopen($fn,'w');
	  fwrite($fh,serialize(array($this->_status,$this->_result)));
	  fclose($fh);
	}
    }
    else
    {
      // get data from the cache.
      $data = unserialize(file_get_contents($fn));
      $this->_status = $data[0];
      $this->_result = $data[1];
    }
  }


  public function setTimeout($val)
  {
    $this->_timeout = max(1,min(1000,(int)$val));
  }

  public function getStatus()
  {
    return $this->_status;
  }

  public function getResult()
  {
    return $this->_result;
  }

  public function clearCache()
  {
    $fn = $this->_getCacheFile();
    if( $fn )
      {
	@unlink($fn);
      }
  }
} // end of class.

#
# EOF
#
?>