<?php

@error_reporting(E_ERROR);

$sql='SELECT * FROM '.cms_db_prefix().'module_simpleslider_images WHERE 1;';
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
	<?=$this->CreateLink($id, 'defaultadmin', $returnid, $this->Lang('add'), array('active_tab'=>'shows', 'addimage'=>$_GET[$id.'addimage'], 'image'=>$fields['id']), '' , false, true, '')?>
	<div><?=$fields['title']?> </div>
	<div>
		<a href="<?=$this->CreateLink($id, 'defaultadmin', $returnid, '', array('active_tab'=>'shows', 'addimage'=>$_GET[$id.'addimage'], 'image'=>$fields['id']), '' , true, true, '')?>">
			<img src="../uploads/sliderimages/img<?=$fields['id']?>.jpg" width="120" height="90" />
		</a>
	</div>
</div>
<?
}
$result->Close();
?>
<div style="clear:both;"><?=$this->CreateLink($id, 'defaultadmin', $returnid, 'Back', array('active_tab'=>'shows'), '' , false, true, '')?></div>
