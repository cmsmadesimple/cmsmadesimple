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
#but WITHOUT ANY WARRANTY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
#You should have received a copy of the GNU General Public License
#along with this program; if not, write to the Free Software
#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
#$Id$

$CMS_ADMIN_PAGE=1;

require_once("../include.php");
check_login();
$urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];
$thisurl=basename(__FILE__).$urlext;

if (isset($_POST['cancel']))
	redirect('listcontent.php'.$urlext);

$userid = get_userid();

$action = '';
if (isset($_POST['multiaction'])) $action = $_POST['multiaction'];
if (isset($_POST['reorderpages'])) $action = 'reorder';

{
  $tmp = explode('::',$action,2);
  if( count($tmp) == 1 )
    {
      // do nothing here.
    }
  else if( $tmp[0] == 'core')
    {
      $action = $tmp[1];
    }
  else if( $tmp[0] == '-1' )
    {
      redirect("listcontent.php".$urlext.'&message='.lang('no_bulk_performed'));
    }
  else
    {
      // it's a module action.
      $gCms = cmsms();
      $module = cms_utils::get_module($tmp[0]);
      if( !is_object($module) )
	{
	  redirect("listcontent.php".$urlext.'&message='.lang('no_bulk_performed'));
	}

      // strip out the multicontent params
      $tmp2 = array();
      foreach( $_POST as $key => $val )
	{
	  if( startswith($key,'multicontent-') )
	    {
	      $tmp2[] = substr($key,strlen('multicontent-'));
	    }
	}
      // populate the REQUEST with a mact
      $_SESSION['cms_passthru'] = array();
      $_SESSION['cms_passthru']['mact'] = implode(',',array($tmp[0],'m1_',$tmp[1]));
      if( count($tmp2) > 0 )
	{
	  $_SESSION['cms_passthru']['m1_contentlist'] = implode(',',$tmp2);
	}
	
      // handle any normal module action.
      redirect("moduleinterface.php".$urlext);
    }
}

include_once("header.php");

$gCms = cmsms();
$db = $gCms->GetDb();
$hm = $gCms->GetHierarchyManager();

$nodelist = array();
$bulk = false;

$mypages = author_pages($userid);

function get_delete_list($sel_nodes,&$parent,&$final_result,$depth = 0)
{
  // get the list of items we should delete
  $userid = get_userid();
  if( !check_permission($userid,'Remove Pages') ) return FALSE;
  global $mypages;

  $status = TRUE;
  foreach( $sel_nodes as $node )
    {
      if( check_ownership($userid, $node->getTag()) || quick_check_authorship($node->getTag(), $mypages) )
	{
	  $content = $node->GetContent(false,false,true);
	  if( !is_object($content) ) continue;

	  $children =& $node->getChildren(false,true);
	  $child_status = array();
	  if( isset($children) && count($children) )
	    {
	      // we have children.. but we may not have access to 
	      // any or all of them.
	      $tmp = array();
	      $child_status = get_delete_list($children,$node,$tmp,$depth+1);
	      if( $child_status === FALSE || count($tmp) == 0 )
		{
		  // there are children, but for one reason or another
		  // we can't delete em. which means we can't delete this
		  // parent either, or any of its parents.
		  $status = FALSE;
		}
	      else
		{
		  if( !isset($final_result[$content->Id()]) )
		    {
		      $final_result[$content->Id()] = $content;
		    }
		}

	      if( count($tmp) )
		{
		  // there are children se can delete.
		  foreach( $tmp as $content_id => $one )
		    {
		      if( !isset($final_result[$content_id]) )
			{
			  $final_result[$content_id] = $one;
			}
		    }
		}
	    }
	  else
	    {
	      // no children
	      if( !isset($final_result[$content->Id()]) )
		{
		  $final_result[$content->Id()] = $content;
		}
	    }
	}
      else
	{
	  $status = FALSE;
	}
    }

  return $status;
}

function toggleexpand($contentid, $collapse = false)
{
	$userid = get_userid();
	$openedArray=array();
	if (get_preference($userid, 'collapse', '') != '')
	{
		$tmp  = explode('.',get_preference($userid, 'collapse'));
		foreach ($tmp as $thisCol)
		{
			$colind = substr($thisCol,0,strpos($thisCol,'='));
			$openedArray[$colind] = 1;
		}
	}
	if ($collapse)
	{
		$openedArray[$contentid] = 0;
	}
	else
	{
		$openedArray[$contentid] = 1;
	}
	$cs = '';
	foreach ($openedArray as $key=>$val)
	{
		if ($val == 1)
		{
			$cs .= $key.'=1.';
		}
	}
	set_preference($userid, 'collapse', $cs);
}

