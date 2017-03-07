{* set a canonical variable that can be used in the head section if process_whole_template is false in the config.php *}
{if isset($entry->canonical)}
  {* note this syntax ensures that the canonical variable is set into global scope *}
  {assign var='canonical' value=$entry->canonical scope=global}
{/if}

{if $entry->postdate}
	<div id="NewsPostDetailDate">
		{$entry->postdate|cms_date_format}
	</div>
{/if}
<h3 id="NewsPostDetailTitle">{$entry->title|cms_escape:htmlall}</h3>

<hr id="NewsPostDetailHorizRule" />

{if $entry->summary}
	<div id="NewsPostDetailSummary">
		<strong>
			{$entry->summary}
		</strong>
	</div>
{/if}

{if $entry->category}
	<div id="NewsPostDetailCategory">
		{$category_label} {$entry->category}
	</div>
{/if}
{if $entry->author}
	<div id="NewsPostDetailAuthor">
		{$author_label} {$entry->author}
	</div>
{/if}

<div id="NewsPostDetailContent">
        {* note, for security purposes we do not pass the content through smarty before displaying it.  This is incase your articles can come from untrusted sources. *}
	{$entry->content}
</div>

{if $entry->extra}
	<div id="NewsPostDetailExtra">
		{$extra_label} {$entry->extra}
	</div>
{/if}

{if $return_url != ""}
<div id="NewsPostDetailReturnLink">{$return_url}{if $category_name != ''} - {$category_link}{/if}</div>
{/if}

{if isset($entry->fields)}
  {foreach $entry->fields as $fieldname => $field}
     <div class="NewsDetailField">
        {if $field->type == 'file'}
	  {* this template assumes that every file uploaded is an image of some sort, because News doesn't distinguish *}
          {if isset($field->value) && $field->value}
            <img src="{$entry->file_location}/{$field->value}" alt="{$field->value}"/>
          {/if}
        {elseif $field->type == 'linkedfile'}
          {* also assume it's an image... *}
          {if !empty($field->value)}
            <img src="{file_url file=$field->value}" alt="{$field->value}"/>
          {/if}
        {else}
          {$field->name}:&nbsp;{$field->value}
        {/if}
     </div>
  {/foreach}
{/if}
