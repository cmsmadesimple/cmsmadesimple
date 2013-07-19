<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
#This project's homepage is: http://www.cmsmadesimple.org
#
#This program is free software; you can redistribute it and/or modify
#it under the terms of the GNU General Public License as published by
#the Free Software Foundation; either version 2 of the License, or
#(at your option) any later version.
#
#This program is distributed in the hope that it will be useful,
#but WITHOUT ANY WARRANthe TY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
#You should have received a copy of the GNU General Public License
#along with this program; if not, write to the Free Software
#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
#$Id: moduleinterface.php 8558 2012-12-10 00:59:49Z calguy1000 $

$CMS_ADMIN_PAGE=1;
require_once("../include.php");
$ruid = get_userid();

$opt = get_parameter_value($_REQUEST,'opt','setup');
$type = get_parameter_value($_REQUEST,'type');
$oid = get_parameter_value($_REQUEST,'oid');
$uid = get_parameter_value($_REQUEST,'uid');
$lock_id = get_parameter_value($_REQUEST,'lock_id');

$out = array();
$out['status'] = 'success';
try {
  switch( $opt ) {
  case 'setup':
    $out['lang'] = array();
    $out['lang']['error_lock_useridmismatch'] = lang('CMSEX_L006');
    $out['lang']['error_lock_othererror'] = lang('CMSEX_L007');
    $out['lang']['error_lock_cmslockexception'] = lang('CMSEX_L008');
    $out['lang']['error_lock_cmslockownerexception'] = lang('CMSEX_L006');
    $out['lang']['error_lock_cmsunlockexception'] = lang('CMSEX_L009');
    $out['lang']['error_lock_cmsnolockexception'] = lang('CMSEX_L005');
    if( $uid != $ruid ) {
      $out['status'] = 'error';
      $out['error'] = array('type'=>'useridmismatch','msg'=>lang('CMSEX_L006'));
    }
    $out['uid'] = $ruid;
    $out['lock_timeout'] = (int)cms_siteprefs::get('lock_timeout');
    $out['lock_refresh'] = (int)cms_siteprefs::get('lock_refresh');
    break;

  case 'test':      // alias for check
  case 'is_locked': // alias for check
  case 'check':
    if( !$type || !$oid ) throw new CmsInvalidDataException(lang('missingparams'));
    $out['lock_id'] = CmsLockOperations::is_locked($type,$oid) ? 1 : 0;
    break;

  case 'lock':
    if( !$type || !$oid || !$uid ) throw new CmsInvalidDataException(lang('missingparams'));
    if( $uid != $ruid ) throw new CmsLockOwnerException(lang('CMSEX_L006'));

    // see if we can get this lock... if we can, it's just a touch
    $lock = null;
    try {
      $lock = CmsLock::load($type,$oid,$uid);
    }
    catch( CmsNoLockException $e ) {
      // lock doesn't exist, gotta create one.
      $lock = new CmsLock($type,$oid);
    }
    $lock->save();
    $out['lock_id'] = $lock['id'];
    $out['lock_expires'] = $lock['expires'];
    // and we''re done.
    break;

  case 'touch':
    if( !$type || !$oid || !$uid || $lock_id < 1 ) throw new CmsInvalidDataException(lang('missingparams'));
    if( $uid != $ruid ) throw new CmsLockOwnerException(lang('CMSEX_L006'));
    $out['lock_expires'] = CmsLockOperations::touch($lock_id,$type,$oid);
    break;

  case 'unlock':
    if( !$type || !$oid || !$uid || $lock_id < 1 ) throw new CmsInvalidDataException(lang('missingparams'));
    if( $uid != $ruid ) throw new CmsLockOwnerException(lang('CMSEX_L006'));
    CmsLockOperations::delete($lock_id,$type,$oid);
    break;
  }
}
catch( CmsNoLockException $e ) {
  $out['status'] = 'error';
  $out['error'] = array('type'=>strtolower(get_class($e)),'msg'=>$e->GetMessage());
}
catch( CmsLockException $e ) {
  $out['status'] = 'error';
  $out['error'] = array('type'=>strtolower(get_class($e)),'msg'=>$e->GetMessage());
}
catch( Exception $e ) {
  $out['status'] = 'error';
  $out['error'] = array('type'=>'othererror','msg'=>$e->GetMessage());
}

debug_to_log($out);
header('Pragma: public');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Cache-Control: private',false);
header('Content-Type: application/json');
echo json_encode($out);
exit;

#
# EOF
#
?>