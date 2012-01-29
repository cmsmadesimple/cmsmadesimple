{$mod->StartTabHeaders()}
{$mod->SetTabHeader('database',$lang_database,isset($active_general))}
{$mod->SetTabHeader('changelog',$lang_changelog,isset($active_listcontent))}
{*{$mod->SetTabHeader('editcontent',$lang_editcontent_settings,$active_editcontent)}
{$mod->SetTabHeader('sitedown',$lang_sitedown,$active_sitedown)}
{$mod->SetTabHeader('setup',$lang_setup,$active_setup)}
*}
{$mod->EndTabHeaders()}
{$mod->StartTabContent()}


{$mod->StartTab('database')}
nothing yet
{$mod->EndTab()}

{* editcontent tab *}
{$mod->StartTab('changelog')}


<strong>Changelog</strong><br/>
({$changelogfilename})
(may be slightly formatted?)

    <br><br>

<tt>{$changelog}</tt>


{$mod->EndTab()}

{* sitedown tab *}
{*
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
        <p class="pagetext">{$lang_enablewysiwyg}:</p>
        <p class="pageinput"><input type="hidden" name="use_wysiwyg" value="0"/><input type="checkbox" name="use_wysiwyg" id='use_wysiwyg' value="1" class="pagenb" {if $use_wysiwyg == "1"}checked="checked"{/if}/></p>
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
            <input type="submit" name="submit" value="{$lang_submit}" class="pagebutton"  />
            <input type="submit" name="cancel" value="{$lang_cancel}" class="pagebutton"  />
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

    <fieldset>
        <legend>{'browser_cache_settings'|lang}:&nbsp;</legend>
        <div class="pageoverflow">
            <p class="pagetext">{'allow_browser_cache'|lang}:</p>
            <p class="pageinput">
                <input type="hidden" name="allow_browser_cache" value="0"/><input class="pagenb" value="1" type="checkbox" name="allow_browser_cache" {if $allow_browser_cache}checked="checked"{/if} /><br/>{'info_browser_cache'|lang}</p>
        </div>
        <div class="pageoverflow">
            <p class="pagetext">{'browser_cache_expiry'|lang}:</p>
            <p class="pageinput">
                <input type="text" name="browser_cache_expiry" value="{$browser_cache_expiry}" size="6" maxlength="10"/><br/>{'info_browser_cache_expiry'|lang}</p>
        </div>
    </fieldset>

    <fieldset>
        <legend>{'server_cache_settings'|lang}:&nbsp;</legend>
        <div class="pageoverflow">
            <p class="pagetext">{$lang_clearcache}:</p>
            <p class="pageinput">
                <input class="pagebutton"  type="submit" name="clearcache" value="{$lang_clear}" />
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
    </fieldset>

    <fieldset>
        <legend>{'general_operation_settings'|lang}:&nbsp;</legend>
        <div class="pageoverflow">
            <p class="pagetext">{$lang_global_umask}:</p>
            <p class="pageinput">
                <input type="text" class="pagesmalltextarea" name="global_umask" size="4" value="{$global_umask}" />
                <br/>
            {'info_umask'|lang}
            </p>
        </div>
    {if isset($testresults)}
        <div class="pageoverflow">
            <p class="pagetext">{$lang_results}</p>
            <p class="pageinput"><strong>{$testresults}</strong></p>
        </div>
    {/if}
        <div class="pageoverflow">
            <p class="pagetext">&nbsp;</p>
            <p class="pageinput"><input type="submit" name="testumask" value="{$lang_test}" class="pagebutton"  /></p>
        </div>
        <div class="pageoverflow">
            <p class="pagetext">{$lang_disablesafemodewarning}:</p>
            <p class="pageinput"><input type="hidden" name="disablesafemodwarning" value="0"/><input class="pagenb" type="checkbox" value="1" name="disablesafemodewarning" {if $disablesafemodewarning}checked="checked"{/if} /></p>
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
            <p class="pagetext">{$lang_adminlog_lifetime}:</p>
            <p class="pageinput">
                <select name="adminlog_lifetime">
                {html_options options=$adminlog_options selected=$adminlog_lifetime}
                </select><br/>
            {$lang_info_adminlog_lifetime}
            </p>
        </div>
        <div class="pageoverflow">
            <p class="pagetext">{$lang_checkversion}:</p>
            <p class="pageinput">
                <input type="hidden" name="checkversion" value="0"/>
                <input type="checkbox" name="checkversion" value="1" {if $checkversion}checked="checked"{/if}/>
                <br/>{'info_checkversion'|lang}
            </p>
        </div>
    </fieldset>

    <div class="pageoverflow">
        <p class="pagetext">&nbsp;</p>
        <p class="pageinput">
            <input type="submit" name="submit" value="{$lang_submit}" class="pagebutton"  />
            <input type="submit" name="cancel" value="{$lang_cancel}" class="pagebutton"  />
        </p>
    </div>
</form>
{$mod->EndTab()}
*}

{$mod->EndTabContent()}
