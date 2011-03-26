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
if( !isset($gCms) ) exit;

// see if there are saved results
$search_data = '';
if( isset($_SESSION['modmgr_search']) )
  {
    $search_data = unserialize($_SESSION['modmgr_search']);
  }

if( isset($params['submit']) )
  {
    $url = $this->GetPreference('module_repository');
    $error = 0;
    $term = trim($params['term']);
    if( strlen($term) < 3 )
      {
	echo $this->ShowErrors($this->Lang('error_searchterm'));
	$error = 1;
      }

    $nusoap =& $this->GetModuleInstance('nuSOAP');
    $nusoap->Load();
    $nu_soapclient = '';
    if( !$error )
      {
	$nu_soapclient = new nu_soapclient($url,false,false,false,false,false,90,90);
	if( $err = $nu_soapclient->GetError() )
	  {
	    echo $this->ShowErrors('SOAP ERROR: '.$err);
	    $error = 1;
	  }
      }

    if( !$error )
      {
	$repversion = $nu_soapclient->call('ModuleRepository.soap_version');
	if( $err = $nu_soapclient->GetError() )
	  {
	    echo $this->ShowErrors('SOAP ERROR: '.$err);
	    $error = 1;
	  }
      }

    if( !$error )
      {
	if( version_compare($repversion,MINIMUM_REPOSITORY_VERSION) < 0 )
	  {
	    echo $this->ShowErrors($this->Lang('error_minimumrepository'));
	    $error = 1;
	  }
      }

    $res = '';
    if( !$error )
      {
	$qparms = array();
	$filter = array();
	$filter['term'] = $term;
	$filter['advanced'] = $params['advanced'];
	$filter['newest'] = 1;
	$filter['sortby'] = 'score';
	$qparms['filter'] = $filter;
	$qparms['clientcmsversion'] = CMS_VERSION;

	$res = $nu_soapclient->call('ModuleRepository.soap_search',$qparms);
	if( $err = $nu_soapclient->GetError() )
	  {
	    echo $this->ShowErrors('SOAP Error: '.$err);
	    echo $nu_soapclient->getDebug();
	    $error = 1;
	  }
      }

    if( !$error )
      {
	if( !$res || !is_array($res) )
	  {
	    echo $this->ShowMessage($this->Lang('search_noresults'));
	  }
	else
	  {
	    $search_data = array();
	    for( $i = 0; $i < count($res); $i++ )
	      {
		$row =& $res[$i];
		$obj = new stdClass();
		foreach( $row as $k => $v )
		  {
		    $obj->$k = $v;
		  }
		$obj->helplink = $this->CreateLink( $id, 'modulehelp', $returnid,
						    $this->Lang('helptxt'),
						    array('name'=>$row['name'],
							  'version'=>$row['version'],
							  'filename'=>$row['filename']));
		$obj->size = (int)((float) $row['size'] / 1024.0 + 0.5);
		$obj->rowclass = $rowclass;
		if( isset( $row['description'] ) )
		  {
		    $obj->description=$row['description'];
		  }

		$search_data[] = $obj;
	      }
	    $_SESSION['modmgr_search'] = serialize($search_data);
	  }
      }
  }

if( is_array($search_data) )
  {
    $smarty->assign('search_data',$search_data);
  }
$smarty->assign('formstart',$this->CreateFormStart($id,'defaultadmin','','post','',false,'',
						   array('active_tab'=>'search')));
$smarty->assign('formend',$this->CreateFormEnd());
$smarty->assign('actionid',$id);
$smarty->assign('mod',$this);

echo $this->ProcessTemplate('admin_search_tab.tpl');
#
# EOF
#
?>