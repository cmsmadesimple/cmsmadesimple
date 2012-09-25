{if !isset($noform)}
{literal}
<script type="text/javascript">
var refresh_url = '{/literal}{$refresh_url}{literal}'+'&showtemplate=false';
refresh_url = refresh_url.replace(/amp;/g,'');
// <![CDATA[
  function enable_action_buttons() {
    var files = $("#filesarea input[type='checkbox'].fileselect").filter(':checked').length;
    var dirs = $("#filesarea input[type='checkbox'].dir").filter(':checked:').length;
    var arch = $("#filesarea input[type='checkbox'].archive").filter(':checked:').length;
    var imgs = $("#filesarea input[type='checkbox'].image").filter(':checked:').length;
    $('.filebtn').attr('disabled','disabled');
    if( jQuery.ui ) $('.filebtn').button( "option", "disabled", true ).addClass('disabled');
    if( files == 0 &&  dirs == 0 ) {
      // nothing selected, enable anything with select_none
      $('#btn_newdir').removeAttr('disabled').removeClass('disabled');
      if( jQuery.ui ) $('#btn_newdir').button( "option", "disabled", false );
    }
    else if( files == 1 ) {
      // 1 selected, enable anything with select_one
      $('#btn_rename').removeAttr('disabled').removeClass('disabled');
      if( jQuery.ui ) $('#btn_rename').button( "option", "disabled", false );

      $('#btn_move').removeAttr('disabled').removeClass('disabled');
      if( jQuery.ui ) $('#btn_move').button( "option", "disabled", false );
   
      $('#btn_delete').removeAttr('disabled').removeClass('disabled');
      if( jQuery.ui ) $('#btn_delete').button( "option", "disabled", false );
   
      if( dirs == 0 ) {
        // one selected, it's not a directory
        $('#btn_copy').removeAttr('disabled').removeClass('disabled');
        if( jQuery.ui ) $('#btn_copy').button( "option", "disabled", false );
      }
      if( arch == 1 ) {
	// one selected, it's an archive.
        $('#btn_unpack').removeAttr('disabled').removeClass('disabled');
        if( jQuery.ui ) $('#btn_unpack').button( "option", "disabled", false );
      }
      if( imgs == 1 ) {
        $('#btn_thumb').removeAttr('disabled').removeClass('disabled');
        if( jQuery.ui ) $('#btn_thumb').button( "option", "disabled", false );
      }
    }
    else if( files > 1 && dirs == 0 ) {
      // multiple files selected
      $('#btn_delete').removeAttr('disabled').removeClass('disabled');
      if( jQuery.ui ) $('#btn_delete').button( "option", "disabled", false );

      $('#btn_copy').removeAttr('disabled').removeClass('disabled');
      if( jQuery.ui ) $('#btn_copy').button( "option", "disabled", false );

      $('#btn_move').removeAttr('disabled').removeClass('disabled');
      if( jQuery.ui ) $('#btn_move').button( "option", "disabled", false );
    }
    else if( files > 1 && dirs > 0 ) {
      // multiple selected, at least one dir.
      $('#btn_delete').removeAttr('disabled').removeClass('disabled');
      if( jQuery.ui ) $('#btn_delete').button( "option", "disabled", false );

      $('#btn_move').removeAttr('disabled').removeClass('disabled');
      if( jQuery.ui ) $('#btn_move').button( "option", "disabled", false );
    }
  }

  $(document).ready(function(){
    enable_action_buttons(); 
    $('#refresh').unbind('click');
    $('#refresh').live('click',function(){
      // ajaxy reload for the files area.
      $('#filesarea').load(refresh_url);
      return false;
    });

    $(this).live('dropzone_chdir',function(e,data){
      // if change dir via the dropzone, make sure filemanager refreshes.
      location.reload();
    });

    $("#filesarea input[type='checkbox'].fileselect").live('change',function(e){
      // find the parent row
      e.stopPropagation();
      var t = $(this).attr('checked');
      if( t ) {
        $(this).closest('tr').addClass('selected');
      }
      else {
        $(this).closest('tr').removeClass('selected');
      }
      enable_action_buttons();
    });

    $('#tagall').live('change',function(event){
      if( $(this).attr('checked') == 'checked' ) {
        $('#filesarea input:checkbox.fileselect').attr('checked','checked').trigger('change');
      }
      else {
        $('#filesarea input:checkbox.fileselect').removeAttr('checked').trigger('change');
      }
    });

    $('td.clickable').live('click',function(){
      var t = $(this).parent().find(':checkbox:').attr('checked');
      if( t != 'checked' ) {
        $(this).parent().find(':checkbox:').attr('checked','checked').trigger('change');
      }
      else {
        $(this).parent().find(':checkbox:').removeAttr('checked').trigger('change');
      }
    });
  });
