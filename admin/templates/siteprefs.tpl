{$mod->StartTabHeaders()}
{$mod->SetTabHeader('general',$lang_general,$active_general)}
{$mod->SetTabHeader('listcontent',$lang_listcontent,$active_listcontent)}
{$mod->SetTabHeader('editcontent',$lang_editcontent_settings,$active_editcontent)}
{$mod->SetTabHeader('sitedown',$lang_sitedown,$active_sitedown)}
{$mod->SetTabHeader('setup',$lang_setup,$active_setup)}
{$mod->EndTabHeaders()}
{$mod->StartTabContent()}

{$mod->StartTab('general')}
<form id="siteprefform_general" method="post" action="siteprefs.php">
<div>
  <input type="hidden" name="{$SECURE_PARAM_NAME}" value="{$CMS_USER_KEY}"/>
  <input type="hidden" name="active_tab" value="general" />
  <input type="hidden" name="editsiteprefs" value="true" />
</div>

<div class="pageoverflow">
  <p class="pagetext">{$lang_sitename}</p>
  <p class="pageinput"><input type="text" class="pagesmalltextarea" name="sitename" size="30" value="{$sitename}" /></p>
</div>
<div class="pageoverflow">
  <p class="pagetext">{$lang_frontendlang}</p>
  <p class="pageinput">
    <select name="frontendlang" style="vertical-align: middle;">
       {html_options options=$languages selected=$frontendlang}
    </select>
  </p>
</div>

<div class="pageoverflow">
	<p class="pagetext">{$lang_frontendwysiwygtouse}:</p>
	<p class="pageinput">
		<select name="frontendwysiwyg">
		{html_options options=$wysiwyg selected=$frontendwysiwyg}
		</select>
	</p>
</div>

<div class="pageoverflow">
  <p class="pagetext">{$lang_nogcbwysiwyg}:</p>
  <p class="pageinput"><input type="hidden" name="nogcbwysiwyg" value="0"/><input class="pagenb" type="checkbox" value="1" name="nogcbwysiwyg" {if $nogcbwysiwyg == "1"}checked="checked"{/if} /></p>
</div>
<div class="pageoverflow">
  <p class="pagetext">{$lang_globalmetadata}:</p>
  <p class="pageinput"><textarea class="pagesmalltextarea" name="metadata" cols="80" rows="20">{$metadata}</textarea>
  </p>
</div>
{if isset($themes)}
<div class="pageoverflow">
  <p class="pagetext">{$lang_logintheme}:</p>
  <p class="pageinput">
    <select name="logintheme">
      {html_options options=$themes selected=$logintheme}
    </select>
  </p>
</div>
{/if}

<div class="pageoverflow">
  <p class="pagetext">{$lang_date_format_string}:</p>
  <p class="pageinput">
    <input class="pagenb" type="text" name="defaultdateformat" size="20" maxlength="255" value="{$defaultdateformat}"/>
    <br/>{$lang_date_format_string_help}
  </p>
</div>

<div class="pageoverflow">
  <p class="pagetext">{$lang_thumbnail_width}:</p>
  <p class="pageinput">
    <input class="pagenb" type="text" name="thumbnail_width" size="3" maxlength="3" value="{$thumbnail_width}"/>
  </p>
</div>

<div class="pageoverflow">
  <p class="pagetext">{$lang_thumbnail_height}:</p>
  <p class="pageinput">
    <input class="pagenb" type="text" name="thumbnail_height" size="3" maxlength="3" value="{$thumbnail_height}"/>
  </p>
</div>

<div class="pageoverflow">
  <p class="pagetext">&nbsp;</p>
  <p class="pageinput">
    <input type="submit" name="submit" value="{$lang_submit}" class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" />
    <input type="submit" name="cancel" value="{$lang_cancel}" class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" />
  </p>
</div>
</form>
{$mod->EndTab()}

{$mod->StartTab('listcontent')}
<form id="siteprefform_general" method="post" action="siteprefs.php">
<div>
  <input type="hidden" name="{$SECURE_PARAM_NAME}" value="{$CMS_USER_KEY}"/>
  <input type="hidden" name="active_tab" value="listcontent" />
  <input type="hidden" name="editsiteprefs" value="true" />
</div>

<div class="pageoverflow">
  <p class="pagetext">{'listcontent_showtitle'|lang}:</p>
  <p class="pageinput">
    <select name="listcontent_showtitle">
      {html_options options=$titlemenu selected=$listcontent_showtitle}
    </select>
  </p>
</div>

<div class="pageoverflow">
  <p class="pagetext">{'listcontent_showalias'|lang}:</p>
  <p class="pageinput">
    <select name="listcontent_showalias">
      {html_options options=$yesno selected=$listcontent_showalias}
    </select>
  </p>
</div>

