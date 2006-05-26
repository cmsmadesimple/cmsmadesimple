<?php
# Image Gallery Plugin
# Russ Baldwin 
# http://www.shoesforindustry.net
# For more information see the help sections at the end of this file
# ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# Converted from my User Plugin which was based on the code by RedGuy which in 
# turn was was inspired by a thumbnailGenerator v2.25 (21|07|2004) by 
# michael kloepzig mischer@save-the-gummybears.org
# http://www.save-the-gummybears.org
# 
# ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++


function smarty_cms_function_ImageGallery($params, &$smarty)
{
//yu
$type = 'click'; // "click" or "popup"
$picFolder = 'uploads/images/';  //path to pics, ending with /
$divID = 'imagegallery'; // Set the wrapping div id to allow you to have different CSS for each gallery.
$sortBy = 'name'; //Sort image files by 'name' o 'date'
$sortByOrder = 'asc'; //Sort image files in ascending order: 'asc' or decending order: 'desc'
$bigPicCaption = 'name'; // either 'name', 'file', 'number' or 'none', Sets caption above big image.
$thumbPicCaption = 'name'; // either 'name', 'file', 'number' or 'none', Sets caption below thumbnails
$bigPicAltTag = 'name'; // either 'name', 'file', 'number'. Sets alt tag - compulsory
$bigPicTitleTag = 'name'; // either 'name', 'file', 'number' or 'none'. Sets title tag or removes it
$thumbPicAltTag = 'name'; // either 'name', 'file', 'number'. Sets alt tag - compulsory
$thumbPicTitleTag = ''; // either the default or 'name', 'file', 'number' or 'none'. Sets title tag or removes it

if(isset($params['divID'])) $divID = $params['divID'];
if(isset($params['type'])) $type = $params['type'];
if(isset($params['picFolder'])) $picFolder = $params['picFolder'];
if(isset($params['bigPicCaption'])) $bigPicCaption = $params['bigPicCaption'];
if(isset($params['thumbPicCaption'])) $thumbPicCaption = $params['thumbPicCaption'];
if(isset($params['bigPicAltTag'])) $bigPicAltTag = $params['bigPicAltTag'];
if(isset($params['bigPicTitleTag'])) $bigPicTitleTag = $params['bigPicTitleTag'];
if(isset($params['thumbPicAltTag'])) $thumbPicAltTag = $params['thumbPicAltTag'];
if(isset($params['thumbPicTitleTag'])) $thumbPicTitleTag = $params['thumbPicTitleTag'];
if(isset($params['sortBy'])) $sortBy = $params['sortBy'];
if(isset($params['sortByOrder'])) $sortByOrder = $params['sortByOrder'];



//Read Image Folder
$selfA = explode('/', $_SERVER["PHP_SELF"]);
$self = $selfA[sizeOf($selfA)-1] . '?page=' . $_GET['page'];
$picDir = dir($picFolder);
$liste = array();
while($check = $picDir->read()) {
//if(strpos($check,'.jpg') || strpos($check,'.gif') || strpos($check,'.png')) {
if(strpos($check,'.jpg') || strpos($check,'.JPG') || strpos($check,'.jpeg')
|| strpos($check,'.JPEG') || strpos($check,'.gif') || strpos($check,'.GIF')
|| strpos($check,'.png') || strpos($check,'.PNG'))  {

$cThumb = explode("_", $check);
if($cThumb[0] != "thumb" && $cThumb[0] != "editor") {
$liste[] = $check;
}
}
}

//Sort by date
if($sortBy == "date") {
$tmp = array();
foreach($liste as $k => $v) {
$tmp['file'][$k] = $v;
$tmp['date'][$k] = filemtime($picFolder . $v);
}

//Sort by Order
($sortByOrder == 'desc') ? array_multisort($tmp['date'], SORT_DESC, $tmp['file'], SORT_DESC) : array_multisort($tmp['date'], SORT_ASC, $tmp['file'], SORT_ASC);
$liste = $tmp['file'];
} else ($sortByOrder == 'desc') ? rsort($liste) : sort($liste);

//Output
$count = 1;
$output = '';

 if($type=="popup") {
   $output .= generate_javascript();
 }

//thumbcount
$deci = array();
for($i=1; $i<=sizeof($liste); $i++) {
$deci[$i] = $i;
while(strlen($deci[$i]) < strlen(sizeof($liste))) $deci[$i] = '0' . $deci[$i];
}

//Click through gallery image = after you have clicked on an image
if($type == 'click' && isset($_GET['img'])) { 
$bigPic = $picFolder . $liste[$_GET['img']];
$imgSize = getImageSize($bigPic);
$img = (!isset($_GET['img'])) ? 1 : $_GET['img'];
$next = ($_GET['img'] == (sizeOf($liste)-1)) ? $self : $self . "&amp;img=" . ($_GET['img']+1);
$output .= '<div id="'.$divID.'"><div class="bigPic">'. "\n";
$path_parts = pathinfo($bigPic);
$extension='.'.$path_parts['extension'];
$ImageFileName = basename($bigPic); 
$bigPicName = basename($bigPic, $extension); 

// Set big pic captions
switch($bigPicCaption)
 {
  case "name":        
        $output .= '<p class="bigPicCaption">'.$bigPicName.'</p>'."\n";
       break;   
  case "number":
         $output .= '<p class="bigPicCaption">'.($_GET['img']+1).'</p>'."\n";
       break;
  case "file":
        $output .= '<p class="bigPicCaption">'.$ImageFileName.'</p>'."\n";
       break; 
  case "none":
  	   break;
   default:
       $output .= '<p class="bigPicCaption">'.$bigPicName.'</p>'."\n";
       break;   
}

//Set Image
$output .= '<img src="' . $bigPic .'"';

//title tags
switch($bigPicTitleTag)
 {
  case "name":        
       $output .=' title="'.$bigPicName.'"';
       break;   
  case "number":
       $output .=' title="'.($_GET['img']+1).'"';
       break;
  case "file":
       $output .=' title="'.$ImageFileName.'"';
       break;  
  case "none":
       break;  
  default:
       $output .=' title="'.$bigPicName.'"';
       break;   
 }

//alt tags - compulsory
switch($bigPicAltTag)
 {
  case "name":        
       $output .=' alt="'.$bigPicName.'"';
       break;   
  case "number":
       $output .=' alt="'.($_GET['img']+1).'"';
       break;
  case "file":
       $output .=' alt="'.$ImageFileName.'"';
       break;   
   default:
       $output .=' alt="'.$bigPicName.'"';
       break;   
 }

//Close tags
$output .='/> <br />' . "\n";
$output .= '<p class="bigPicNav">Image ' . ($_GET['img']+1) . ' of ' . sizeOf($liste) . '<br />' . "\n";
$output .= ($_GET['img'] == 0) ? "" : "<a href='" . $self . "&amp;img=" . ($_GET['img']-1) ."'> &lt; Previous</a> | ";
$output .= "<a href='" . $self . "'>Index</a>";
$output .= ($_GET['img'] == (sizeOf($liste)-1)) ? "" : " | <a href='" . $next . "'>Next &gt; </a>";
$output .= '</p></div></div>'."\n";

} else {
 
//Else we are on thumb generation & normal page
$output .= '<div id="'.$divID.'">'. "\n";
$i = 1;
foreach($liste as $key => $value) {
$bigPic = $picFolder . $value;
list($bigPicWidth, $bigPicHeight) = getImageSize($bigPic);
$thumbPic = $picFolder . 'thumb_' . $value;
$thumbSize = @getImageSize($thumbPic) or ($thumbSize[0] = 96) and ($thumbSize[1] = 96);
$output .= '<div class="thumb">';
if($type == "click") $output .= '<a href="' . $self . '&amp;img=' . $key . '">' . "\n";
if($type == "popup") $output .= '<a href="javascript:PopupPic(\'' . $bigPic . '\',\'' . ($key+1) . '\',\'' . $bigPicWidth . '\',\''. $bigPicHeight . '\')">' . "\n";
$path_parts = pathinfo($bigPic);
$extension='.'.$path_parts['extension'];
$ImageFileName = basename($bigPic); 
$bigPicName = basename($bigPic, $extension);

//Set Image
$output .= '<img src="' . $thumbPic .'"';

//title tags
switch($thumbPicTitleTag)
 {
  case "name":        
       $output .=' title="'.$bigPicName.'... click for a bigger image"';
       break;   
  case "number":
       $output .=' title="'.($key+1).'... click for a bigger image"';
       break;
  case "file":
       $output .=' title="'.$ImageFileName.'... click for a bigger image"';
       break;  
  case "none":
       break;  
  default:
       $output .=' title="Click for a bigger image..."';
       break;   
 }

//alt tags - compulsory
switch($thumbPicAltTag)
 {
  case "name":        
       $output .=' alt="'.$bigPicName.'"';
       break;   
  case "number":
       $output .=' alt="'.($key+1).'"';
       break;
  case "file":
       $output .=' alt="'.$ImageFileName.'"';
       break;   
   default:
       $output .=' alt="'.$bigPicName.'"';
       break;   
 }

//Close tags
$output .='/></a> <br />' . "\n";
// Set thumb captions
switch($thumbPicCaption)
 {
  case "name":        
        $output .= '<p class="thumbPicCaption">'.$bigPicName.'</p>'."\n";
       break;   
  case "number":
        $output .= '<p class="thumbPicCaption">'.($key+1).'</p>'."\n";
       break;
  case "file":
        $output .= '<p class="thumbPicCaption">'.$ImageFileName.'</p>'."\n";
       break; 
  case "none":
  	   break;
   default:
       $output .= '<p class="thumbPicCaption">'.$bigPicName.'</p>'."\n";
       break;   
 }

$output .= '</div>' . "\n";
}
$output .= '</div>' . "\n\n";
}
return $output;
}

