<form id="siteprefform" method="post" action="siteprefs.php">
<div>
  <input type="hidden" name="{$SECURE_PARAM_NAME}" value="{$CMS_USER_KEY}"/>
  <input type="hidden" name="editsiteprefs" value="true" />
</div>

<div class="pageoverflow">
  <p class="pagetext">&nbsp;</p>
  <p class="pageinput">
    <input type="submit" name="submit" value="{$lang_submit}" class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" />
    <input type="submit" name="cancel" value="{$lang_cancel}" class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" />
  </p>
</div>

{$mod->StartTabHeaders()}
{$mod->SetTabHeader('general',$lang_general)}
{$mod->SetTabHeader('sitedown',$lang_sitedown)}
{$mod->SetTabHeader('handle_404',$lang_handle404)}
{$mod->SetTabHeader('setup',$lang_setup)}
{$mod->EndTabHeaders()}
{$mod->StartTabContent()}

{$mod->StartTab('general')}
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
  <p class="pagetext">{$lang_nogcbwysiwyg}:</p>
  <p class="pageinput"><input class="pagenb" type="checkbox" name="nogcbwysiwyg" {if $nogcbwysiwyg == "1"}checked="checked"{/if} /></p>
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

{$mod->EndTab()}


{$mod->StartTab('sitedown')}
<div class="pageoverflow">
  <p class="pagetext">{$lang_enablesitedown}:</p>
  <p class="pageinput"><input class="pagenb" type="checkbox" name="enablesitedownmessage" {if $enablesitedownmessage == "1"}checked="checked"{/if}/></p>
</div>
<div class="pageoverflow">
  <p class="pagetext">{$lang_sitedownmessage}:</p>
  <p class="pageinput">{$textarea_sitedownmessage}</p>
</div>
<div class="pageoverflow">
  <p class="pagetext">{$lang_sitedownexcludes}:</p>
  <p class="pageinput">
     <input type="text" name="sitedownexcludes" size="50" maxlength="255" value="{$sitedownexcludes}"/>
     <br/>
     {$lang_info_sitedownexcludes}
  </p>
</div>
{$mod->EndTab()}


{$mod->StartTab('handle_404')}
<div class="pageoverflow">
  <p class="pagetext">{$lang_enablecustom404}:</p>
  <p class="pageinput"><input class="pagenb" type="checkbox" name="enablecustom404" {if $enablecustom404 == "1"}checked="checked"{/if}/></p>
</div>
<div class="pageoverflow">
  <p class="pagetext">{$lang_custom404}</p>
  <p class="pageinput">{$textarea_custom404}</p>
</div>
<div class="pageoverflow">
  <p class="pagetext">{$lang_template}:</p>
  <p class="pageinput">
    <select name="custom404template">
      {html_options options=$templates selected=$custom404template}
    </select>
  </p>
</div>

{$mod->EndTab()}

{$mod->StartTab('setup')}
<div class="pageoverflow">
  <p class="pagetext">{$lang_clearcache}:</p>
  <p class="pageinput">
    <input class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" type="submit" name="clearcache" value="{$lang_clear}" />
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
  <p class="pagetext">{$lang_css_max_age}:</p>
  <p class="pageinput">
    <input type="text" class="pagesmalltextarea" name="css_max_age" size="10" maxlength="10" value="{$css_max_age}" />
    <br/>{$lang_help_css_max_age}
  </p>
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
  <p class="pageinput"><input class="pagenb" type="checkbox" name="clear_vc_cache" /></p>
</div>

<div class="pageoverflow">
  <p class="pagetext">{$lang_disablesafemodewarning}:</p>
  <p class="pageinput"><input class="pagenb" type="checkbox" name="disablesafemodewarning" {if $disablesafemodewarning}checked="checked"{/if} /></p>
</div>

<div class="pageoverflow">
  <p class="pagetext">{$lang_allowparamcheckwarnings}:</p>
  <p class="pageinput"><input class="pagenb" type="checkbox" name="allowparamcheckwarnings" {if $allowparamcheckwarnings}checked="checked"{/if} /></p>
</div>

<div class="pageoverflow">
  <p class="pagetext">{$lang_admin_enablenotifications}:</p>
  <p class="pageinput"><input class="pagenb" type="checkbox" name="enablenotifications" {if $enablenotifications}checked="checked"{/if} /></p>
</div>

{$mod->EndTab()}

{$mod->EndTabContent()}

{* duplicate submit and cancel buttons *}
<div class="pageoverflow">
  <p class="pagetext">&nbsp;</p>
  <p class="pageinput">
    <input type="submit" name="submit" value="{$lang_submit}" class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" />
    <input type="submit" name="cancel" value="{$lang_cancel}" class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" />
  </p>
</div>


</form>
