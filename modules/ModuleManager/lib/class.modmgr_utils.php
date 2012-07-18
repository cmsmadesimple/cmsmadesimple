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

final class modmgr_utils
{
  protected function __construct() {}


  public static function get_installed_modules($include_inactive = FALSE, $as_hash = FALSE)
  {
    $modops = ModuleOperations::get_instance();
    $module_list = $modops->GetInstalledModules($include_inactive);

    $results = array();
    foreach( $module_list as $module_name )
      {
	$inst = $modops->get_module_instance($module_name);
	if( !$inst )
	  {
	    continue;
	  }

	$details = array();
	$details['name'] = $inst->GetName();
	$details['description'] = $inst->GetDescription();
	$details['version'] = $inst->GetVersion();
	$details['active'] = $modops->IsModuleActive($module_name);

	if( $as_hash )
	  {
	    $results[$module_name] = $details;
	  }
	else
	  {
	    $results[] = $details;
	  }
      }
    return array(true,$results);
  }


  private static function uasort_cmp_details( $e1, $e2 )
  {
    $n1 = $n2 = '';
    $v1 = $v2 = '';
    if( is_object($e1) ) 
      {
	$n1 = $e1->name; 
	$v1 = $e1->version;
      }
    else 
      {
	$n1 = $e1['name'];
	$v1 = $e1['version'];
      }
    if( is_object($e2) ) 
      {
	$n2 = $e2->name; 
	$v2 = $e2->version;
      }
    else 
      {
	$n2 = $e2['name'];
	$v2 = $e2['version'];
      }

    if( strcasecmp($n1,$n2) < 0 )
      {
	return -1;
      }
    elseif( strcasecmp($n1,$n2) > 0 )
      {
	return 1;
      }
    return version_compare( $e2['version'], $e1['version'] );
  }


  public static function build_module_data( &$xmldetails, &$installdetails, $newest = true )
  {
    if( !is_array($xmldetails) ) return;

    // sort
    uasort( $xmldetails, array('modmgr_utils','uasort_cmp_details') );

    $mod = cms_utils::get_module('ModuleManager');

    //
    // Process the xmldetails, and only keep the latest version
    // of each (according to a preference)
    //
    // Note: should be redundant with 1.2, but kept in here for
    // a while just in case..
    {
      if( $newest && $mod->GetPreference('onlynewest',1) == 1 )
	{
	  $thexmldetails = array();
	  $prev = '';
	  foreach( $xmldetails as $det )
	    {
	      if( is_array($prev) && $prev['name'] == $det['name'] )
		{
		  continue;
		}
	      
	      $prev = $det;
	      $thexmldetails[] = $det;
	    }
	  $xmldetails = $thexmldetails;
	}
    }

    $results = array();
    foreach( $xmldetails as $det1 )
      {
	$found = 0;
	foreach( $installdetails as $det2 )
	  {
	    if( $det1['name'] == $det2['name'] )
	      {
		$found = 1;
		// if the version of the xml file is greater than that of the
		// installed module, we have an upgrade
		$res = version_compare( $det1['version'], $det2['version'] );
		if( $res == 1 )
		  {
		    $det1['status'] = 'upgrade';
		  }
		else if( $res == 0 )
		  {
		    $det1['status'] = 'uptodate';
		  }
		else
		  {
		    $det1['status'] = 'newerversion';
		  }

		$results[] = $det1;
		break;
	      }
	  }
	if( $found == 0 )
	  {
	    // we don't have this module installed
	    $det1['status'] = 'notinstalled';
	    $results[] = $det1;
	  }
      }

    //
    // Do a third loop
    // and check min and max cms version
    //
    global $CMS_VERSION;
    $results2 = array();
    foreach( $results as $oneresult )
      {
	if( (!empty($oneresult['maxcmsversion']) && 
	     version_compare($CMS_VERSION,$oneresult['maxcmsversion']) > 0) ||
	    (!empty($oneresult['mincmsversion']) &&
	     version_compare($CMS_VERSION,$oneresult['mincmsversion']) < 0) )
	  {
	    $oneresult['status'] = 'incompatible';
	  }
	$results2[] = $oneresult;
      }
    $results = $results2;

    // now we have everything
    // let's try sorting it
    uasort( $results, array('modmgr_utils','uasort_cmp_details') );
    return $results;
  }


