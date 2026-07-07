<?php
#--------------------------------------------------
# See DOCS/LICENSE for full license information.
#--------------------------------------------------

final class modmgr_cached_request
{
  private $_status;
  private $_result;
  private $_timeout;
  private $_signature;

  private function _getCacheFile()
  {
    if( $this->_signature ) {
      $fn = TMP_CACHE_LOCATION.'/modmgr_'.$this->_signature.'.dat';
      return $fn;
    }
  }

  public function execute($target = '',$data = array(), $age = '', $method = 'GET')
  {
    $mod = cms_utils::get_module('ModuleManager');
    $config = cmsms()->GetConfig();
    if( !$age ) $age = get_site_preference('browser_cache_expiry',60);
    if( $age ) $age = max(1,(int)$age);

    // build a signature
    $this->_signature = md5(json_encode(array($target,$data)));
    $fn = $this->_getCacheFile();
    if( !$fn ) return;

    // check for the cached file
    $atime = time() - ($age * 60);
    $status = '';
    $resutl = '';
    if( (isset($config['developer_mode']) && $mod->GetPreference('disable_caching',0)) ||
        !file_exists($fn) || filemtime($fn) <= $atime ) {
        // execute the request
        $req = new cms_http_request();
        if( $this->_timeout ) $req->setTimeout($this->_timeout);
        $req->execute($target,'',$method,$data);
        $this->_status = $req->getStatus();
        $this->_result = $req->getResult();

        if( file_exists($fn) ) unlink($fn);
        if( $this->_status == 200 ) {
            // create a cache file
            $fh = fopen($fn,'w');
            fwrite($fh,json_encode(array($this->_status,$this->_result)));
            fclose($fh);
        } else {
            audit('','ModuleManager','Request to module repository resulted in status '.$this->_status);
        }
    }
    else {
        // get data from the cache.
        $data = json_decode(file_get_contents($fn), true);
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
    if( $fn && file_exists($fn) ) unlink($fn);
  }
} // end of class.

#
# EOF
#
?>
