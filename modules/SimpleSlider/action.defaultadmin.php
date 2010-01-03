<?php

@error_reporting(E_ERROR);

if (!$this->VisibleToAdminUser()) {
	$this->ShowError($this->Lang("accessdenied"));
	return;
}

if (!isset($gCms)) exit;

$db = $this->cms->db;

if(isset($_POST[$id.'updatesettings'])){
	$this->setPreference('defwidth', $_POST[$id.'defwidth']);
	$this->setPreference('defheight', $_POST[$id.'defheight']);
	$this->setPreference('deffadetime', $_POST[$id.'deffadetime']);
}

if(isset($_GET[$id.'editshow'])){
	$showid=mysql_real_escape_string($_GET[$id.'editshow']);	
	$sql='SELECT * FROM '.cms_db_prefix().'module_simpleslider_shows WHERE showid="'.$showid.'";';
	$result=$db->Execute($sql);
	if($this->debug){
		//echo $sql;
		echo $db->ErrorMsg();
	}
	$curshow=$result->fields;
}

if(isset($_GET[$id.'delshow'])){
	$showid=mysql_real_escape_string($_GET[$id.'delshow']);	
	
	$db->Execute('DELETE FROM '.cms_db_prefix().'module_simpleslider_showimages WHERE `showid`="'.$showid.'";');
	if($this->debug){
		//echo $sql;
		echo $db->ErrorMsg();
	}
	$db->Execute('DELETE FROM '.cms_db_prefix().'module_simpleslider_shows WHERE `showid`="'.$showid.'";');
	if($this->debug){
		//echo $sql;
		echo $db->ErrorMsg();
	}
}

if(isset($_POST[$id.'addshow'])){
	$fields=array();
	$fields['showname']=mysql_real_escape_string($_POST[$id.'showname']);
	$fields['width']=$this->getPreference('defwidth');
	$fields['height']=$this->getPreference('defheight');
	$fields['fadetime']=$this->getPreference('deffadetime');
	if($_POST[$id.'width']>0){
		$fields['width']=mysql_real_escape_string($_POST[$id.'width']);
	}
	if($_POST[$id.'height']>0){
		$fields['height']=mysql_real_escape_string($_POST[$id.'height']);
	}
	if($_POST[$id.'fadetime']>0){
		$fields['fadetime']=mysql_real_escape_string($_POST[$id.'fadetime']);
	}
		
	$sql=$this->GenerateInsertSQL(cms_db_prefix().'module_simpleslider_shows', $fields);
	$db->Execute($sql);
	
	if($this->debug){
		//echo $sql;
		echo $db->ErrorMsg();
	}
	
	unset($_POST[$id.'showname']);
	unset($_POST[$id.'width']);
	unset($_POST[$id.'height']);
	unset($_POST[$id.'fadetime']);
}

if(isset($_POST[$id.'updateshow'])){
	$fields=array();
	$showid=mysql_real_escape_string($_GET[$id.'show']);
	$fields['width']=mysql_real_escape_string($_POST[$id.'width']);
	$fields['height']=mysql_real_escape_string($_POST[$id.'height']);
	$fields['fadetime']=mysql_real_escape_string($_POST[$id.'fadetime']);
		
	$fields['showname']=mysql_real_escape_string($_POST[$id.'showname']);
	unset($_POST[$id.'showname']);
	unset($_POST[$id.'width']);
	unset($_POST[$id.'height']);
	unset($_POST[$id.'fadetime']);
		
	$sql=$this->GenerateUpdateSQL(cms_db_prefix().'module_simpleslider_shows', $fields, '`showid`="'.$showid.'"');
	$db->Execute($sql);
	if($this->debug){
		//echo $sql;
		echo $db->ErrorMsg();
	}
}

if(isset($_GET[$id.'addimage']) AND isset($_GET[$id.'image'])){
	$showid=mysql_real_escape_string($_GET[$id.'addimage']);
	$imgid=mysql_real_escape_string($_GET[$id.'image']);	
	$fields=array();
	$fields['showid']=$showid;
	$fields['imageid']=$imgid;
		
	$sql=$this->GenerateInsertSQL(cms_db_prefix().'module_simpleslider_showimages', $fields);
	$db->Execute($sql);
	
	if($this->debug){
		//echo $sql;
		echo $db->ErrorMsg();
	}
}

