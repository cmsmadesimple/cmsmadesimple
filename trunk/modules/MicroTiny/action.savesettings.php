<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (ted@cmsmadesimple.org)
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
#

if (!$this->VisibleToAdminUser()) $this->Redirect($id,"defaultadmin",$returnid);

$this->SetPreference('show_statusbar',(isset($params['show_statusbar']))?1:0);
$this->SetPreference('allow_resize',(isset($params['allow_resize']))?1:0);
$this->SetPreference('strip_background',(isset($params['strip_background']))?1:0);
$this->SetPreference('force_blackonwhite',(isset($params['force_blackonwhite']))?1:0);

if (isset($params['allowimages'])) $this->SetPreference('allowimages', 1 ); else $this->SetPreference('allowimages', 0 );

if (isset($params['css_styles'])) $this->SetPreference('css_styles',$params['css_styles']);

$this->Redirect($id,"defaultadmin",$returnid,array("module_message"=>$this->Lang("settingssaved"),"tab"=>"settings"));

#
# EOF
#
?>