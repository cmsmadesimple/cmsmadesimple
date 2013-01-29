<div class="pagecontainer">

{$tab_start}

{$general_start}
	<form id="siteprefform_general" method="post" action="{$formurl}">
		<div>
		  <input type="hidden" name="{$SECURE_PARAM_NAME}" value="{$CMS_USER_KEY}"/>
		  <input type="hidden" name="active_tab" value="general" />
		  <input type="hidden" name="editsiteprefs" value="true" />
		</div>

		<div class="pageoverflow">
		  <p class="pagetext"><label for="sitename">{lang('sitename')}:</label></p>
		  <p class="pageinput"><input type="text" id="sitename" class="pagesmalltextarea" name="sitename" size="30" value="{$sitename}" /></p>
		</div>
		<div class="pageoverflow">
		  <p class="pagetext"><label for="frontendlang">{lang('frontendlang')}:</label></p>
		  <p class="pageinput">
			<select id="frontendlang" name="frontendlang" style="vertical-align: middle;">
			   {html_options options=$languages selected=$frontendlang}
			</select>
		  </p>
		</div>

		<div class="pageoverflow">
			<p class="pagetext"><label for="frontendwysiwyg">{lang('frontendwysiwygtouse')}:</label></p>
			<p class="pageinput">
				<select id="frontendwysiwyg" name="frontendwysiwyg">
				{html_options options=$wysiwyg selected=$frontendwysiwyg}
				</select>
			</p>
		</div>

		<div class="pageoverflow">
		  <p class="pagetext"><label for="nogcbwysiwyg">{lang('nogcbwysiwyg')}:</label></p>
		  <p class="pageinput"><input type="hidden" name="nogcbwysiwyg" value="0"/><input id="nogcbwysiwyg" class="pagenb" type="checkbox" value="1" name="nogcbwysiwyg" {if $nogcbwysiwyg == "1"}checked="checked"{/if} /></p>
		</div>
		<div class="pageoverflow">
		  <p class="pagetext"><label for="globalmetadata">{lang('globalmetadata')}:</label></p>
		  <p class="pageinput"><textarea id="globalmetadata" class="pagesmalltextarea" name="metadata" cols="80" rows="20">{$metadata}</textarea>
		  </p>
		</div>
		{if isset($themes)}
		<div class="pageoverflow">
		  <p class="pagetext"><label for="logintheme">{lang('master_admintheme')}:</label></p>
		  <p class="pageinput">
			<select id="logintheme" name="logintheme">
			  {html_options options=$themes selected=$logintheme}
			</select>
		  </p>
		</div>
		{/if}
		<div class="pageoverflow">
			<p class="pagetext"><label for="backendwysiwyg">{lang('backendwysiwygtouse')}:</label></p>
			<p class="pageinput">
				<select id="backendwysiwyg" name="backendwysiwyg">
				{html_options options=$wysiwyg selected=$backendwysiwyg}
				</select>
			</p>
		</div>
		<div class="pageoverflow">
		  <p class="pagetext"><label for="defaultdateformat">{lang('date_format_string')}:</label></p>
		  <p class="pageinput">
			<input class="pagenb" id="defaultdateformat" type="text" name="defaultdateformat" size="20" maxlength="255" value="{$defaultdateformat}"/>
			<br/>{$lang_date_format_string_help}
		  </p>
		</div>

		<div class="pageoverflow">
		  <p class="pagetext"><label for="thumbnail_width">{lang('thumbnail_width')}:</label></p>
		  <p class="pageinput">
			<input class="pagenb" id="thumbnail_width" type="text" name="thumbnail_width" size="3" maxlength="3" value="{$thumbnail_width}"/>
		  </p>
		</div>

		<div class="pageoverflow">
		  <p class="pagetext"><label for="thumbnail_height">{lang('thumbnail_height')}:</label></p>
		  <p class="pageinput">
			<input id="thumbnail_height" class="pagenb" type="text" name="thumbnail_height" size="3" maxlength="3" value="{$thumbnail_height}"/>
		  </p>
		</div>

		{if isset($search_modules)}
		  <p class="pagetext"><label for="search_module">{'search_module'|lang}:</label></p>
		  <p class="pageinput">
			<select id="search_module" name="search_module">
			{html_options options=$search_modules selected=$search_module}
			</select>
			<br/>
			{'info_search_module'|lang}
		  </p>
		{/if}

		<div class="pageoverflow">
		  <p class="pagetext">&nbsp;</p>
		  <p class="pageinput">
			<input type="submit" name="submit" value="{lang('submit')}" class="pagebutton"  />
			<input type="submit" name="cancel" value="{lang('cancel')}" class="pagebutton"  />
		  </p>
		</div>
	</form>
{$tab_end}