if(isset($_GET[$id.'remimage']) AND isset($_GET[$id.'image'])){
	$showid=mysql_real_escape_string($_GET[$id.'remimage']);
	$imgid=mysql_real_escape_string($_GET[$id.'image']);	
	
	$db->Execute('DELETE FROM '.cms_db_prefix().'module_simpleslider_showimages WHERE `showid`="'.$showid.'" AND `imageid`="'.$imgid.'";');
	
	if($this->debug){
		//echo $sql;
		echo $db->ErrorMsg();
	}
}

if(isset($_GET[$id.'delimage'])){
	$imgid=mysql_real_escape_string($_GET[$id.'delimage']);	
	
	$db->Execute('DELETE FROM '.cms_db_prefix().'module_simpleslider_images WHERE `id`="'.$imgid.'";');
	if($this->debug){
		//echo $sql;
		echo $db->ErrorMsg();
	}
}

if(isset($_POST[$id.'addimage'])){
	if($_FILES[$id.'image']['name']!=''){
		$fields=array();
		$fields['title']=mysql_real_escape_string($_POST[$id.'title']);
		$fields['description']=mysql_real_escape_string($_POST[$id.'description']);
		$fields['imagelink']=mysql_real_escape_string($_POST[$id.'imagelink']);
			
		$sql=$this->GenerateInsertSQL(cms_db_prefix().'module_simpleslider_images', $fields);
		$db->Execute($sql);
		
		if($this->debug){
			//echo $sql;
			echo $db->ErrorMsg();
		}
		
		cms_move_uploaded_file($_FILES[$id.'image']['tmp_name'], '../uploads/sliderimages/img'.$db->Insert_ID().'.jpg');
		
		unset($_POST[$id.'title']);
		unset($_POST[$id.'description']);
		unset($_POST[$id.'imagelink']);
	}
}

if(isset($_POST[$id.'updateimage'])){
	$fields=array();
	$imgid=mysql_real_escape_string($_GET[$id.'image']);
		
	$fields['title']=mysql_real_escape_string($_POST[$id.'title']);
	$fields['description']=mysql_real_escape_string($_POST[$id.'description']);
	$fields['imagelink']=mysql_real_escape_string($_POST[$id.'imagelink']);
	unset($_POST[$id.'title']);
	unset($_POST[$id.'description']);
	unset($_POST[$id.'imagelink']);
		
	$sql=$this->GenerateUpdateSQL(cms_db_prefix().'module_simpleslider_images', $fields, '`id`="'.$imgid.'"');
	$db->Execute($sql);
	if($_FILES[$id.'image']['name']!=''){
		unlink('../uploads/sliderimages/img'.$imgid.'.jpg');
		cms_move_uploaded_file($_FILES[$id.'image']['tmp_name'], '../uploads/sliderimages/img'.$imgid.'.jpg');
	}
	if($this->debug){
		//echo $sql;
		echo $db->ErrorMsg();
	}
}


if(isset($_GET[$id.'edit'])){
	$imgid=mysql_real_escape_string($_GET[$id.'edit']);	
	$sql='SELECT * FROM '.cms_db_prefix().'module_simpleslider_images WHERE id="'.$imgid.'";';
	$result=$db->Execute($sql);
	if($this->debug){
		//echo $sql;
		echo $db->ErrorMsg();
	}
	$curimage=$result->fields;
}
// choose the tab to display. If no tab is set, select 'shows' as the default
// tab to display, because - in my opinion - this is the mostly needed tab.
if (!empty($params['active_tab']))
  $tab = $params['active_tab'];
else
  $tab = 'images';

// and finally, display all those tabs. First, setup the tabs, and than include
// the function.{tab}.php file, in which the tab's code is stored to keep this
// file a bit tidier.
echo $this->StartTabHeaders();

echo $this->SetTabHeader('images', $this->Lang('images'), 'images' == $tab ? true : false);

echo $this->SetTabHeader('shows', $this->Lang('shows'), 'shows' == $tab ? true : false);

echo $this->SetTabHeader('settings', $this->Lang('editsettings'), 'settings' == $tab ? true : false);

echo $this->EndTabHeaders();

// Display each tab's content
echo $this->StartTabContent();

echo $this->StartTab('images');
include 'function.images.php';
echo $this->EndTab();

echo $this->StartTab('shows');
if(isset($_GET[$id.'addimage'])){
	include 'function.addimages.php';
}else if(isset($_GET[$id.'remimage'])){
	include 'function.remimages.php';
}else{
	include 'function.shows.php';
}
echo $this->EndTab();

echo $this->StartTab('settings');
include 'function.settings.php';
echo $this->EndTab();
echo $this->EndTabContent();

?>
