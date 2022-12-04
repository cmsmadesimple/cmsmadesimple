<?php
#BEGIN_LICENSE
#-------------------------------------------------------------------------
# Module: ModuleManager (c) 2008 by Robert Campbell
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
if (!isset($gCms)) exit;
if( !$this->CheckPermission('Modify Modules') ) return;
$this->SetCurrentTab('modules');

if( isset($params['cancel']) ) {
  $this->SetMessage($this->Lang('msg_cancelled'));
  $this->RedirectToAdminTab();
}

try {
    $module_name = get_parameter_value($params,'name');
    $module_version  = get_parameter_value($params,'version');
    $module_filename  = get_parameter_value($params,'filename');
    $module_size = get_parameter_value($params,'size');
    if( !isset($params['doinstall']) ) {
        if( $module_name == '' || $module_version == '' || $module_filename == '' || $module_size < 100 ) {
            throw new CmsInvalidDataException( $this->Lang('error_missingparams') );
        }
    }

    if( isset($params['submit']) ) {
        // phase one... organize and download
        set_time_limit(9999);
        if( isset($params['modlist']) && $params['modlist'] != '' ) {
            $modlist = json_decode(base64_decode($params['modlist']),TRUE);
            if( !is_array($modlist) || count($modlist) == 0 ) throw new CmsInvalidDataException( $this->Lang('error_missingparams') );

            // cache all of the xml files first... make sure we can download everything, and that it gets cached.
            foreach( $modlist as $key => $rec ) {
                if( $rec['action'] != 'i' && $rec['action'] != 'u' ) continue;
                if( !isset($rec['filename']) ) throw new CmsInvalidDataException( $this->Lang('error_missingparams') );
                if( !isset($rec['size']) ) throw new CmsInvalidDataException( $this->Lang('error_missingparams') );
                $filename = modmgr_utils::get_module_xml($rec['filename'],$rec['size']);
            }

            // expand all of the xml files.
            $ops = cmsms()->GetModuleOperations();
            foreach( $modlist as $key => &$rec ) {
                if( $rec['action'] != 'i' && $rec['action'] != 'u' ) continue;
                $xml_filename = modmgr_utils::get_module_xml($rec['filename'],$rec['size'],(isset($rec['md5sum']))?$rec['md5sum']:'');
                $rec['tmpfile'] = $xml_filename;
                $res = $ops->ExpandXMLPackage( $xml_filename, 1 );
            }

            // now put this data into the session and redirect for the install part
            $key = '_'.md5(__FILE__.time());
            $_SESSION[$key] = $modlist;
            $this->Redirect($id,'installmodule',$returnid,['doinstall'=>$key]);
        }
    }

    if( isset($params['doinstall']) ) {
        $key = trim($params['doinstall']);
        if( !isset($_SESSION[$key]) ) throw new \LogicException('No doinstall data found in the session');

        set_time_limit(999);
        $modlist = $_SESSION[$key];
        if( !is_array($modlist) || !count($modlist) ) throw new \LogicException('Invalid modlist data found in session');
        unset($_SESSION[$key]);

        // note: by here, the $modlist should represent a set of instructions, in the proper order.

        // install/upgrade the modules that need to be installed or upgraded.
        $ops = cmsms()->GetModuleOperations();
        foreach( $modlist as $name => $rec ) {
            switch( $rec['action'] ) {
            case 'i': // install
                $res = $ops->InstallModule($name);
                break;
            case 'u': // upgrade
                $res = $ops->UpgradeModule($name,$rec['version']);
                break;
            case 'a': // activate
                $res = $ops->ActivateModule($name);
                $res = [ $res ];
                break;
            }

            if( !is_array($res) || !$res[0] ) {
                audit('',$this->GetName(),' Problem installing, upgrading or activating '.$name);
                debug_buffer('ERROR: problem installing/upgrading/activating '.$name);
                debug_buffer($rec,'action record');
                debug_buffer($res,'error info');
                throw new CmsException( (isset($res[1])) ? $res[1] : 'Error processing module '.$name);
            }
        }

        // done, rest will be done when the module is loaded.
        $this->RedirectToAdminTab();
    }

    // recursive function to resolve dependencies given a module name and a module version
    $mod = $this;
    $resolve_deps = function($module_name,$module_version,$uselatest,$depth = 0) use (&$resolve_deps,&$mod) {

        $array_to_hash = function($in,$key) {
            $out = array();
            $idx = 0;
            foreach( $in as $rec ) {
                if( isset($rec[$key]) ) {
                    $out[$rec[$key]] = $rec;
                } else {
                    $out[$idx++] = $rec;
                }
            }
            return $out;
        };

        $extract_member = function($in,$key) {
            $out = array();
            foreach( $in as $rec ) {
                if( isset($rec[$key]) ) $out[] = $rec[$key];
            }
            if( count($out) ) {
                $out = array_unique($out);
                return $out;
            }
        };

        $update_latest_deps = function($indeps,$latest) use (&$mod) {
            $out = array();
            foreach( $indeps as $name => $onedep ) {
                if( isset($latest[$name]) ) {
                    $out[$name] = $latest[$name];
                } else {
                    // module not found in forge?? could be a system module,
                    // but it's still a dependency.
                    if( !ModuleOperations::get_instance()->IsSystemModule($name) ) throw new \CmsInvalidDataException($mod->Lang('error_dependencynotfound2',$name,$onedep['version']));
                    $out[$name] = $onedep;
                }
            }
            return $out;
        };

        $deps = null;
        list($res,$deps) = modulerep_client::get_module_dependencies($module_name,$module_version);
        if( is_array($deps) && count($deps) ) {

            $deps = $array_to_hash($deps,'name');
            $dep_module_names = $extract_member($deps,'name');

            if( $uselatest ) {
                // we want the latest of all of the dependencies.
		$latest = null;
		try {
                    $latest = modulerep_client::get_modulelatest($dep_module_names);
                    if( $latest ) {
                        // throw new CmsInvalidDataException($this->Lang('error_dependencynotfound'));
                        $latest = $array_to_hash($latest,'name');
                        $deps = $update_latest_deps($deps,$latest);
	            }
		}
                catch( ModuleNoDataException $e ) {
		    // nothing here
                }
            } else {
                $info = modulerep_client::get_multiple_moduleinfo($deps);
                $info = $array_to_hash($info,'name');
                $deps = $update_latest_deps($deps,$info);
            }

            foreach( $deps as $row ) {
                // now see if these dependencies, have dependencies.
                $child_deps = $resolve_deps($row['name'],$row['version'],$uselatest,$depth + 1);

                // grab the latest version of any duplicates
                if( $child_deps ) {
                    foreach( $child_deps as $child_name => $child_row ) {
                        if( !isset($deps[$child_name]) ) {
                            $tmp = [$child_name => $child_row];
                            $deps = $tmp + $deps;
                            //$deps[$child_name] = $child_row;
                        } else {
                            if( version_compare($deps[$child_name]['version'],$child_row['version']) < 0 ) $deps[$child_name] = $child_row;
                        }
                    }
                }
            }
        }

        return $deps;
    };

    // algorithm
    // given a desired module name, module version, and wether we want latest versions
    // get module dependencies for the target module version
    // if we want latest versions of dependants
    //   get latest version info for all dependencies
    //   get module dependencies again as they may have changed
    //   merge results
    // else
    //   get module info for all dependencies

    // recursively (depth first) get the dependencies for the module+version we specified.
    $alldeps = array();
    $uselatest = (int) $this->GetPreference('latestdepends',1);
    $alldeps = $resolve_deps($module_name,$module_version,$uselatest);

    // get information for all dependencies, and make sure that they are all there.
    if( is_array($alldeps) && count($alldeps) ) {
        $res = null;
        try {
            if( $this->GetPreference('latestdepends',1) ) {
                // get the latest version of dependency (but not necessarily of the module we're installing)
                $res = modulerep_client::get_modulelatest(array_keys($alldeps));
                $new_deps = array();
            }
            else {
                // get the info for all dependencies
                $res = modulerep_client::get_multiple_moduleinfo($alldeps);
            }
        }
        catch( \ModuleNoDataException $e ) {
            // at least one of the dependencies could not be found on the server.
            // may be a system module... if it is not a system module, throw an exception
            audit('','ModuleManager','At least one requested module was not available on the forge ('.$this->GetName().' '.$this->GetVersion().')');
        } 

        foreach( $alldeps as $name => $row ) {
            $fnd = FALSE;
            $tmp = null;
            if( is_array($res) && count($res) ) {
                foreach( $res as $rec ) {
                    if( $rec['name'] != $name ) continue;
                    $tmp = version_compare($row['version'],$rec['version']);
                    if( $tmp <= 0 ) {
                        $fnd = TRUE;
                        $alldeps[$name] = $rec;
                        break;
                    }
                }
            }
        }
    }

    // add our current item into alldeps.
    $alldeps[$module_name] = array('name'=>$module_name,'version'=>$module_version,'filename'=>$module_filename,'size'=>$module_size);

    // remove items that are already installed (where installed version is greater or equal)
    // and create actions as to what we're going to do.
    if( count($alldeps) ) {
        $allmoduleinfo = ModuleManagerModuleInfo::get_all_module_info(FALSE);
        foreach( $alldeps as $name => &$rec ) {
            $rec['has_custom'] = FALSE;
            if( isset($allmoduleinfo[$name]) ) $rec['has_custom'] = ($allmoduleinfo[$name]['has_custom']) ? TRUE : FALSE;
            if( !isset($allmoduleinfo[$name]) ) {
                // install
                $rec['action']='i';
            }
            else if( version_compare($allmoduleinfo[$name]['version'],$rec['version']) < 0 ) {
                // upgrade
                $rec['action']='u';
            }
            else if( !$allmoduleinfo[$name]['active'] ) {
                // activate
                $rec['action']='a';
            }
            else {
                // already installed, do nothing.
                unset($alldeps[$name]);
            }
        }
    }

    // test to make sure we have the required info for each record.
    foreach( $alldeps as $mname => &$rec ) {
        if( $rec['action'] == 'a' ) continue; // if just activating we don't have to worry.
        if( !isset($rec['filename']) ) throw new CmsInvalidDataException( $this->Lang('error_missingmoduleinfo',$mname) );
        if( !isset($rec['version']) ) throw new CmsInvalidDataException( $this->Lang('error_missingmoduleinfo',$mname) );
        if( !isset($rec['size']) ) throw new CmsInvalidDataException( $this->Lang('error_missingmoduleinfo',$mname.' '.$rec['version']) );
    }

    // here, if alldeps is empty... we have nothing to do.
    if( !count($alldeps) ) {
        $this->SetError($this->Lang('err_nothingtodo'));
        $this->RedirectToAdminTab();
    }

    $smarty->assign('return_url',$this->create_url($id,'defaultadmin',$returnid, array('__activetab'=>'modules')));
    $parms = array('name'=>$module_name,'version'=>$module_version,'filename'=>$module_filename,'size'=>$module_size);
    $smarty->assign('form_start',$this->CreateFormStart($id, 'installmodule', $returnid, 'post', '', FALSE, '', $parms).
                    $this->CreateInputHidden($id,'modlist',base64_encode(json_encode($alldeps))));
    $smarty->assign('formend',$this->CreateFormEnd());
    $smarty->assign('module_name',$module_name);
    $smarty->assign('module_version',$module_version);
    $tmp = array_keys($alldeps);
    $n = count($tmp) - 1;
    $key = $tmp[$n];
    $action = $alldeps[$key]['action'];
    $smarty->assign('is_upgrade',($action == 'u')?1:0);

    $smarty->assign('dependencies',$alldeps);
    echo $this->ProcessTemplate('installinfo.tpl');
    return;
}
catch( Exception $e ) {
    $msg = $e->GetMessage();
    if( !$msg ) $msg = get_class($e);
    $this->SetError($msg);
    $this->RedirectToAdminTab();
}
#
# EOF
#