{$listcontent_start}
	<form id="siteprefform_general" method="post" action="{$formurl}">
		<div>
		  <input type="hidden" name="{$SECURE_PARAM_NAME}" value="{$CMS_USER_KEY}"/>
		  <input type="hidden" name="active_tab" value="listcontent" />
		  <input type="hidden" name="editsiteprefs" value="true" />
		</div>

		<div class="pageoverflow">
		  <p class="pagetext"><label for="listcontent_showtitle">{'listcontent_showtitle'|lang}:</label></p>
		  <p class="pageinput">
			<select id="listcontent_showtitle" name="listcontent_showtitle">
			  {html_options options=$titlemenu selected=$listcontent_showtitle}
			</select>
		  </p>
		</div>

		<div class="pageoverflow">
		  <p class="pagetext"><label for="listcontent_showalias">{'listcontent_showalias'|lang}:</label></p>
		  <p class="pageinput">
			<select id="listcontent_showalias" name="listcontent_showalias">
			  {html_options options=$yesno selected=$listcontent_showalias}
			</select>
		  </p>
		</div>

		<div class="pageoverflow">
		  <p class="pagetext"><label for="listcontent_showurl">{'listcontent_showurl'|lang}:</label></p>
		  <p class="pageinput">
			<select id="listcontent_showurl" name="listcontent_showurl">
			  {html_options options=$yesno selected=$listcontent_showurl}
			</select>
		  </p>
		</div>

		<div class="pageoverflow">
		  <p class="pagetext">&nbsp;</p>
		  <p class="pageinput">
			<input type="submit" name="submit" value="{lang('submit')}" class="pagebutton"  />
			<input type="submit" name="cancel" value="{lang('cancel')}" class="pagebutton"  />
		  </p>
		</div>
	</form>
{$tab_end}

