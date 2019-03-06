{* original summary view *}
{if !count($articles)}
   <div class="alert alert-info">{$mod->Lang('warn_nomatching_articles')}</div>
{else}
   {if $articles->pagecount > 1}
       <div class="news2-pagination" style="float: right;">
          {$pages=$articles->pageList()}
	  {$mod->Lang('lbl_page')};
	  {foreach $pages as $pagenum}
	     <a href="{cms_action_url args=$params_str news_page=$pagenum}"/>{$pagenum}</a>
	  {/foreach}
       </div>
   {/if}
   {foreach $articles as $article}
      <section class="news2-article" class="row">
         <h4 class="news2-article-title">{$article->title}</h4>
	 {cms_action_url action=detail article=$article->id assign='detail_url'}
	 <div>
	   <a href="{$detail_url}">{$mod->Lang('view_more')}</a>
	 </div>

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
		         {* don't want to display this in a summary view *}
		      {else}
		         <span class="news2-prompt">{$fielddef->label}</span>
			 <span class="news2-value">{$value}</span>
		      {/if}
		 </div>
	     {/foreach}{* field *}
	     </div>
	 {/if}{* have fields *}
      </section>
   {/foreach}{* article *}
{/if}