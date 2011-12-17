<div class="pagecontainer">
	{$header}
	<form method="post" action="editprefs.php" name="prefsform">
		<div class="pageoverflow">
			<p class="pageinput" style="margin-left:0;">
				<input type="submit" name="submit_form" value="{'submit'|lang}" class="pagebutton" />
				<input type="submit" name="cancel" value="{'cancel'|lang}" class="pagebutton" />
			</p>
		</div>		
		<div class="invisible">
			<input type="hidden" name="{$SECURE_PARAM_NAME}" value="{$CMS_USER_KEY}" />
		</div>	
		<fieldset>
			<legend>
				{'lang_settings_legend'|lang}
			</legend>
			<!-- Language -->
			<div class="pageoverflow">
				<p class="pagetext">
					{'language'|lang}
				</p>
				<p class="pageinput">
					<select name="default_cms_language">
						{html_options options=$language_opts selected=$default_cms_language}
					</select>
				</p>
			</div>
			<!-- language //-->
			<!-- date format -->
			<div class="pageoverflow">
				<p class="pagetext">
					{'date_format_string'|lang}:
				</p>
				<p class="pageinput">
					<input class="pagenb" size="20" maxlength="255" type="text" name="date_format_string" value="{$date_format_string}" />
					{'date_format_string_help'|lang}
				</p>
			</div>
			<!-- date format //-->
		</fieldset>
		<fieldset>
			<legend>{'content_editor_legend'|lang}</legend>
			<!-- wysiwyg to use -->
			<div class="pageoverflow">
				<p class="pagetext">
					{'wysiwygtouse'|lang}
				</p>
				<p class="pageinput">
					<select name="wysiwyg">
						{html_options options=$wysiwyg_opts selected=$wysiwyg}
					</select>
				</p>
			</div>
			<!-- wysiwyg to use //-->
			<!-- syntaxhighlighter to use -->
			<div class="pageoverflow">
				<p class="pagetext">
					{'syntaxhighlightertouse'|lang}
				</p>
				<p class="pageinput">
					<select name="syntaxhighlighter">
						{html_options options=$syntax_opts selected=$syntaxhighlighter}
					</select>
				</p>
			</div>
			<!-- syntaxhighlighter to use //-->	
			<!-- enable gcb wysiwyg -->
			<div class="pageoverflow">
				<p class="pagetext">
					{'gcb_wysiwyg'|lang}:
				</p>
				<p class="pageinput">
					<input class="pagenb" type="checkbox" name="gcb_wysiwyg" {if $gcb_wysiwyg == 1}checked="checked"{/if} />
					{'gcb_wysiwyg_help'|lang}
				</p>
			</div>
			<!-- enable gcb wysiwyg //-->
			<!-- content display -->
			<div class="pageoverflow">
				<p class="pagetext">
					{'adminindent'|lang}:
				</p>
				<p class="pageinput">
					<input class="pagenb" type="checkbox" name="indent" {if $indent == true}checked="checked"{/if} />
					{'indent'|lang}
				</p>
			</div>
			<!-- content display //-->								
		</fieldset>
		<fieldset>
			<legend>{'admin_layout_legend'|lang}</legend>
			<!-- administration theme --> 
			<div class="pageoverflow">
				<p class="pagetext">{'admintheme'|lang}</p>
				<p class="pageinput">
					<select name="admintheme">
						{html_options options=$themes_opts selected=$admintheme}
					</select>
				</p>
			</div>
			<!-- administration theme //-->
			<!-- homepage -->
			<div class="pageoverflow">
				<p class="pagetext">{'homepage'|lang}</p>
				<p class="pageinput">
					{$homepage}
				</p>
			</div>
			<!-- homepage //-->
			<!-- bookmarks -->
			<div class="pageoverflow">
				<p class="pagetext">
					{'admincallout'|lang}:
				</p>
				<p class="pageinput">
					<input class="pagenb" type="checkbox" name="bookmarks" {if $bookmarks == true}checked="checked"{/if} />
					{'showbookmarks'|lang}
				</p>
			</div>
			<!-- bookmarks //-->
			<!-- hide module help link -->
			<div class="pageoverflow">
				<p class="pagetext">
					{'hide_help_links'|lang}:
				</p>
				<p class="pageinput">
					<input class="pagenb" type="checkbox" name="hide_help_links" {if $hide_help_links == true}checked="checked"{/if} />
					{'hide_help_links_help'|lang}
				</p>
			</div>
			<!-- hide module help link //-->
			<!-- default parent page --> 
			<div class="pageoverflow">
				<p class="pagetext">{'defaultparentpage'|lang}</p>
				<p class="pageinput">
					{$default_parent}
				</p>
			</div>
			<!-- default parent page //-->	
			<!-- number of rows per page when viewing templates --> 
			<div class="pageoverflow">
				<p class="pagetext">{'listtemplates_pagelimit'|lang}</p>
				<p class="pageinput">
					<select name="listtemplates_pagelimit">
						{html_options options=$pagelimit_opts selected=$listtemplates_pagelimit}
					</select>
				</p>
			</div>
			<!-- number of rows per page when viewing templates //-->	
			<!-- number of rows per page when viewing stylesheets --> 
			<div class="pageoverflow">
				<p class="pagetext">{'liststylesheets_pagelimit'|lang}</p>
				<p class="pageinput">
					<select name="liststylesheets_pagelimit">
						{html_options options=$pagelimit_opts selected=$liststylesheets_pagelimit}
					</select>
				</p>
			</div>
			<!-- number of rows per page when viewing stylesheets //-->	
			<!-- number of rows per page when viewing gcbs --> 
			<div class="pageoverflow">
				<p class="pagetext">{'listgcbs_pagelimit'|lang}</p>
				<p class="pageinput">
					<select name="listgcbs_pagelimit">
						{html_options options=$pagelimit_opts selected=$listgcbs_pagelimit}
					</select>
				</p>
			</div>
			<!-- number of rows per page when viewing gcbs //-->
			<!-- enable user notifications in the admin section -->
			<div class="pageoverflow">
				<p class="pagetext">
					{'enablenotifications'|lang}:
				</p>
				<p class="pageinput">
					<input class="pagenb" type="checkbox" name="enablenotifications" {if $enablenotifications == 1}checked="checked"{/if} />
				</p>
			</div>
			<!-- enable user notifications in the admin section //-->	
			<!-- ignore notifications from these modules --> 
			<div class="pageoverflow">
				<p class="pagetext">{'ignorenotificationsfrommodules'|lang}</p>
				<p class="pageinput">
					<select name="ignoredmodules[]" multiple="multiple" size="5">
						{html_options options=$module_opts selected=$ignoredmodules}
					</select>
				</p>
			</div>
			<!-- ignore notifications from these modules //-->																
		</fieldset>
		<div class="pageoverflow">
			<p class="pageinput">
				<div class="invisible">
				<input type="hidden" name="edituserprefs" value="true" />
				<input type="hidden" name="old_default_cms_lang" value="{$old_default_cms_lang}" />
                </div>				
				<input type="submit" name="submit_form" value="{'submit'|lang}" class="pagebutton" />
				<input type="submit" name="cancel" value="{'cancel'|lang}" class="pagebutton" />
			</p>
		</div>		
	</form>
	<p class="pageback">
		<a class="pageback" href="{$backurl}">&#171; {'back'|lang}</a>
	</p>
</div>
