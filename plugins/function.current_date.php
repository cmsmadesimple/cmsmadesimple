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

  // this method is deprecated and should be removed.
  // it is redundant.
function smarty_cms_function_current_date($params, &$template) {
  $smarty = $template->smarty;
  $format = '%b %c, %Y';
  if( isset($params['format']) && !empty($params['format']) )
    $format = trim($params['format']);
  
  $string = strftime($format,time());  
  if(isset($params['ucwords']) && $params['ucwords'] != '') $string = ucwords($string);
  
  $out = cms_htmlentities($string);
  if( isset($params['assign']) )
    {
      $smarty->assign(trim($params['assign']),$out);
      return;
    }
  return $out;
}

function smarty_cms_help_function_current_date() {
  echo lang('help_function_current_date');
}

function smarty_cms_about_function_current_date() {
	?>
	<p>Author: Ted Kulp&lt;tedkulp@users.sf.net&gt;</p>
	<p>Version: 1.0</p>
	<p>
	Change History:<br/>
	None
	</p>
	<?php
}
?>
