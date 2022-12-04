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
if( !cmsms() ) exit;
if (!$this->VisibleToAdminUser()) return;
$this->SetCurrentTab('settings');

try {
  $name = trim(get_parameter_value($params,'profile'));
  if( !$name ) throw new Exception($this->Lang('error_missingparam'));

  if( isset($params['cancel']) ) {
    // handle cancel
    $this->SetMessage($this->Lang('msg_cancelled'));
    $this->RedirectToAdminTab();
  }

  // load the profile
  $profile = microtiny_profile::load($name);

  if( isset($params['submit']) ) {
    //
    // handle submit
    //

    foreach( $params as $key => $value ) {
      if( startswith($key,'profile_') ) {
	$key = substr($key,strlen('profile_'));
	$profile[$key] = $value;
      }
    }

    // check if name changed, and if object is a system object, puke
    if( isset($profile['system']) && $profile['system'] && $profile['name'] != $name ) {
      throw new CmsInvalidDataException($this->lang('error_cantchangesysprofilename'));
    }

    $profile->save();
    $this->RedirectToAdminTab();
  }

  // display data, strange formatting but it works...
  $smarty->assign('profile',$name);
  $smarty->assign('data',$profile);

  $stylesheets = CmsLayoutStylesheet::get_all(TRUE);
  $stylesheets = array('-1'=>$this->Lang('none')) + $stylesheets;
  $smarty->assign('stylesheets',$stylesheets);

  echo $this->ProcessTemplate('admin_editprofile.tpl');
}
catch( Exception $e ) {
  $this->SetError($e->GetMessage());
  $this->RedirectToAdminTab();
}

#
# EOF
#
?>