function smarty_cms_help_function_ImageGallery() {
	?>
	<h3>What does this do?</h3>
	<p>Creates a gallery out of a folder of images (.gif, .jpg or .png). 
	You can click on a thumbnail image to view the bigger image. It can use 
	captions which are based on the image name, minus the file extension. It 
	follows web standards and uses CSS for formatting. There are classes 
	for various elements and for the surrounding 'div'. Check out the CSS below for
	more information.</p>

	<h3>How do I use it?</h3>
	<p>Just insert the tag into your template or page like: </p>
	<code>{ImageGallery picFolder="uploads/images/yourfolder/"}</code>
	<p>Where picFolder is the folder where your images are stored.</p>
	
    <h3>What parameters does it take?</h3>
    <p>It can take quite a few parameters, but the example above is probably 
good for most people :) </p>
        <ol>
		<li><strong>picFolder e.g. picFolder="uploads/images/yourfolder/"</strong><br/>
		Is the path to the gallery (yourfolder) ending in'/'. So you can have 
		lots of directories and lots of galleries.</li>

		<li><strong>type e.g. type="click" or type="popup"</strong><br/>
		For the "popup" function to work you need to include the popup javascript into
		the head of your template e.g. "&lt;head&gt;&lt;/head&gt;". The javascript is at
		the bottom of this page! <em>The default is 'click'.</em></li>

		<li><strong>divID e.g. divID ="imagegallery"</strong><br/>
		Sets the wrapping 'div id' around your gallery so that you can have 
		different CSS for each gallery. <em>The default is 'imagegallery'.</em></li>

		<li><strong>sortBy e.g. sortBy = "name" or sortBy = "date"</strong><br/>
		Sort images by 'name' OR 'date'. <em>No default.</em></li>

		<li><strong>sortByOrder e.g. sortByOrder = "asc" or sortByOrder = "desc"</strong><br/> 
		 <em>No default.</em>.</li>

		<li>This sets caption above the big (clicked on) image<br/>
		<strong>bigPicCaption = "name" </strong>(filename excluding extension)<em> or </em><br/>
		<strong>bigPicCaption = "file" </strong>(filename including extension)<em> or </em><br/>
		<strong>bigPicCaption = "number" </strong>(a number sequence)<em> or </em><br/>
		<strong>bigPicCaption = "none" </strong>(No caption)<br/>
		<em>The Default is "name". </em></li>

		<li>This sets the caption below the small thumbnail<br/>
		<strong>thumbPicCaption = "name"</strong> (filename excluding extension)<em> or </em><br/>
		<strong>thumbPicCaption = "file"</strong> (filename including extension)<em> or </em><br/>
		<strong>thumbPicCaption = "number" </strong>(a number sequence)<em> or </em><br/>
		<strong>thumbPicCaption = "none" </strong>(No caption)<br/>
		<em>The Default is "name".</em></li>

		<li>Sets the 'alt' tag for the big image - compulsory.<br/>
		<strong>bigPicAltTag = "name" </strong>(filename excluding extension)<em> or </em><br/>
		<strong>bigPicAltTag = "file" </strong>(filename including extension)<em> or </em><br/>
		<strong>bigPicAltTag = "number" </strong>(a number sequence)<br/>
		<em>The Default is "name".</em></li>

		<li> Sets the 'title' tag for the big image. <br/>
		<strong>bigPicTitleTag = "name" </strong>(filename excluding extension)<em> or </em><br/>
		<strong>bigPicTitleTag = "file" </strong>(filename including extension)<em> or </em><br/>
		<strong>bigPicTitleTag = "number" </strong>(a number sequence)<em> or </em><br/>
		<strong>bigPicTitleTag = "none" </strong>(No title)<br/>
		<em>The Default is "name".</em></li>

		<li><strong>thumbPicAltTag</strong><br/>
		<em>Is the same as bigPicAltTag, but for the small thumbnail images.<em></li>

		<li><strong>thumbPicTitleTag *</strong><br/>
		<em>Is the same as bigPicTitleTag but for the small thumbnail images.<br/>
		<strong>*Except that after the options you have '... click for a bigger image' 
		or if you do not set this option then you get the default of 
		'Click for a bigger image...'</em></strong></li>
        </ol>
  <p>A More Complex Example</p>
        <p>'div id' is 'cdcovers', no Caption on big images, thumbs have default caption. 
        'alt' tags for the big image are set to the name of the image file without the extension 
        and the big image 'title' tag is set to the same but with an extension. 
        The thumbs have the default 'alt' and 'title' tags. The default being the name 
        of the image file without the extension for 'alt' and 'Click for a bigger image...' for the 'title',
		would be:</p>
		<code>{ImageGallery picFolder="uploads/images/cdcovers/" divID="cdcovers" bigPicCaption="none"  bigPicAltTag="name" bigPicTitleTag="file"}</code>
        <br/>
		<p>It's got lots of options but I wanted to keep it very flexible and you don't have to set them, the defaults are sensible.</p>
		
  <br/>
	<h4>Example CSS</h4>
