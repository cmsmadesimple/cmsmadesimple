<script type="text/javascript">
    $(document).ready(function () {
        $('[name$=apply],[name$=submit]').hide();

        $('#edit_news').dirtyForm({
            onDirty : function () {
                $('[name$=apply],[name$=submit]').show('slow');
            }
        });
        $(document).on('cmsms_textchange', function (event) {
            // editor text change, set the form dirty.
            $('#edit_news').dirtyForm('option', 'dirty', true);
        });
        $(document).on('click', '[name$=submit],[name$=apply],[name$=cancel]', function () {
            $('#edit_news').dirtyForm('option', 'disabled', true);
        });
        $('#fld11').click(function () {
            $('#expiryinfo').toggle('slow');
        });
        $('[name$=cancel]').click(function () {
            $(this).closest('form').attr('novalidate', 'novalidate');
        });
    });
</script>
<h3>{if isset($articleid)}{$mod->Lang('editarticle')}{else}{$mod->Lang('addarticle')}{/if}</h3>
{strip}
<div id="editarticle_result"></div>

<div id="edit_news">
    {$startform}
    <div class="pageoptions">
        <p class="pageinput">
            {$hidden|default:''}
            <input type="submit" name="{$actionid}submit" value="{$mod->Lang('submit')}"/>
			<input type="submit" id="{$actionid}cancel" name="{$actionid}cancel" value="{$mod->Lang('cancel')}"/>
            {if isset($articleid)}
                <input type="submit" name="{$actionid}apply" value="{$mod->Lang('apply')}"/>
            {/if}
        </p>
    </div>

    {if isset($start_tab_headers)}
    {$start_tab_headers}
    {$tabheader_article}
    {$tabheader_preview}
    {$end_tab_headers}

    {$start_tab_content}
    {$start_tab_article}
    {/if}
    <div id="edit_article">
        {if $inputauthor}
        <div class="pageoverflow">
            <p class="pagetext">
                *{$authortext}:
            </p>
            <p class="pageinput">
                {$inputauthor}
            </p>
        </div>
        {/if}
        <div class="pageoverflow">
            <p class="pagetext">
                <label for="fld1">*{$titletext}:</label> {cms_help key='help_article_title' title=$titletext}
            </p>
            <p class="pageinput">
                <input type="text" id="fld1" name="{$actionid}title" value="{$title|escape:htmlall}" size="80" maxlength="255" required/>
            </p>
        </div>
        <div class="pageoverflow">
            <p class="pagetext">
                <label for="fld2">*{$categorytext}:</label> {cms_help key='help_article_category' title=$categorytext}
            </p>
            <p class="pageinput">
                <select name="{$actionid}category" id="fld2">
                    {html_options options=$categorylist selected=$category}
                </select>
            </p>
        </div>
        {if !isset($hide_summary_field) or $hide_summary_field == '0'}
        <div class="pageoverflow">
            <p class="pagetext">
                {$summarytext}: {cms_help key='help_article_summary' title=$summarytext}
            </p>
            <p class="pageinput">
                {$inputsummary}
            </p>
        </div>
        {/if}
        <div class="pageoverflow">
            <p class="pagetext">
                *{$contenttext}: {cms_help key='help_article_content' title=$contenttext}
            </p>
            <p class="pageinput">
                {$inputcontent}
            </p>
        </div>
        {if isset($statustext)}
        <div class="pageoverflow">
            <p class="pagetext">
                <label for="fld9">*{$statustext}:</label> {cms_help key='help_article_status' title=$statustext}
            </p>
            <p class="pageinput">
                <select id="fld9" name="{$actionid}status">
                    {html_options options=$statuses selected=$status}
                </select>
            </p>
        </div>
        {else}
        <input type="hidden" name="{$actionid}status" value="{$status}"/>
        {/if}

        <div class="pageoverflow">
            <p class="pagetext">
                <label for="fld7">{$urltext}:</label> {cms_help key='help_article_url' title=$urltext}
            </p>
            <p class="pageinput">
                <input type="text" id="fld7" name="{$actionid}news_url" value="{$news_url}" size="50" maxlength="255"/>
            </p>
        </div>
        <div class="pageoverflow">
            <p class="pagetext">
                <label for="fld5">{$extratext}:</label> {cms_help key='help_article_extra' title=$extratext}
            </p>
            <p class="pageinput">
                <input type="text" id="fld5" name="{$actionid}extra" value="{$extra|cms_escape}" size="50" maxlength="255"/>
            </p>
        </div>

        <div class="pageoverflow">
            <p class="pagetext">
                {$postdatetext}: {cms_help key='help_article_postdate' title=$postdatetext}
            </p>
            <p class="pageinput">
                {html_select_date prefix=$postdateprefix time=$postdate start_year='1980' end_year='+15'} {html_select_time prefix=$postdateprefix time=$postdate}
            </p>
        </div>
        <div class="pageoverflow">
            <p class="pagetext">
                <label for="searchable">{$mod->Lang('searchable')}:</label> {cms_help key='help_article_searchable' title=$mod->Lang('searchable')}
            </p>
            <p class="pageinput">
                <select name="{$actionid}searchable" id="searchable">
                    {cms_yesno selected=$searchable}
                </select>
                <br/>
                {$mod->Lang('info_searchable')}
            </p>
        </div>

        <div class="pageoverflow">
            <p class="pagetext">
                <label for="fld11">{$useexpirationtext}:</label> {cms_help key='help_article_useexpiry' title=$useexpirationtext}
            </p>
            <p class="pageinput">
                <input id="fld11" type="checkbox" name="{$actionid}useexp" {if $useexp == 1}checked="checked"{/if} class="pagecheckbox" />
            </p>
        </div>
        <div id="expiryinfo" {if $useexp != 1}style="display: none;"{/if}>
            <div class="pageoverflow">
                <p class="pagetext">
                    {$startdatetext}: {cms_help key='help_article_startdate' title=$startdatetext}
                </p>
                <p class="pageinput">
                    {html_select_date prefix=$startdateprefix time=$startdate start_year="-10" end_year="+15"} {html_select_time prefix=$startdateprefix time=$startdate}
                </p>
            </div>
            <div class="pageoverflow">
                <p class="pagetext">
                    {$enddatetext}: {cms_help key='help_article_enddate' title=$enddatetext}
                </p>
                <p class="pageinput">
                    {html_select_date prefix=$enddateprefix time=$enddate start_year="-10" end_year="+15"} {html_select_time prefix=$enddateprefix time=$enddate}
                </p>
            </div>
        </div>
        {if isset($custom_fields)}
        {foreach $custom_fields as $field}
        <div class="pageoverflow">
            <p class="pagetext">
                <label for="{$field->idattr}">{$field->prompt|cms_escape}:</label>
            </p>
            <p class="pageinput">
                {if $field->type == 'textbox'}
                    <input type="text" id="{$field->idattr}" name="{$field->nameattr}" value="{$field->value}" size="{$field->size}" maxlength="{$field->max_len}" />
                {elseif $field->type == 'checkbox'}
                    <input type="hidden" name="{$field->nameattr}" value="0" />
                    <input type="checkbox" id="{$field->idattr}" name="{$field->nameattr}" value="1"{if $field->value == 1} checked="checked"{/if} />
                {elseif $field->type == 'textarea'}
                    {cms_textarea id=$field->idattr name=$field->nameattr enablewysiwyg=1 value=$field->value maxlength=$field->max_len}
                {elseif $field->type == 'file'}
                    {if !empty($field->value)}{$field->value}<br />{/if} <input type="file" id="{$field->idattr}" name="{$field->nameattr}" />{if !empty($field->value)} {$delete_field_val} <input type="checkbox" name="{$field->delete}" value="delete" />{/if}
                {elseif $field->type == 'dropdown'}
                    <select id="{$field->idattr}" name="{$field->nameattr}">
                        <option value="-1">{$select_option}</option>
                        {html_options options=$field->options selected=$field->value}
                    </select>
		{elseif $field->type == 'linkedfile'}
		    {if $field->value}
		       {thumbnail_url file=$field->value assign=tmp}
		       {if $tmp}<img src="{$tmp}" alt="{$field->value}"/>{/if}
		    {/if}
                    {cms_filepicker name="{$field->nameattr}" value=$field->value}
                {/if}
            </p>
        </div>
        {/foreach}
        {/if}
    </div>
    {if isset($end_tab_article)}
        {$end_tab_article}
    {/if}

