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

final class modulerep_client
{
    private static $_latest_installed_modules;

    protected function __construct() {}

    public static function get_repository_version()
    {
        $mod = cms_utils::get_module('ModuleManager');
        $url = $mod->GetPreference('module_repository');
        if( !$url )	return array(false,$mod->Lang('error_norepositoryurl'));
        $url .= '/version';

        $req = new modmgr_cached_request();
        $req->execute($url);
        $status = $req->getStatus();
        $result = $req->getResult();
        if( $status != 200 || $result == '' ) return array(FALSE,$mod->Lang('error_request_problem'));

        $data = json_decode($result,true);
        return array(true,$data);
    }


    /**
     * Given an array of hashes with name/version members return module info for all matches.
     * maximum of 25 rows, and no guarantee that there will be results for each request.
     */
    public static function get_multiple_moduleinfo($input)
    {
        $mod = cms_utils::get_module('ModuleManager');
        if( !is_array($input) || count($input) == 0 ) throw new CmsInvalidDataException($mod->Lang('error_missingparam'));

        $out = array();
        foreach( $input as $key => $data ) {
            if( is_array($data) && isset($data['name']) && isset($data['version']) && $data['name'] && $data['version'] ) {
                $out[] = array('name'=>$data['name'],'version'=>$data['version']);
            }
            else if( is_string($key) && (int)$key == 0 ) {
                $out[] = array('name'=>$key,'version'=>$data);
            }
            else {
                throw new CmsInvalidDataException($mod->Lang('error_missingparam'));
            }
        }
        if( count($out) == 0 ) new CmsInvalidDataException($mod->Lang('error_missingparam'));

        $url = $mod->GetPreference('module_repository');
        if( !$url )	return array(false,$mod->Lang('error_norepositoryurl'));
        $url .= '/multimoduleinfo';
        $data = array('data'=>json_encode($out));

        $req = new modmgr_cached_request();
        $req->execute($url,$data);
        $status = $req->getStatus();
        $result = $req->getResult();
        if( $status == 400 ) {
            return;
        }
        else if( $status != 200 || $result == '' ) {
            throw new CmsCommunicationException($mod->Lang('error_request_problem'));
        }

        return json_decode($result,true);
    }

    public static function get_repository_modules($prefix = '',$newest = 1,$exact = FALSE)
    {
        $mod = cms_utils::get_module('ModuleManager');
        $url = $mod->GetPreference('module_repository');
        if( !$url )	return array(false,$mod->Lang('error_norepositoryurl'));
        $url .= '/moduledetailsgetall';

        global $CMS_VERSION;
        $data = array('newest'=>$newest);
        if( $prefix ) $data['prefix'] = ltrim($prefix);
        if( $exact ) $data['exact'] = 1;
        $data['clientcmsversion'] = $CMS_VERSION;

        $req = new modmgr_cached_request();
        $req->execute($url,$data);
        $status = $req->getStatus();
        $result = $req->getResult();
        if( $status == 400 ) {
            return array(true,array());
        }
        else if( $status != 200 || $result == '' ) {
            return array(FALSE,$mod->Lang('error_request_problem'));
        }

        $data = json_decode($result,true);
        return array(true,$data);
    }

    public static function get_module_dependencies($module_name,$module_version = '')
    {
        $mod = cms_utils::get_module('ModuleManager');
        if( !$module_name ) throw new CmsInvalidDataException($mod->Lang('error_missingparams'));
        $url = $mod->GetPreference('module_repository');
        if( $url == '' ) throw new CmsInvalidDataException($mod->Lang('error_norepositoryurl'));
        $url .= '/moduledependencies';

        $parms = array('name'=>$module_name);
        if( $module_version ) $parms['version'] = $module_version;
        $req = new modmgr_cached_request();
        $req->execute($url,$parms);
        $status = $req->getStatus();
        $result = $req->getResult();
        if( $status == 400 ) {
            // no dependencies found
            return;
        }
        else if( $status != 200 || $result == '' ) {
            throw new CmsCommunicationException($mod->Lang('error_request_problem'));
        }

        $data = json_decode($result,true);
        return $data;
    }

    // old...
    public static function get_module_depends($xmlfile)
    {
        $mod = cms_utils::get_module('ModuleManager');
        if( !$xmlfile ) throw new CmsInvalidDataException($mod->Lang('error_nofilename'));
        $url = $mod->GetPreference('module_repository');
        if( $url == '' ) throw new CmsInvalidDataException($mod->Lang('error_norepositoryurl'));
        $url .= '/moduledepends';

        $req = new modmgr_cached_request();
        $req->execute($url,array('name'=>$xmlfile));
        $status = $req->getStatus();
        $result = $req->getResult();
        if( $status != 200 || $result == '' ) throw new CmsCommunicationException($mod->Lang('error_request_problem'));

        $data = json_decode($result,true);
        return $data;
    }