<pre>
	/* Image Gallery - Small Thumbnail Images */
	.thumb {
		margin: 1em 1em 1.6em 0; /* Space between images */
		padding: 0;
		float: left;
		text-decoration: none;
		line-height: normal;
		text-align: left;
	}

	.thumb img, .thumb a img, .thumb a:link img{ /* Set link formatting*/
		width: 100px; /* Image width*/
		height: 100px; /* Image height*/
		display: inline;
		padding: 12px; /* Image padding to form photo frame */
		/* You can set the above to 0px = no frame - but no hover indication! Adjust other widths ot text!*/
		margin: 0;
		background-color: white; /*Background of photo */ 
		border-top: 1px solid #eee; /* Borders of photo frame */
		border-right: 2px solid #ccc;
		border-bottom: 2px solid #ccc;
		border-left: 1px solid #eee;
		text-decoration: none;
	}

	.thumb a:visited img {
		background-color: #eee; /*Background of photo on hover - sort of a light grey */
	}

	.thumb a:hover img {
		background-color: #dae6e4; /*Background of photo on hover - sort of light blue/green */
	}

	.thumbPicCaption {
		text-align: center;
		font-size: smaller;
		margin: 0 1px 0 0;
		padding: 0;
		width: 124px; /* Image width plus 2 x padding for image (photo frame) - to center text on image */
		/* display: none;  if you do not want to display this text */
	}

	/* Image Gallery - Big Images */
	.bigPic {
		margin: 10px 0 5px 0;
		padding: 0;
		line-height: normal;
	}

	.bigPicCaption { /*Big Image Name - above image above .bigpicImageFileName (Without extension) */
		text-align: center;
		font-weight: bold;
		font-variant: small-caps;
		font-weight: bold;
		margin: 0 1px 0 0;
		padding: 0;
		width: 386px; /* Image width plus 2 x padding for image (photo frame) - to center text on image */
		/* display: none;  if you do not want to display this text */
	}

	.bigPic img{ /* Big Image settings */
		width: 350px; /* Width of Big Image */
			height: auto;
		display: inline;
		padding: 18px; /* Image padding to form photo frame. */
		/* You can set the above to 0px = no frame - but no hover indication! Adjust other widths ot text!*/
		margin: 0;
		background-color: white; /* Background of photo */ 
		border-top: 1px solid #eee; /* Borders of photo frame */
		border-right: 2px solid #ccc; 
		border-bottom: 2px solid #ccc;
		border-left: 1px solid #eee;
		text-decoration: none; 
		text-align: left;
	}

	.bigPicNav { /* Big Image information: 'Image 1 of 4' and gallery navigation */
		margin: 0;
		width: 386px; /* Image width plus 2 x padding for image (photo frame) - to center text on image */
		padding: 0;
		color: #000;
		font-size: smaller;
		line-height: normal;
		text-align: center;
		/* display: none;  if you do not want to display this text. Why? You Lose Navigation! */
	}

