<?php
#--------------------------------------------------
# See DOCS/LICENSE for full license information.
#--------------------------------------------------

final class modmgr_utils
{
    protected function __construct() {}

    public static function get_installed_modules($include_inactive = FALSE, $as_hash = FALSE)
    {
        $modops = ModuleOperations::get_instance();
        $module_list = $modops->GetInstalledModules($include_inactive);

        $results = array();
        foreach( $module_list as $module_name ) {
            $inst = $modops->get_module_instance($module_name);
            if( !$inst ) continue;

            $details = array();
            $details['name'] = $inst->GetName();
            $details['description'] = $inst->GetDescription();
            $details['version'] = $inst->GetVersion();
            $details['active'] = $modops->IsModuleActive($module_name);

            if( $as_hash ) {
                $results[$module_name] = $details;
            }
            else {
                $results[] = $details;
            }
        }
        return array(true,$results);
    }

    private static function uasort_cmp_details( $e1, $e2 )
    {
        $n1 = $n2 = '';
        $v1 = $v2 = '';
        if( is_object($e1) ) {
            $n1 = $e1->name;
            $v1 = $e1->version;
        }
        else {
            $n1 = $e1['name'];
            $v1 = $e1['version'];
        }
        if( is_object($e2) ) {
            $n2 = $e2->name;
            $v2 = $e2->version;
        }
        else {
            $n2 = $e2['name'];
            $v2 = $e2['version'];
        }

        if( strcasecmp($n1,$n2) < 0 ) {
            return -1;
        }
        elseif( strcasecmp($n1,$n2) > 0 ) {
            return 1;
        }
        return version_compare( $e2['version'], $e1['version'] );
    }

    public static function build_module_data( &$xmldetails, &$installdetails, $newest = true )
    {
        if( !is_array($xmldetails) ) return;

        // Filter out beta/pre-release modules unless preference is enabled
        $mod = cms_utils::get_module('ModuleManager');
        if( !$mod->GetPreference('show_beta',0) ) {
            $xmldetails = array_filter($xmldetails, function($det) {
                $ver = isset($det['version']) ? strtolower($det['version']) : '';
                $fn = isset($det['filename']) ? strtolower($det['filename']) : '';
                return !preg_match('/(alpha|beta|rc|dev|\d+[ab]\d+)/i', $ver . $fn);
            });
            $xmldetails = array_values($xmldetails);
        }

        // sort
        uasort( $xmldetails, array('modmgr_utils','uasort_cmp_details') );

        $mod = cms_utils::get_module('ModuleManager');

        //
        // Process the xmldetails, and only keep the latest version
        // of each (according to a preference)
        //
        // Note: should be redundant with 1.2, but kept in here for
        // a while just in case..
        if( $newest && $mod->GetPreference('onlynewest',1) == 1 ) {
            $thexmldetails = array();
            $prev = '';
            foreach( $xmldetails as $det ) {
                if( is_array($prev) && $prev['name'] == $det['name'] ) continue;

                $prev = $det;
                $thexmldetails[] = $det;
            }
            $xmldetails = $thexmldetails;
        }

        $results = array();
        foreach( $xmldetails as $det1 ) {
            $found = 0;
            foreach( $installdetails as $det2 ) {
                if( $det1['name'] == $det2['name'] ) {
                    $found = 1;
                    // if the version of the xml file is greater than that of the
                    // installed module, we have an upgrade
                    $res = version_compare( $det1['version'], $det2['version'] );
                    if( $res == 1 ) {
                        $det1['status'] = 'upgrade';
                    }
                    else if( $res == 0 ) {
                        $det1['status'] = 'uptodate';
                    }
                    else {
                        $det1['status'] = 'newerversion';
                    }

                    $results[] = $det1;
                    break;
                }
            }
            if( $found == 0 ) {
                // we don't have this module installed
                $det1['status'] = 'notinstalled';
                $results[] = $det1;
            }
        }

        //
        // Do a third loop
        // and check min and max cms version
        //
        $results2 = array();
        foreach( $results as $oneresult ) {
            if( (!empty($oneresult['maxcmsversion']) && version_compare(CMS_VERSION,$oneresult['maxcmsversion']) > 0) ||
                (!empty($oneresult['mincmsversion']) && version_compare(CMS_VERSION,$oneresult['mincmsversion']) < 0) ||
                (!empty($oneresult['cmsms_max']) && version_compare(CMS_VERSION,$oneresult['cmsms_max'].'.99') > 0) ||
                (!empty($oneresult['php_max']) && version_compare(PHP_VERSION,$oneresult['php_max'].'.99') > 0) ) {
                $oneresult['status'] = 'incompatible';
            }
            elseif( !empty($oneresult['cmsms_tested']) && version_compare(CMS_VERSION,$oneresult['cmsms_tested']) > 0 ) {
                $oneresult['untested'] = true;
            }
            $results2[] = $oneresult;
        }
        $results = $results2;

        // Filter out incompatible modules unless preference is enabled
        if( !$mod->GetPreference('show_incompatible',0) ) {
            $results = array_filter($results, function($r) {
                return $r['status'] !== 'incompatible';
            });
            $results = array_values($results);
        }

        // now we have everything
        // let's try sorting it
        uasort( $results, array('modmgr_utils','uasort_cmp_details') );
        return $results;
    }