{$editcontent_start}
	<form id="siteprefform_editcontent" method="post" action="{$formurl}">
		<div>
		  <input type="hidden" name="{$SECURE_PARAM_NAME}" value="{$CMS_USER_KEY}"/>
		  <input type="hidden" name="active_tab" value="editcontent" />
		  <input type="hidden" name="editsiteprefs" value="true" />
		</div>

		<div class="pageoverflow">
		  <p class="pagetext"><label for="content_autocreate_urls">{'content_autocreate_urls'|lang}:</label></p>
		  <p class="pageinput">
			<select id="content_autocreate_urls" name="content_autocreate_urls">
			{html_options options=$yesno selected=$content_autocreate_urls}
			</select>
		  </p>
		</div>

		<div class="pageoverflow">
		  <p class="pagetext"><label for="content_autocreate_flaturls">{'content_autocreate_flaturls'|lang}:</label></p>
		  <p class="pageinput">
			<select id="content_autocreate_flaturls" name="content_autocreate_flaturls">
			{html_options options=$yesno selected=$content_autocreate_flaturls}
			</select>
			<br/>
			{'info_content_autocreate_flaturls'|lang}
		  </p>
		</div>

		<div class="pageoverflow">
		  <p class="pagetext"><label for="content_mandatory_urls">{'content_mandatory_urls'|lang}:</label></p>
		  <p class="pageinput">
			<select id="content_mandatory_urls" name="content_mandatory_urls">
			{html_options options=$yesno selected=$content_mandatory_urls}
			</select>
		  </p>
		</div>

		<div class="pageoverflow">
		  <p class="pagetext"><label for="disallowed_contenttypes">{'disallowed_contenttypes'|lang}:</label></p>
		  <p class="pageinput">
			<select id="disallowed_contenttypes" name="disallowed_contenttypes[]" multiple="multiple" size="5">
			  {html_options options=$all_contenttypes selected=$disallowed_contenttypes}
			</select>
			<br/>
			{'info_disallowed_contenttypes'|lang}
		  </p>
		</div>

		<div class="pageoverflow">
		  <p class="pagetext"><label for="basic_attributes">{lang('basic_attributes')}:</label></p>
		  <p class="pageinput">
			<select id="basic_attributes" class="multicolumn" name="basic_attributes[]" multiple="multiple" size="5">
			  {html_options options=$all_attributes selected=$basic_attributes}
			</select>
			<br/>
			{$lang_info_basic_attributes}
		  </p>
		</div>

		<div class="pageoverflow">
		  <p class="pagetext"><label for="imagefield_path">{'content_imagefield_path'|lang}:</label></p>
		  <p class="pageinput">
			<input id="imagefield_path" type="text" name="content_imagefield_path" size="50" maxlength="255" value="{$content_imagefield_path}"/>
			<br/>
			{'info_content_imagefield_path'|lang}
		  </p>
		</div>

		<div class="pageoverflow">
		  <p class="pagetext"><label for="thumbfield_path">{'content_thumbnailfield_path'|lang}:</label></p>
		  <p class="pageinput">
			<input id="thumbfield_path" type="text" name="content_thumbnailfield_path" size="50" maxlength="255" value="{$content_thumbnailfield_path}"/>
			<br/>
			{'info_content_thumbnailfield_path'|lang}
		  </p>
		</div>

		<div class="pageoverflow">
		  <p class="pagetext"><label for="contentimage_path">{'contentimage_path'|lang}:</label></p>
		  <p class="pageinput">
			<input type="text" id="contentimage_path" name="contentimage_path" size="50" maxlength="255" value="{$contentimage_path}"/>
			<br/>
			{'info_contentimage_path'|lang}
		  </p>
		</div>

		<div class="pageoverflow">
		  <p class="pagetext">&nbsp;</p>
		  <p class="pageinput">
			<input type="submit" name="submit" value="{lang('submit')}" class="pagebutton"  />
			<input type="submit" name="cancel" value="{lang('cancel')}" class="pagebutton"  />
		  </p>
		</div>

	</form>
{$tab_end}

{$sitedown_start}
	<form id="siteprefform_sitedown" method="post" action="{$formurl}">
		<div>
		  <input type="hidden" name="{$SECURE_PARAM_NAME}" value="{$CMS_USER_KEY}"/>
		  <input type="hidden" name="active_tab" value="sitedown" />
		  <input type="hidden" name="editsiteprefs" value="true" />
		</div>

		<div class="pageoverflow">
		  <p class="pagetext"><label for="enablesitedown">{lang('enablesitedown')}:</label></p>
		  <p class="pageinput"><input type="hidden" name="enablesitedownmessage" value="0"/><input id="enablesitedown" class="pagenb" type="checkbox" value="1" name="enablesitedownmessage" {if $enablesitedownmessage == "1"}checked="checked"{/if}/></p>
		</div>
		<div class="pageoverflow">
		  <p class="pagetext"><label for="enablewysiwyg">{lang('enablewysiwyg')}:</label></p>
		  <p class="pageinput"><input type="hidden" name="use_wysiwyg" value="0"/><input id="enablewysiwyg" type="checkbox" name="use_wysiwyg" id='use_wysiwyg' value="1" class="pagenb" {if $use_wysiwyg == "1"}checked="checked"{/if}/></p>
		</div>

		<div class="pageoverflow">
		  <p class="pagetext">{lang('sitedownmessage')}:</p>
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
		  <p class="pagetext"><label for="sitedownexcludes">{lang('sitedownexcludes')}:</label></p>
		  <p class="pageinput">
			 <input id="sitedownexcludes" type="text" name="sitedownexcludes" size="50" maxlength="255" value="{$sitedownexcludes}"/>
			 <br/>
			 <strong>{'your_ipaddress'|lang}:</strong>&nbsp;{$smarty.server.REMOTE_ADDR}<br/>
			 {$lang_info_sitedownexcludes}
		  </p>
		</div>

		<div class="pageoverflow">
		  <p class="pagetext">&nbsp;</p>
		  <p class="pageinput">
			<input type="submit" name="submit" value="{lang('submit')}" class="pagebutton"  />
			<input type="submit" name="cancel" value="{lang('cancel')}" class="pagebutton"  />
		  </p>
		</div>
	</form>
{$tab_end}

