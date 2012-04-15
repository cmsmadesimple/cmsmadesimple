<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
#This project's homepage is: http://www.cmsmadesimple.org
#
#This program is free software; you can redistribute it and/or modify
#it under the terms of the GNU General Public License as published by
#the Free Software Foundation; either version 2 of the License, or
#(at your option) any later version.
#
#This program is distributed in the hope that it will be useful,
#but WITHOUT ANY WARRANTY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
#You should have received a copy of the GNU General Public License
#along with this program; if not, write to the Free Software
#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

#Author: Sorin Sbarnea / INTERSOL SRL
function smarty_function_embed($params, &$template)
{
  $gCms = cmsms();

  $name = 'myframe';
  if(isset($params['name']) )
    {
      $name = trim($params['name']);
    }

  if(isset($params['header']))
    {
      $code = <<<IFRAMECODE
<script type="text/javascript">
  // <![CDATA[


/***********************************************
* IFrame SSI script II- S Dynamic Drive DHTML code library (http://www.dynamicdrive.com)
* Visit DynamicDrive.com for hundreds of original DHTML scripts
* This notice must stay intact for legal use
***********************************************/

//Input the IDs of the IFRAMES you wish to dynamically resize to match its content height:
//Separate each ID with a comma. Examples: ["myframe1", "myframe2"] or ["myframe"] or [] for none:

IFRAMECODE;
      // and add the name.
      $code .= 'var iframeids=["'.$name.'"]'."\n";
      $tmp = <<<IFRAMECODE

//Should script hide iframe from browsers that don't support this script (non IE5+/NS6+ browsers. Recommended):
var iframehide="yes"

var getFFVersion=navigator.userAgent.substring(navigator.userAgent.indexOf("Firefox")).split("/")[1]
var FFextraHeight=parseFloat(getFFVersion)>=0.1? 16 : 0 //extra height in px to add to iframe in FireFox 1.0+ browsers

function resizeCaller() {
var dyniframe=new Array()
for (i=0; i<iframeids.length; i++){
if (document.getElementById)
resizeIframe(iframeids[i])
//reveal iframe for lower end browsers? (see var above):
if ((document.all || document.getElementById) && iframehide=="no"){
var tempobj=document.all? document.all[iframeids[i]] : document.getElementById(iframeids[i])
tempobj.style.display="block"
}
}
}

function resizeIframe(frameid){
var currentfr=document.getElementById(frameid)
if (currentfr && !window.opera){
currentfr.style.display="block"
if (currentfr.contentDocument && currentfr.contentDocument.body.offsetHeight) //ns6 syntax
currentfr.height = currentfr.contentDocument.body.offsetHeight+FFextraHeight; 
else if (currentfr.Document && currentfr.Document.body.scrollHeight) //ie5+ syntax
currentfr.height = currentfr.Document.body.scrollHeight;
if (currentfr.addEventListener)
currentfr.addEventListener("load", readjustIframe, false)
else if (currentfr.attachEvent){
currentfr.detachEvent("onload", readjustIframe) // Bug fix line
currentfr.attachEvent("onload", readjustIframe)
}
}
}

function readjustIframe(loadevt) {
var crossevt=(window.event)? event : loadevt
var iframeroot=(crossevt.currentTarget)? crossevt.currentTarget : crossevt.srcElement
if (iframeroot)
resizeIframe(iframeroot.id);
}

function loadintoIframe(iframeid, url){
if (document.getElementById)
document.getElementById(iframeid).src=url
}

if (window.addEventListener)
window.addEventListener("load", resizeCaller, false)
else if (window.attachEvent)
window.attachEvent("onload", resizeCaller)
else
window.onload=resizeCaller
  // ]]>
</script>
IFRAMECODE;
 $code .= $tmp;
 return $code;
    }
  
  if (isset($params['url']))
    {
      $url = trim($params['url']);
    } 
  else if( isset($params['src']) )
    {
      $url = trim($params['src']);
    }
  else return "<pre>Invalid call for embed function.<pre>";
  
  //	$params['height']='200%';
  //	$params['width']='100%';
  //
  //	return '<iframe width="'.$params['width'].
  //		'" scrolling="yes" height="'.$params['height'].
  //		'" frameborder="0" marginwidth="0" marginheight="0" src="http://www2.romanianoffice.ro/forum/index.php"></iframe>';
  
  return   "<iframe id='{$name}' name='{$name}' src='$url' scrolling='no' marginwidth='0' marginheight='0' frameborder='0' style='overflow:visible; width:99%; display:none'></iframe>";
  
}

function smarty_cms_help_function_embed() {
  echo lang('help_function_embed');
}

function smarty_cms_about_function_embed() {
	?>
	<p>Author: Sorin Sbarnea&lt;sorin2000@intersol.ro&gt; (remove 2000)</p>
	<p>Version: 1.0</p>
	<p>
	Change History:<br/>
	None
	</p>
	<?php
}

?>