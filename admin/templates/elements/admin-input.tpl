<div class="row{if isset($params.class)} {$params.class}{/if}">
	<label for="{$params.id}">
		{lang string=$params.label}
	</label>
	{$input_html}
</div>