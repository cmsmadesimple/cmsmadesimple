<?php // -*- mode:php; tab-width:2; indent-tabs-mode:t; c-basic-offset:2; -*-
#-------------------------------------------------------------------------
# Module: DesignManager - A CMSMS addon module to provide template management.
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

debug_to_log('admin_ajax');
debug_to_log($params);

$out = null;
try {
  if( isset($params['mode']) ) {
    switch( $params['mode'] ) {
    case 'template-tooltip':
      if( isset($params['tpl']) ) {
				$tpl = CmsLayoutTemplate::load((int)$params['tpl']);
				$smarty->assign('template',$tpl);
				$out = $this->ProcessTemplate('admin_ajax_template_tooltip.tpl');
      }
      break;
		case 'tpltype-tooltip':
			if( isset($params['type']) ) {
				$type = CmsLayoutTemplateType::load((int)$params['type']);
				$smarty->assign('tpltype',$type);
				$out = $this->ProcessTemplate('admin_ajax_templatetype_tooltip.tpl');
			}
			break;
    } // switch
  }

	debug_to_log($out,'output');
  echo $out;
  exit;
}
catch( Exception $e ) {
  audit('',$this->GetName(),'failed ajax request');
  debug_to_log($e->GetMessage());
	echo $e->GetMessage();
	exit;
}
#
# EOF
#
?>