<div class="pageoverflow">
  <p class="pagetext">{'listcontent_showurl'|lang}:</p>
  <p class="pageinput">
    <select name="listcontent_showurl">
      {html_options options=$yesno selected=$listcontent_showurl}
    </select>
  </p>
</div>

<div class="pageoverflow">
  <p class="pagetext">&nbsp;</p>
  <p class="pageinput">
    <input type="submit" name="submit" value="{$lang_submit}" class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" />
    <input type="submit" name="cancel" value="{$lang_cancel}" class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" />
  </p>
</div>
</form>
{$mod->EndTab()}

{* editcontent tab *}
{$mod->StartTab('editcontent')}
<form id="siteprefform_editcontent" method="post" action="siteprefs.php">
<div>
  <input type="hidden" name="{$SECURE_PARAM_NAME}" value="{$CMS_USER_KEY}"/>
  <input type="hidden" name="active_tab" value="editcontent" />
  <input type="hidden" name="editsiteprefs" value="true" />
</div>

<div class="pageoverflow">
  <p class="pagetext">{'content_autocreate_urls'|lang}:</p>
  <p class="pageinput">
    <select name="content_autocreate_urls">
    {html_options options=$yesno selected=$content_autocreate_urls}
    </select>
  </p>
</div>

<div class="pageoverflow">
  <p class="pagetext">{'content_autocreate_flaturls'|lang}:</p>
  <p class="pageinput">
    <select name="content_autocreate_flaturls">
    {html_options options=$yesno selected=$content_autocreate_flaturls}
    </select>
    <br/>
    {'info_content_autocreate_flaturls'|lang}
  </p>
</div>

<div class="pageoverflow">
  <p class="pagetext">{'content_mandatory_urls'|lang}:</p>
  <p class="pageinput">
    <select name="content_mandatory_urls">
    {html_options options=$yesno selected=$content_mandatory_urls}
    </select>
  </p>
</div>

<div class="pageoverflow">
  <p class="pagetext">{'content_imagefield_path'|lang}:</p>
  <p class="pageinput">
    <input type="text" name="content_imagefield_path" size="50" maxlength="255" value="{$content_imagefield_path}"/>
    <br/>
    {'info_content_imagefield_path'|lang}
  </p>
</div>

<div class="pageoverflow">
  <p class="pagetext">{'content_thumbnailfield_path'|lang}:</p>
  <p class="pageinput">
    <input type="text" name="content_thumbnailfield_path" size="50" maxlength="255" value="{$content_thumbnailfield_path}"/>
    <br/>
    {'info_content_thumbnailfield_path'|lang}
  </p>
</div>

<div class="pageoverflow">
  <p class="pagetext">{'contentimage_path'|lang}:</p>
  <p class="pageinput">
    <input type="text" name="contentimage_path" size="50" maxlength="255" value="{$contentimage_path}"/>
    <br/>
    {'info_contentimage_path'|lang}
  </p>
</div>

<div class="pageoverflow">
  <p class="pagetext">{$lang_basic_attributes}:</p>
  <p class="pageinput">
    <select name="basic_attributes[]" multiple="multiple" size="5">
      {html_options options=$all_attributes selected=$basic_attributes}
    </select>
    <br/>
    {$lang_info_basic_attributes}
  </p>
</div>

<div class="pageoverflow">
  <p class="pagetext">&nbsp;</p>
  <p class="pageinput">
    <input type="submit" name="submit" value="{$lang_submit}" class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" />
    <input type="submit" name="cancel" value="{$lang_cancel}" class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" />
  </p>
</div>

</form>
{$mod->EndTab()}

{* sitedown tab *}
{$mod->StartTab('sitedown')}
<form id="siteprefform_sitedown" method="post" action="siteprefs.php">
<div>
  <input type="hidden" name="{$SECURE_PARAM_NAME}" value="{$CMS_USER_KEY}"/>
  <input type="hidden" name="active_tab" value="sitedown" />
  <input type="hidden" name="editsiteprefs" value="true" />
</div>

<div class="pageoverflow">
  <p class="pagetext">{$lang_enablesitedown}:</p>
  <p class="pageinput"><input type="hidden" name="enablesitedownmessage" value="0"/><input class="pagenb" type="checkbox" value="1" name="enablesitedownmessage" {if $enablesitedownmessage == "1"}checked="checked"{/if}/></p>
</div>
<div class="pageoverflow">
  <p class="pagetext">{$lang_sitedownmessage}:</p>
  <p class="pageinput">{$textarea_sitedownmessage}</p>
</div>
<div class="pageoverflow">
  <p class="pagetext">{'sitedownexcludeadmins'|lang}:</p>
  <p class="pageinput">
    <input type="hidden" name="sitedownexcludeadmins" value="0"/>
    <input type="checkbox" name="sitedownexcludeadmins" value="1" {if $sitedownexcludeadmins == 1}checked="checked"{/if}/>
  </p>
