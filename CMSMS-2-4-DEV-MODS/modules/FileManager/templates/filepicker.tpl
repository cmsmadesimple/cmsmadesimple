<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>{$filepickertitle}</title>
<link rel="stylesheet" type="text/css" href="{$rooturl}/FileManager/filepicker.css" />
{literal}
<script language="javascript" type="text/javascript">

function ChooseFile(filename) {
{/literal}
  var URL = filename;

  opener.document.{$formname}.{$fieldname}.value=filename;
   // close popup window
  window.close();
{literal}
}
{/literal}
</script>
</head>
<body>
<div id="full-fp">

<div class="header">

{if isset($messagefail) && $messagefail!=""}
<fieldset class="fp-error">
<legend>{$errortext}</legend>
{$messagefail}
</fieldset>
{/if}

{if isset($messagesuccess) && $messagesuccess!=""}
<fieldset class="fp-sucess">
<legend>{$successtext}</legend>
{$messagesuccess}
</fieldset>
{/if}

<fieldset>
<legend>{$youareintext}</legend>
<h2><img src="{$rooturl}/modules/FileManager/icons/themes/{$admintheme}/extensions/dir.png" title="{$subdir}" alt="{$subdir}" />/{$subdir}</h2>
</fieldset>

{if isset($formstart) && $formstart!=''}
<fieldset>
<legend>{$fileoperations}</legend>
{$formstart}

<table width="100%">
<tr>
<td align="left">
{$fileuploadtext}: {$fileuploadinput}{$fileuploadsubmit}
</td>
<td align="right">
{$newdirtext}: {$newdirinput}{$newdirsubmit}
</td>
</tr>
</table>

{$formend}
</fieldset>
{/if}

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
    <td width="1%" align="center"><img src="{$rooturl}/modules/FileManager/icons/themes/{$admintheme}/extensions/dir.png" title="Dir" alt="Dir" /></td> <!-- diricon?? -->
    <td>{$file->namelink} </td>
    <td width="1%">&nbsp;</td>
    <td width="1%">&nbsp;</td>
  {else}
    <td align="right">
    {if $filepickerstyle=="filename"}
      {if $file->isimage=="1"}
      <img src="{$rooturl}/modules/FileManager/icons/themes/{$admintheme}/extensions/png.png" title="{$file->name}" alt="{$file->name}" />
      {else}
      <img src="{$rooturl}/modules/FileManager/icons/themes/{$admintheme}/extensions/{$file->fileicon}" title="{$file->name}" alt="{$file->name}" />
      {/if}
    {else}
      <div class="thumbnail">
      <a title="{$file->name}" href='#' onclick='ChooseFile("{$file->fullurl}")'>
      {if isset($file->thumbnail) && $file->thumbnail!=''}

        {$file->thumbnail}
      {else}

        {if $file->isimage=="1"}
        <img src="{$rooturl}/modules/FileManager/icons/themes/{$admintheme}/extensions/png.png" title="{$file->name}" alt="{$file->name}" />
        {else}
        <img src="{$rooturl}/modules/FileManager/icons/themes/{$admintheme}/extensions/{$file->fileicon}" title="{$file->name}" alt="{$file->name}" />
        {/if}
      {/if}
      </a>
      </div>
    {/if}
    </td>
    <td align="left">
       <a title="{$file->name}" href='#' onclick='ChooseFile("{$file->fullurl}")'>
     {$file->name}
       </a>
    </td>
    <td width="1%" align="right">{$file->dimensions}</td>
    <td width="1%" align="right">{$file->size}</td>
  {/if}
  </tr>
  {/foreach}
<tr><td colspan="4">&nbsp;</td></tr>
</table>
</div>
{*
<div class="rightbox">
Toolbox, what should go here?

</div>
*}
</div><!--end full-fp-->
</body>
</html>