function DoContent(&$list, &$node, $checkdefault = true, $checkchildren = true)
{
  $content = $node->GetContent(false,true,true);
	if (isset($content))
	{
		if (!$checkdefault || ($checkdefault && !$content->DefaultContent()))
		{
			$inarray = false;
			foreach ($list as $oneitem)
			{
				if ($oneitem == $content)
				{
					$inarray = true;
					break;
				}
			}

			if (!$inarray)
				$list[] =& $content;

			if ($checkchildren)
			{
				$children =& $node->getChildren();
				if (isset($children) && count($children))
				{
					reset($children);
					while (list($key) = each($children))
					{
						$onechild =& $children[$key];
						DoContent($list, $onechild, $checkdefault, $checkchildren);
					}
				}
			}
		}
	}
}

$reorder_error = FALSE;
if (isset($_POST['idlist']))
{
	foreach (explode(':', $_POST['idlist']) as $id)
	{
		$node = $hm->sureGetNodeById($id);
		if (isset($node))
		{
		  $content = $node->GetContent(false,false,true);
		  if (isset($content))
		    {
		      $nodelist[] = $content;
		    }
		}
	}
}
else
{
	foreach ($_POST as $k=>$v)
	{
		if (startswith($k, 'multicontent-'))
		{
			$id = substr($k, strlen('multicontent-'));
			$node = $hm->sureGetNodeById($id);
			if (isset($node))
			{
				if ($action == 'inactive')
				  DoContent($nodelist, $node, true, false);
				else if ($action == 'active' || $action == 'settemplate' || $action == 'changeowner' || $action == 'setcachable' || $action == 'setnoncachable'
                                         || $action == 'showinmenu' || $action == 'hidefrommenu' || $action == 'secure' || $action == 'insecure')
				  {
				    DoContent($nodelist, $node, false, false);
				  }
				else if ($action == 'delete')
				  {
				    $nodelist[] = $node;
				  }
			}
		}
		if ($action == 'reorder')
		  {	  
		    if (startswith($k, 'order-'))
		      {
			$id = substr($k, strlen('order-'));
			$node = $hm->sureGetNodeById($id);
			$one =& $node->getContent(true);
			$parentNode = &$node->getParentNode();
			if (FALSE == empty($reorder_parents_array[$one->ParentId()]) && array_key_exists($v, $reorder_parents_array[$one->ParentId()]))
			  {
			    $reorder_error = 'sibling_duplicate_order';
			  }
			else if ($v > count($parentNode->getChildren()))
			  {
			    $reorder_error = 'order_too_large';
			  }
			else if (0 == $v)
			  {
			    $reorder_error = 'order_too_small';
			  }
			else
			  {
			    $reorder_array[$id] = $v;
			    $reorder_parents_array[$one->ParentId()][$v] = $v;
			  }
		      }

		  }
	}
	if ($action == 'delete' )
	  {
	    $parent = NULL;
	    $result = array();
	    $status = get_delete_list($nodelist,$parent,$result);
	    $nodelist = $result;
	  }

	if ($action == 'reorder' && $reorder_error == FALSE)
	  {
	    $order_changed = FALSE;
	    foreach ($reorder_array AS $page_id => $page_order)
	      {
		$node = $hm->sureGetNodeById($page_id);
		$one =& $node->getContent(true);
		// Only update if order has changed.
		if ($one->ItemOrder() != $page_order)
		  {
		    $order_changed = TRUE;
		    $query = 'UPDATE '.cms_db_prefix().'content SET item_order ='.intval($page_order).' WHERE content_id = ?';
		    $db->Execute($query, array($page_id));
		  }
	      }
	    if (TRUE == $order_changed)
	    {
			$contentops = $gCms->GetContentOperations();
			$contentops->SetAllHierarchyPositions();
	    }
	    else
	      {
		$reorder_error = 'no_orders_changed';
	      }
	  }
 }

