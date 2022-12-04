<?php
#CMS - CMS Made Simple
#(c)2004-6 by Ted Kulp (ted@cmsmadesimple.org)
#Visit our homepage at: http://cmsmadesimple.org
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
#$Id$
if (!isset($gCms)) exit;
if (!$this->CheckPermission('Modify Site Preferences')) return;

if (isset($params['cancel'])) $this->RedirectToAdminTab('customfields','','admin_settings');

$fdid = '';
if (isset($params['fdid'])) $fdid = $params['fdid'];

$name = '';
if (isset($params['name'])) $name = trim($params['name']);

$arr_options = array();
$options = '';
if( isset($params['options']) ) {
  $options = trim($params['options']);
  $arr_options = news_admin_ops::optionstext_to_array($options);
}

$type = '';
if (isset($params['type'])) $type = $params['type'];

$max_length = 255;
if (isset($params['max_length'])) $max_length = max(0,(int)$params['max_length']);

$origname = '';
if (isset($params['origname'])) $origname = $params['origname'];

$public = 0;
if( isset($params['public']) ) $public = (int)$params['public'];

if (isset($params['submit'])) {
  // @todo: sanitizing input
  $error = '';
  if ($name == '') $error = $this->Lang('nonamegiven');

  if( !$error ) {
    $query = 'SELECT id FROM '.CMS_DB_PREFIX.'module_news_fielddefs WHERE name = ? AND id != ?';
    $tmp = $db->GetOne($query,array($name,$fdid));
    if( $tmp ) $error = $this->Lang('nameexists');
  }

  if( !$error ) {
    $extra = array('options'=>$arr_options);
    $query = 'UPDATE '.CMS_DB_PREFIX.'module_news_fielddefs SET name = ?, type = ?, max_length = ?, modified_date = '.$db->DBTimeStamp(time()).', public = ?, extra = ? WHERE id = ?';
    $res = $db->Execute($query, array($name, $type, $max_length, $public, serialize($extra), $fdid));

    if( !$res ) die( $db->ErrorMsg() );
    // put mention into the admin log
    audit($name, 'News custom: '.$name, 'Field definition edited');
    $this->SetMessage($this->Lang('fielddefupdated'));
    $this->RedirectToAdminTab('customfields','','admin_settings');
  }
}
else {
   $query = 'SELECT * FROM '.CMS_DB_PREFIX.'module_news_fielddefs WHERE id = ?';
   $row = $db->GetRow($query, array($fdid));

   if ($row) {
     $name = $row['name'];
     $type = $row['type'];
     $max_length = $row['max_length'];
     $origname = $row['name'];
     $public = $row['public'];
     $extra = unserialize($row['extra']);
     if( isset($extra['options']) ) {
       $options = news_admin_ops::array_to_optionstext($extra['options']);
     }
   }
}

#Display template
$smarty->assign('title',$this->Lang('editfielddef'));
$smarty->assign('startform', $this->CreateFormStart($id, 'admin_editfielddef', $returnid));
$smarty->assign('endform', $this->CreateFormEnd());
$smarty->assign('nametext', $this->Lang('name'));
$smarty->assign('typetext', $this->Lang('type'));
$smarty->assign('maxlengthtext', $this->Lang('maxlength'));
$smarty->assign('showinputtype', false);
$smarty->assign('inputtype', $this->CreateInputHidden($id, 'type', $type));
$smarty->assign('info_maxlength', $this->Lang('info_maxlength'));
$smarty->assign('userviewtext',$this->Lang('public'));

$smarty->assign('name',$name);
$smarty->assign('fieldtypes',$this->GetFieldTypes());
$smarty->assign('type',$type);
$smarty->assign('max_length',$max_length);
$smarty->assign('public',$public);
$smarty->assign('options',$options);

$smarty->assign('mod',$this);
$smarty->assign('hidden',
		$this->CreateInputHidden($id, 'fdid', $fdid).
		$this->CreateInputHidden($id, 'origname', htmlspecialchars($origname)));
echo $this->ProcessTemplate('editfielddef.tpl');

// EOF