<?php
#CMS - CMS Made Simple
#(c)2004-2012 by Ted Kulp (wishy@users.sf.net)
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
#$Id: listusertags.php 7396 2011-09-15 12:57:25Z rolf1 $

$CMS_ADMIN_PAGE=1;
require_once("../lib/include.php");
check_login();
$urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];
$userid = get_userid();
if( !check_permission($userid, 'Modify User-defined Tags') ) return;
$tagops = cmsms()->GetUserTagOperations();
$themeObject = $userplugin_id = null;

if( !isset($_POST['ajax']) ) {
    include_once('header.php');
    $themeObject->set_value('pagetitle','userdefinedtags'); // generic header for oneeleven
}

$record = array('userplugin_id'=>'',
		'userplugin_name'=>'',
		'code'=>'',
		'description'=>'',
		'create_date'=>'',
                'modified_date'=>'');

if( isset($_REQUEST['userplugin_id']) && $_REQUEST['userplugin_id'] != '' ) {
    $record = $tagops->GetUserTag((int)$_REQUEST['userplugin_id']);
}

if( isset($_POST['cancel']) ) redirect('listusertags.php'.$urlext);

$error = array();
if( isset($_POST['submit']) || isset($_POST['apply']) ) {
    $record['userplugin_name'] = trim(cleanValue($_POST['userplugin_name']));
    $record['code'] = trim($_POST['code']);
    $record['description'] = trim(cleanValue($_POST['description']));

    if( isset($_POST['userplugin_id']) ) {
        $userplugin_id = (int) $_POST['userplugin_id'];
        if( $userplugin_id < 1 ) $userplugin_id = null;
    }

    // validate
    if( $record['userplugin_name'] == '' ) {
        $error[] = lang('nofieldgiven',array(lang('name')));
    }
    elseif(preg_match('<^[ a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$>',$record['userplugin_name'])==0) {
        $error[] = lang('error_udt_name_chars');
        $validinfo = false;
    }
    else {
        // check for duplicate name.
        $all_tags = $tagops->ListUserTags();
        foreach( $all_tags as $id => $name ) {
            if( $name == $record['userplugin_name'] ) {
                if( $id != $record['userplugin_id'] ) $error[] = lang('usertagexists');
            }
        }
    }

    if( $record['code'] == '' ) $error[] = lang('nofieldgiven', array(lang('code')));

    $code = $record['code'];
    if( startswith($code,'<?php') ) $code = substr($code,5);
    if( endswith($code,'?>') ) $code = substr($code,0,-2);

    $lastopenbrace = strrpos($code, '{');
    $lastclosebrace = strrpos($code, '}');
    if ($lastopenbrace > $lastclosebrace) {
        $error[] = lang('invalidcode');
        $error[] = lang('invalidcode_brace_missing');
    }

    if( count($error) == 0 ) {
        srand();
        ob_start();
        if (eval('function testfunction'.rand().'() {'.$code."\n}") === FALSE) {
            $error[] = lang('invalidcode');
            $buffer = ob_get_clean();
            //add error
            $error[] = preg_replace('/<br \/>/', '', $buffer );
            $validinfo = false;
        }
        else {
            ob_get_clean();
        }
    }

    if( count($error) == 0 ) {
        $res = $tagops->SetUserTag($record['userplugin_name'],$record['code'],$record['description'],$userplugin_id);
        if( !$res ) $error = lang('errorupdatingusertag');
    }

    $details = lang('usertagupdated');

    if( !$error ) {
        if( isset($_POST['run']) ) {
            @ob_start();
            $params = array();
            $res = $tagops->CallUserTag($record['userplugin_name'],$params);
            $tmp = @ob_get_contents();
            @ob_end_clean();

            if( $tmp )
                $details = $tmp;
            else
                $details = $res;
        }
    }

    if( !$error ) {
        // save the UDT.
        if( isset($_POST['submit']) ) {
            redirect('listusertags.php'.$urlext);
        }
        else {
            // got here via ajax.
            $out = array('response'=>'Success','details'=>$details);
            echo json_encode($out);
            exit;
        }
    }
    else {
        if( isset($_POST['submit']) ) {
            echo $themeObject->ShowErrors($error);
        }
        else {
            // ajaxy.
            $out = array('response'=>'Error','details'=>$error);
            echo json_encode($out);
            exit;
        }
    }
}

//
// give everything to smarty.
//
$smarty = \Smarty_CMS::get_instance();
$smarty->assign('record',$record);
echo $smarty->display('editusertag.tpl');
include_once("footer.php");

#
# EOF
#
?>