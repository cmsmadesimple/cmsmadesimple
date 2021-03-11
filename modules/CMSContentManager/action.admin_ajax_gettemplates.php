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

if( !isset($gCms) ) exit;
if( !$this->CanEditContent() ) exit;

$out = null;
try {
    $design_id = (int) get_parameter_value($params,'design_id',-1);
    if( $design_id > 0 ) {
        $mode = $this->GetPreference('template_list_mode','designpage');
        switch( $mode ) {
        case 'alldesign':
            // all templates for this design
            $design = CmsLayoutCollection::load($design_id);
            $template_list = $design->get_templates();

            $templates = CmsLayoutTemplate::load_bulk($template_list);
            $out = array();
            foreach( $templates as $one ) {
                if( !$one->get_listable() ) continue;
                $out[$one->get_id()] = $one->get_name();
            }
            break;

        case 'designpage':
            $type = CmsLayoutTemplateType::load(CmsLayoutTemplateType::CORE.'::page');
            $type_id = $type->get_id();
            $design = CmsLayoutCollection::load($design_id);
            $template_list = $design->get_templates();

            $templates = CmsLayoutTemplate::load_bulk($template_list);
            if( is_array($templates) && count($templates) ) {
                $out = array();
                foreach( $templates as $one ) {
                    if( $one->get_type_id() != $type_id ) continue;
                    if( !$one->get_listable() ) continue;
                    $out[$one->get_id()] = $one->get_name();
                }
            }
            break;

        case 'allpage':
            $type = CmsLayoutTemplateType::load(CmsLayoutTemplateType::CORE.'::page');
            $template_list = CmsLayoutTemplate::load_all_by_type($type);
            $out = array();
            foreach( $template_list as $one ) {
                if( !$one->get_listable() ) continue;
                $out[$one->get_id()] = $one->get_name();
            }
            break;
        }
    }
}
catch( Exception $e ) {
    $out = null;
}

if( !is_array($out) || count($out) == 0 ) $out = null;
echo json_encode($out);
exit;

#
# EOF
#