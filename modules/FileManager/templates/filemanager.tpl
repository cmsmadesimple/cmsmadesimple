{if !isset($noform)}
<script type="text/javascript">
var refresh_url = '{$refresh_url}'+'&showtemplate=false';
refresh_url = refresh_url.replace(/amp;/g,'');
// <![CDATA[
function initButton(buttonid) {

    $(buttonid).attr('disabled', false);
    if (jQuery.ui) {
        $(buttonid).button('option', 'disabled', false);
    }
}

function enable_action_buttons() {

    var files = $("#filesarea input[type='checkbox'].fileselect").filter(':checked').length,
        dirs = $("#filesarea input[type='checkbox'].dir").filter(':checked').length,
        arch = $("#filesarea input[type='checkbox'].archive").filter(':checked').length,
        text = $("#filesarea input[type='checkbox'].text").filter(':checked').length,
        imgs = $("#filesarea input[type='checkbox'].image").filter(':checked').length;


    $('.filebtn').attr('disabled', true);
    if (jQuery.ui) {
        $('.filebtn').button();
        $('.filebtn').button('option', 'disabled', true);
    }

    if (files == 0 && dirs == 0) {
        // nothing selected, enable anything with select_none        
        initButton('#btn_newdir');

    } else if (files == 1) {
        // 1 selected, enable anything with select_one
        initButton('#btn_rename');
        initButton('#btn_move');
        initButton('#btn_delete');

        if (dirs == 0) {
            // one selected, it's not a directory
            initButton('#btn_copy');
        }
        if (arch == 1) {
            // one selected, it's an archive.
            initButton('#btn_unpack');
        }
        if (imgs == 1) {
            initButton('#btn_thumb');
            initButton('#btn_resizecrop');
            initButton('#btn_rotate');
        }
        if (text == 1) {
            initButton('#btn_view');
        }
    } else if (files > 1 && dirs == 0) {
        // multiple files selected
        initButton('#btn_delete');
        initButton('#btn_copy');
        initButton('#btn_move');
    } else if (files > 1 && dirs > 0) {
        // multiple selected, at least one dir.
        initButton('#btn_delete');
        initButton('#btn_move');
    }
}

$(document).ready(function () {

    enable_action_buttons();

    $('#refresh').unbind('click');
    $('#refresh').bind('click', function () {
        // ajaxy reload for the files area.
        $('#filesarea').load(refresh_url);
        return false;
    });

    $(document).on('dropzone_chdir', $(this), function (e, data) {
        // if change dir via the dropzone, make sure filemanager refreshes.
        location.reload();
    });

    $(document).on('change', '#filesarea input[type="checkbox"].fileselect', function (e) {
        // find the parent row
        e.stopPropagation();
        var t = $(this).attr('checked');
        if (t) {
            $(this).closest('tr').addClass('selected');
        } else {
            $(this).closest('tr').removeClass('selected');
        }
        enable_action_buttons();
    });

    $(document).on('change', '#tagall', function (event) {
        if ($(this).is(':checked')) {
            $('#filesarea input:checkbox.fileselect').attr('checked', true).trigger('change');
        } else {
            $('#filesarea input:checkbox.fileselect').attr('checked', false).trigger('change');
        }
    });

    $(document).on('click', '#btn_view', function () {
        // find the selected item.
        var tmp = $("#filesarea input[type='checkbox']").filter(':visible').val();
        var url = '{$viewfile_url}&showtemplate=false&{$actionid}viewfile=' + tmp;
        url = url.replace(/amp;/g, '');
        $('#popup_contents').load(url);
        $('#popup').dialog();
        return false;
    });

    $(document).on('click', 'td.clickable', function () {
        var t = $(this).parent().find(':checkbox:').attr('checked');
        if (t != 'checked') {
            $(this).parent().find(':checkbox:').attr('checked', true).trigger('change');
        } else {
            $(this).parent().find(':checkbox:').attr('checked', false).trigger('change');
        }
    });
});
// ]]>
</script>

<h3>{$currentpath} {$path}</h3>

<div id="popup" style="display: none;">
	<div id="popup_contents" style="height: 400px; width: 500px; overflow: auto; font-family: monospace;"></div>
</div>

<div>
  {$formstart}

<div>
	<fieldset>
		<input type="submit" id="btn_newdir" name="{$actionid}fileactionnewdir" value="{$mod->Lang('newdir')}" class="filebtn"/>
		<input type="submit" id="btn_view"   value="{$mod->Lang('view')}" class="filebtn"/> 
		<input type="submit" id="btn_rename" name="{$actionid}fileactionrename" value="{$mod->Lang('rename')}" class="filebtn"/>
		<input type="submit" id="btn_delete" name="{$actionid}fileactiondelete" value="{$mod->Lang('delete')}" class="filebtn"/> 
		<input type="submit" id="btn_move" name="{$actionid}fileactionmove" value="{$mod->Lang('move')}" class="filebtn"/> 
		<input type="submit" id="btn_copy" name="{$actionid}fileactioncopy" value="{$mod->Lang('copy')}" class="filebtn"/> 
		<input type="submit" id="btn_unpack" name="{$actionid}fileactionunpack" value="{$mod->Lang('unpack')}" class="filebtn" onclick="return confirm('{$confirm_unpack}');"/>
		<input type="submit" id="btn_thumb" name="{$actionid}fileactionthumb" value="{$mod->Lang('thumbnail')}" class="filebtn"/>
		<input type="submit" id="btn_resizecrop" name="{$actionid}fileactionresizecrop" value="{$mod->Lang('resizecrop')}" class="filebtn"/>
		<input type="submit" id="btn_rotate" name="{$actionid}fileactionrotate" value="{$mod->Lang('rotate')}" class="filebtn"/>
	</fieldset>
</div>
{$hiddenpath}
{/if}

<div id="filesarea">  
	<table width="100%" class="pagetable scrollable" cellspacing="0">
		<thead>
			<tr>
				<th class="pageicon">&nbsp;</th>
				<th>{$filenametext}</th>
				<th class="pageicon">{$mod->Lang('mimetype')}</th>
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
				<td class="clickable" valign="middle">{$file->mime}</td>
				<td class="clickable" style="padding-right:8px;" valign="middle">{$file->fileinfo}</td>
				<td class="clickable" style="padding-right:8px;" valign="middle">{if isset($file->fileowner)}{$file->fileowner}{else}&nbsp;{/if}</td>
				<td class="clickable" style="padding-right:8px;" valign="middle">{$file->filepermissions}</td>
				<td class="clickable" style="padding-right:2px;text-align:right;" valign="middle">{$file->filesize}</td>
				<td class="clickable" style="padding-right:8px;" valign="middle">{if isset($file->filesizeunit)}{$file->filesizeunit}{else}&nbsp;{/if}</td>
				<td class="clickable" style="padding-right:8px;" valign="middle">{$file->filedate|cms_date_format|replace:" ":"&nbsp;"|replace:"-":"&minus;"}</td>
				<td>
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

{if !isset($noform)}
	{*{$actiondropdown}{$targetdir}{$okinput}*}
	{$formend}
</div>
{/if}
