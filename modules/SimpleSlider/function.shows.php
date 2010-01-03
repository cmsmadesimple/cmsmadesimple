<?php

@error_reporting(E_ERROR);

$sql='SELECT * FROM '.cms_db_prefix().'module_simpleslider_shows WHERE 1;';
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
	<div>
		<?=$fields['showname']?>
		<?=$this->CreateLink($id, 'defaultadmin', $returnid, 'Edit', array('active_tab'=>'shows', 'editshow'=>$fields['showid']), '' , false, true, '')?>
		<?=$this->CreateLink($id, 'defaultadmin', $returnid, 'Del', array('active_tab'=>'shows', 'delshow'=>$fields['showid']), 'Are you sure you want to delete this entry? Name :'.$fields['name'] , false, true, '')?> </div>
	<div>
		<?=$this->CreateLink($id, 'defaultadmin', $returnid, 'Add Images', array('active_tab'=>'shows', 'addimage'=>$fields['showid']), '' , false, true, '')?>
		<?=$this->CreateLink($id, 'defaultadmin', $returnid, 'Remove Images', array('active_tab'=>'shows', 'remimage'=>$fields['showid']), '' , false, true, '')?>
	</div>
</div>
<?
}
$result->Close();

if(isset($_GET[$id.'editshow'])){
?>
<div style="clear:both;"></div>
<form enctype="multipart/form-data" action="<?=$this->CreateLink($id, 'defaultadmin', $returnid, '', array('active_tab'=>'shows', 'show'=>$curshow['showid']), '', true, true, '');?>" method="post" enctype="multipart/form-data" name="form1" id="form1">
  <table>
    <tr>
      <td width="100"><?=$this->Lang('name')?> :</td>
      <td width="400"><input type="text" name="<?=$id?>showname" id="showname" value="<?=$curshow['showname']?>" /></td>
    </tr>
    <tr>
      <td><?=$this->Lang('width')?> :</td>
      <td><input type="text" name="<?=$id?>width" id="width" value="<?=$curshow['width']?>" /></td>
    </tr>
    <tr>
      <td><?=$this->Lang('height')?> :</td>
      <td><input type="text" name="<?=$id?>height" id="height" value="<?=$curshow['height']?>" /></td>
    </tr>
    <tr>
      <td><?=$this->Lang('fadetime')?> :</td>
      <td><input type="text" name="<?=$id?>fadetime" id="fadetime" value="<?=$curshow['fadetime']?>" /></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><input type="submit" name="<?=$id?>updateshow" id="updateshow" value="<?=$this->Lang('update')?>" /></td>
    </tr>
  </table>
</form>
<? }else{ ?>
<div style="clear:both;"></div>
<form enctype="multipart/form-data" action="<?=$this->CreateLink($id, 'defaultadmin', $returnid, '', array('active_tab'=>'shows'), '', true, true, '');?>" method="post" enctype="multipart/form-data" name="form1" id="form1">
  <table>
    <tr>
      <td width="100">Name :</td>
      <td width="400"><input type="text" name="<?=$id?>showname" id="showname" value="<?=$_POST[$id.'showname']?>" /></td>
    </tr>
    <tr>
      <td>Width :</td>
      <td><input type="text" name="<?=$id?>width" id="width" value="<?=$_POST[$id.'width']?>" /></td>
    </tr>
    <tr>
      <td>Height :</td>
      <td><input type="text" name="<?=$id?>height" id="height" value="<?=$_POST[$id.'height']?>" /></td>
    </tr>
    <tr>
      <td>Fadeout Time (ms) :</td>
      <td><input type="text" name="<?=$id?>fadetime" id="fadetime" value="<?=$_POST[$id.'fadetime']?>" /></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><input type="submit" name="<?=$id?>addshow" id="addshow" value="Add" /></td>
    </tr>
  </table>
</form>
<? } ?>
