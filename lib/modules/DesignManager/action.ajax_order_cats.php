<?php
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
if( !$this->CheckPermission('Modify Templates') ) return;

$handlers = ob_list_handlers();
for ($cnt = 0; $cnt < sizeof($handlers); $cnt++) { ob_end_clean(); }

$out = null;
try {
	if( isset($_GET['cat']) && is_array($_GET['cat']) && count($_GET['cat']) > 0 ) {
		foreach( $_GET['cat'] as $idx => $cat_id ) {
			$cat = CmsLayoutTemplateCategory::load($cat_id);
			$cat->set_item_order($idx+1);
			$cat->save();
		}
	}
	audit('',$this->GetName(),'Category order changed');
	$out = $this->Lang('category_reordered');
	$response = 'success';
}
catch( CmsException $e ) {
	audit('','CMSMS','Problem working with category in ajax: '.$e->GetMessage());
	$out = 'ERROR: '.$e->GetMessage();
	$response = 'error';
}

$this->GetJSONResponse($response, $out);
#
# EOF
#
?>
