<?php
#--------------------------------------------------
# See DOCS/LICENSE for full license information.
#--------------------------------------------------

final class modmgr_install_helper
{
    protected function __construct() {}

    public static function array_to_hash($in, $key)
    {
        $out = [];
        $idx = 0;
        foreach( $in as $rec ) {
            $out[isset($rec[$key]) ? $rec[$key] : $idx++] = $rec;
        }
        return $out;
    }

    public static function update_latest_deps($indeps, $latest)
    {
        $mod = cms_utils::get_module('ModuleManager');
        $ops = ModuleOperations::get_instance();
        $out = [];
        foreach( $indeps as $name => $onedep ) {
            if( isset($latest[$name]) ) { $out[$name] = $latest[$name]; continue; }
            // Not in repository — check if it's a system module or already installed locally
            if( $ops->IsSystemModule($name) ) { $out[$name] = $onedep; continue; }
            $inst = $ops->get_module_instance($name);
            if( is_object($inst) && version_compare($inst->GetVersion(), $onedep['version']) >= 0 ) continue;
            throw new \CmsInvalidDataException($mod->Lang('error_dependencynotfound2', $name, $onedep['version']));
        }
        return $out;
    }

    public static function resolve_dependencies($module_name, $module_version, $uselatest, $depth = 0)
    {
        list($res, $deps) = modmgr_rep_client::get_module_dependencies($module_name, $module_version);
        if( !is_array($deps) || !count($deps) ) return $deps;

        $deps = self::array_to_hash($deps, 'name');
        $dep_names = array_unique(array_filter(array_column($deps, 'name')));
        if( !$dep_names ) return $deps;

        if( $uselatest ) {
            try {
                $latest = modmgr_rep_client::get_modulelatest($dep_names);
                if( $latest ) $deps = self::update_latest_deps($deps, self::array_to_hash($latest, 'name'));
            }
            catch( ModuleNoDataException $e ) {
                audit('', 'ModuleManager', 'No data for dependencies: ' . $e->GetMessage());
            }
        } else {
            $info = self::array_to_hash(modmgr_rep_client::get_multiple_moduleinfo($deps), 'name');
            $deps = self::update_latest_deps($deps, $info);
        }

        foreach( $deps as $row ) {
            $child_deps = self::resolve_dependencies($row['name'], $row['version'], $uselatest, $depth + 1);
            if( !$child_deps ) continue;
            foreach( $child_deps as $child_name => $child_row ) {
                if( !isset($deps[$child_name]) ) {
                    $deps = [$child_name => $child_row] + $deps;
                } elseif( version_compare($deps[$child_name]['version'], $child_row['version']) < 0 ) {
                    $deps[$child_name] = $child_row;
                }
            }
        }

        return $deps;
    }

    public static function execute_queued_actions($doinstall_key, $mod)
    {
        $key = trim($doinstall_key);
        $tmp_file = TMP_CACHE_LOCATION . '/' . $key . '.json';
        if( !file_exists($tmp_file) ) throw new \LogicException('No doinstall data found');

        set_time_limit(999);
        $modlist = json_decode(file_get_contents($tmp_file), true);
        @unlink($tmp_file);
        if( !is_array($modlist) || !count($modlist) ) throw new \LogicException('Invalid modlist data');

        $ops = cmsms()->GetModuleOperations();
        foreach( $modlist as $name => $rec ) {
            $res = self::execute_module_action($ops, $name, $rec);
            if( !is_array($res) || !$res[0] ) {
                audit('', $mod->GetName(), 'Problem installing, upgrading or activating '.$name);
                throw new CmsException(isset($res[1]) ? $res[1] : 'Error processing module '.$name);
            }
        }
    }

    public static function execute_module_action($ops, $name, $rec)
    {
        $action_map = ['i' => 'install', 'u' => 'upgrade', 'a' => 'activate'];
        $action = $rec['action'] ?? '';

        if( $action === 'i' ) {
            $res = $ops->InstallModule($name);
        } elseif( $action === 'u' ) {
            $res = $ops->UpgradeModule($name, $rec['version']);
        } elseif( $action === 'a' ) {
            return [$ops->ActivateModule($name)];
        } else {
            return [false, 'Unknown action: '.$action];
        }

        if( is_array($res) && $res[0] && isset($action_map[$action]) ) {
            modmgr_utils::track_module_event($name, $action_map[$action], $rec['version']);
        }
        return $res;
    }

    public static function merge_latest_dep_info($alldeps, $uselatest, $mod)
    {
        if( !is_array($alldeps) || !count($alldeps) ) return $alldeps;

        $res = null;
        try {
            $res = $uselatest
                ? modmgr_rep_client::get_modulelatest(array_keys($alldeps))
                : modmgr_rep_client::get_multiple_moduleinfo($alldeps);
        }
        catch( \ModuleNoDataException $e ) {
            audit('', 'ModuleManager', 'At least one requested module was not available on the forge ('.$mod->GetName().' '.$mod->GetVersion().')');
        }

        if( !is_array($res) || !count($res) ) return $alldeps;

        $res_hash = self::array_to_hash($res, 'name');
        foreach( $alldeps as $name => $row ) {
            if( !isset($res_hash[$name]) ) continue;
            if( version_compare($row['version'], $res_hash[$name]['version']) <= 0 ) {
                $alldeps[$name] = $res_hash[$name];
            }
        }
        return $alldeps;
    }

    public static function assign_actions($alldeps)
    {
        $allmoduleinfo = ModuleManagerModuleInfo::get_all_module_info(FALSE);
        foreach( $alldeps as $name => &$rec ) {
            $rec['has_custom'] = isset($allmoduleinfo[$name]) && !empty($allmoduleinfo[$name]['has_custom']);

            if( !isset($allmoduleinfo[$name]) ) { $rec['action'] = 'i'; continue; }
            if( version_compare($allmoduleinfo[$name]['version'], $rec['version']) < 0 ) { $rec['action'] = 'u'; continue; }
            if( !$allmoduleinfo[$name]['active'] ) { $rec['action'] = 'a'; continue; }
            unset($alldeps[$name]);
        }
        return $alldeps;
    }

    public static function validate_deps($alldeps, $mod)
    {
        foreach( $alldeps as $mname => $rec ) {
            if( ($rec['action'] ?? '') == 'a' ) continue;
            if( !isset($rec['filename']) ) throw new CmsInvalidDataException($mod->Lang('error_missingmoduleinfo', $mname));
            if( !isset($rec['version']) ) throw new CmsInvalidDataException($mod->Lang('error_missingmoduleinfo', $mname));
            if( !isset($rec['size']) ) throw new CmsInvalidDataException($mod->Lang('error_missingmoduleinfo', $mname.' '.$rec['version']));
        }
    }
}

#
# EOF
#