</pre>
<br/>

	<h4>The popup javascript is now included in plugin code and will be generated automatically if you still have javascript in your template please remove it.</h4>
<?php
/*
<pre>
{literal}
&lt;script type=&quot;text/javascript&quot;&gt;
&lt;!--
function PopupPic(bigPic,title,w,h) {
	var winl = (screen.width - w) / 2;
	var wint = (screen.height - h) / 2;
	var smarty_hack = 'head';
	newWindow = window.open('',title,'height='+h+',width='+w+',top='+wint+',left='+winl+',resizable=0,scrollbars=0');
	newWindow.document.write('&lt;!DOCTYPE html PUBLIC &quot;-//W3C//DTD XHTML 1.0 Strict//EN&quot; &quot;http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd&quot;&gt;');
	newWindow.document.write('&lt;html&gt;&lt;head&gt;&lt;title&gt;'+title+'&lt;/title&gt;');
	newWindow.document.write('&lt;meta http-equiv=&quot;content-type&quot; content=&quot;text/html; charset=UTF-8&quot; /&gt;');
	newWindow.document.write('&lt;style type=&quot;text/css&quot;&gt;&lt;!-- html, body {margin: 0px; background-color: #000;} --&gt;&lt;/style&gt;');
	newWindow.document.write('&lt;/'+smarty_hack+'&gt;&lt;body onclick=&quot;self.close()&quot;&gt;');
	newWindow.document.write('&lt;p&gt;&lt;img src=&quot;'+bigPic+'&quot; alt=&quot;'+title+'&quot; /&gt;&lt;/p&gt;');
	newWindow.document.write('&lt;/body&gt;&lt;/html&gt;');
	newWindow.document.close();
	newWindow.focus();
}
--&gt;
&lt;/script&gt;
{/literal}
</pre>
<br />
*/
?>
	<?php
}

