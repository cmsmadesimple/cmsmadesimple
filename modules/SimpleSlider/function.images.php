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
	<div><?=$fields['title']?> </div>
	<div><img src="../uploads/sliderimages/img<?=$fields['id']?>.jpg" width="120" height="90" /></div>
	<div>
		<?=$this->CreateLink($id, 'defaultadmin', $returnid, $this->Lang('edit'), array('active_tab'=>'images', 'edit'=>$fields['id']), '' , false, true, '')?>
		<?=$this->CreateLink($id, 'defaultadmin', $returnid, $this->Lang('del'), array('active_tab'=>'images', 'delimage'=>$fields['id']), 'Are you sure you want to delete this entry? Name :'.$fields['title'] , false, true, '')?>
	</div>
</div>
<?php
}
$result->Close();

if(isset($_GET[$id.'edit'])){
?>
<div style="clear:both;"></div>
<form enctype="multipart/form-data" action="<?=$this->CreateLink($id, 'defaultadmin', $returnid, '', array('active_tab'=>'images', 'image'=>$curimage['id']), '', true, true, '');?>" method="post" enctype="multipart/form-data" name="form1" id="form1">
  <table>
    <tr>
      <td width="80"><?=$this->Lang('name')?> :</td>
      <td width="400"><input type="text" name="<?=$id?>title" id="title" value="<?=$curimage['title']?>" /></td>
    </tr>
    <tr>
      <td valign="top"><?=$this->Lang('description')?> :</td>
      <td><textarea type="text" name="<?=$id?>description" style="width:400px; height:80px;" ><?=$curimage['description']?></textarea></td>
    </tr>
    <tr>
      <td valign="top"><?=$this->Lang('link')?> :</td>
      <td width="400"><input type="text" name="<?=$id?>imagelink" id="imagelink" value="<?=$curimage['imagelink']?>" /></td>
    </tr>
    <tr>
      <td colspan="2"><input type="file" name="<?=$id?>image" id="image" /></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><input type="submit" name="<?=$id?>updateimage" id="updateimage" value="<?=$this->Lang('name')?>Update" /></td>
    </tr>
  </table>
</form>
<?php }else{ ?>
<div style="clear:both;"></div>
<form enctype="multipart/form-data" action="<?=$this->CreateLink($id, 'defaultadmin', $returnid, '', array('active_tab'=>'images'), '', true, true, '');?>" method="post" enctype="multipart/form-data" name="form1" id="form1">
  <table>
    <tr>
      <td width="80"><?=$this->Lang('name')?> :</td>
      <td width="400"><input type="text" name="<?=$id?>title" id="title" value="<?=$_POST[$id.'title']?>" /></td>
    </tr>
    <tr>
      <td valign="top"><?=$this->Lang('description')?> :</td>
      <td><textarea name="<?=$id?>description" id="description" style="width:400px; height:80px;" ><?=$_POST[$id.'description']?></textarea></td>
    </tr>
    <tr>
      <td valign="top"><?=$this->Lang('link')?> :</td>
      <td width="400"><input type="text" name="<?=$id?>imagelink" id="imagelink" value="<?=$_POST[$id.'imagelink']?>" /></td>
    </tr>
    <tr>
      <td colspan="2"><input type="file" name="<?=$id?>image" id="image" /></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><input type="submit" name="<?=$id?>addimage" id="addimage" value="<?=$this->Lang('add')?>" /></td>
    </tr>
  </table>
</form>
<?php } ?>
