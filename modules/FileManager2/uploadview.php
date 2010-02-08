<?php
if (!isset($gCms)) exit;
if (!$this->CheckPermission('Modify Files')) exit;
$config = $this->config;


$this->smarty->assign('formstart',$this->CreateFormStart($id, 'upload', $returnid,"post","multipart/form-data"));
$this->smarty->assign('maxfilesize',$config["max_upload_size"]);
$this->smarty->assign('path',$this->CreateInputHidden($id,"path",$path));
$this->smarty->assign('unpacktext',$this->Lang("unpackafterupload"));


$inputs=array();
for($i=0; $i<$uploadboxes;$i++) {
	$onerow = new stdClass();
	$onerow->label=$this->Lang("fileno").($i+1);
	$onerow->fileinput=$this->CreateInputFile($id,"file_".$i,"",40);
	$onerow->unpackinput=$this->CreateInputCheckBox($id,"unpack".$i,"unpack");

  array_push($inputs, $onerow);
	
}

$this->smarty->assign_by_ref('inputs', $inputs);
$this->smarty->assign('submit',$this->CreateInputSubmit($id,"ok",$this->Lang("ok"),"",""));
$this->smarty->assign('formend',$this->CreateFormEnd());


echo $this->ProcessTemplate('uploadview.tpl');

?>
