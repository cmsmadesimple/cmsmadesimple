<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>{$filepickertitle}</title>
<link rel="stylesheet" type="text/css" href="{$rooturl}/modules/MicroTiny/filepicker.css" />
<link rel="stylesheet" type="text/css" href="{$rooturl}/modules/MicroTiny/tinymce/themes/advanced/skins/default/dialog.css" />
<script language="javascript" type="text/javascript" src="{$rooturl}/modules/MicroTiny/tinymce/tiny_mce_popup.js"></script>
{literal}
<script language="javascript" type="text/javascript">

function SubmitElement(filename) {
  var URL = filename;
  var win = tinyMCEPopup.getWindowArg("window");

  // insert information now
  win.document.getElementById(tinyMCEPopup.getWindowArg("input")).value = URL;
{/literal}
  
  {if $isimage=='1'}
  // for image browsers: update image dimensions
  if (win.ImageDialog.getImageData) win.ImageDialog.getImageData();
  if (win.ImageDialog.showPreviewImage) win.ImageDialog.showPreviewImage(URL);
  {/if} 
  
{literal}
   // close popup window
  tinyMCEPopup.close();
}
{/literal}
</script>
</head>
<body>
<div id="full-fp">

<div class="header">

<fieldset>
<legend>{$youareintext}</legend>
<h2><img src="{$rooturl}/modules/MicroTiny/images/dir.gif" title="{$startpath}" alt="{$startpath}" />{$startpath}</h2>
</fieldset>

</div>
<div class="filelist">
<table width="100%">
<thead>
<tr>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td width="1%" align="right" style="white-space:nowrap;"><b>{$dimensionstext}</b></td>
<td width="1%" align="right" style="white-space:nowrap;"><b>{$sizetext}</b></td>
</tr>
</thead>
  {foreach from=$files item=file}
  <tr>
  {if $file->isdir=="1"}
    <td width="1%" align="center"><img src="{$rooturl}/modules/MicroTiny/images/dir.gif" title="Dir" alt="Dir" /></td>
    <td>{$file->namelink} </td>
    <td width="1%">&nbsp;</td>
    <td width="1%">&nbsp;</td>
    <td width="1%">&nbsp;</td>
  {else}
    <td align="right">
    {if $filepickerstyle=="filename"}
      {if $file->isimage=="1"}
      <img src="{$rooturl}/modules/MicroTiny/images/images.gif" title="{$file->name}" alt="{$file->name}" />
      {elseif $file->fileicon!=""}
        {$file->fileicon}{*<img src="{$file->fileicon}" title="{$file->name}" alt="{$file->name}" />*}
      {else}

      <img src="{$rooturl}/modules/MicroTiny/images/fileicon.gif" title="{$file->name}" alt="{$file->name}" />
      {/if}
    {else}
      <div class="thumbnail">
      <a title="{$file->name}" href='#' onclick='SubmitElement("{$file->fullurl}")'>
      {if isset($file->thumbnail) && $file->thumbnail!=''}
      
        {$file->thumbnail}
      {else}
      
        {if $file->isimage=="1"}        
        <img src="{$rooturl}/modules/MicroTiny/images/images.gif" title="{$file->name}" alt="{$file->name}" />
        {elseif $file->fileicon!=""}
        {$file->fileicon}{*<img src="{$file->fileicon}" title="{$file->name}" alt="{$file->name}" />*}
        {else}
        <img src="{$rooturl}/modules/MicroTiny/images/fileicon.gif" title="{$file->name}" alt="{$file->name}" />
        {/if}
      {/if}
      </a>
      </div>
    {/if}
    </td>
    <td align="left">
       <a  title="{$file->name}" href='#' onclick='SubmitElement("{$file->fullurl}")'>
     {$file->name}
       </a>
    </td>
    <td width="1%" align="right">{$file->dimensions}</td>
    <td width="1%" align="right">{$file->size}</td>
    <td width="1%" align="right">{$file->deletelink|default:''}</td>
  {/if}
  </tr>
  {/foreach}
<tr><td colspan="4">&nbsp;</td></tr>
</table>
</div>
</div><!--end full-fp-->
</body>
</html>
