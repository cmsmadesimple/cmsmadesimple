<div id="filepicker">
  <table width="100%" class="pagetable scrollable">
    <thead>
      <tr>
        <th class="pageicon">&nbsp;</th>
        <th>{*$filenametext*}</th>
        <th class="pageicon">{*$mod->Lang('mimetype')*}</th>
        <th class="pageicon">{*$fileinfotext*}</th>
        <th class="pageicon" title="{*$mod->Lang('title_col_fileowner')*}">{*$fileownertext*}</th>
        <th class="pageicon" title="{*$mod->Lang('title_col_fileperms')*}">{*$filepermstext*}</th>
        <th class="pageicon" title="{*$mod->Lang('title_col_filesize')*}" style="text-align:right;">{*$filesizetext*}</th>
        <th class="pageicon"></th>
        <th class="pageicon" title="{*$mod->Lang('title_col_filedate')*}">{*$filedatetext*}</th>
        <th class="pageicon">
          <input type="checkbox" name="tagall" value="tagall" id="tagall" title="{*$mod->Lang('title_tagall')*}"/>
        </th>
      </tr>
    </thead>
    <tbody>
    <pre>{*$files|print_r:1*}</pre>
    {foreach from=$files item=file}
    <pre>{$file|print_r:1}</pre>
      {cycle values="row1,row2" assign=rowclass}
      <tr class="{$rowclass}">
        <td valign="middle">{if isset($file->thumbnail) && $file->thumbnail!=''}{$file->thumbnail}{else}{$file->iconlink}{/if}</td>
        <td class="clickable" valign="middle">{$file->txtlink}</td>
        <td class="clickable" valign="middle">{$file->mime}</td>
        <td class="clickable" style="padding-right:8px;white-space:pre;" valign="middle">{$file->fileinfo}</td>
        <td class="clickable" style="padding-right:8px;white-space:pre;" valign="middle">{if isset($file->fileowner)}{$file->fileowner}{else}&nbsp;{/if}</td>
        <td class="clickable" style="padding-right:8px;" valign="middle">{$file->filepermissions}</td>
        <td class="clickable" style="padding-right:8px;white-space:pre;text-align:right;" valign="middle">{$file->filesize}</td>
        <td class="clickable" style="padding-right:8px;" valign="middle">{if isset($file->filesizeunit)}{$file->filesizeunit}{else}&nbsp;{/if}</td>
        <td class="clickable" style="padding-right:8px;white-space:pre;" valign="middle">{$file->filedate|cms_date_format|replace:" ":"&nbsp;"|replace:"-":"&minus;"}</td>
        {if !isset($file->noCheckbox)}
          <label for="x_{$file->urlname}" style="display: none;">{$mod->Lang('toggle')}</label>
          <input type="checkbox" title="{$mod->Lang('toggle')}" id="x_{$file->urlname}" name="{$actionid}selall[]" value="{$file->urlname}" class="fileselect {implode(' ',$file->type)}" {if isset($file->checked)}checked="checked"{/if}/>
        {/if}
        </td>
      </tr>
    {/foreach}
    </tbody>
    <tfoot>
      <tr>
        <td>&nbsp;</td>
        <td colspan="7">{$countstext}</td>
      </tr>
    </tfoot>
  </table>
</div>