<?php
#---------------------------------------------------------------------------
# CMS Made Simple - Power for the professional, Simplicity for the end user.
# (c) 2004 - 2011 by Ted Kulp
# (c) 2011 - 2018 by the CMS Made Simple Development Team
# (c) 2018 and beyond by the CMS Made Simple Foundation
# This project's homepage is: https://www.cmsmadesimple.org
#---------------------------------------------------------------------------
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
#---------------------------------------------------------------------------

$handlers = ob_list_handlers();
for ($cnt = 0; $cnt < sizeof($handlers); $cnt++) { ob_end_clean(); }

try {
    if( !$this->CheckPermission('Manage Stylesheets') ) throw new \Exception($this->Lang('error_permission'));
    $tmp = get_parameter_value($_REQUEST,'filter');
    if( !$tmp ) throw new \Exception($this->Lang('error_missingparam'));
    $filter = json_decode($tmp,TRUE);
    $smarty->assign('css_filter',$filter);

    $designs = CmsLayoutCollection::get_all();
    if( count($designs) ) {
        $smarty->assign('list_designs',$designs);
        $tmp = array();
        for( $i = 0; $i < count($designs); $i++ ) {
            $tmp['d:'.$designs[$i]->get_id()] = $designs[$i]->get_name();
            $tmp2[$designs[$i]->get_id()] = $designs[$i]->get_name();
        }
        $smarty->assign('design_names',$tmp2);
    }

	$css_query = new CmsLayoutStylesheetQuery($filter);
	$csslist = $css_query->GetMatches();
	$smarty->assign('stylesheets',$csslist);
	$css_nav = array();
	$css_nav['pagelimit'] = $css_query->limit;
	$css_nav['numpages'] = $css_query->numpages;
	$css_nav['numrows'] = $css_query->totalrows;
	$css_nav['curpage'] = (int)($css_query->offset / $css_query->limit) + 1;
	$smarty->assign('css_nav',$css_nav);
    $smarty->assign('manage_designs',$this->CheckPermission('Manage Designs'));
    $locks = \CmsLockOperations::get_locks('stylesheet');
    $smarty->assign('have_css_locks',($locks) ? count($locks) : 0 );
    $smarty->assign('lock_timeout', $this->GetPreference('lock_timeout'));

    echo $this->ProcessTemplate('ajax_get_stylesheets.tpl');
}
catch( Exception $e ) {
    echo '<div class="red">'.$e->GetMessage().'</div>';
}

exit;

#
# EOF
#