<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
#This project's homepage is: http://cmsmadesimple.sf.net
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
#$Id: listcontent.php 6788 2010-11-14 20:17:47Z calguy1000 $

$CMS_ADMIN_PAGE=1;
$CMS_TOP_MENU='content';

require_once('../include.php');
$urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];
$thisurl=basename(__FILE__).$urlext;
include_once("header.php");

function get_username($uid)
{
  static $_userinfo = null;
  
  if( !$_userinfo )
    {
      $userops = cmsms()->GetUserOperations();
      $_userinfo = $userops->LoadUsers();
    }

  if( is_array($_userinfo) )
    {
      for( $i = 0; $i < count($_userinfo); $i++ )
	{
	  if( $_userinfo[$i]->id == $uid )
	    {
	      return $_userinfo[$i]->username;
	    }
	}
    }
}

function pretty_time($nseconds)
{
  $ndays = (int)($nseconds / (3600 * 24));
  $rem = $nseconds % (3600 * 24);
  $nhours = (int)($rem / 3600);
  $rem = $rem % 3600;
  $nminutes = (int)($rem / 60);
  $rem = $rem % 60;

  if( $ndays > 1 )
    {
      return lang('days_hours',$ndays,$nhours);
    }
  if( $ndays == 1 )
    {
      return lang('day_hours',$ndays,$nhours);
    }
  if( $nhours > 1 )
    {
      return lang('hours_minutes',$nhours,$nminutes);
    }
  if( $nhours == 1 )
    {
      return lang('hour_minutes',$nhours,$nminutes);
    }
  return lang('n_minutes',$nminutes);
}

check_login();
$userid = get_userid();

$obj_type = '';
$content_id = '';
if( isset($_GET['obj_type']) )
  {
    $content_id = (int)$_GET['obj_type'];
  }
if( isset($_GET['content_id']) )
  {
    $content_id = (int)$_GET['content_id'];
  }
if( !$content_id )
  {
    redirect('listcontent.php'.$urlext.'&error=missingparams');
    return;
  }

$error = '';
$redirect_back = '';
$redirect_to  = '';
$display_info = array('type'=>'s','title'=>'s','owner'=>'s','lock_uid'=>'i','lock_user'=>'s','lock_created'=>'d','lock_modified'=>'d','pretty_time'=>'s');
$display_prompts = array('type'=>lang('type'),'title'=>lang('title'),'owner'=>lang('owner'),'lock_uid'=>lang('lock_owner'),'lock_user'=>lang('lock_owner'),'lock_created'=>lang('lock_created'),
			 'lock_modified'=>lang('lock_modified'),'pretty_time'=>lang('time_locked'));
$display_data = array('type'=>'','title'=>'','owner'=>'','lock_user'=>'','lock_created'=>'','lock_modified'=>'');
$access = false;
switch( $obj_type )
  {
  case 'content':
  case 'c':
  default:
    // stealing a lock for a content object.
    $display_data['type'] = lang('content');
    $access = check_ownership($userid, $content_id) || check_permission($userid, 'Modify Any Page') ||
      check_permission($userid, 'Manage All Content') || check_permission($userid, 'Steal Content Lock');
    $redirect_back = 'listcontent.php'.$urlext;
    $redirect_to = 'editcontent.php'.$urlext.'&content_id='.$content_id;
    $error = '';
    if( !$access && !$error )
      {
	$access = check_authorship($userid, $content_id);
      }

    if( !$access )
      {
	$error = 'no_permission';
      }

    if( $access && !$error )
      {
	$contentops = cmsms()->GetContentOperations();
	$content_obj = $contentops->LoadContentFromId($content_id);
	if( !$content_obj ) 
	  {
	    $error = 'missingparams';
	  }
	else
	  {
	    $display_data['title'] = $content_obj->Name();
	    $display_data['owner'] = get_username($content_obj->Owner());
	  }
      }

    $lock = '';
    if( !$error )
      {
	// get lock information
	$lock = cms_lock_manager::get_lock('Core::Content',$content_id);
	if( !$lock )
	  {
	    $error = 'nolockfound';
	  }
	else
	  {
	    $age = (int)get_site_preference('listcontent_lockstaleage',30) * 60;
	    if( $age <= 0 )
	      {
		$error = 'nolockexpiry';
	      }
	    else
	      {
		$db = cmsms()->GetDb();
		$modified = $db->UnixTimeStamp($lock['modified']);
		if( $modified >= time() - $age )
		  {
		    $error = 'locknotstale';
		  }
		else
		  {
		    $display_data['lock_uid'] = $lock['uid'];
		    $display_data['lock_user'] = get_username($lock['uid']);
		    $display_data['lock_created'] = $lock['created'];
		    $display_data['lock_modified'] = $lock['modified'];
		  }
	      }
	  }
      }
    break;

  }

if( isset($_POST['cancel']) )
  {
    redirect($redirect_back);
  }

if( $error )
  {
    if( $redirect_back )
      {
	redirect($redirect_back.'&error='.$error);
      }
    else
      {
	echo $themeObject->ShowErrors($error);
      }
  }

if( isset($_POST['steal']) )
  {
    cms_lock_manager::delete('Core::Content',$content_id,$display_data['lock_uid']);
    audit($content_id,lang('Content'),lang('content_lock_deleted',get_username($userid),$display_data['lock_user'],$display_data['title']));
    redirect($redirect_to);
  }

$db = cmsms()->GetDb();
$display_data['pretty_time'] = pretty_time(time() - $db->UnixTimeStamp($display_data['lock_modified']));
// get the user information
// make sure the lock is still stale.
$smarty->assign('display_info',$display_info);
$smarty->assign('display_data',$display_data);
$smarty->assign('display_prompts',$display_prompts);
$smarty->assign('error',$error);
echo '<div class="pagecontainer">'.$themeObject->ShowHeader('steal_lock')."\n";
echo $smarty->fetch('steallock.tpl');
echo '</div>'."\n";
echo '<p class="pageback"><a class="pageback" href="'.$themeObject->BackUrl().'">&#171; '.lang('back').'</a></p>'."\n";
include_once("footer.php");

#
# EOF
#
?>