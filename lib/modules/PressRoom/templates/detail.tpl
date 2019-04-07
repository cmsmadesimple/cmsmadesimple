{* the canonical URL is the one true URL for an item.
   Use caution when not using URL slugs, and a single article can be displayed from multiple paths, or on multiple different pages
*}
{cms_action_url action=detail article=$article->id assign='this_url'}
{cms_pagestr->set canonical=$this_url}
{cms_pagestr->set pagetitle=$article->title}

      <section class="pressroom-article pressroom-article-detail row">
         <h4 class="pressroom-article-title">{$article->title}</h4>
	 <div class="pressroom-article-backlink">
	    {*
	       note: there may have been many ways where a user got to this detail view.
	       so a builtin return page is not reliable.
	       you could use the browser history, or redirect to some other page.
	       <a href="{cms_selflink href=$page_alias}">Go Back</a>
             *}
	     <a href="javascript:history.back()">Go Back</a>
	 </div>

	 {if $article->summary}
	     <div class="pressroom-article-summary">{$article->summary}</div>
	 {/if}
	 <div class="pressroom-article-summary">{$article->content|strip_tags|nl2br}</div>

	 <div class="pressroom-article-postdate">
	      <span class="pressroom-prompt">{$mod->Lang('lbl_newsdate')}:</span>
	      <span class="pressroom-date">{$article->news_date|cms_date_format}</span>
	 </div>

	 {if $article->category_id > 0 && !empty($category)}
  	 <div class="pressroom-article-category">
	      <span class="pressroom-prompt">{$mod->Lang('lbl_category')}:</span>
	      <span class="pressroom-value">{$category->name}</span>
	 </div>
	 {/if}

	 {if $article->fields}
	     <div class="pressroom-article-fields">
	     {foreach $article->fields as $fldname => $value}
	         {$fielddef=$fielddefs[$fldname]}
		 {$type=$fielddef->short_type}
		 <div class="pressroom-article-field">
		      {if $fldname == 'foo'}
		         {* an example of how to format fields of a specific name *}
			 {* you can also address them outside of the foreach loop like: $article->fields['foo'] *}
		         <span class="pressroom-prompt">{$fielddef->label}</span>
			 <span class="pressroom-date">{$value|cms_date_format}</span>
		      {elseif $type == 'TextAreaFieldType'}
		         {* an example of how to format fields of a specific type *}
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
		         {$tmp=[]}
			 {foreach $value as $val}
			    {$tmp[]=$fielddef->options[$val]}
			 {/foreach}
		         <span class="pressroom-prompt">{$fielddef->label}</span>
			 <span class="pressroom-value">{implode(', ',$tmp)}</span>
		      {elseif $type == 'RelatedArticlesFieldType'}
		         <p><strong>-Related articles -</strong></p>
		         {PressRoom idlist=$value template='related_articles_sub.tpl'}
		      {else}
		         <span class="pressroom-prompt">{$fielddef->label}</span>
			 <span class="pressroom-value">{$value}</span>
		      {/if}
		 </div>
	     {/foreach}{* field *}
	     </div>
	 {/if}{* have fields *}

         {* display next and previous articles *}
	 {pressroom_nextarticle id=$article->id assign='next_article'}
	 {pressroom_prevarticle id=$article->id assign='prev_article'}
	 {if $prev_article}
	    <div class="pressroom-article-prev">
	       <span>Previous article:</span>
	       <a href="{cms_action_url action=detail article=$prev_article->id detailtemplate=$smarty.template}">
	          {$prev_article->title}
	       </a>
	    </div>
	 {/if}
	 {if $next_article}
	    <div class="pressroom-article-next">
	       <span>Next article:</span>
	       <a href="{cms_action_url action=detail article=$next_article->id detailtemplate=$smarty.template}">
	          {$next_article->title}
	       </a>
	    </div>
	 {/if}

      </section>