</div>
<div class="pageoverflow">
  <p class="pagetext">{$lang_sitedownexcludes}:</p>
  <p class="pageinput">
     <input type="text" name="sitedownexcludes" size="50" maxlength="255" value="{$sitedownexcludes}"/>
     <br/>
     <strong>{'your_ipaddress'|lang}:</strong>&nbsp;{$smarty.server.REMOTE_ADDR}<br/>
     {$lang_info_sitedownexcludes}
  </p>
</div>

<div class="pageoverflow">
  <p class="pagetext">&nbsp;</p>
  <p class="pageinput">
    <input type="submit" name="submit" value="{$lang_submit}" class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" />
    <input type="submit" name="cancel" value="{$lang_cancel}" class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" />
  </p>
</div>
</form>
{$mod->EndTab()}


{$mod->StartTab('setup')}
<form id="siteprefform_setup" method="post" action="siteprefs.php">
<div>
  <input type="hidden" name="{$SECURE_PARAM_NAME}" value="{$CMS_USER_KEY}"/>
  <input type="hidden" name="active_tab" value="setup" />
  <input type="hidden" name="editsiteprefs" value="true" />
</div>

<div class="pageoverflow">
  <p class="pagetext">{$lang_clearcache}:</p>
  <p class="pageinput">
    <input class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" type="submit" name="clearcache" value="{$lang_clear}" />
  </p>
</div>  
<div class="pageoverflow">
  <p class="pagetext">{'autoclearcache2'|lang}:</p>
  <p class="pageinput">
    <input type="text" class="pagesmalltextarea"  name="auto_clear_cache_age" size="4" value="{$auto_clear_cache_age}" maxlength="4"/>
    <br/>
    {$lang_info_autoclearcache}
  </p>
</div>  

<div class="pageoverflow">
  <p class="pagetext">{$lang_global_umask}:</p>
  <p class="pageinput"><input type="text" class="pagesmalltextarea" name="global_umask" size="4" value="{$global_umask}" /></p>
</div>
{if isset($testresults)}
<div class="pageoverflow">
  <p class="pagetext">{$lang_results}</p>
  <p class="pageinput"><strong>{$testresults}</strong></p>
</div>
{/if}
<div class="pageoverflow">
  <p class="pagetext">&nbsp;</p>
  <p class="pageinput"><input type="submit" name="testumask" value="{$lang_test}" class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" /></p>
</div>

<div class="pageoverflow">
  <p class="pagetext">{$lang_urlcheckversion}:</p>
  <p class="pageinput">
    <input class="pagenb" type="text" name="urlcheckversion" size="80" maxlength="255" value="{$urlcheckversion}"/>
    <br/>{$lang_info_urlcheckversion}
  </p>
</div>
<div class="pageoverflow">
  <p class="pagetext">{$lang_clear_version_check_cache}:</p>
  <p class="pageinput"><input type="hidden" name="clear_vc_cache" value="0"/><input class="pagenb" value="1" type="checkbox" name="clear_vc_cache" {if $clear_vc_cache}checked="checked"{/if} /></p>
</div>

<div class="pageoverflow">
  <p class="pagetext">{$lang_disablesafemodewarning}:</p>
  <p class="pageinput"><input type="hidden" name="disablesafemodwarning" value="0"/><input class="pagenb" type="checkbox" value="1" name="disablesafemodewarning" {if $disablesafemodewarning}checked="checked"{/if} /></p>
</div>

<div class="pageoverflow">
  <p class="pagetext">{$lang_allowparamcheckwarnings}:</p>
  <p class="pageinput"><input type="hidden" name="allowparamcheckwarnings" value="0" /><input class="pagenb" type="checkbox" value="1" name="allowparamcheckwarnings" {if $allowparamcheckwarnings}checked="checked"{/if} /></p>
</div>

<div class="pageoverflow">
  <p class="pagetext">{$lang_admin_enablenotifications}:</p>
  <p class="pageinput"><input type="hidden" name="enablenotifications" value="0"/><input class="pagenb" type="checkbox" value="1" name="enablenotifications" {if $enablenotifications}checked="checked"{/if} /></p>
</div>

<div class="pageoverflow">
  <p class="pagetext">{$lang_pseudocron_granularity}:</p>
  <p class="pageinput">
    <select name="pseudocron_granularity">
    {html_options options=$pseudocron_options selected=$pseudocron_granularity}
    </select><br/>
    {$lang_info_pseudocron_granularity}
  </p>
</div>  

<div class="pageoverflow">
  <p class="pagetext">&nbsp;</p>
  <p class="pageinput">
    <input type="submit" name="submit" value="{$lang_submit}" class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" />
    <input type="submit" name="cancel" value="{$lang_cancel}" class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" />
  </p>
</div>
</form>
{$mod->EndTab()}

{$mod->EndTabContent()}
