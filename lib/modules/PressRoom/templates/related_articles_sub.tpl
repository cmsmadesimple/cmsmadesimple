{* related articles sub view *}
   <strong>Related Articles</strong>
   {foreach $articles as $article}
      <section class="pressroom-article-sub" class="row">
         <h4>{$article->title}</h4>
	 {if $article->summary}
	     <div class="pressroom-article-summary">{$article->summary}</div>
	 {else}
	     <div class="pressroom-article-summary">{$article->content|strip_tags|nl2br}</div>
	 {/if}

	 <div class="pressroom-article-postdate">
	      <span class="pressroom-prompt">{$mod->Lang('lbl_newsdate')}:</span>
	      <span class="pressroom-date">{$article->news_date|cms_date_format}</span>
	 </div>

	 {if $article->category_id > 0}
  	 <div class="pressroom-article-category">
	      <span class="pressroom-prompt">{$mod->Lang('lbl_category')}:</span>
	      <span class="pressroom-value">{pressroom_category_name catid=$article->category_id}</span>
	 </div>
	 {/if}

	 {if $article->fields}
	     <div class="pressroom-article-fields">
	     {foreach $article->fields as $fldname => $value}
	         {$fielddef=$fielddefs[$fldname]}
		 {$type=$fielddef->short_type}
		 <div class="pressroom-article-field">
		      {if $type == 'RelatedArticlesFieldType'}
			 {* do nothing *}
		      {elseif $fldname == 'foo'}
		         <span class="pressroom-prompt">{$fielddef->label}</span>
			 <span class="pressroom-date">{$value|cms_date_format}</span>
		      {elseif $type == 'TextAreaFieldType'}
		         <h5 class="pressroom-prompt">{$fielddef->label}</h5>
			 <div class="pressroom-value">{$value|nl2br}</div>
		      {elseif $type == 'AttachmentFieldType'}
		         <span class="pressroom-prompt">{$fielddef->label}</span>
			 <a class="pressroom-value" href="uploads/{$value}">{$value}</a>
		      {elseif $type == 'SeparatorFieldType'}
		      {elseif $type == 'StaticFieldType'}
		      {elseif $type == 'SelectFieldType'}
		         <span class="pressroom-prompt">{$fielddef->label}</span>
			 <span class="pressroom-value">{$fielddef->options[$value]}</span>
		      {elseif $type == 'MultiSelectFieldType'}
			 {foreach $value as $val}
			    {$tmp[]=$fielddef->options[$val]}
			 {/foreach}
		         <span class="pressroom-prompt">{$fielddef->label}</span>
			 <span class="pressroom-value">{implode(', ',$tmp)}</span>
		      {else}
		         <pre>{$fielddef->type}</pre>
		         <span class="pressroom-prompt">{$fielddef->label}</span>
			 <span class="pressroom-value">{$value}</span>
		      {/if}
		 </div>
	     {/foreach}{* field *}
	     </div>
	 {/if}{* have fields *}
      </section>
   {/foreach}{* article *}