if (count($nodelist) == 0 && 'reorder' != $action)
{
	redirect("listcontent.php".$urlext.'&message=no_bulk_performed');
}
else
{
	if ($action == 'delete')
	{
	  echo '<div class="pagecontainer"><p class="pageheader">'.lang('deletecontent').'</p><br/>';
	  $userid = get_userid();
	  $access = (check_permission($userid, 'Remove Pages'));
	  if ($access)
	    {
	      echo '<form method="post" action="multicontent.php">' . "\n";
	      echo '<div>
                    <input type="hidden" name="'.CMS_SECURE_PARAM_NAME.'" value="'.$_SESSION[CMS_USER_KEY].'" />
                    </div>';
      
	      echo '<p>'.lang('deletepages').'&nbsp;<em>('.lang('info_deletepages').')</em></p><p>' . "\n";
			$idlist = array();
			foreach ($nodelist as $node)
			{
			  if (!is_object($node)) continue;
			  if ($node->DefaultContent())
			    {
			      redirect('listcontent.php'.$urlext.'&error=error_delete_default_parent');
			    }

				echo $node->Name() . ' (' . $node->Hierarchy() . ')' . '<br />' . "\n";
				$idlist[] = $node->Id();
			}
			echo '</p>';

			echo '<div class="pageoverflow">
				<p class="pagetext">&nbsp;</p>
				<p class="pageinput">';

			echo '<input type="hidden" name="multiaction" value="core::dodelete" /><input type="hidden" name="idlist" value="'.implode(':', $idlist).'" />' . "\n";
			?>
								<input type="submit" name="confirm" value="<?php echo lang('submit') ?>"  class="pagebutton" />
								<input type="submit" name="cancel" value="<?php echo lang('cancel') ?>" class="pagebutton" />
							</p>
						</div>
					</form>
				</div>
			
			<?php
		}
		else
		{
			redirect('listcontent.php'.$urlext.'&error=no_permission');
		}
	}
        else if ($action == 'settemplate')
	  {
	    $pages = array();
	    $idlist = array();
	    foreach( $nodelist as $one )
	      {
		$pages[] = array('name'=>$one->Name(),'hierarchy'=>$one->Hierarchy());
		$idlist[] = $one->Id();
	      }
	    $templateops = $gCms->GetTemplateOperations();
	    $smarty->assign('input_template',$templateops->TemplateDropdown());
	    $smarty->assign('pages',$pages);
	    $smarty->assign('idlist',$idlist);
	    $smarty->assign('text_settemplate',lang('text_settemplate'));
	    $smarty->assign('text_pages',lang('pages'));
	    $smarty->assign('text_template',lang('template'));
            $smarty->assign('text_submit',lang('submit'));
	    $smarty->assign('text_cancel',lang('cancel'));
	    $smarty->assign('formurl',$thisurl);
	    echo $smarty->fetch('multicontent_template.tpl');
	  }
        else if ($action == 'dosettemplate')
	  {
	    $template_id = (int)$_POST['template_id'];
		$modifyall = check_permission($userid, 'Modify Any Page');
	    foreach( $nodelist as $node )
	      {
		$permission = ($modifyall || check_ownership($userid, $node->Id()) || 
			       check_authorship($userid, $node->Id()) || 
			       check_permission($userid, 'Manage All Content'));

		if( $permission )
		  {
		    $node->SetTemplateId($template_id);
		    $node->Save();
		  }
	      }
	    redirect('listcontent.php'.$urlext.'&message=bulk_success');
	  }
	  
	  else if ($action == 'changeowner')
	  {
	    $pages = array();
	    $idlist = array();
	    foreach( $nodelist as $one )
	      {
		$pages[] = array('name'=>$one->Name(),'hierarchy'=>$one->Hierarchy());
		$idlist[] = $one->Id();
	      }
	    $userops = $gCms->GetUserOperations();
	    $smarty->assign('input_changeowner',$userops->GenerateDropdown());
	    $smarty->assign('pages',$pages);
	    $smarty->assign('idlist',$idlist);
	    $smarty->assign('text_changeowner',lang('text_changeowner'));
	    $smarty->assign('text_pages',lang('pages'));
	    $smarty->assign('text_owner',lang('owner'));
            $smarty->assign('text_submit',lang('submit'));
	    $smarty->assign('text_cancel',lang('cancel'));
	    $smarty->assign('formurl',$thisurl);
	    echo $smarty->fetch('multicontent_owner.tpl');
	  }
        else if ($action == 'dochangeowner')
	  {
	    $ownerid = $_POST['ownerid'];
		$modifyall = check_permission($userid, 'Modify Any Page');
	    foreach( $nodelist as $node )
	      {
		$permission = ($modifyall || check_ownership($userid, $node->Id()) || 
			       check_authorship($userid, $node->Id()) || 
			       check_permission($userid, 'Manage All Content'));

		if( $permission )
		  {
		    $node->SetOwner($ownerid);
		    $node->Save();
		  }
	      }
	    redirect('listcontent.php'.$urlext.'&message=bulk_success');
	  }
	  
	  
	else if ($action == 'dodelete')
	{
		$userid = get_userid();
		$access = check_permission($userid, 'Remove Pages' || check_permission($userid, 'Manage All Content'));
		if ($access)
		{
			$db = $gCms->GetDb();
			foreach (array_reverse($nodelist) as $node)
			{
				$id = $node->Id();
				$title = $node->Name() . ' (Parent ID: ' . $node->Hierarchy() . ')';

				$childcount = 0;
				$parentid = -1;

				#Hack alert...  hierarchy manager doesn't take into account for deleted nodes
				$query = 'SELECT parent_id FROM '.cms_db_prefix().'content WHERE content_id = '.$db->qstr($id);
				$result = $db->Execute($query);

				while ($result && !$result->EOF)
				{
					$parentid = $result->fields['parent_id'];
					$result->MoveNext();
				}
				
				$query = 'SELECT count(*) as thecount FROM '.cms_db_prefix().'content WHERE parent_id = '.$db->qstr($parentid);
				$result = $db->Execute($query);

				while ($result && !$result->EOF)
				{
					$childcount = $result->fields['thecount'];
					$result->MoveNext();
				}
				
				$node->Delete();

				#See if this is the last child... if so, remove
				#the expand for it
				if ($childcount == 1 && $parentid > -1)
				{
					toggleexpand($parentid, true);
				}
				
				#Do the same with this page as well
				toggleexpand($id, true);

				// put mention into the admin log
				audit($id, 'Content Item: '.$title, 'Deleted');
			}
			$ops = $gCms->GetContentOperations();
			$ops->SetAllHierarchyPositions();
			$bulk = true;
		}
		if(! $bulk)
		{
			redirect("listcontent.php".$urlext.'&message=no_bulk_performed');
		}
		redirect("listcontent.php".$urlext.'&message=bulk_success');
	}
	else if ($action == 'inactive')
	{
		$userid = get_userid();
		$modifyall = check_permission($userid, 'Modify Any Page');

		foreach ($nodelist as $node)
		{
			$permission = ($modifyall || check_ownership($userid, $node->Id()) || check_authorship($userid, $node->Id()) || check_permission($userid, 'Manage All Content'));

			if ($permission)
			{
				if ($node->Active())
				{
					$bulk = true;
					$node->SetActive(false);
					$node->Save();
				}
			}
		}
		if(! $bulk)
		{
			redirect("listcontent.php".$urlext.'&message=no_bulk_performed');
		}
		redirect("listcontent.php".$urlext.'&message=bulk_success');
	}
	else if ($action == 'active')
	{
		$userid = get_userid();
		$modifyall = check_permission($userid, 'Modify Any Page');

		foreach ($nodelist as $node)
		{
			$permission = ($modifyall || check_ownership($userid, $node->Id()) || check_authorship($userid, $node->Id()) || check_permission($userid, 'Manage All Content'));

			if ($permission)
			{
				if (!$node->Active())
				{
					$node->SetActive(true);
					$node->Save();
					$bulk = true;
				}
			}
		}
		if(! $bulk)
		{
			redirect("listcontent.php".$urlext.'&message=no_bulk_performed');
		}
		redirect("listcontent.php".$urlext.'&message=bulk_success');
	}
        else if ($action == 'setcachable' || $action == 'setnoncachable')
        {
                $flag = ($action == 'setcachable')?true:false;

		$userid = get_userid();
		$modifyall = check_permission($userid, 'Modify Any Page');

		foreach ($nodelist as $node)
		{
			$permission = ($modifyall || check_ownership($userid, $node->Id()) || check_authorship($userid, $node->Id()) || check_permission($userid, 'Manage All Content'));

			if ($permission)
			{
			   $node->SetCachable($flag);
			   $node->Save();
			}
		}
		redirect("listcontent.php".$urlext.'&message=bulk_success');
        }
	else if ($action == 'secure' || $action == 'insecure')
        {
	  $flag = ($action == 'secure')?1:0;

	  $userid = get_userid();
	  $modifyall = check_permission($userid, 'Modify Any Page');

	  foreach ($nodelist as $node)
	    {
	      $permission = ($modifyall || check_ownership($userid, $node->Id()) || check_authorship($userid, $node->Id()) || check_permission($userid, 'Manage All Content'));

	      if ($permission)
		{
		  $node->SetSecure($flag);
		  $node->Save();
		}
	    }
	  redirect("listcontent.php".$urlext.'&message=bulk_success');
	}
        else if ($action == 'showinmenu' || $action == 'hidefrommenu')
        {
                $flag = ($action == 'showinmenu')?true:false;

		$userid = get_userid();
		$modifyall = check_permission($userid, 'Modify Any Page');

		foreach ($nodelist as $node)
		{
			$permission = ($modifyall || check_ownership($userid, $node->Id()) || check_authorship($userid, $node->Id()) || check_permission($userid, 'Manage All Content'));

			if ($permission)
			{
			   $node->SetShowInMenu($flag);
			   $node->Save();
			}
		}
		redirect("listcontent.php".$urlext.'&message=bulk_success');
        }
	else if ($action == 'reorder')
	{
	  if (FALSE == $reorder_error)
	    {
	      redirect('listcontent.php'.$urlext.'&amp;message=pages_reordered');
	    }
	  else
	    {
	      redirect('listcontent.php'.$urlext.'&amp;error='.$reorder_error);
	    }
	}
	else
	{
		redirect('listcontent.php'.$urlext.'&message=no_bulk_performed');
	}
}

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
