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

use FilePicker\Profile;

if( !defined('CMS_VERSION') ) exit;
if( !$this->VisibleToAdminUser() ) exit;

if( isset($params['cancel']) ) $this->RedirectToAdminTab();

try {
    $profile_id = (int) get_parameter_value($params,'pid');
    $profile = new Profile();

    if( $profile_id > 0 ) {
        $profile = $this->_dao->loadById( $profile_id );
        if( !$profile ) throw new \LogicException('Invalid profile id passed to edit_profile action');
    }

    if( isset($params['submit']) ) {
        try {
            $profile = $profile->overrideWith( $params );
            $this->_dao->save( $profile );
            $this->RedirectToAdminTab();
        }
        catch( \FilePicker\ProfileException $e ) {
            echo $this->ShowErrors($this->Lang($e->GetMessage()));
        }
    }

    $smarty->assign('profile',$profile);
    echo $this->ProcessTemplate('edit_profile.tpl');
}
catch( \CmsInvalidDataException $e ) {
    $this->SetError( $this->Lang( $e->GetMessage() ) );
    $this->RedirectToAdminTab();
}
catch( \Exception $e ) {
    $this->SetError( $e->GetMessage() );
    $this->RedirectToAdminTab();
}

#
# EOF
#