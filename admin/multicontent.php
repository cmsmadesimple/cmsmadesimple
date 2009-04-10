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

include_once("header.php");

global $gCms;
$db =& $gCms->GetDb();
$hm =& $gCms->GetHierarchyManager();

$nodelist = array();
$bulk = false;

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
	$content =& $node->GetContent();
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
		$node =& $hm->sureGetNodeById($id);
		if (isset($node))
		{
			$content =& $node->GetContent();
			if (isset($content))
			{
				$nodelist[] =& $content;
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
			$node =& $hm->sureGetNodeById($id);
			if (isset($node))
			{
				if ($action == 'inactive')
					DoContent($nodelist, $node, true, false);
				else if ($action == 'active' || $action == 'settemplate' || $action == 'setcachable' || $action == 'setnoncachable'
                                         || $action == 'showinmenu' || $action == 'hidefrommenu')
					DoContent($nodelist, $node, false, false);
				else if ($action == 'delete')
 				  DoContent($nodelist, $node, false, true);
			}
		}
		if ($action == 'reorder')
		  {	  
		    if (startswith($k, 'order-'))
		      {
			$id = substr($k, strlen('order-'));
			$node =& $hm->sureGetNodeById($id);
			$one =& $node->getContent();
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
	if ($action == 'reorder' && $reorder_error == FALSE)
	  {
	    $order_changed = FALSE;
	    foreach ($reorder_array AS $page_id => $page_order)
	      {
		$node =& $hm->sureGetNodeById($page_id);
		$one =& $node->getContent();
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
			global $gCms;
			$contentops =& $gCms->GetContentOperations();
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
		?>
		<div class="pagecontainer">
			<p class="pageheader"><?php echo lang('deletecontent') ?></p><br />
		<?php
		$userid = get_userid();
		$access = (check_permission($userid, 'Remove Pages') ||
            check_permission($userid, 'Modify Page Structure'));
		if ($access)
		{
			echo '<form method="post" action="multicontent.php">' . "\n";
      echo '<div>
          <input type="hidden" name="'.CMS_SECURE_PARAM_NAME.'" value="'.$_SESSION[CMS_USER_KEY].'" />
        </div>';
      
			echo '<p>'.lang('deletepages').'</p><p>' . "\n";
			$idlist = array();
			foreach ($nodelist as $node)
			{
			  if ($node->DefaultContent())
			    {
			      redirect('listcontent.php'.$urlext.'&amp;error=error_delete_default_parent');
			    }

				echo $node->Name() . ' (' . $node->Hierarchy() . ')' . '<br />' . "\n";
				$idlist[] = $node->Id();
			}
			echo '</p>';

			echo '<div class="pageoverflow">
				<p class="pagetext">&nbsp;</p>
				<p class="pageinput">';

			echo '<input type="hidden" name="multiaction" value="dodelete" /><input type="hidden" name="idlist" value="'.implode(':', $idlist).'" />' . "\n";
			?>
								<input type="submit" name="confirm" value="<?php echo lang('submit') ?>"  class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" />
								<input type="submit" name="cancel" value="<?php echo lang('cancel') ?>" class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" />
							</p>
						</div>
					</form>
				</div>
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
	    $templateops =& $gCms->GetTemplateOperations();
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
			       check_permission($userid, 'Modify Page Structure'));

		if( $permission )
		  {
		    $node->SetTemplateId($template_id);
		    $node->Save();
		  }
	      }
	    redirect('listcontent.php'.$urlext.'&message=bulk_success');
	  }
	else if ($action == 'dodelete')
	{
		$userid = get_userid();
		$access = check_permission($userid, 'Remove Pages' || check_persmission($userid, 'Modify Page Structure'));
		if ($access)
		{
			global $gCms;
			$db = $gCms->GetDb();
			foreach (array_reverse($nodelist) as $node)
			{
				$id = $node->Id();
				$title = $node->Name() . ' (' . $node->Hierarchy() . ') -- ' . $id;
				
				$childcount = 0;
				$parentid = -1;

				#Hack alert...  hierarchy manager doesn't take into account for deleted nodes
				$query = 'SELECT parent_id FROM '.cms_db_prefix().'content WHERE content_id = '.$db->qstr($id);
				$result = &$db->Execute($query);

				while ($result && !$result->EOF)
				{
					$parentid = $result->fields['parent_id'];
					$result->MoveNext();
				}
				
				$query = 'SELECT count(*) as thecount FROM '.cms_db_prefix().'content WHERE parent_id = '.$db->qstr($parentid);
				$result = &$db->Execute($query);

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

				audit($id, $title, 'Deleted Content');
			}
			ContentManager::SetAllHierarchyPositions();
			$bulk = true;
		}
		//include_once("footer.php");
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
			$permission = ($modifyall || check_ownership($userid, $node->Id()) || check_authorship($userid, $node->Id()) || check_persmission($userid, 'Modify Page Structure'));

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
			$permission = ($modifyall || check_ownership($userid, $node->Id()) || check_authorship($userid, $node->Id()) || check_persmission($userid, 'Modify Page Structure'));

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
			$permission = ($modifyall || check_ownership($userid, $node->Id()) || check_authorship($userid, $node->Id()) || check_persmission($userid, 'Modify Page Structure'));

			if ($permission)
			{
			   $node->SetCachable($flag);
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
			$permission = ($modifyall || check_ownership($userid, $node->Id()) || check_authorship($userid, $node->Id()) || check_persmission($userid, 'Modify Page Structure'));

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