  public static function add_dependencies_to_list($startspec, $allmods, &$deplist)
  {
    list($res, $this_file_deps) = modulerep_client::get_module_depends($startspec);
    if (! $res)
      {
	return array(false, $this_file_deps);				
      }
    $mod = cms_utils::get_module('ModuleManager');
    if (is_array($this_file_deps))
      {
	foreach($this_file_deps as $this_dep)
	  {
	    $found = false;
	    foreach ($allmods[1] as $tm)
	      {
		if ($tm['name'] == $this_dep['name'] && version_compare($tm['version'], $this_dep['version']) >= 0)
		  {
		    // found the module - add to list; we'll de-dupe later
		    $found = true;
		    $newDep = array('filename'=>$tm['filename'],'name'=>$tm['name'],
				    'version'=>$tm['version'],'by'=>$startspec,
				    'size'=>$tm['size']);
		    $deplist[] = $newDep;
		    self::add_dependencies_to_list($tm['filename'], $allmods, $deplist);

		    if( !$mod->GetPreference('latestdepends',1) ) break;
		  }
	      }
	    if (! $found)
	      {
		// check if it's an installed module (maybe it's a core module that isn't distributed)
		$tmp = array($this_dep);
		$res = self::find_unfulfilled_dependencies($tmp);
		if( !$res )
		  {
		    return array(false, $mod->Lang('error_unsatisfiable_dependency', 
						   array($this_dep['name'],$this_dep['version'], 
							 self::file_to_module_name($allmods[1],$startspec) )));
		  }
	      }
	  }
      }
    return array(true,'');
  }


  public static function remove_duplicate_dependencies($deps)
  {
    $output = array();
    for( $i = 0; $i < count($deps); $i++ )
      {
	$name = $deps[$i]['name'];
	if( isset($output[$name]) )
	  {
	    if( version_compare($output[$name]['version'],$deps[$i]['version'],'<') )
	      {
		$output[$name] = $deps[$i];
	      }
	  }
	else
	  {
	    $output[$name] = $deps[$i];
	  }
      }
    return $output;
  }


  /* Given an array of module dependencies (as returned from _DoRecursiveInstall),
	 this returns a boolean indicating whether they're all satisfied.
	 It also modified the incoming array to add a status for each dependency, indicating
	 what, if anything, needs to be done:
		s - satisfied
		a - needs to be activated
		i - needs to be installed
		u - needs to be upgraded
  */
  public static function find_unfulfilled_dependencies(&$depends)
  {
    $satisfied = true;
    if( is_array($depends) )
      {
	$installed = self::get_installed_modules(true,true);
	if (is_array($installed[1]))
	  {
	    // check dependencies against what is installed.
	    foreach( $depends as $depkey=>$onedep )
	      {
		if (isset($installed[1][$onedep['name']]) ) 
		  {
		    $mod = $installed[1][$onedep['name']];
		    if ($mod['active'])
		      {
			if (version_compare($mod['version'],$onedep['version']) >= 0 )
			  {
			    $depends[$depkey]['status'] = 's';
			    continue;
			  }
			else
			  {
			    $depends[$depkey]['status'] = 'u';
			  }							
		      }
		    else
		      {
			$depends[$depkey]['status'] = 'a';
		      }
		  }
		if (! isset($depends[$depkey]['status']))
		  {
		    $depends[$depkey]['status'] = 'i';
		  }
		$satisfied = false;
	      }
	  }
      }
    return $satisfied;
  }


