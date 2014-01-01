<script type="text/javascript">
$(document).ready(function(){
  $('[name$=apply],[name$=submit]').hide();

  $('#edit_news').dirtyForm({
    onDirty: function(){
      $('[name$=apply],[name$=submit]').show('slow');
    }
  });
  $(document).on('cmsms_textchange',function(event){
    // editor textchange, set the form dirty.
    $('#edit_news').dirtyForm('option','dirty',true);
  });
  $(document).on('click','[name$=submit],[name$=apply],[name$=cancel]',function(){
    $('#edit_news').dirtyForm('option','dirty',false);
  });

  $('#fld11').click(function(){
    $('#expiryinfo').toggle('slow');	
  });
});
</script>

{if isset($articleid)}
<h3>{$mod->Lang('editarticle')}</h3>
{else}
<h3>{$mod->Lang('addarticle')}</h3>
{/if}

<div id="editarticle_result"></div>

<div id="edit_news">
{$startform}
<div class="pageoptions">
  <p class="pageinput">{$hidden|default:''}{$submit}{$cancel}{if isset($apply)}{$apply}{/if}</p>
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
		<p class="pagetext">*{$authortext}:</p>
		<p class="pageinput">{$inputauthor}</p>
	</div>
{/if}
	<div class="pageoverflow">
		<p class="pagetext"><label for="fld1">*{$titletext}:</label></p>
		<p class="pageinput">
                  <input type="text" id="fld1" name="{$actionid}title" value="{$title}" size="80" maxlength="255"/>
                </p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext"><label for="fld2">*{$categorytext}:</label></p>
		<p class="pageinput">{$inputcategory|default:''}
                  <select name="{$actionid}category" id="fld2">
                  {html_options options=$categorylist selected=$category}
                  </select>
                </p>
	</div>
{if !isset($hide_summary_field) or $hide_summary_field == '0'}
	<div class="pageoverflow">
		<p class="pagetext">{$summarytext}:</p>
		<p class="pageinput">{$inputsummary}</p>
	</div>
{/if}
	<div class="pageoverflow">
		<p class="pagetext">*{$contenttext}:</p>
		<p class="pageinput">{$inputcontent}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext"><label for="fld5">{$extratext}:</label></p>
		<p class="pageinput">
                  <input type="text" id="fld5" name="{$actionid}extra" value="{$extra}" size="50" maxlength="255"/>
                </p>
		
	</div>
        <div class="pageoverflow">
                <p class="pagetext"><label for="fld7">{$urltext}:</label></p>
                <p class="pageinput">
                  <input type="text" id="fld7" name="{$actionid}news_url" value="{$news_url}" size="50" maxlength="255"/>
                </p>
        </div>

	<div class="pageoverflow">
		<p class="pagetext">{$postdatetext}:</p>
		<p class="pageinput">{html_select_date prefix=$postdateprefix time=$postdate start_year='1980' end_year='+15'} {html_select_time prefix=$postdateprefix time=$postdate}</p>
	</div>
{if isset($statustext)}
	<div class="pageoverflow">
		<p class="pagetext"><label for="fld9">*{$statustext}:</label></p>
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
		<p class="pagetext"><label for="searchable">{$mod->Lang('searchable')}:</label></p>
		<p class="pageinput">
			<select name="{$actionid}searchable" id="searchable">
			{cms_yesno selected=$searchable}
			</select>
			<br/>{$mod->Lang('info_searchable')}
		</p>
	</div>

	<div class="pageoverflow">
		<p class="pagetext"><label for="fld11">{$useexpirationtext}:</label></p>
		<p class="pageinput"><input id="fld11" type="checkbox" name="{$actionid}useexp" {if $useexp == 1}checked="checked"{/if} class="pagecheckbox"/></p>
	</div>
	<div id="expiryinfo" {if $useexp != 1}style="display: none;"{/if}>
	<div class="pageoverflow">
		<p class="pagetext">{$startdatetext}:</p>
		<p class="pageinput">{html_select_date prefix=$startdateprefix time=$startdate start_year="-10" end_year="+15"} {html_select_time prefix=$startdateprefix time=$startdate}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$enddatetext}:</p>
		<p class="pageinput">{html_select_date prefix=$enddateprefix time=$enddate start_year="-10" end_year="+15"} {html_select_time prefix=$enddateprefix time=$enddate}</p>
	</div>
	</div>
{if isset($custom_fields)}
{foreach $custom_fields as $field}
        <div class="pageoverflow">
           <p class="pagetext">{$field->prompt}</p>
           <p class="pageinput">{$field->field}</p>
        </div>
{/foreach}
{/if}
</div>
{if isset($end_tab_article)}{$end_tab_article}{/if}