function smarty_cms_about_function_ImageGallery() {
	?>
	<p>Author: <strong>Russ Baldwin</strong> (but see below.)</p>
	<p>Website: <strong><a href="http://www.shoesforindustry.net"> www.shoesforindustry.net</a></strong></p>
	<p>Version: <strong>0.3 Beta</strong></p>
	<p>Date: <strong>2006-02-22</strong></p>
	<p>
	Change History:<br/>
	<strong>2006-02-22 - Version 0.3 Beta</strong><br/>Fixed a bug with searching for images based on
	lower case extensions. It now searches for both upper and lower case extensions.<br />Re-added the popup function. <em>(Redguy)</em>.<br/>
	<strong>2006-02-21 - Version 0.2 Beta</strong><br/>Second release fixed some typos<br/>
	<strong>2006-02-20 - Version 0.1 Beta</strong><br/>First release as a Plugin (Tag)</p>
	<p>***************************************</p>
	<p>This plugin (tag) is based on my original 'user-tag' which was itself 
	based upon a 'user tag' by RedGuy. This in turn was based on some code by 
	Michael Kloepzig <a href='http://save-the-gummybears.org/stg/' target='_blank'>Save-the-gummybears.org</a>. Phew...</p>
	<p>All credit where credit is due :)</p>

	<?php
}

function generate_javascript() {
global $gCms;
$config=$gCms->config; 

static $count;
if ($count==0) {

$out = "
<script type=\"text/javascript\">
<!--
function PopupPic(bigPic,title,w,h) {
var winl = (screen.width - w) / 2;
var wint = (screen.height - h) / 2;
var smarty_hack = 'head';
newWindow = window.open('',title,'height='+h+',width='+w+',top='+wint+',left='+winl+',resizable=0,scrollbars=0');
newWindow.document.write('<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">');
newWindow.document.write('<html><head><title>'+title+'</title><base href=\"".$config[root_url]."/\" />');
newWindow.document.write('<meta http-equiv=\"content-type\" content=\"text/html; charset=UTF-8\" />');
newWindow.document.write('<style type=\"text/css\"><!-- html, body {margin: 0px; background-color: #000;} --></style>');
newWindow.document.write('</'+smarty_hack+'><body onclick=\"self.close()\">');
newWindow.document.write('<p><img src=\"'+bigPic+'\" alt=\"'+title+'\" /></p>');
newWindow.document.write('</body></html>');
newWindow.document.close();
newWindow.focus();
}
-->
</script>
";
}

$count++;

return $out;
}

?>
