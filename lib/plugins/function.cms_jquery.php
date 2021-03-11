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

function smarty_function_cms_jquery($params, $smarty)
{
	$exclude = trim(get_parameter_value($params,'exclude'));
	$cdn = cms_to_bool(get_parameter_value($params,'cdn'));
	$append = trim(get_parameter_value($params,'append'));
	$ssl = cms_to_bool(get_parameter_value($params,'ssl'));
	$custom_root = trim(get_parameter_value($params,'custom_root'));
	$include_css = cms_to_bool(get_parameter_value($params,'include_css',1));

	// get the output
	$out = cms_get_jquery($exclude,$ssl,$cdn,$append,$custom_root,$include_css);
	if( isset($params['assign']) ) {
		$smarty->assign(trim($params['assign']),$out);
		return;
	}

	return $out;
}

#
# EOF
#