<?php

$CMS_ADMIN_PAGE=1;

require_once("../include.php");

$gCms = cmsms();
check_login();
$userid = get_userid();
$urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];
$thisurl=basename(__FILE__).$urlext;

if( isset($_POST['cancel']) || isset($_POST['submit']))
  {
    redirect('listcontent.php'.$urlext);
    return;
  }
if(isset( $_POST['revert'] ))
  {
	$_POST = array();
  } 
if( isset($_POST['data']) )
  {
    function ordercontent_get_node_rec($str,$prefix = 'page_')
    {
      $gCms = cmsms();
      $tree = $gCms->GetHierarchyManager();

      if( !is_numeric($str) && startswith($str,$prefix) )
	{
	  $str = substr($str,strlen($prefix));
	}

      $id = (int)$str;
      $tmp = $tree->find_by_tag('id',$id);
      $content = '';
      if( $tmp )
	{
	  $content = $tmp->getContent(false,true,true);
	  if( $content )
	    {
	      $rec = aray();
	      $rec['id'] = (int)$str;
	    }
	}
    }

    function ordercontent_create_flatlist($tree,$parent_id = -1)
    {
      $data = array();
      $order = 1;
      foreach( $tree as &$node )
	{
	  if( is_array($node) && count($node) == 2 )
	    {
	      $pid = substr($node[0],strlen('page_'));
	      $data[] = array('id'=>$pid,'parent_id'=>$parent_id,'order'=>$order);
	      if( isset($node[1]) && is_array($node[1]) )
		{
		  $data = array_merge($data,ordercontent_create_flatlist($node[1],$pid));
		}
	    }
	  else
	    {
	      $pid = substr($node,strlen('page_'));
	      $data[] = array('id'=>$pid,'parent_id'=>$parent_id,'order'=>$order);
	    }
	  $order++;
	}
      return $data;
    }

    $data = json_decode($_POST['data']);

    // step 1, create a flat list of the content items, and their new orders, and new parents.
    $data = ordercontent_create_flatlist($data);

    // step 2. merge in old orders, and old parents.
    $gCms = cmsms();
    $tree = $gCms->GetHierarchyManager();
    $data2 = array();
    for( $i = 0; $i < count($data); $i++ )
      {
	$rec =& $data[$i];
	$node = $tree->find_by_tag('id',$rec['id']);
	$content = $node->getContent(false,true,true);
	if( $content )
	  {
	    $rec['old_parent'] = $content->ParentId();
	    $rec['old_order'] = $content->ItemOrder();

	    if( ($rec['old_parent'] != $rec['parent_id']) ||
		$rec['old_order'] != $rec['order'] )
	      {
		$data2[] = $rec;
	      }
	  }
      }
    
    // do the updates
    if( count($data2) > 0 )
      {
	$db = $gCms->GetDb();
	$query = 'UPDATE '.cms_db_prefix().'content SET item_order = ?, parent_id = ? WHERE content_id = ?';
	for( $i = 0; $i < count($data2); $i++ )
	  {
	    $rec =& $data2[$i];
	    $db->Execute($query,array($rec['order'],$rec['parent_id'],$rec['id']));
	  }
	$contentops = $gCms->GetContentOperations();
	$contentops->SetAllHierarchyPositions();
	$contentops->ClearCache();
	return;
      }
    else
      {
	echo lang('nothingtodo');
	return;
      }
  }

//
// begin the output
//
include_once("header.php");
include_once("../lib/classes/class.admintheme.inc.php");

//echo '<div class="pageoverflow">';
//echo $themeObject->ShowHeader('reorderpages');

$tree = $gCms->GetHierarchyManager();
$smarty->assign('showheader', $themeObject->ShowHeader('reorderpages'));
$smarty->assign('tree',$tree);
$smarty->assign('urlext',$urlext);


echo $smarty->fetch('ordercontent.tpl');
/*
<p class="pageback"><a class="pageback" href="<?php echo $themeObject->BackUrl(); ?>">&#171; <?php echo lang('back')?></a></p>
*/

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>