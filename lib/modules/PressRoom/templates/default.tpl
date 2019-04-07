{* original summary view *}
{if !count($articles)}
   <div class="alert alert-info">{$mod->Lang('warn_nomatching_articles')}</div>
{else}
   {if $articles->pagecount > 1}
       <div class="pressroom-pagination" style="float: right;">
          {$pages=$articles->pageList()}
	  {$mod->Lang('lbl_page')};
	  {foreach $pages as $pagenum}
	     <a {if $pagenum == $articles->page}class="active"{/if} href="{cms_action_url args=$params_str category_id=$actionparams.category_id|default:0 news_page=$pagenum inline=1}"/>{$pagenum}</a>
	  {/foreach}
       </div>
   {/if}
   {foreach $articles as $article}
      <section class="pressroom-article" class="row">
         <h4 class="pressroom-article-title">{$article->title}</h4>
	 {cms_action_url action=detail article=$article->id assign='detail_url'}
	 <div>
	   <a href="{$detail_url}">{$mod->Lang('view_more')}</a>
	 </div>

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
	      <span class="pressroom-value">
	          <a href="{cms_action_url action=default category_id=$article->category_id}">{pressroom_category_name catid=$article->category_id}</a>
              </span>
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
		         {* don't want to display this in a summary view *}
		      {else}
		         <span class="pressroom-prompt">{$fielddef->label}</span>
			 <span class="pressroom-value">{$value}</span>
		      {/if}
		 </div>
	     {/foreach}{* field *}
	     </div>
	 {/if}{* have fields *}
      </section>
   {/foreach}{* article *}
{/if}