{* News module entry object reference:
   ------------------------------
   In previous versions of News the 'object' returned in $entry was quite simple, and a <pre>{$entry|@print_r}</pre> would output all of the available data
   This has changed in News 2.12, the object is not quite as 'simple' as it was in previous versions, and that method will no longer work.  Hence, below
   you will find a referennce to the available data.

   ====
   news_article Object Reference
   ====

     Members:
     --
     Members can be displayed by the following syntax: {$entry->membername} or assigned to another smarty variable using {assign var='foo' value=$entry->membername}.

     The following members are available in the entry array:
   
     id (integer)           = The unique article id.
     author_id (integer)    = The userid of the author who created the article.  This value may be negative to indicate an FEU userid.
     title (string)         = The title of the article.
     summary (text)         = The summary text (may be empty or unset).
     extra (string)         = The "extra" data associated with the article (may be empty or unset).
     news_url (string)      = The url segment associated with this article (may be empty or unset).
     postdate (string)      = A string representing the news article post date.  You may filter this through cms_date_format for different display possibilities.
     startdate (string)     = A string representing the date the article should begin to appear.  (may be empty or unset)
     enddate (string)       = A string representing the date the article should stop appearing on the site (may be empty or unset).
     category_id (integer)  = The unique id of the hierarchy level where this article resides (may be empty or unset)
     status (string)        = either 'draft' or 'published' indicating the status of this article.
     author (string)        = The username of the original author of the article.  If the article was created by frontend submission, this will attempt to retrieve the username from the FEU module.
     authorname (string)    = The full name of the original author of the website. Only applicable if article was created by an administrator and that information exists in the administrators profile.
     category (string)      = The name of the category that this article is associated with.
     canonical (string)     = A full URL (prettified) to this articles detail view using defaults if necessary.
     fields (associative)   = An associative array of field objects, representing the fields, and their values for this article.  See the information below on the field object definition.   In past versions of News this was a simple array, now it is an associative one.
     customfieldsbyname     = (deprecated) - A synonym for the 'fields' member
     fieldsbyname           = (deprecated) - A synonym for the 'fields' member
     useexp (integer)       = A flag indicating wether this article is using the expiry information.
     file_location (string) = A url containing the location where files attached the article are stored... the field value should be appended to this url.
     

   ====
   news_field Object Reference
   ====
   The news_field object contains data about the fields and their values that are associated with a particular news article.

     Members:
     --------
     id (integer)  = The id of the field definition
     name (string) = The name of the field
     type (string) = The type of field
     max_length (integer) = The maximum length of the field (applicable only to text fields)
     item_order (integer) = The order of the field
     public (integer) = A flag indicating wether the field is public or not
     value (mixed)    = The value of the field.


   ====
   Below, you will find the normal detail template information.  Modify this template as desired.
*}

{* set a canonical variable that can be used in the head section if process_whole_template is false in the config.php *}
{if isset($entry->canonical)}
  {assign var='canonical' value=$entry->canonical}
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
			{eval var=$entry->summary}
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
	{eval var=$entry->content}
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
  {foreach from=$entry->fields item='field'}
     <div class="NewsDetailField">
        {if $field->type == 'file'}
	  {* this template assumes that every file uploaded is an image of some sort, because News doesn't distinguish *}
          <img src="{$entry->file_location}/{$field->value}"/>
        {else}
          {$field->name}:&nbsp;{eval var=$field->value}
        {/if}
     </div>
  {/foreach}
{/if}