    public static function get_repository_xml($xmlfile, $size = -1)
    {
        if( !$xmlfile ) return FALSE;

        // this is manually cached.
        $tmpname = TMP_CACHE_LOCATION.'/modmgr_'.md5(__DIR__.$xmlfile).'.dat';
        $mod = cms_utils::get_module('ModuleManager');
        if( !file_exists($tmpname) || $mod->GetPreference('disable_caching',0) || (time() - filemtime($tmpname)) > 7200 ) {
            @unlink($tmpname);

            // must download
            $orig_chunksize = $mod->GetPreference('dl_chunksize',256);
            $chunksize = $orig_chunksize * 1024;
            $url = $mod->GetPreference('module_repository');
            if( $url == '' ) return FALSE;

            if( $size <= $chunksize ) {
                // downloading the whole file at one shot.
                $url .= '/modulexml';
                $req = new cms_http_request();
                $req->execute($url,'','POST',array('name'=>$xmlfile));
                $status = $req->GetStatus();
                $result = $req->GetResult();
                if( $status != 200 || $result == '' ) {
                    $req->clear();
                    return FALSE;
                }
                $fh = fopen($tmpname,'w');
                fwrite($fh,$result);
                fclose($fh);
                return $tmpname;
                $req->clear();
            }

            // download in chunks
            $url .= '/modulegetpart';
            $nchunks = (int)ceil($size / $chunksize);
            $req = new cms_http_request();
            for( $i = 0; $i < $nchunks; $i++ ) {
                $req->execute($url,'','POST', array('name'=>$xmlfile,'partnum'=>$i,'sizekb'=>$orig_chunksize));
                $status = $req->GetStatus();
                $result = $req->GetResult();
                if( $status != 200 || $result == '' ) {
                    unlink($tmpname);
                    $req->clear();
                    return FALSE;
                }

                $fh = fopen($tmpname,'a');
                fwrite($fh,base64_decode($result));
                fclose($fh);
                $req->clear();
            }
        }

        return $tmpname;
    }


    public static function get_module_md5($xmlfile)
    {
        $mod = cms_utils::get_module('ModuleManager');
        if( !$xmlfile ) throw new CmsInvalidDataException($mod->Lang('error_nofilename'));
        $url = $mod->GetPreference('module_repository');
        if( $url == '' ) throw new CmsInvalidDataException($mod->Lang('error_norepositoryurl'));
        $url .= '/modulemd5sum';

        $req = new modmgr_cached_request();
        $req->execute($url,array('name'=>$xmlfile));
        $status = $req->getStatus();
        $result = $req->getResult();
        if( $status != 200 || $result == '' ) throw new CmsCommunicationException($mod->Lang('error_request_problem'));

        $data = json_decode($result,true);
        return $data;
    }


    public static function search($term,$advanced)
    {
        $qparms = array();
        $filter = array();
        $filter['term'] = $term;
        $filter['advanced'] = (int)$advanced;
        $filter['newest'] = 1;
        $filter['sortby'] = 'score';
        $qparms['filter'] = $filter;
        $qparms['clientcmsversion'] = CMS_VERSION;

        $mod = cms_utils::get_module('ModuleManager');
        $url = $mod->GetPreference('module_repository');
        if( $url == '' ) return array(FALSE,$mod->Lang('error_norepositoryurl'));
        $url .= '/modulesearch';

        $req = new modmgr_cached_request();
        $req->execute($url,array('json'=>json_encode($qparms)));
        $status = $req->getStatus();
        $result = $req->getResult();
        if( $status == 200 && $result == ''  ) return array(TRUE,null); // no results.
        if( $status != 200 || $result == '' ) return array(FALSE,$mod->Lang('error_request_problem'));

        $data = json_decode($result,true);
        return array(TRUE,$data);
    }

    /**
     * returns the latest info about all specified modules
     * on success returns associative array of info about modules
     * on error throws an exception.
     * @return array
     */
    public static function get_modulelatest($modules)
    {
        $mod = cms_utils::get_module('ModuleManager');
        if( !is_array($modules) || count($modules) == 0 ) throw new CmsInvalidDataException($mod->Lang('error_missingparam'));

        $url = $mod->GetPreference('module_repository');
        if( $url == '' ) throw new CmsInvalidDataException($mod->Lang('error_norepositoryurl'));
        $qparms = array();
        $qparms['names'] =  implode(',',$modules);
        $qparms['newest'] = '1';
        $qparms['clientcmsversion'] = CMS_VERSION;
        $url .= '/upgradelistgetall';

        $req = new modmgr_cached_request();
        $req->execute($url,$qparms);
        $status = $req->getStatus();
        $result = $req->getResult();
        if( $status != 200 ) throw new CmsCommunicationException($mod->Lang('error_request_problem'));
        if( !$result ) {
            throw new ModuleNoDataException();
	}

        $data = json_decode($result,true);
        if( !$data || !is_array($data) ) throw new CmsInvalidDataException($mod->Lang('error_nomatchingmodules'));

        return $data;
    }

    /**
     * returns the latest info about installed modules.
     * on success returns associative array of info about modules
     * on error throw exception.
     * @return array
     */
    public static function get_allmoduleversions()
    {
        if( is_array(self::$_latest_installed_modules) ) return self::$_latest_installed_modules;

        $modules = ModuleOperations::get_instance()->GetInstalledModules();
        self::$_latest_installed_modules = self::get_modulelatest($modules);
        return self::$_latest_installed_modules;
    }

    /**
     * Return info about installed modules that have newer versions available.
     * return mixed (FALSE on error, NULL or associative array on success
     */
    public static function get_newmoduleversions()
    {
        $versions = self::get_allmoduleversions();
        if( !is_array($versions) ) return FALSE;
        if( count($versions) == 2 && $versions[0] === FALSE ) return FALSE;

        $out = array();
        foreach( $versions as $row ) {
            $info = ModuleManagerModuleInfo::get_module_info( $row['name'] );
            if( version_compare($row['version'],$info['version']) > 0 ) {
                $data = array();
                $out[$row['name']] = $row;
            }
        }
        if( count($out) ) return $out;
    }

    public static function get_upgrade_module_info($module_name)
    {
        $versions = self::get_allmoduleversions();
        if( !is_array($versions) ) return FALSE;
        if( count($versions) == 2 && $versions[0] === FALSE ) return FALSE;

        foreach( $versions as $row ) {
            if( $row['name'] == $module_name ) return $row;
        }
    }
} // end of class

class ModuleManagerException extends \CmsException {}
class ModuleNoDataException extends ModuleManagerException {}
class ModuleNotFoundException extends ModuleManagerException {}

#
# EOF
#
