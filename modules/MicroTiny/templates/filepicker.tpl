<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>{$mod->Lang('filepickertitle')}</title>
  <link rel="stylesheet" type="text/css" href="{$mod->GetModuleURLPath()}/filepicker.css?x={$smarty.now}" />
  {cms_jquery}
  <script language="javascript" type="text/javascript">
  $(document).ready(function(){
    $('a.file_action').click(function(e){
      e.preventDefault();
      var win = top.tinymce.activeEditor.windowManager.getParams().window;
      win.document.getElementById('{$field}').value = $(this).attr('href');
      top.tinymce.activeEditor.windowManager.close();
    });
  });
  </script>
</head>
<body>
<div id="full-fp">
  <fieldset>
    <legend>{$mod->Lang('youareintext')}:</legend>
    <h3><img src="{$mod->GetModuleURLPath()}/images/dir.gif" title="{$startpath}" alt="{$startpath}" />{$startpath}</h3>
  </fieldset>

  <div id="filelist">
    <table class="pagetable">
      <thead>
        <tr>
          <th>&nbsp;</th>
          <th>&nbsp;</th>
          <th>{$mod->Lang('dimensions')}</th>
          <th>{$mod->Lang('size')}</th>
        </tr>
      </thead>
      <tbody>
      {foreach $files as $file}
        {$type_alt='File'}
        {if $file.isdir}{$type_alt='Folder'}{/if}
        <tr>
         <td>
           {if $showthumbnails && isset($file.thumbnail) && $file.thumbnail != ''}
             {$file.thumbnail}{* already an img tag *}
           {elseif $file.isdir}
             <a class="dir_icon" href="{$file.chdir_url}">{$file.icon}</a>
           {else}
             {$file.icon}
           {/if}
         </td>
         <td>
           {if $file.isdir}
             <a class="dir_action" href="{$file.chdir_url}">{$file.name}</a>
           {else}
             <a class="file_action" href="{$file.fullurl}">{$file.name}</a>
           {/if}
         </td>
         <td>
           {$file.dimensions|default:''}
         </td>
         <td>
           {$file.size|default:''}
         </td>
       </tr>
     {/foreach}
     </table>
  </div>
</div><!--end full-fp-->
</body>
</html>
