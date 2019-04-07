<h3>{$mod->Lang('view_article')}</h3>

{form_start article=$article->id}
<div class="c_full cf">
   {if $article->status == $article::STATUS_NEEDSAPPROVAL}
   <input type="submit" name="setpublished" value="{$mod->Lang('set_status_published')}"/>
   {elseif $article->status == $article::STATUS_PUBLISHED}
   <input type="submit" name="setneedsapproval" value="{$mod->Lang('set_status_approve')}"/>
   {/if}
   <input type="submit" name="cancel" value="{$mod->Lang('cancel')}"/>
</div>
{form_end}

{tab_header name='content' label=$mod->Lang('tab_content')}
{if !empty($fielddef_list)}
    {tab_header name='fields' label=$mod->Lang('tab_fields')}
{/if}
{tab_header name='more' label=$mod->Lang('tab_more')}

{tab_start name='content'}
<div class="c_full cf">
   <label class="grid_3">{$mod->Lang('lbl_title')}</label>
   <div class="grid_8">{$article->title}</div>
</div>
{if $settings->editor_summary_enabled}
<div class="c_full cf">
   <label class="grid_3">{$mod->Lang('lbl_summary')}</label>
   <div class="grid_8">{$article->summary}</div>
</div>
{/if}
<div class="c_full cf">
   <label class="grid_3">{$mod->Lang('lbl_content')}</label>
   <div class="grid_8">{$article->content}</div>
</div>
<div class="c_full cf">
   <label class="grid_3">{$mod->Lang('lbl_category')}</label>
   <div class="grid_8">
   	{if $article->category_id < 1}
	   {$mod->Lang('none')}
	{else}
	   {$category->long_name}
	{/if}
   </div>
</div>


{if !empty($fielddef_list)}
    {tab_start name='fields'}
    {foreach $article->fields as $fldname => $value}
         {$fielddef=$fielddef_list[$fldname]}
	 {$type=$fielddef->short_type}
	 <div class="c_full cf">
	      {if $fldname == '___foo'}
	         {* an example of how to format fields of a specific name *}
		 {* you can also address them outside of the foreach loop like: $article->fields['foo'] *}
	         <label class="grid_3">{$fielddef->label}</label>
		 <div class="grid_8">{$value|cms_date_format}</div>
	      {elseif $type == 'TextAreaFieldType'}
	         {* an example of how to format fields of a specific type *}
	         <label class="grid_3">{$fielddef->label}</label>
		 <div class="grid_8">{$value|nl2br}</div>>
	      {elseif $type == 'AttachmentFieldType'}
	         <label class="grid_3">{$fielddef->label}</span>
		 <div class="grid_8"><a href="uploads/{$value}">{$value}</a></div>
	      {elseif $type == 'SeparatorFieldType'}
	         {* do not render here *}
	      {elseif $type == 'StaticFieldType'}
	         [* do not render here *]
	      {elseif $type == 'SelectFieldType'}
	         <label class="grid_3">{$fielddef->label}</label>
		 <div class="grid_8">{$fielddef->options[$value]} <em>({$value})</em></div>
	      {elseif $type == 'MultiSelectFieldType'}
	         {$tmp=[]}
		 {foreach $value as $val}
		    {$tmp[]=$fielddef->options[$val]}
		 {/foreach}
	         <label class="grid_3">{$fielddef->label}</label>
		 <div class="grid_8">{implode('<br/>',$tmp)}</div>
	      {elseif $type == 'RelatedArticlesFieldType'}
	         <label class="grid_3">{$fielddef->label}</label>
	         <div class="grid_8">
		    {*PressRoom idlist=$value template='related_articles_sub.tpl'*}
		 </div>
	      {else}
	         <label class="grid_3">{$fielddef->label}</label>
		 <div class="grid_8">{$value}</div>
	      {/if}
	 </div>
    {/foreach}
{/if}

{tab_start name='more'}
<div class="c_full cf">
     <label class="grid_3">{$mod->Lang('lbl_newsdate')}</label>
     <div class="grid_8">{$article->news_date|date_format:'%x %H:%M'}</div>
</div>
<div class="c_full cf">
     <label class="grid_3">{$mod->Lang('lbl_starttime')}</label>
     <div class="grid_8">
         {if $article->start_time > 0}
             {$article->start_time|date_format:'%x %H:%M'}
	 {else}
	     --
	 {/if}
     </div>
</div>
<div class="c_full cf">
     <label class="grid_3">{$mod->Lang('lbl_endtime')}</label>
     <div class="grid_8">
         {if $article->end_time > 0}
             {$article->end_time|date_format:'%x %H:%M'}
	 {else}
	     --
	 {/if}
     </div>
</div>
<div class="c_full cf">
     <label class="grid_3">{$mod->Lang('lbl_status')}</label>
     <div class="grid_8">
         {$status_list[$article->status]}
     </div>
</div>
<div class="c_full cf">
     <label class="grid_3">{$mod->Lang('lbl_urlslug')}</label>
     <div class="grid_8">{$article->url_slug|default:'--'}</div>
</div>
<div class="c_full cf">
     <label class="grid_3">{$mod->Lang('lbl_searchable')}</label>
     <div class="grid_8">{if $article->searchable}{lang('yes')}{else}{lang('no')}{/if}</div>
</div>
<hr/>
<div class="c_full cf">
     <label class="grid_3">{$mod->Lang('lbl_searchable')}</label>
     <div class="grid_8">{if $article->searchable}{lang('yes')}{else}{lang('no')}{/if}</div>
</div>
<div class="c_full cf">
     <label class="grid_3">{$mod->Lang('lbl_author')}</label>
     <div class="grid_8">{cms_module module=PressRoom action=admin_user uid=$article->author_id}</div>
</div>
<div class="c_full cf">
     <label class="grid_3">{$mod->Lang('lbl_createdate')}</label>
     <div class="grid_8">{$article->create_date|date_format:'%x %H:%M'}</div>
</div>
<div class="c_full cf">
     <label class="grid_3">{$mod->Lang('lbl_modifieddate')}</label>
     <div class="grid_8">
        {if $article->modified_date}
	    {$article->modified_date|relative_time}
	{else}
	    --
	{/if}
     </div>
</div>


{tab_end}