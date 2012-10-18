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

try {
  if( !isset($params['theme']) ) {
    throw new CmsException($this->Lang('error_missingparam'));
  }
  $theme = CmsLayoutTheme::load($params['theme']);

  $can_delete_stylesheets = $this->CheckPermission('Remove Stylesheets');
  $can_delete_templates = $this->CheckPermission('Modify Templates');

  if( isset($params['submit']) ) {
    if( !isset($params['confirm_delete']) || $params['confirm_delete'] != 'yes' ) {
      $this->SetError($this->Lang('error_notconfirmed'));
      $this->RedirectToAdminTab();
    }

    if( isset($params['delete_templates']) && $can_delete_templates ) {
      $tpl_id_list = $theme->get_templates();
      if( is_array($tpl_id_list) && count($tpl_id_list) ) {
				$templates = CmsLayoutTemplate::load_bulk($tpl_id_list);
				if( is_array($templates) && count($templates) ) {
					foreach( $templates as &$tpl ) {
						$x = $tpl->get_themes();
						if( is_array($x) && count($x) == 1 && $x[0] == $theme->get_id() ) {
							// its orphaned
							$tpl->delete();
						}
					}
				}
      }
    }

    if( isset($params['delete_stylesheets']) && $can_delete_stylesheets ) {
      $css_id_list = $theme->get_stylesheets();
      if( is_array($css_id_list) && count($css_id_list) ) {
				// get the themes that are attached to these stylesheets
				$query = 'SELECT theme_id FROM '.cms_db_prefix().CmsLayoutTheme::CSSTABLE.'
                  WHERE css_id != ?';
				foreach( $css_id_list as $css_id ) {
					$dbr = $db->GetOne($query,array($css_id));
					if( $dbr ) continue; // not orphaned
					// here it's orphaned, we'll nuke it.
					$ops = StylesheetOperations::get_instance()->DeleteStylesheetByID($css_id);
				}
      }
    }

    // done.
    $theme->delete();
    $this->SetMessage($this->Lang('msg_theme_deleted'));
    $this->RedirectToAdminTab();
  }

  $smarty->assign('tpl_permission',$can_delete_templates);
  $smarty->assign('css_permission',$can_delete_stylesheets);
  $smarty->assign('theme',$theme);
  echo $this->ProcessTemplate('admin_delete_theme.tpl');
}
catch( CmsException $e ) {
  $this->SetError($e->GetMessage());
  $this->RedirectToAdminTab();
}
