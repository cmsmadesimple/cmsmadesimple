<?php
if (!isset($gCms)) exit;

if (!$this->Permission->check('Manage Menu'))
{
	exit;
}

$admintheme =& $gCms->variables['admintheme'];
$default_template = $this->Preference->get('default_template','simple_navigation.tpl');

/*****************************************
 * Handle the Database Tab
 ****************************************/
$entryarray = array();

$templates = $this->Template->get_list();
foreach ($templates as $onetemplate)
{
  $onerow = new stdClass();

  $onerow->templatename = $onetemplate;
  $onerow->templatelink = $this->Url->link(array('action' => 'edittemplate', 'contents' => $onetemplate, 'tplname' => $onetemplate));
  
  $onerow->editlink = $this->CreateLink($id, 'edittemplate', $returnid, $gCms->variables['admintheme']->DisplayImage('icons/system/edit.gif', $this->Lang('edittemplate'),'','','systemicon'), array('tplname' => $onetemplate));

  if( $default_template != $onetemplate )
    {
      $onerow->setdefault_link = $this->CreateLink($id,'setdefault',$returnid,
						   $admintheme->DisplayImage('icons/system/false.gif',
									     $this->Lang('set_as_default'),'','','systemicon'),array('template'=>$onetemplate));

      $onerow->deletelink = $this->CreateLink($id, 'deletetemplate', $returnid, $gCms->variables['admintheme']->DisplayImage('icons/system/delete.gif', $this->Lang('deletetemplate'),'','','systemicon'), array('tplname' => $onetemplate), $this->Lang('areyousure'));
    }
  $entryarray[] = $onerow;
}





/*****************************************
 * Handle the File Tab
 ****************************************/
$dir = dirname(__FILE__) . '/templates';
$dh  = opendir($dir);
$files = array();
while (false !== ($filename = readdir($dh)))
{
	$files[] = $filename;
}
if (isset($dh))
	closedir($dh);

$badfiles = array('filetpllist.tpl', 'dbtpllist.tpl', 'edittemplate.tpl', 'importtemplate.tpl');
foreach ($files as $onefile)
{
	//If this is not a .tpl file, skip it
	if (!endswith($onefile, '.tpl')) continue;

	//If this is in badfiles, skip it
	if (in_array($onefile, $badfiles)) continue;

	$onerow = new stdClass();

	$onerow->templatename = $onefile;
	$onerow->importlink = $this->Url->link(array('action' => 'importtemplate', 'tplname' => $onefile, 'contents' => $gCms->variables['admintheme']->DisplayImage('icons/system/import.gif', $this->lang('importtemplate'),'','','systemicon')));
	//$onerow->importlink = $this->CreateLink($id, 'importtemplate', $returnid, $gCms->variables['admintheme']->DisplayImage('icons/system/import.gif', $this->Lang('importtemplate'),'','','systemicon'), array('tplname' => $onefile));

	if( $default_template != $onefile )
	{
		$onerow->setdefault_link = $this->Url->link(array('action' => 'setdefault', 'template' => $onefile, 'contents' => $admintheme->DisplayImage('icons/system/false.gif', $this->Lang('set_as_default'),'','','systemicon')));
	}
	$entryarray[] = $onerow;
}

$smarty->assign_by_ref('items', $entryarray);
$smarty->assign('itemcount', count($entryarray));

$smarty->assign('default_template',$default_template);
$smarty->assign('filenametext', $this->lang('filename'));
$smarty->assign('nofilestext', $this->lang('notemplatefiles', dirname(__FILE__) . '/templates'));
$smarty->assign('addlink', 
	$this->Url->link(
		array(
			'action' => 'addtemplate',
			'contents' => $gCms->variables['admintheme']->DisplayImage('icons/system/newobject.gif', $this->Lang('addtemplate'),'','','systemicon')
		)
	) . ' ' .
	$this->Url->link(
		array(
			'action' => 'addtemplate',
			'contents' => $this->lang('addtemplate')
		)
	)
);
$smarty->assign('templatetext', $this->lang('templates'));
$smarty->assign('defaulttext',$this->lang('default'));
$smarty->assign('yesimg',$admintheme->DisplayImage('icons/system/true.gif',$this->Lang('this_is_default'),'','','systemicon'));
$smarty->assign('readonlytext',$this->lang('readonly'));


#Display template
echo $this->Template->process('dbtpllist.tpl');

/*****************************************
 * Finished File Tab
 ****************************************/

echo $this->Tabs->end_tab_content();
?>
