{* the canonical URL is the one true URL for an item.
   Use caution when not using URL slugs, and a single article can be displayed from multiple paths, or on multiple different pages
*}
{cms_action_url action=detail article=$article->id assign='this_url'}
{cms_pagestr->set canonical=$this_url}
{cms_pagestr->set pagetitle=$article->title}

      <section class="news2-article news2-article-detail row">
         <h4 class="news2-article-title">{$article->title}</h4>
	 <div class="news2-article-backlink">
	    {*
	       note: there may have been many ways where a user got to this detail view.
	       so a builtin return page is not reliable.
	       you could use the browser history, or redirect to some other page.
             *}
	    <a href="{cms_selflink href=$page_alias}">Go Back</a>
	 </div>

	 {if $article->summary}
	     <div class="news2-article-summary">{$article->summary}</div>
	 {/if}
	 <div class="news2-article-summary">{$article->content|strip_tags|nl2br}</div>

	 <div class="news2-article-postdate">
	      <span class="news2-prompt">{$mod->Lang('lbl_newsdate')}:</span>
	      <span class="news2-date">{$article->news_date|cms_date_format}</span>
	 </div>

	 {if $article->category_id > 0 && !empty($category)}
  	 <div class="news2-article-category">
	      <span class="news2-prompt">{$mod->Lang('lbl_category')}:</span>
	      <span class="news2-value">{$category->name}</span>
	 </div>
	 {/if}

	 {if $article->fields}
	     <div class="news2-article-fields">
	     {foreach $article->fields as $fldname => $value}
	         {$fielddef=$fielddefs[$fldname]}
		 {$type=$fielddef->short_type}
		 <div class="news2-article-field">
		      {if $fldname == 'foo'}
		         {* an example of how to format fields of a specific name *}
			 {* you can also address them outside of the foreach loop like: $article->fields['foo'] *}
		         <span class="news2-prompt">{$fielddef->label}</span>
			 <span class="news2-date">{$value|cms_date_format}</span>
		      {elseif $type == 'TextAreaFieldType'}
		         {* an example of how to format fields of a specific type *}
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
		         {$tmp=[]}
			 {foreach $value as $val}
			    {$tmp[]=$fielddef->options[$val]}
			 {/foreach}
		         <span class="news2-prompt">{$fielddef->label}</span>
			 <span class="news2-value">{implode(', ',$tmp)}</span>
		      {elseif $type == 'RelatedArticlesFieldType'}
		         <p><strong>-Related articles -</strong></p>
		         {News2 idlist=$value template='related_articles_sub.tpl'}
		      {else}
		         <span class="news2-prompt">{$fielddef->label}</span>
			 <span class="news2-value">{$value}</span>
		      {/if}
		 </div>
	     {/foreach}{* field *}
	     </div>
	 {/if}{* have fields *}

         {* display next and previous articles *}
	 {news2_nextarticle id=$article->id assign='next_article'}
	 {news2_prevarticle id=$article->id assign='prev_article'}
	 {if $prev_article}
	    <div class="news2-article-prev">
	       <span>Previous article:</span>
	       <a href="{cms_action_url action=detail article=$prev_article->id detailtemplate=$smarty.template}">
	          {$prev_article->title}
	       </a>
	    </div>
	 {/if}
	 {if $next_article}
	    <div class="news2-article-next">
	       <span>Next article:</span>
	       <a href="{cms_action_url action=detail article=$next_article->id detailtemplate=$smarty.template}">
	          {$next_article->title}
	       </a>
	    </div>
	 {/if}

      </section>
