<h3>{$mod->Lang('edit_profile')}: <em>{$data.label}</em></h3>

{form_start}
	<input type="hidden" name="{$actionid}profile" value="{$profile}"/>
	<input type="hidden" name="{$actionid}origname" value="{$data.name}"/>

	{if $data.system}<div class="information">{$tmp='profiledesc_'|cat:$data.name}{$mod->Lang($tmp)}</div>{/if}

	<div class="pageoverflow">
		<p class="pagetext"></p>
		<p class="pageinput">
			<input type="submit" name="{$actionid}submit" value="{$mod->Lang('submit')}"/>
			<input type="submit" name="{$actionid}cancel" value="{$mod->Lang('cancel')}"/>
		</p>
	</div>

	{if !$data.system}
		<div class="pageoverflow">
			<p class="pagetext">
				<label for="profile_name">*{$mod->Lang('profile_name')}:</label>&nbsp;{cms_help key2='mthelp_profilename' title=$mod->Lang('profile_name')}
			</p>
			<p class="pageinput">
				<input type="text" size="40" id="profile_name" name="{$actionid}profile_name" value="{$data.name}" />
			</p>
		</div>

		<div class="pageoverflow">
			<p class="pagetext">
				<label for="profile_label">*{$mod->Lang('profile_label')}:</label>&nbsp;{cms_help key2='mthelp_profilelabel' title=$mod->Lang('profile_label')}
			</p>
			<p class="pageinput">
				<input type="text" size="80" id="profile_label" name="{$actionid}profile_label" value="{$data.label}" />
			</p>
		</div>
	{/if}

	<div class="pageoverflow">
		<p class="pagetext">
			<label for="profile_menubar">{$mod->Lang('profile_menubar')}:</label>&nbsp;{cms_help key2='mthelp_profilemenubar' title=$mod->Lang('profile_menubar')}
		</p>
		<p class="pageinput">
			<select id="profile_menubar" name="{$actionid}profile_menubar">{cms_yesno selected=$data.menubar}</select>
		</p>
	</div>

	<div class="pageoverflow">
		<p class="pagetext">
			<label for="profile_allowimages">{$mod->Lang('profile_allowimages')}:</label>&nbsp;{cms_help key2='mthelp_profileallowimages' title=$mod->Lang('profile_allowimages')}
		</p>
		<p class="pageinput">
			<select id="profile_allowimages" name="{$actionid}profile_allowimages">{cms_yesno selected=$data.allowimages}</select>
		</p>
	</div>

	<div class="pageoverflow">
		<p class="pagetext">
			<label for="profile_allowtables">{$mod->Lang('profile_allowtables')}:</label>&nbsp;{cms_help key2='mthelp_profileallowtables' title=$mod->Lang('profile_allowtables')}
		</p>
		<p class="pageinput">
			<select id="profile_allowtables" name="{$actionid}profile_allowtables">{cms_yesno selected=$data.allowtables}</select>
		</p>
	</div>

	<div class="pageoverflow">
		<p class="pagetext">
			<label for="profile_showstatusbar">{$mod->Lang('profile_showstatusbar')}:</label>&nbsp;{cms_help key2='mthelp_profilestatusbar' title=$mod->Lang('profile_showstatusbar')}
		</p>
		<p class="pageinput">
			<select id="profile_showstatusbar" name="{$actionid}profile_showstatusbar">{cms_yesno selected=$data.showstatusbar}</select>
		</p>
	</div>

	<div class="pageoverflow">
		<p class="pagetext">
			<label for="profile_allowresize">{$mod->Lang('profile_allowresize')}:</label>&nbsp;{cms_help key2='mthelp_profileresize' title=$mod->Lang('profile_allowresize')}
		</p>
		<p class="pageinput">
			<select id="profile_allowresize" name="{$actionid}profile_allowresize">{cms_yesno selected=$data.allowresize}</select>
		</p>
	</div>

	<div class="pageoverflow">
		<p class="pagetext">
			<label for="profile_dfltstylesheet">{$mod->Lang('profile_dfltstylesheet')}:</label>&nbsp;{cms_help key2='mthelp_dfltstylesheet' title=$mod->Lang('profile_dfltstylesheet')}
		</p>
		<p class="pageinput">
			<select id="profile_dfltstylesheet" name="{$actionid}profile_dfltstylesheet">
				{html_options options=$stylesheets selected=$data.dfltstylesheet}
			</select>
		</p>
	</div>

	<div class="pageoverflow">
		<p class="pagetext">
			<label for="profile_allowcssoverride">{$mod->Lang('profile_allowcssoverride')}:</label>&nbsp;{cms_help key2='mthelp_allowcssoverride' title=$mod->Lang('profile_allowcssoverride')}
		</p>
		<p class="pageinput">
			<select id="profile_allowcssoverride" name="{$actionid}profile_allowcssoverride">{cms_yesno selected=$data.allowcssoverride}</select>
		</p>
	</div>

	<div class="pageoverflow">
		<p class="pagetext"></p>
		<p class="pageinput">
			<input type="submit" name="{$actionid}submit" value="{$mod->Lang('submit')}"/>
			<input type="submit" name="{$actionid}cancel" value="{$mod->Lang('cancel')}"/>
		</p>
	</div>
{form_end}