// ]]>
</script>{/literal}

<h3>{$currentpath} {$path}</h3>

<div>
  {$formstart}

<div>
  <fieldset>
    <input type="submit" id="btn_newdir" name="{$actionid}fileactionnewdir" value="{$mod->Lang('newdir')}" class="filebtn"/>
    <input type="submit" id="btn_rename" name="{$actionid}fileactionrename" value="{$mod->Lang('rename')}" class="filebtn"/>
    <input type="submit" id="btn_delete" name="{$actionid}fileactiondelete" value="{$mod->Lang('delete')}" class="filebtn"/> 
    <input type="submit" id="btn_move" name="{$actionid}fileactionmove" value="{$mod->Lang('move')}" class="filebtn"/> 
    <input type="submit" id="btn_copy" name="{$actionid}fileactioncopy" value="{$mod->Lang('copy')}" class="filebtn"/> 
    <input type="submit" id="btn_unpack" name="{$actionid}fileactionunpack" value="{$mod->Lang('unpack')}" class="filebtn" onclick="return confirm('{$confirm_unpack}');"/>
    <input type="submit" id="btn_thumb" name="{$actionid}fileactionthumb" value="{$mod->Lang('thumbnail')}" class="filebtn"/>
  </fieldset>

</div>
{$hiddenpath}
{/if}

  <div id="filesarea">  
  <table width="100%" class="pagetable" cellspacing="0">
  <thead>
  <tr>
    <th class="pageicon">&nbsp;</th>
    <th>{$filenametext}</th>

    <th class="pageicon">{$fileinfotext}</th>
    <th class="pageicon">{$fileownertext}</th>
    <th class="pageicon">{$filepermstext}</th>
    <th class="pageicon" style="text-align:right;">{$filesizetext}</th>
    <th class="pageicon">&nbsp;</th>
    <th class="pageicon">{$filedatetext}</th>
    {*<th class="pageicon">{$actionstext}</th>*}
    <th class="pageicon">
     <input type="checkbox" name="tagall" value="tagall" id="tagall"/>
    </th>
  </tr>
  </thead>
  <tbody>
  {foreach from=$files item=file}
	{cycle values="row1,row2" assign=rowclass}
  <tr class="{$rowclass}">    
    <td valign="middle">{if isset($file->thumbnail) && $file->thumbnail!=''}{$file->thumbnail}{else}{$file->iconlink}{/if}</td>
    <td class="clickable" valign="middle">{$file->txtlink}</td>

    <td class="clickable" style="padding-right:8px;" valign="middle">{$file->fileinfo}</td>
    <td class="clickable" style="padding-right:8px;" valign="middle">{if isset($file->fileowner)}{$file->fileowner}{else}&nbsp;{/if}</td>
    <td class="clickable" style="padding-right:8px;" valign="middle">{$file->filepermissions}</td>
    <td class="clickable" style="padding-right:2px;text-align:right;" valign="middle">{$file->filesize}</td>
    <td class="clickable" style="padding-right:8px;" valign="middle">{if isset($file->filesizeunit)}{$file->filesizeunit}{else}&nbsp;{/if}</td>
    <td class="clickable" style="padding-right:8px;" valign="middle">{$file->filedate|cms_date_format|replace:" ":"&nbsp;"|replace:"-":"&minus;"}</td>
    <td>
      {if !isset($file->noCheckbox)}
      <input type="checkbox" name="{$actionid}selall[]" value="{$file->urlname}" class="fileselect {$file->type}" {if isset($file->checked)}checked="checked"{/if}/>
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

{if !isset($noform)}
  {*{$actiondropdown}{$targetdir}{$okinput}*}
  {$formend}
</div>
{/if}  