{/strip}

    {if isset($start_tab_preview)}
    {$start_tab_preview}
<script type="text/javascript">
    $(document).ready(function(){
        $(document).on('click', '[name=m1_apply]', function(e){

            e.preventDefault();

            if (typeof tinyMCE !== 'undefined') {
                tinyMCE.triggerSave();
            }

            var data = $('form').find('input:not([type=submit]), select, textarea').serializeArray(),
                url = $('form').attr('action');

            data.push({ 'name': 'm1_ajax', 'value': 1 });
            data.push({ 'name': 'm1_apply', 'value': 1 });
            data.push({ 'name': 'showtemplate', 'value': 'false' });

            $.post(url,data,function(resultdata,text){

                var resp = $(resultdata).find('Response').text(),
                    details = $(resultdata).find('Details').text(),
                    htmlShow = '';

                if (resp === 'Success' && details !== '' ) {
                    $('[name$=cancel]').button('option','label','{$mod->Lang('close')}');
                    $('[name$=cancel]').val('{$mod->Lang('close')}');
                    htmlShow = '<div class="pagemcontainer"><p class="pagemessage">'+details+'<\/p><\/div>';
                } else {
                    htmlShow = '<div class="pageerrorcontainer"><ul class="pageerror">';
                    htmlShow += details;
                    htmlShow += '<\/ul><\/div>';
                }

                $('#editarticle_result').html(htmlShow);
            },'xml');

        });

    function news_dopreview() {

        if (typeof tinyMCE != 'undefined') {
            tinyMCE.triggerSave();
        }

        var data = $('form').find('input:not([type=submit]), select, textarea').serializeArray(),
            url = $('form').attr('action');

        data.push({ 'name': 'm1_ajax', 'value': 1 });
        data.push({ 'name': 'm1_preview', 'value': 1 });
        data.push({ 'name': 'showtemplate', 'value': 'false' });
        data.push({ 'name': 'm1_previewpage', 'value': $("input[name='preview_returnid']").val() });
        data.push({ 'name': 'm1_detailtemplate', 'value': $('#preview_template').val() });

        $.post(url,data,function(resultdata,text){

            var resp = $(resultdata).find('Response').text(),
                details = $(resultdata).find('Details').text(),
                htmlShow = '';

            if (resp === 'Success' && details !== '' ) {

                // preview worked... now the details should contain the url
                details = details.replace(/amp;/g,'');
                $('#previewframe').attr('src',details);
            } else {
                if (details === '' ) {
                    details = 'An unknown error occurred';
                }

                // preview save did not work.
                htmlShow = '<div class="pageerrorcontainer"><ul class="pageerror">';
                htmlShow += details;
                htmlShow += '<\/ul><\/div>';

                $('#editarticle_result').html(htmlShow);
            }
        },'xml');
    }

    $('#preview').click(function(e){
        news_dopreview();
        e.preventDefault();
    });

    $(document).on('change', "input[name='preview_returnid'],#preview_template", function(e){
        news_dopreview();
        e.preventDefault();
    });
});
</script>

{strip}

    {* display a warning *}
    <div class="pagewarning">
        {$warning_preview}
    </div>
    <fieldset>
        <label for="preview_template">{$prompt_detail_template}:</label>&nbsp;
        <select id="preview_template" name="preview_template">
            {html_options options=$detail_templates selected=$cur_detail_template}
        </select>&nbsp;

        <label>{$prompt_detail_page}: {$preview_returnid}</label>&nbsp;
    </fieldset>
    <br/>
    <iframe id="previewframe" style="height: 800px; width: 100%; border: 1px solid black; overflow: auto;"></iframe>
    {$end_tab_preview}
    {$end_tab_content}
    {/if}

    <div class="pageoverflow">
        <p class="pageinput">
            <input type="submit" name="{$actionid}submit" value="{$mod->Lang('submit')}"/>&nbsp;
            <input type="submit" id="{$actionid}cancel" name="{$actionid}cancel" value="{$mod->Lang('cancel')}"/>
            {if isset($articleid)}
                &nbsp;<input type="submit" name="{$actionid}apply" value="{$mod->Lang('apply')}"/>
            {/if}
        </p>
    </div>
    {$endform}
</div>

{/strip}