  public static function file_to_module_name(&$allmods,$filename)
  {
    $mod = cms_utils::get_module('ModuleManager');
    foreach ($allmods as $tm)
      {
	if ($tm['filename'] == $filename)
	  {
	    return $mod->Lang('mod_name_ver',array($tm['name'],$tm['version']));
	  }
      }
    return $mod->Lang('unknown');
  }
		

  public static function install_module($module_meta,$is_upgrade = FALSE)
  {
    // get the module xml to a temporary location
    $mod = cms_utils::get_module('ModuleManager');
    $xml_filename = modulerep_client::get_repository_xml($module_meta['filename'],$module_meta['size']);
    if( !$xml_filename )
      return array(FALSE,$mod->Lang('error_downloadxml',$module_meta['filename']));
      
    // get the md5sum of the data from the server.
    $server_md5 = modulerep_client::get_module_md5($module_meta['filename']);

    // verify the md5
    $dl_md5 = md5_file($xml_filename);
    if( $server_md5 != $dl_md5 )
      {
	@unlink($xml_filename);
	return array(FALSE,$mod->Lang('error_checksum',array($server_md5,$dl_md5)));
      }

    // expand the xml
    $ops = cmsms()->GetModuleOperations();
    if( !$ops->ExpandXMLPackage( $xml_filename, 1 ) )
      {
	debug_display('error:'); die($ops->GetLastError());
	return array(FALSE,$ops->GetLastError());
      }

    @unlink($xml_filename);

    // update the database.
    ModuleOperations::get_instance()->QueueForInstall($module_meta['name']);
    return array(true,'');
  }


  public static function install_module_list($installs)
  {
    if( !is_array($installs) || count($installs) == 0 ) return FALSE;

    $mod = cms_utils::get_module('ModuleManager');
    $messages = array();
    $ok = true;
    $modops = cmsms()->GetModuleOperations();
    foreach($installs as $this_inst)
      {
	$thisRes = new stdClass();
	$thisRes->success = false;
	$thisRes->module_name = $this_inst['name'];
	if ($ok)
	  {
	    if ($this_inst['status'] == 'a')
	      {
		// activating
		$modops->ActivateModule($this_inst['name']);
		$thisRes->message = $mod->Lang('action_activated',$this_inst['name']);
		$thisRes->success = true;
	      }
	    else if ($this_inst['status'] == 'u')
	      {
		// upgrading	
		list($success, $msgs) = self::install_module($this_inst, true);
		if (!$success)
		  {
		    $ok = false;
		  }
		else
		  {
		    $thisRes->success = true;
		  }
		$thisRes->message = $msgs;
	      }
	    else if ($this_inst['status'] == 'i')
	      {
		// installing
		list($success, $msgs) = self::install_module($this_inst, false);
		if (!$success)
		  {
		    $ok=false;
		  }
		else
		  {
		    $thisRes->success = true;
		  }
		$thisRes->message = $msgs;
	      }
	  }
	else
	  {
	    $thisRes->message = $mod->Lang('error_skipping',$this_inst['name']);
	  }
	if ($this_inst['status'] != 's')
	  {
	    $messages[] = $thisRes;
	  }
      }

    return $messages;
  }


  public static function is_connection_ok()
  {
    static $ok = -1;
    if( $ok != -1 ) return $ok;

    $mod = cms_utils::get_module('ModuleManager');
    $url = $mod->GetPreference('module_repository');
    if( $url )
      {
	$url .= 'version';
	$req = new modmgr_cached_request($url);
	$req->setTimeout(3);
	$req->execute($url);
	if( $req->getStatus() == 200 )
	  {
	    $tmp = $req->getResult();
	    if( empty($tmp) )
	      {
		$req->clearCache();
		$ok = FALSE;
		return FALSE;
	      }

	    $data = json_decode($req->getResult(),true);
	    if( version_compare($data,MINIMUM_REPOSITORY_VERSION) >= 0 )
	      {
		$ok = TRUE;
		return TRUE;
	      }
	  }
	else
	  {
	    $req->clearCache();
	  }
      }
    $ok = FALSE;
    return FALSE;
  }
    
} // end of class

#
# EOF
#
?>
