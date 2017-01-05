<?php
#-------------------------------------------------------------------------
# Module: FilePicker - A CMSMS addon module to provide file picking capabilities.
# (c) 2016 by Fernando Morgado <jomorg@cmsmadesimple.org>
# (c) 2016 by Robert Campbell <calguy1000@cmsmadesimple.org>
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2006 by Ted Kulp (wishy@cmsmadesimple.org)
# This projects homepage is: http://www.cmsmadesimple.org
#-------------------------------------------------------------------------
#-------------------------------------------------------------------------
# BEGIN_LICENSE
#-------------------------------------------------------------------------
# This file is part of FilePicker
# FilePicker is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# FilePicker is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
# Or read it online: http://www.gnu.org/licenses/licenses.html#GPL
#-------------------------------------------------------------------------
# END_LICENSE
#-------------------------------------------------------------------------
use FilePicker\ProfileDAO;
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
        $profile = $profile->overrideWith( $params );
        $this->_dao->save( $profile );
        $this->RedirectToAdminTab();
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
