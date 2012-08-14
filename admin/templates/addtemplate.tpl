<div class="pagecontainer">
	{$themeobj->ShowHeader('addtemplate')}
	<form method="post" action="{$formurl}">
		<div class="pageoverflow">
			<p class="pagetext">*{lang('name')}:</p>
			<p class="pageinput"><input class="name" type="text" name="template" maxlength="255" value="{$template}" /></p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext">*{lang('content')}:</p>

			<p class="pageinput">
      <textarea name="content" class="pagebigtextarea">{$content}</textarea>
      </p>
		</div>
		{*<?php if ($templateops->StylesheetsUsed() > 0) { ?>
		<div class="pageoverflow">
			<p class="pagetext"><?php echo lang('stylesheet')?>:</p>
			<p class="pageinput"><?php echo create_textarea(false, $stylesheet, 'stylesheet', 'pagebigtextarea', '', '', '', '80', '15','','css')?></p>
		</div>
		<?php } ?>
    *}
		<div class="pageoverflow">
			<p class="pagetext">{lang('active')}:</p>
			<p class="pageinput">
        <input class="pagecheckbox" type="checkbox" name="active" checked />
      </p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext">&nbsp;</p>
			<p class="pageinput">
				{*<input type="hidden" name="from" value="{$from}" />*}
				<input type="hidden" name="addtemplate" value="true"/>
				<!--<input type="submit" name="preview" value="<?php echo lang('preview')?>" class="pagebutton"  />-->
				<input type="submit" name="submit" value="{lang('submit')}" class="pagebutton" />
				<input type="submit" name="cancel" value="{lang('cancel')}" class="pagebutton" />
			</p>
		</div>
	</form>
</div>

<p class="pageback"><a class="pageback" href="{$themeobj->BackUrl()}">&#171; {lang('back')}</a></p>