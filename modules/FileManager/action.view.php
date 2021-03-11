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

if (!function_exists("cmsms")) exit;
if( get_userid(FALSE) < 1 || $this->cms->is_frontend_request() ) throw new \CmsError403Exception('Permission denied');

if( !isset($params['file']) ) {
    $params["fmerror"]="nofilesselected";
    $this->Redirect($id,"defaultadmin",$returnid,$params);
}

$config=cmsms()->GetConfig();
$filename=$this->decodefilename($params['file']);
$src = filemanager_utils::join_path($config['root_path'],filemanager_utils::get_cwd(),$filename);
if( !file_exists($src) ) {
    $params["fmerror"]="filenotfound";
    $this->Redirect($id,"defaultadmin",$returnid,$params);
}

// get its mime type
$mimetype = filemanager_utils::mime_content_type($src);

$handlers = ob_list_handlers();
for ($cnt = 0; $cnt < sizeof($handlers); $cnt++) { ob_end_clean(); }
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Content-Type: $mimetype");
echo file_get_contents($src);

exit;

#
# EOF
#