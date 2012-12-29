<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
#This project's homepage is: http://www.cmsmadesimple.org
#
#This program is free software; you can redistribute it and/or modify
#it under the terms of the GNU General Public License as published by
#the Free Software Foundation; either version 2 of the License, or
#(at your option) any later version.
#
#This program is distributed in the hope that it will be useful,
#but WITHOUT ANY WARRANTY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
#You should have received a copy of the GNU General Public License
#along with this program; if not, write to the Free Software
#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

function smarty_cms_function_form_start($params, &$template)
{
	$smarty   = $template->smarty;
	$tagparms = array();
	$mactparms = array();
	$tmp = $smarty->get_template_vars('actionparams');
	if( is_array($tmp) && isset($tmp['action']) ) {
		$mactparms['action'] = $tmp['action'];
	}
	$mactparms['module'] = $smarty->get_template_vars('actionmodule');
	$mactparms['mid'] = $smarty->get_template_vars('actionid');
	$mactparms['returnid'] = $smarty->get_template_vars('returnid');
	$mactparms['inline'] = 0;
	$tagparms['method'] = 'post';
	$tagparms['enctype'] = 'multipart/form-data';
	$tagparms['action'] = 'moduleinterface.php';
	if( cmsms()->test_state(CmsApp::STATE_ADMIN_PAGE) ) {
		if( !isset($mactparms['action']) ) {
			$mactparms['action'] = 'defaultadmin';
		}
		$mactparms['returnid'] = '';
	}
	else if( cmsms()->is_frontend_request() ) {
		if( !isset($mactparms['action']) ) {
			$mactparms['action'] = 'default';
		}
		$mactparms['mid'] = 'cntnt01';
	}

	if( $mactparms['returnid'] != '' ) {
		$hm = $gCms->GetHierarchyManager();
		$node = $hm->sureGetNodeById($returnid);
		if( $node ) {
			$content_obj = $node->getContent();
			if( $content_obj ) $tagparms['action'] = $content_obj->GetURL();
		}
	}

	$parms = array();
	foreach( $params as $key => $value ) {
		switch( $key ) {
			case 'module':
			case 'action':
			case 'mid':
			case 'returnid':
			case 'inline':
				$mactparms[$key] = trim($value);
				break;

			case 'method':
				$tagparms[$key] = strtolower(trim($value));
				break;

			case 'url':
				$key = 'action';
				if( dirname($value) == '.' ) {
					$config = cmsms()->GetConfig();
					$value = $config['admin_url'].'/'.trim($value);
				}
				$tagparms[$key] = trim($value);
				break;

			case 'enctype':
			case 'id':
			case 'class':
				$tagparms[$key] = trim($value);
				break;

			case 'extraparms':
				if( is_array($value) && count($value) ) {
					foreach( $value as $key=>$value2 ) {
						$parms[$key] = $value2;
					}
				}
				break;
			case 'assign':
				break;

			default:
				$parms[$key] = $value;
			break;
		}
	}

	$out = '<form';
	foreach( $tagparms as $key => $value ) {
		if( $value ) {
			$out .= " $key=\"$value\"";
		}
	}
	$out .= '><div class="hidden">';
	if( $mactparms['module'] && $mactparms['action'] ) {
		$mact = $mactparms['module'].','.$mactparms['mid'].','.$mactparms['action'].','.$mactparms['inline'];
		$out .= '<input type="hidden" name="mact" value="'.$mact.'"/>';
		if( $mactparms['returnid'] != '' ) {
			$out .= '<input type="hidden" name="'.$mactparms['mid'].'returnid" value="'.$mactparms['returnid'].'"/>';
		}
	}
	if( !isset($mactparms['returnid']) || $mactparms['returnid'] == '' ) {
		$out .= '<input type="hidden" name="'.CMS_SECURE_PARAM_NAME.'" value="'.$_SESSION[CMS_USER_KEY].'"/>';
	}
	foreach( $parms as $key => $value ) {
		$out .= '<input type="hidden" name="'.$mactparms['mid'].$key.'" value="'.$value.'"/>';
	}
	$out .= '</div>';

	if( isset($params['assign']) ) {
		$smarty->assign($params['assign'],$out);
		return;
	}
	return $out;
}
?>