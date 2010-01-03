<?php

@error_reporting(E_ERROR);

if (!isset($gCms)) exit;

$db = $this->GetDb();

$vars=array();
if(isset($params['show'])){	
	$sql='SELECT * FROM '.cms_db_prefix().'module_simpleslider_shows WHERE showname=?;';
	$result=$db->Execute($sql,array($params['show']));
	$fields=$result->fields;
	$showid=$fields['showid'];
	$width=$fields['width'];
	$height=$fields['height'];
	$fadetime=$fields['fadetime'];
  $vars=array($showid);
	$sql='SELECT * FROM '.cms_db_prefix().'module_simpleslider_images, '.cms_db_prefix().'module_simpleslider_showimages WHERE `id`=`imageid` AND showid=?;';
}else{
	$width=$this->getPreference('defwidth');
	$height=$this->getPreference('defheight');
	$fadetime=$this->getPreference('deffadetime');
	$sql='SELECT * FROM '.cms_db_prefix().'module_simpleslider_images WHERE 1;';
}
$result=$db->Execute($sql,$vars);
if($this->debug){
	//echo $sql;
	echo $db->ErrorMsg();
}
$flds=$result->fields;
?>
<style type="text/css">
	#simpleslider {
	   width: <?=$width?>px; 
	   height: <?=$height?>px; 
	   position: relative;
	   overflow: hidden;
	}

	#simplesliderContent{
	    width: 720px;
	    position: absolute;
		top: 0;
		margin-left: 0;
	}
	.simplesliderImage{
	    float: left;
	    position: relative;
		display: none;
	}
	.simplesliderImage span {
	    position: absolute;
		font: 10px/15px Arial, Helvetica, sans-serif;
	    padding: 10px 13px;
	    width: <?=($width-16)?>px;
	    background-color: #333333;
	    filter: alpha(opacity=60);
	    -moz-opacity: 0.6;
		-khtml-opacity: 0.6;
	    opacity: 0.6;
	    color: #fff;
	    display: none;
	}

	.clear {
		clear: both;
	}
	
	.top {
		top: 0;
		left: 0;
	}
	.bottom {
		bottom: 0;
	    left: 0;
	}
	.left {
		top: 0;
	    left: 0;
		width: 110px !important;
		height: 280px;
	}
	.right {
		right: 0;
		bottom: 0;
		width: 90px !important;
		height: 290px;
	}
	ul, li{
		margin: 0;
		padding: 0;
		border: 0;
		outline: 0;
		font-size: 100%;
		vertical-align: baseline;
		background: transparent;
	}
	ul {
		list-style: none;
	}


</style>
<script src="lib/jquery.js" type="text/javascript"></script>
<script src="lib/s3Slider.js" type="text/javascript"></script> 
<script type="text/javascript">
	$(document).ready(function() {
	   $('#simpleslider').s3Slider({
	      timeOut: <?=$fadetime?>
	   });
	});
</script>
<div id="simpleslider" style="margin:auto;">
   <ul id="simplesliderContent">
<?

while(!$result->EOF()){
$fields=$result->fields;
$result->MoveNext();
?>
      <li class="simplesliderImage">
		<img src="uploads/sliderimages/img<?=$fields['id']?>.jpg" width="<?=$width?>" height="<?=$height?>" <? if($fields['imagelink']!=''){ ?> onClick="javascript:location.href='<?=$fields['imagelink']?>';" style="cursor:pointer;" <? } ?> />
		<span class="bottom">
			<div style="font-weight:bold; font-size:14px;"><?=$fields['title']?></div>
			<? if($fields['imagelink']!=''){ ?>
			<a href="<?=$fields['imagelink']?>" class="slidelink">
			<?
			}
			?>
			<?=$fields['description']?>
			<? if($fields['imagelink']!=''){ ?>
			</a>
			<?
			}
			?>			
		</span>
      </li>
<?
}

?>
      <div class="clear simplesliderImage"></div>
   </ul>
</div>
<?

$result->Close();

?>
