<?php // -*- mode:php; tab-width:2; indent-tabs-mode:t; c-basic-offset:2; -*-
#-------------------------------------------------------------------------
# Module: AdminSearch - A CMSMS addon module to provide template management.
# (c) 2012 by Robert Campbell <calguy1000@cmsmadesimple.org>
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
if( !isset($gCms) ) exit;
if( !$this->CheckPermission('Manage Themes') ) return;

$this->SetCurrentTab('themes');
if( isset($params['cancel']) ) {
  $this->SetMessage($this->Lang('msg_cancelled'));
  $this->RedirectToAdminTab();
}

$theme = null;
try {
  if( !isset($params['theme']) || $params['theme'] == '' ) {
    $theme= new CmsLayoutTheme;
    $theme->set_name('New Theme');
  }
  else {
    $theme = CmsLayoutTheme::load($params['theme']);
  }

	try {
		if( isset($params['submit']) ) {
			$theme->set_name($params['name']);
			$theme->set_description($params['description']);
			$tpl_assoc = array();
			if( isset($params['assoc_tpl']) ) {
				$tpl_assoc = $params['assoc_tpl'];
			}
			$theme->set_templates($tpl_assoc);

			$css_assoc = array();
			if( isset($params['assoc_css']) ) {
				$css_assoc = $params['assoc_css'];
			}
			$theme->set_stylesheets($css_assoc);
			$theme->save();
			$this->SetMessage($this->Lang('msg_theme_saved'));
			$this->RedirectToAdminTab();
		}
	}
	catch( Exception $e ) {
		echo $this->ShowErrors($e->GetMessage());
	}

  $templates = CmsLayoutTemplate::get_editable_templates(get_userid());
  if( count($templates) ) {
    $smarty->assign('all_templates',$templates);
	}

	$stylesheets = StylesheetOperations::get_instance()->LoadStylesheets();
	if( is_array($stylesheets) && count($stylesheets) ) {
		$out = array();
		$out2 = array();
		for( $i = 0; $i < count($stylesheets); $i++ ) {
			$out[$stylesheets[$i]->id] = $stylesheets[$i]->name;
			$out2[$stylesheets[$i]->id] = $stylesheets[$i];
		}
		$smarty->assign('list_stylesheets',$out);
		$smarty->assign('all_stylesheets',$out2);
	}

  $smarty->assign('theme',$theme);
  echo $this->ProcessTemplate('admin_edit_theme.tpl');
}
catch( CmsException $e ) {
  $this->SetError($e->GetMessage());
  $this->RedirectToAdminTab();
}


#
# EOF
#
?>