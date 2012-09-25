<?php
#-------------------------------------------------------------------------
# Module: ThemeManager - a module for importing and exporting template
#   and stylesheet packages.
# Version: 1.0.6, Robert Campbell <rob@techcom.dyndns.org>
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
if (isset($params["message"]))echo $this->ShowMessage($params["message"]);
if (isset($params["error"]))echo $this->ShowErrors($params["error"]);

    echo $this->StartTabHeaders();
    if ($this->CheckPermission('Manage Themes'))
      {
	echo $this->SetTabHeader('export', $this->Lang('export'));
      }
    if ($this->CheckPermission('Add Stylesheets') &&
	$this->CheckPermission('Add Templates') &&
	$this->CheckPermission('Add Stylesheet Assoc'))
      {
	echo $this->SetTabHeader('import', $this->Lang('import'));
      }
    echo $this->EndTabHeaders();

    echo $this->StartTabContent();

    // the export tab
    if ($this->CheckPermission('Manage Themes'))
      {
	echo $this->StartTab('export');
	$gCms = cmsms();
	$templateops =& $gCms->GetTemplateOperations();
	$alltemplates = $templateops->LoadTemplates();
	
	$this->smarty->assign('startform',
			      $this->CreateFormStart($id,'exporttheme',$returnid));
	
	$this->smarty->assign('idtext', $this->Lang('id'));
	$this->smarty->assign('nametext', $this->Lang('name'));
	$this->smarty->assign('activetext', $this->Lang('active'));
	$this->smarty->assign('defaulttext', $this->Lang('default'));
	$this->smarty->assign('exporttext', $this->Lang('export'));
	
	// iterate through all of the templates
	$gCms = cmsms();
	$rowclass = 'row1';
	$rowarray = array();
	foreach( $alltemplates as $templ )
	  {
	    $onerow = new stdClass();
	    $onerow->id = $templ->id;
	    $onerow->name = $templ->name;
	    if( $templ->active )
	      {
		$onerow->active = $gCms->variables['admintheme']->DisplayImage('icons/system/true.gif',lang('true'));
	      }
	    if( $templ->default )
	      {
		$onerow->default = $gCms->variables['admintheme']->DisplayImage('icons/system/true.gif',lang('true'));
	      }
	      else
	      {
		$onerow->default = '';
	      }
	    $onerow->rowclass = $rowclass;
	    if( $templ->active )
	      {
		$onerow->select = 
		  $this->CreateInputCheckbox( $id, 'export'.$onerow->id, $onerow->id );
	      }
	    $rowarray[] = $onerow;
	    ($rowclass == "row1" ? $rowclass = "row2" : $rowclass = "row1");
	  }
	$this->smarty->assign ('items', $rowarray);
	$this->smarty->assign ('itemcount', count($rowarray));
	
	$this->smarty->assign('prompt_themename',
			      $this->Lang('prompt_themename'));
	$this->smarty->assign('input_themename',
			      $this->CreateInputText($id,'input_themename','',20,20));
	$this->smarty->assign('info_themename',
			      $this->Lang('info_themename'));
	$this->smarty->assign('submit',
			      $this->CreateInputSubmit($id,'submit',$this->Lang('export')));
	$this->smarty->assign('endform',
			      $this->CreateFormEnd());
	echo $this->ProcessTemplate('export.tpl');
	echo $this->EndTab();
      }

    // the import tab
    if ($this->CheckPermission('Add Stylesheets') &&
	$this->CheckPermission('Add Templates') &&
	$this->CheckPermission('Add Stylesheet Assoc'))
      {
	echo $this->StartTab('import');
	if( FALSE == can_admin_upload() )
	  {
	    echo '<div class="pageerrorcontainer"><div class="pageoverflow"><p class="pageerror">'.$this->Lang('error_uploadpermissions').'</p></div></div>';
	    return;
	  }
	else
	  {
	    $this->smarty->assign('startform',
				  $this->CreateFormStart($id,'importtheme',$returnid,'post', 'multipart/form-data'));
	    $this->smarty->assign('prompt_browse', $this->Lang('upload'));
	    $this->smarty->assign('input_browse',
				  $this->CreateFileUploadInput($id,'input_browse','',30));
	    $this->smarty->assign('submit', 
				  $this->CreateInputSubmit($id,'submit',
							   $this->Lang('import')));
	    $this->smarty->assign('endform',
				  $this->CreateFormEnd());
	    echo $this->ProcessTemplate('import.tpl');
	  }
	echo $this->EndTab();
      }
	echo "</div>"; // <--strange that one, but valid...
?>