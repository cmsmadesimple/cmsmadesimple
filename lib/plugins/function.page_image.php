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

function smarty_function_page_image($params, $smarty)
{
    $get_bool = function(array $params,$key,$dflt) {
        if( !isset($params[$key]) ) return (bool) $dflt;
        if( empty($params[$key]) ) return (bool) $dflt;
        return (bool) cms_to_bool($params[$key]);
    };

    $full = $get_bool($params,'full',false);
    $thumbnail = $get_bool($params,'thumbnail',false);
    $tag = $get_bool($params,'tag',false);
    $assign = trim(get_parameter_value($params,'assign'));
    unset($params['full'], $params['thumbnail'], $params['tag'], $params['assign']);

	$propname = 'image';
    if( $thumbnail ) $propname = 'thumbnail';
    if( $tag ) $full = true;

	$contentobj = cms_utils::get_current_content();
    $val = null;
	if( is_object($contentobj) ) {
		$val = $contentobj->GetPropertyValue($propname);
		if( $val == -1 ) $val = null;
    }

    $out = null;
    if( $val ) {
        $orig_val = $val;
        $config = \cms_config::get_instance();
        if( $full ) $val = $config['image_uploads_url'].'/'.$val;
        if( ! $tag ) {
            $out = $val;
        } else {
            if( !isset($params['alt']) ) $params['alt'] = $orig_val;
            // build a tag.
            $out = "<img src=\"$val\"";
            foreach( $params as $key => $val ) {
                $key = trim($key);
                $val = trim($val);
                if( !$key ) continue;
                $out .= " $key=\"$val\"";
            }
            $out .= "/>";
        }
    }

	if( $assign ) {
		$smarty->assign($assign,$out);
		return;
    }
	return $out;
}

#
# EOF
#