{if isset($start_tab_preview)}
{$start_tab_preview}
<script type="text/javascript">
jQuery(document).ready(function(){
  jQuery(document).on('click', '[name=m1_apply]', function(){
    if( typeof tinyMCE != 'undefined') tinyMCE.triggerSave();
    var data = jQuery('form').find('input:not([type=submit]), select, textarea').serializeArray();
    data.push({ 'name': 'm1_ajax', 'value': 1 });
    data.push({ 'name': 'm1_apply', 'value': 1 });
    data.push({ 'name': 'showtemplate', 'value': 'false' });
    var url = jQuery('form').attr('action');
    jQuery.post(url,data,function(resultdata,text){
      var resp = jQuery(resultdata).find('Response').text();
      var details = jQuery(resultdata).find('Details').text();
      var htmlShow = '';
      if( resp == 'Success' && details != '' ) {
         $('[name$=cancel]').button('option','label','{$mod->Lang('close')}');
	 $('[name$=cancel]').val('{$mod->Lang('close')}');
	 htmlShow = '<div class="pagemcontainer"><p class="pagemessage">'+details+'<\/p><\/div>';
      }
      else {
	 htmlShow = '<div class="pageerrorcontainer"><ul class="pageerror">';
	 htmlShow += details;
	 htmlShow += '<\/ul><\/div>';
      }
      jQuery('#editarticle_result').html(htmlShow);
    },'xml');
    return false;
  });

  function news_dopreview()
  {
    if( typeof tinyMCE != 'undefined') tinyMCE.triggerSave();
    var data = jQuery('form').find('input:not([type=submit]), select, textarea').serializeArray();
    data.push({ 'name': 'm1_ajax', 'value': 1 });
    data.push({ 'name': 'm1_preview', 'value': 1 });
    data.push({ 'name': 'showtemplate', 'value': 'false' });
    data.push({ 'name': 'm1_previewpage', 'value': jQuery('#preview_returnid').val() });
    data.push({ 'name': 'm1_detailtemplate', 'value': jQuery('#preview_template').val() });
    var url = jQuery('form').attr('action');
    jQuery.post(url,data,function(resultdata,text){
      var resp = jQuery(resultdata).find('Response').text();
      var details = jQuery(resultdata).find('Details').text();
      var htmlShow = '';
      if( resp == 'Success' && details != '' ) {
	 // preview worked... now the details should contain the url
         details = details.replace(/amp;/g,'');
         jQuery('#previewframe').attr('src',details);
      }
      else {
	 if( details == '' ) details = 'An unknown error occurred';

	 // preview save did not work.
	 htmlShow = '<div class="pageerrorcontainer"><ul class="pageerror">';
	 htmlShow += details;
	 htmlShow += '<\/ul><\/div>';
         jQuery('#editarticle_result').html(htmlShow);
      }
    },'xml');
  }

  jQuery('#preview').click(function(){
    news_dopreview();
    return false;
  });

  jQuery('#preview_returnid,#preview_template').change(function(){
    news_dopreview();
    return false;
  });
});
</script>

{* display a warning *}
<div class="pagewarning">{$warning_preview}</div>
<fieldset>
  <label for="preview_template">{$prompt_detail_template}:</label>&nbsp;
  <select id="preview_template" name="preview_template">
  {html_options options=$detail_templates selected=$cur_detail_template}
  </select>&nbsp;

  <label for="preview_returnid">{$prompt_detail_page}:</label>&nbsp;
  {$preview_returnid}
</fieldset>
<br/>
<iframe id="previewframe" style="height: 800px; width: 100%; border: 1px solid black; overflow: auto;" src=""></iframe>
{$end_tab_preview}
{$end_tab_content}
{/if}

<div class="pageoverflow">
  <p class="pagetext">&nbsp;</p>
  <p class="pageinput">{$hidden|default:''}{$submit}{$cancel}{if isset($apply)}{$apply}{/if}</p>
</div>
{$endform}
</div>{* #edit_news *}