<?php
if (!function_exists("cmsms")) exit;
if (!$this->CheckPermission("Modify Files") && !$this->AdvancedAccessAllowed()) exit;

$config = cmsms()->getConfig();

if(empty($params['imagesrc'])) {
	echo "empty parameter imagesrc";
	return;
}

$imagesrc = $params['imagesrc'];

//Security : refuse all the crap like "../../"
$secuPattern = array(NULL, "\x1a", "\n", "\r", "\\", "‘", "»", "\\x00", "..", "./", "/.", '*', '<', '>');
foreach($secuPattern as $pattern) {
	if(FALSE !== strpos($imagesrc, $pattern)) {
		echo "no hack allowed [".$pattern."]";
		return;
	}
}

if(empty($params['imagesrc'])) {
	echo "empty parameter imagesrc";
	return;
}

//print_r($params);

$image = $config['root_url'].'/'.$params['imagesrc'];
$imagepath = $config['root_path'].'/'.$params['imagesrc'];
if(!file_exists($imagepath)){
	echo $imagepath ." is not found";
	return;
}

//Create new image Manipulator classe
$imageEditor = new imageEditor();

if(empty($params['reset'])
	&& !empty($params['cx']) && !empty($params['cy']) 
	&& !empty($params['cw']) && !empty($params['ch']) 
	&& !empty($params['iw']) && !empty($params['ih'])){
	
	//Get the mimeType
	$mimeType = $imageEditor->getMime($imagepath);

	//Open new Instance
	$instance = $imageEditor->open($imagepath);

	//Resize it if necessary
	if(!empty($params['resize']) && $params['resize'] == 'resize'){
		$instance = $imageEditor->resize($instance, $mimeType, $params['iw'], $params['ih']);
	}

	//Crop it if necessary
	if(!empty($params['crop']) && $params['crop'] == 'crop'){
		$instance = $imageEditor->crop($instance, $mimeType, $params['cx'], $params['cy'], $params['cw'], $params['ch']);
	}
	
	//Save it
	$imageEditor->save($instance, $imagepath, $mimeType);
}


unset($params['cx']);
unset($params['cy']);
unset($params['cw']);
unset($params['ch']);
unset($params['iw']);
unset($params['ih']);
unset($params['resize']);
unset($params['crop']);
unset($params['reset']);

$hiddens = array();
foreach($params as $key => $value){
	$hiddens[] = "<input type='hidden' id='".$id.$key."' name='".$id.$key."' value='".$value."' />";
}

$hiddens[] = "<input type='hidden' id='mact' name='mact' value='".$_GET['mact']."' />";
$hiddens[] = "<input type='hidden' id='_sx_' name='_sx_' value='".$_GET['_sx_']."' />";

$smarty->assign('image',$image);
$smarty->assign('image_width',$imageEditor->getWidth($imagepath));

$smarty->assign('id',$id);
$smarty->assign('hiddens',$hiddens);
$smarty->assign('formUrl',$this->CreateLink($id, "pie", $returnid, '', $params, '', true));

ini_set('default_charset', 'utf-8');
header("Cache-Control: no-store"); 
header('content-type: text/html; charset: utf-8');

echo $this->ProcessTemplate('pie.tpl');

?>