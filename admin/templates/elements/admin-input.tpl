<div class="row{if isset($params.class)} {$params.class}{/if}">
	<label for="{$params.id}">
		{lang string=$params.label}
	</label>
	{$input_html}{if isset($params.tooltip)}<span class="tooltip_info">{lang string=$params.tooltip}</span>{/if}
</div>