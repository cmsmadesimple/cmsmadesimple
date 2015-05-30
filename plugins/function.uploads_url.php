<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
#Visit our homepage at: http://www.cmsmadesimple.org
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

function smarty_function_uploads_url($params, &$smarty)
{
	$config = CmsApp::get_instance()->GetConfig();

    $out = $config->smart_uploads_url();
	if( isset($params['assign']) ) {
		$smarty->assign(trim($params['assign']),$out);
		return;
	}

	return $out;
}

function smarty_cms_about_function_uploads_url() {
?>
	<p>Author: Nuno Costa &ltnuno.mfcosta@sapo.pt&gt;</p>

	<p>Change History:</p>
	<ul>
		<li>None</li>
	</ul>
<?php
}
?>