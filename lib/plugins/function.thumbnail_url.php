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

function smarty_function_thumbnail_url($params,&$template)
{
    $config = \cms_config::get_instance();
    $dir = $config['uploads_path'];
    $file = trim(get_parameter_value($params,'file'));
    $add_dir = trim(get_parameter_value($params,'dir'));
    $assign = trim(get_parameter_value($params,'assign'));

    if( !$file ) {
        trigger_error('thumbnail_url plugin: invalid file parameter');
        return;
    }

    if( $add_dir ) {
        if( startswith( $add_dir, '/') ) $add_dir = substr($add_dir,1);
        $test = $dir.'/'.$add_dir;
        if( !is_dir($test) || !is_readable($test) ) {
            trigger_error("thumbnail_url plugin: dir=$add_dir invalid directory name specified");
            return;
        }
    }

    $out = null;
    $file = 'thumb_'.$file;
    $fullpath = $dir.'/'.$file;
    if( is_file($fullpath) && is_readable($fullpath) ) {
        // convert it to a url
        $out = $config['uploads_url'].'/';
        if( $add_dir ) $out .= $add_dir.'/';
        $out .= $file;
    }

    if( $assign ) {
        $template->assign($assign,$out);
        return;
    }
    return $out;
}

#
# EOF
#