    public static function get_module_xml($filename,$size,$md5sum = null)
    {
        $mod = cms_utils::get_module('ModuleManager');
        $xml_filename = modmgr_rep_client::get_repository_xml($filename,$size);
        if( !$xml_filename ) throw new CmsCommunicationException($mod->Lang('error_downloadxml',$filename));

        if( !$md5sum ) $md5sum = modmgr_rep_client::get_module_md5($filename);
        $dl_md5 = md5_file($xml_filename);

        if( $md5sum != $dl_md5 ) {
            if( file_exists($xml_filename) ) unlink($xml_filename);
            throw new CmsInvalidDataException($mod->Lang('error_checksum',array($md5sum,$dl_md5)));
        }

        return $xml_filename;
    }

    public static function is_connection_ok()
    {
        static $ok = -1;
        if( $ok != -1 ) return $ok;

        $req = new modmgr_cached_request();
        $req->setTimeout(10);
        $req->execute('https://cdn.cmsmadesimple.org/repository/version.json', array(), 1440);
        if( $req->getStatus() == 200 ) {
            $tmp = $req->getResult();
            if( !empty($tmp) ) {
                $data = json_decode($tmp,true);
                if( version_compare($data,MINIMUM_REPOSITORY_VERSION) >= 0 ) {
                    $ok = TRUE;
                    return TRUE;
                }
            }
        }
        $req->clearCache();
        $ok = FALSE;
        return FALSE;
    }

    public static function get_status($date, $row = null)
    {
        if( $row ) {
            if( (!empty($row['cmsms_max']) && version_compare(CMS_VERSION,$row['cmsms_max'].'.99') > 0) ||
                (!empty($row['php_max']) && version_compare(PHP_VERSION,$row['php_max'].'.99') > 0) ) {
                return 'incompatible';
            }
            // Untested
            if( !empty($row['cmsms_tested']) && version_compare(CMS_VERSION,$row['cmsms_tested']) > 0 ) {
                return 'untested';
            }
        }

        // New module (< 3 months)
        $ts = strtotime($date);
        $new_ts = strtotime('-3 months');
        if( $ts >= $new_ts ) return 'new';

        return null;
    }

    public static function track_module_event($module_name, $event_type, $module_version)
    {
        try {
            $url = 'https://api.cmsmadesimple.org/v1/modules/' . urlencode($module_name) . '/events';
            $data = json_encode([
                'eventType' => $event_type,
                'cmsVersion' => CMS_VERSION,
                'moduleVersion' => $module_version
            ]);

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_TIMEOUT, 8);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 300);
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curl_error = curl_error($ch);
            curl_close($ch);

            audit('', 'ModuleManager', "Event tracking: {$event_type} {$module_name} {$module_version} => HTTP {$http_code}" . ($curl_error ? " Error: {$curl_error}" : '') . ($response ? " Response: {$response}" : ''));
        } catch (Exception $e) {
            audit('', 'ModuleManager', 'Event tracking failed: ' . $e->getMessage());
        }
    }

    public static function get_images()
    {
        // this is a bit ugly.
        $mod = cms_utils::get_module('ModuleManager');
        $smarty = cmsms()->GetSmarty();


        $stale_img=$mod->GetModuleURLPath().'/images/error.png';
        $stale_img = '<img src="'.$stale_img.'" title="'.$mod->Lang('title_stale').'" alt="stale" height="16"/>';
        $smarty->assign('stale_img',$stale_img);

        $stale_img=$mod->GetModuleURLPath().'/images/puzzle.png';
        $stale_img = '<img src="'.$stale_img.'" title="'.$mod->Lang('title_missingdeps').'" alt="missingdeps" height="24"/>';
        $smarty->assign('missingdep_img',$stale_img);

        $warn_img=$mod->GetModuleURLPath().'/images/warn.png';
        $warn_img = '<img src="'.$warn_img.'" title="'.$mod->Lang('title_warning').'" alt="warning" height="16"/>';
        $smarty->assign('warn_img',$warn_img);

        $new_img=$mod->GetModuleURLPath().'/images/new.png';
        $new_img = '<img src="'.$new_img.'" title="'.$mod->Lang('title_new').'" alt="new" height="16"/>';
        $smarty->assign('new_img',$new_img);

        $star_img=$mod->GetModuleURLPath().'/images/star.png';
        $star_img = '<img src="'.$star_img.'" title="'.$mod->Lang('title_star').'" alt="star" height="16"/>';
        $smarty->assign('star_img',$star_img);

        $system_img=$mod->GetModuleURLPath().'/images/system.png';
        $system_img = '<img src="'.$system_img.'" title="'.$mod->Lang('title_system').'" alt="system" height="16"/>';
        $smarty->assign('system_img',$system_img);

        $deprecated_img=$mod->GetModuleURLPath().'/images/deprecate.png';
        $deprecated_img = '<img src="'.$deprecated_img.'" title="'.$mod->Lang('title_deprecated').'" alt="deprecated" height="16"/>';
        $smarty->assign('deprecated_img',$deprecated_img);
    }
} // end of class

#
# EOF
#
