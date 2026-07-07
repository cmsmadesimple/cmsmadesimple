<?php
#--------------------------------------------------
# See DOCS/LICENSE for full license information.
#--------------------------------------------------

final class modmgr_rep_client
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

        if( $exact ) {
            // Exact queries need the API (DynamoDB lookup)
            $url = $mod->GetPreference('module_repository');
            if( !$url ) return array(false,$mod->Lang('error_norepositoryurl'));
            $url .= '/moduledetailsgetall';

            $data = array('newest'=>$newest,'exact'=>1,'clientcmsversion'=>CMS_VERSION);
            if( $prefix ) $data['prefix'] = ltrim($prefix);

            $req = new modmgr_cached_request();
            $req->execute($url,$data);
            $status = $req->getStatus();
            $result = $req->getResult();
            if( $status == 400 ) return array(true,array());
            if( $status != 200 || $result == '' ) return array(FALSE,$mod->Lang('error_request_problem'));

            $data = json_decode($result,true);
            return array(true,$data);
        }

        // Fetch letter cache directly from CDN
        if( !$prefix ) $prefix = 'A';
        $cdn_url = 'https://cdn.cmsmadesimple.org/repository/cache/moduledetailsgetall_' . urlencode(strtoupper($prefix)) . '.json';

        $req = new modmgr_cached_request();
        $req->execute($cdn_url);
        $status = $req->getStatus();
        $result = $req->getResult();
        if( $status == 404 ) return array(true,array());
        if( $status != 200 || $result == '' ) return array(FALSE,$mod->Lang('error_request_problem'));

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
            if( file_exists($tmpname) ) unlink($tmpname);

            // Get s3_key from per-module cache on CDN
            $cache_url = 'https://cdn.cmsmadesimple.org/repository/cache/module_' . urlencode($xmlfile) . '.json';
            $req = new cms_http_request();
            $req->execute($cache_url);
            if( $req->GetStatus() != 200 || !$req->GetResult() ) return FALSE;
            $data = json_decode($req->GetResult(), true);
            if( !$data || empty($data['s3_key']) ) return FALSE;

            // Download XML from CDN using s3_key
            $xml_url = 'https://cdn.cmsmadesimple.org/' . $data['s3_key'];
            $req->clear();
            $req->execute($xml_url);
            if( $req->GetStatus() != 200 || !$req->GetResult() ) return FALSE;

            $fh = fopen($tmpname,'w');
            fwrite($fh, $req->GetResult());
            fclose($fh);
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
        $req->execute($url,array('json'=>json_encode($qparms)),'','POST');
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
            if( !is_object($info) && !is_array($info) ) continue;
            $file_ver = is_object($info) ? $info['version'] : (isset($info['version']) ? $info['version'] : '');
            $db_ver = is_object($info) ? $info['installed_version'] : (isset($info['installed_version']) ? $info['installed_version'] : '');
            $local_ver = ($file_ver && $db_ver) ? (version_compare($file_ver, $db_ver) >= 0 ? $file_ver : $db_ver) : ($file_ver ?: $db_ver);
            if( !$local_ver || version_compare($row['version'], $local_ver) <= 0 ) continue;
            $out[$row['name']] = $row;
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
