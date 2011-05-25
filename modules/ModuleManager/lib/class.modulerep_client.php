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

final class modulerep_client
{
  protected function __construct() {}

  public static function get_repository_modules($prefix = '',$newest = 1)
  {
    $mod = cms_utils::get_module('ModuleManager');
    $url = $mod->GetPreference('module_repository');
    if( !$url )
      {
	return array(false,$mod->Lang('error_norepositoryurl'));
      }
    $url .= '/moduledetailsgetall';

    global $CMS_VERSION;
    $data = array('newest'=>$newest);
    if( $prefix )
      {
	$data['prefix'] = ltrim($prefix);
      }
    $data['clientcmsversion'] = $CMS_VERSION;

    $req = new cms_http_request();
    $req->execute($url,'','POST',$data);
    $status = $req->getStatus();
    $result = $req->getResult();
    if( $status != 200 || $result == '' )
      {
	return array(FALSE,$mod->Lang('error_request_problem'));
      }

    $data = json_decode($result,true);
    return array(true,$data);
  }


  public static function get_module_depends($xmlfile)
  {
    $mod = cms_utils::get_module('ModuleManager');
    if( !$xmlfile ) return array(FALSE,$mod->Lang('error_nofilename'));
    $url = $mod->GetPreference('module_repository');
    if( $url == '' )
      {
	return array(FALSE,$mod->Lang('error_norepositoryurl'));
      }
    $url .= '/moduledepends';

    $req = new cms_http_request();
    $req->execute($url,'','POST',array('name'=>$xmlfile));
    $status = $req->getStatus();
    $result = $req->getResult();
    if( $status != 200 || $result == '' )
      {
	return array(FALSE,$mod->Lang('error_request_problem'));
      }

    $data = json_decode($result,true);
    return $data;
  }


  public static function get_repository_xml($filename, $size = -1)
  {
    if( !$filename ) return FALSE;

    $mod = cms_utils::get_module('ModuleManager');
    $orig_chunksize = $mod->GetPreference('dl_chunksize',256);
    $chunsize = $orig_chunksize * 1024;
    $url = $mod->GetPreference('module_repository');
    if( $url == '' ) return FALSE;

    $tmpname = tempnam(TMP_CACHE_LOCATION,'modmgr_');
    if( !$tmpname ) return FALSE;

    if( $size > 0 && $size <= $chunksize )
    {
      // downloading the whole file at one shot.
      $url .= '/modulexml';
      $req = new cms_http_request();
      $req->execute($url,'','POST',array('name'=>$xmlfile));
      $status = $req->GetStatus();
      $result = $req->GetResult();
      if( $status != 200 || $result == '' )
	{
	  $req->clear();
	  return FALSE;
	}
      $fh = fopen($tmpname,'w');
      fwrite($tmpname,$result);
      fclose($fh);
      return TRUE;
    }

    // download in chunks
    $url .= '/modulegetpart';
    $nchunks = (int)($size / $chunksize);
    if( $size % $chunksize ) $ncunks++;
    $req = new cms_http_req();
    for( $i = 0; $i < $nchunks; $i++ )
      {
	$req->execute($url,'','POST',
		      array('name'=>$xmlfile,'partnum'=>$i,'sizekb'=>$orig_junksize));
	$status = $req->GetStatus();
	$result = $req->GetResult();
	if( $status != 200 || $result == '' )
	  {
	    unlink($tmpname);
	    $req->clear();
	    return FALSE;
	  }

	$fh = fopen($tmpname,'a');
	fwrite($fh,$result);
	fclose($fh);
	$req->clear();
      }

    return $tmpname;
  }


  public static function get_module_md5($xmlfile)
  {
    $mod = cms_utils::get_module('ModuleManager');
    if( !$xmlfile ) return array(FALSE,$mod->Lang('error_nofilename'));
    $url = $mod->GetPreference('module_repository');
    if( $url == '' )
      {
	return array(FALSE,$mod->Lang('error_norepositoryurl'));
      }
    $url .= '/modulemd5sum';

    $req = new cms_http_request();
    $req->execute($url,'','POST',array('name'=>$xmlfile));
    $status = $req->getStatus();
    $result = $req->getResult();
    if( $status != 200 || $result == '' )
      {
	return array(FALSE,$mod->Lang('error_request_problem'));
      }

    $data = json_decode($result,true);
    return $data;
  }

} // end of class

# 
# EOF
#
?>