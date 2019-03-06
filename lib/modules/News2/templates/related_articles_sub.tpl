{* related articles sub view *}
   <strong>Related Articles</strong>
   {foreach $articles as $article}
      <section class="news2-article-sub" class="row">
         <h4>{$article->title}</h4>
	 {if $article->summary}
	     <div class="news2-article-summary">{$article->summary}</div>
	 {else}
	     <div class="news2-article-summary">{$article->content|strip_tags|nl2br}</div>
	 {/if}

	 <div class="news2-article-postdate">
	      <span class="news2-prompt">{$mod->Lang('lbl_newsdate')}:</span>
	      <span class="news2-date">{$article->news_date|cms_date_format}</span>
	 </div>

	 {if $article->category_id > 0}
  	 <div class="news2-article-category">
	      <span class="news2-prompt">{$mod->Lang('lbl_category')}:</span>
	      <span class="news2-value">{news2_category_name catid=$article->category_id}</span>
	 </div>
	 {/if}

	 {if $article->fields}
	     <div class="news2-article-fields">
	     {foreach $article->fields as $fldname => $value}
	         {$fielddef=$fielddefs[$fldname]}
		 {$type=$fielddef->short_type}
		 <div class="news2-article-field">
		      {if $type == 'RelatedArticlesFieldType'}
			 {* do nothing *}
		      {elseif $fldname == 'foo'}
		         <span class="news2-prompt">{$fielddef->label}</span>
			 <span class="news2-date">{$value|cms_date_format}</span>
		      {elseif $type == 'TextAreaFieldType'}
		         <h5 class="news2-prompt">{$fielddef->label}</h5>
			 <div class="news2-value">{$value|nl2br}</div>
		      {elseif $type == 'AttachmentFieldType'}
		         <span class="news2-prompt">{$fielddef->label}</span>
			 <a class="news2-value" href="uploads/{$value}">{$value}</a>
		      {elseif $type == 'SeparatorFieldType'}
		      {elseif $type == 'StaticFieldType'}
		      {elseif $type == 'SelectFieldType'}
		         <span class="news2-prompt">{$fielddef->label}</span>
			 <span class="news2-value">{$fielddef->options[$value]}</span>
		      {elseif $type == 'MultiSelectFieldType'}
			 {foreach $value as $val}
			    {$tmp[]=$fielddef->options[$val]}
			 {/foreach}
		         <span class="news2-prompt">{$fielddef->label}</span>
			 <span class="news2-value">{implode(', ',$tmp)}</span>
		      {else}
		         <pre>{$fielddef->type}</pre>
		         <span class="news2-prompt">{$fielddef->label}</span>
			 <span class="news2-value">{$value}</span>
		      {/if}
		 </div>
	     {/foreach}{* field *}
	     </div>
	 {/if}{* have fields *}
      </section>
   {/foreach}{* article *}
