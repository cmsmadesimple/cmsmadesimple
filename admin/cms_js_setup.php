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
#but WITHOUT ANY WARRANthe TY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
#You should have received a copy of the GNU General Public License
#along with this program; if not, write to the Free Software
#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
#$Id: moduleinterface.php 8558 2012-12-10 00:59:49Z calguy1000 $

$CMS_ADMIN_PAGE=1;
require_once("../include.php");
$urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];
check_login();
$config = cmsms()->GetConfig();

//
// get some urls.
//
$data = array();
$data['ajax_help_url'] = 'ajax_help.php'.$urlext;
$data['title_help'] = lang('help');
$data['lang_alert'] = lang('alert');
$data['lang_error'] = lang('error');
$data['lang_ok'] = lang('ok');
$data['lang_cancel'] = lang('cancel');
$data['lang_confirm'] = lang('confirm');
$data['lang_yes'] = lang('yes');
$data['lang_no'] = lang('no');
$data['admin_url'] = $config['admin_url'];
$data['secure_param_name'] = CMS_SECURE_PARAM_NAME;
$data['user_key'] = $_SESSION[CMS_USER_KEY];

// output some javascript
$out = 'var cms_data = {}'."\n";
foreach( $data as $key => $value ) {
  $out .= "cms_data['{$key}'] = '{$value}';\n";
}

$out .= <<<EOT
function cms_lang(key) {
  key = 'lang_'+key;
  if( typeof(cms_data[key]) != 'undefined' ) return cms_data[key];
}
EOT;
header('Pragma: public');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Cache-Control: private',false);
header('Content-type: text/javascript');
echo $out;
exit;
?>