{$setup_start}
	<form id="siteprefform_setup" method="post" action="{$formurl}">
		<div>
		  <input type="hidden" name="{$SECURE_PARAM_NAME}" value="{$CMS_USER_KEY}"/>
		  <input type="hidden" name="active_tab" value="setup" />
		  <input type="hidden" name="editsiteprefs" value="true" />
		</div>

		<fieldset>
		<legend>{'browser_cache_settings'|lang}:&nbsp;</legend>
		<div class="pageoverflow">
		  <p class="pagetext"><label for="allow_browser_cache">{'allow_browser_cache'|lang}:</label></p>
		  <p class="pageinput">
		   <input type="hidden" name="allow_browser_cache" value="0"/><input class="pagenb" id="allow_browser_cache" value="1" type="checkbox" name="allow_browser_cache" {if $allow_browser_cache}checked="checked"{/if} /><br/>{'info_browser_cache'|lang}</p>
		</div>  
		<div class="pageoverflow">
		  <p class="pagetext"><label for="browser_expiry">{'browser_cache_expiry'|lang}:</label></p>
		  <p class="pageinput">
		   <input type="text" id="browser_expiry" name="browser_cache_expiry" value="{$browser_cache_expiry}" size="6" maxlength="10"/><br/>{'info_browser_cache_expiry'|lang}</p>
		</div>  
		</fieldset>

		<fieldset>
		<legend>{'server_cache_settings'|lang}:&nbsp;</legend>

		<div class="pageoverflow">
		  <p class="pagetext"><label for="autoclearcache2">{'autoclearcache2'|lang}:</label></p>
		  <p class="pageinput">
			<input id="autoclearcache2" type="text" class="pagesmalltextarea"  name="auto_clear_cache_age" size="4" value="{$auto_clear_cache_age}" maxlength="4"/>
			<br/>
			{$lang_info_autoclearcache}
		  </p>
		</div>

		</fieldset>

		<fieldset>
		<legend>{'general_operation_settings'|lang}:&nbsp;</legend>
		<div class="pageoverflow">
		  <p class="pagetext"><label for="umask">{lang('global_umask')}:</label></p>
		  <p class="pageinput">
			<input id="umask" type="text" class="pagesmalltextarea" name="global_umask" size="4" value="{$global_umask}" />
			<br/>
			{'info_umask'|lang}
		  </p>
		</div>
		{if isset($testresults)}
		<div class="pageoverflow">
		  <p class="pagetext">{lang('results')}</p>
		  <p class="pageinput"><strong>{$testresults}</strong></p>
		</div>
		{/if}
		<div class="pageoverflow">
		  <p class="pagetext">&nbsp;</p>
		  <p class="pageinput"><input type="submit" name="testumask" value="{lang('test')}" class="pagebutton"  /></p>
		</div>
		<div class="pageoverflow">
		  <p class="pagetext"><label for="safemodewarn">{lang('disablesafemodewarning')}:</label></p>
		  <p class="pageinput"><input type="hidden" name="disablesafemodwarning" value="0"/><input id="safemodewarn" class="pagenb" type="checkbox" value="1" name="disablesafemodewarning" {if $disablesafemodewarning}checked="checked"{/if} /></p>
		</div>
		<div class="pageoverflow">
		  <p class="pagetext"><label for="notifications">{lang('admin_enablenotifications')}:</label></p>
		  <p class="pageinput"><input type="hidden" name="enablenotifications" value="0"/><input id="notifications" class="pagenb" type="checkbox" value="1" name="enablenotifications" {if $enablenotifications}checked="checked"{/if} /></p>
		</div>
		<div class="pageoverflow">
		  <p class="pagetext"><label for="pseudocron">{lang('pseudocron_granularity')}:</label></p>
		  <p class="pageinput">
			<select id="pseudocron" name="pseudocron_granularity">
			{html_options options=$pseudocron_options selected=$pseudocron_granularity}
			</select><br/>
			{$lang_info_pseudocron_granularity}
		  </p>
		</div>
		<div class="pageoverflow">
		  <p class="pagetext"><label for="adminlog">{lang('adminlog_lifetime')}:</label></p>
		  <p class="pageinput">
			<select id="adminlog" name="adminlog_lifetime">
			{html_options options=$adminlog_options selected=$adminlog_lifetime}
			</select><br/>
			{$lang_info_adminlog_lifetime}
		  </p>
		</div>
		<div class="pageoverflow">
		  <p class="pagetext"><label for="checkversion">{lang('checkversion')}:</label></p>
		  <p class="pageinput">
			<input type="hidden" name="checkversion" value="0"/>
			<input id="checkversion" type="checkbox" name="checkversion" value="1" {if $checkversion}checked="checked"{/if}/>
			<br/>{'info_checkversion'|lang}
		  </p>
		</div>
		</fieldset>

		<div class="pageoverflow">
		  <p class="pagetext">&nbsp;</p>
		  <p class="pageinput">
			<input type="submit" name="submit" value="{lang('submit')}" class="pagebutton"  />
			<input type="submit" name="cancel" value="{lang('cancel')}" class="pagebutton"  />
		  </p>
		</div>
	</form>
{$tab_end}

