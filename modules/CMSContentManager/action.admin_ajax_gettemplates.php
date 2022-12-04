<?php
#BEGIN_LICENSE
#-------------------------------------------------------------------------
# Module: Content (c) 2013 by Robert Campbell
#         (calguy1000@cmsmadesimple.org)
#  A module for managing content in CMSMS.
#
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2004 by Ted Kulp (wishy@cmsmadesimple.org)
# Visit our homepage at: http://www.cmsmadesimple.org
#
#-------------------------------------------------------------------------
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# However, as a special exception to the GPL, this software is distributed
# as an addon module to CMS Made Simple.  You may not use this software
# in any Non GPL version of CMS Made simple, or in any version of CMS
# Made simple that does not indicate clearly and obviously in its admin
# section that the site was built with CMS Made simple.
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
#END_LICENSE
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
?>
