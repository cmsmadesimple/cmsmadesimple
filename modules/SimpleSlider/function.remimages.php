<?php

@error_reporting(E_ERROR);

$sql='SELECT * FROM '.cms_db_prefix().'module_simpleslider_showimages WHERE `showid`="'.mysql_real_escape_string($_GET[$id.'remimage']).'";';
$result=$db->Execute($sql);
if($this->debug){
	//echo $sql;
	echo $db->ErrorMsg();
}
$flds=$result->fields;

while(!$result->EOF()){
$fields=$result->fields;
$result->MoveNext();
?>
<div style="float:left; width:160px;">
	<?=$this->CreateLink($id, 'defaultadmin', $returnid, $this->Lang('remove'), array('active_tab'=>'shows', 'remimage'=>$_GET[$id.'remimage'], 'image'=>$fields['imageid']), '' , false, true, '')?>
	<div><?=$fields['title']?> </div>
	<div>
		<a href="<?=$this->CreateLink($id, 'defaultadmin', $returnid, '', array('active_tab'=>'shows', 'remimage'=>$_GET[$id.'remimage'], 'image'=>$fields['imageid']), '' , true, true, '')?>">
			<img src="../uploads/sliderimages/img<?=$fields['imageid']?>.jpg" width="120" height="90" />
		</a>
	</div>
</div>
<?
}
$result->Close();
?>
<div style="clear:both;"><?=$this->CreateLink($id, 'defaultadmin', $returnid, $this->Lang('back'), array('active_tab'=>'shows'), '' , false, true, '')?></div>