{$smarty_start}
	<form id="siteprefform_setup" method="post" action="{$formurl}">
		<div>
		  <input type="hidden" name="{$SECURE_PARAM_NAME}" value="{$CMS_USER_KEY}"/>
		  <input type="hidden" name="active_tab" value="smarty" />
		  <input type="hidden" name="editsiteprefs" value="true" />
		</div>
		<div class="pageoverflow">
		  <p class="pagetext"><label for="smartycache">{'prompt_use_smartycaching'|lang}:</label></p>
		  <p class="pageinput">
			<select id="smartycache" name="use_smartycache">
			  {html_options options=$yesno selected=$use_smartycache}
			</select>
			<br/>
			{'info_smarty_caching'|lang}
		  </p>
		</div>
		<div class="pageoverflow">
		  <p class="pagetext">{'info_smarty_options'|lang}</p>
		</div>
		<div class="pageoverflow">
		  <p class="pagetext"><label for="compilecheck">{'prompt_smarty_compilecheck'|lang}:</label></p>
		  <p class="pageinput">
			<select for="compilecheck" name="use_smartycompilecheck">
			  {html_options options=$yesno selected=$use_smartycompilecheck}
			</select>
			<br/>
			{'info_smarty_compilecheck'|lang}
		  </p>
		</div>
		<div class="pageoverflow">
		  <p class="pagetext"><label for="cachemodules">{'prompt_smarty_cachemodules'|lang}:</label></p>
		  <p class="pageinput">
			<select id="cachemodules" name="smarty_cachemodules">
			  {html_options options=$smarty_cacheoptions selected=$smarty_cachemodules}
			</select>
			<br/>
			{'info_smarty_cachemodules'|lang}
		  </p>
		</div>
		<div class="pageoverflow">
		  <p class="pagetext"><label for="cacheudt">{'prompt_smarty_cacheudt'|lang}:</label></p>
		  <p class="pageinput">
			<select id="cacheudt" name="smarty_cacheudt">
			  {html_options options=$smarty_cacheoptions2 selected=$smarty_cacheudt}
			</select>
			<br/>
			{'info_smarty_cacheudt'|lang}
		  </p>
		</div>

		<div class="pageoverflow">
		  <p class="pagetext">&nbsp;</p>
		  <p class="pageinput">
			<input type="submit" name="submit" value="{lang('submit')}" class="pagebutton"  />
			<input type="submit" name="cancel" value="{lang('cancel')}" class="pagebutton"  />
		  </p>
		</div>
	</form>
{$tab_end}

{$tabs_end}

	<p class="pageback">
		<a class="pageback" href="{$backurl}">&#171; {lang('back')}</a>
	</p>

</div>
