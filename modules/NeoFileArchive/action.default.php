<?php

if (!isset($gCms)) exit;

if (isset($params["header"])) {
  $this->OutputHeaderInfo();
  return;
}

$this->NeoSetup($id, $this, $returnid, $params);

if ($this->IsTableEmpty("links")) {
  echo $this->Lang("nolinksfound");
  return;
}
$showlabels=array();
if (isset($params["labels"]) && $params["labels"]!="") {
  $labels=$params["labels"];
  $showlabels=explode(",",$labels);
}

$links=$this->GetEntitiesFull("links");
$showlinks=array();
if (!empty($showlabels)) {
  foreach($links as $link) {
    $show=false;
    $thislabels=explode(",",$link["labels"]);
    foreach($showlabels as $showlabel) {
      if (in_array($showlabel,$thislabels)) {
        $show=true;
        break;
      }
    }
    if ($show) {
      $showlinks[]=$link;
    }
  }
} else {
  $showlinks=$links;
}

$config=$this->GetConfig();
$outlinks=array();
foreach($showlinks as $showlink) {
  $outlink=array();
  $outlink["title"]=$showlink["title"];
  $outlink["showurl"]=$this->NeoCmsUrl(
    array(
      "action"=>"gotolink",
      "params"=>array("linkid"=>$showlink["id"]),
      "onlyhref"=>true
    )
  );
  $outlink["showurl"].="&showtemplate=false";
  $outlink["imagesrc"]=$this->Slashes($config["root_url"]."/".$this->Pref("imagelocation")."/".$showlink["image"]);
  $outlink["description"]=nl2br($showlink["description"]);
  $outlinks[]=$outlink;
}

$this->SmartyAssign("links",$outlinks);
$this->SmartyAssign("includejquery",$this->Pref("includejq","1"));
//print_r($outlinks);

echo $this->ProcessTemplateFromData($this->GetBodyPart($this->Pref("linktemplate")));
?>