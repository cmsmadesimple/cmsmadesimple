<style type="text/css">
	ul.helptext {
	  list-style-type: disc;
	  margin-left: 1em;
	  margin-bottom: 1em;
	}
</style>

<h2>News summary template help</h2>

<h3>Assigned Variables</h3>
<ul class="helptext">
  <li>$prevpage (string) - If the current page number is one, this is a translated string for the 'prevpage' key.  Otherwise, it is a link that will result in a summary view displaying the previous page</li>
  <li>$prevurl (string) - If the current page number is one, this is not defined/empty.  Otherwise it is a URL that when placed in an &lt;a&gt; tag and clicked on will result in a summary view displaying the previous page.</li>
  <li>$fisstpage (string) - If the current page number is one, this is a translated string for the 'fistpage' key.  Otherwise, it is a link that will result in a summary view displaying the fist page</li>
  <li>$firsturl (string) - If the current page number is one, this is not defined/empty.  Otherwise it is a URL that when placed in an &lt;a&gt; tag and clicked on will result in a summary view displaying the first page.</li>
  <li>$nextpage (string) - If the current page number is the last page given the current criteria, this is a translated string for the 'nextpage' key.  Otherwise, it is a link that will result in a summary view displaying the next page</li>
  <li>$nexturl (string) - If the current page number is the last page givent he current criteria, this is not defined/empty.  Otherwise it is a URL that when placed in an &lt;a&gt; tag and clicked on will result in a summary view displaying the next page.</li>
  <li>$lastpage (string) - If the current page number is the last page given the current criteria, this is a translated string for the 'nextpage' key.  Otherwise, it is a link that will result in a summary view displaying the last page</li>
  <li>$lasturl (string) - If the current page number is the last page givent he current criteria, this is not defined/empty.  Otherwise it is a URL that when placed in an &lt;a&gt; tag and clicked on will result in a summary view displaying the last page.</li>
  <li>$pagenumber (int) - The current page number.  Starting at 1.</li>
  <li>$pagecount (int) - The total number of pages to display, given the current criteria.</li>
  <li>$oftext (string) -  The translated string for &quot;of&quot;</li>
  <li>$pagetext (string) - The translated string for &quot;page&quot;</li>
  <li>$itemcount (int) - The total number of items that will be displayed in this view (count of the $items array)</li>
  <li>$items - (object[]) - An array of objects representing the news articles to be displayed.</li>
  <li>$category_name (string) - If a category id or category name was passed to the call to this module, then this variable will contain the name of the specified category.  Otherwise, it will be empty.</li>
  <li>$cats - (array) - An array of category information representing all categories in the News module.</li>
</ul>

<h3>News article objects</h3>
<p>The $items array contains an array of news article objects.  Below are the members.</p>
<p>Because the news article objects, are objects, you address them like: {$entry->id} etc.</li>
<ul class="helptext">
  <li>author_id (int) - The uid of the author.  If greater than 0 an admin user account is assumed.  If less than zero an FEU user account is assumed.  A value of 0 indicates an anonymous user.</li>
  <li>author (string) The username of the author... or unknown</li>
  <li>authorname (string) - For admin user ids, this is a concatenation of the users first and last names, if they exist.  This is undefined for FEU entries.</li>
  <li>id (int) - The numerical id of the article.</li>
  <li>title (string) - The title of the article.</li>
  <li>content (string) - The HTML content for the article.</li>
  <li>summary (string) - The article summary.</li>
  <li>postdate (string) - The article post date.</li>
  <li>extra (string) - Optional, extra information.</li>
  <li>startdate (string) - Optional, the start date of the article.</li>
  <li>enddate (string) - Optional, the end date of the article.</li>
  <li>create_date (string) - The date the article was first created.</li>
  <li>modified_date (string) - The date the article was last modified.</li>
  <li>category (string) - The article's category name.<li>
  <li>fields (associative) - An associative array of field objects, representing the fields, and their values for this article.  See the information below on the field object definition.   In past versions of News this was a simple array, now it is an associative one.</li>
  <li>fieldsbyname (associative) (deprecated) - A synonym for the 'fields' member</li>
  <li>file_location (string) - The URL prefix to where files for this article are stored.</li>
  <li>detail_url (string) - The URL to the detail view of the article.</li>
  <li>link (string) (deprecated) - The URL to the detail view of the article.</li>
  <li>titlelink (string) (deprecated) - A link (usign the article title as the text) to the detail view of the article.</li>
  <li>morelink (string) (deprecated) - A link (using a translation of the word &quot;more&quot; as the text), to the detail view of the article.</li>
  <li>moreurl (string) (deprecated) - A URL to the detail view of the article.
</ul>

<h3>Category Information</h3>
<p>The following section illustrates the important elements of the category array.  Other items exist but are used for internal organizational purposes.</p>
<p>This information is deprecated, as it could be retrieved by calling the browsecat action.</li>
<p>Note, each of the members of the $cats array is an associiative array of it's own.  You address it like {$cats[0]['news_category_name']} etc.</p>
<ul class="helptext">
   <li>news_category_id (int) - The id of this category entry.</li>
   <li>news_category_name (string) - The name of this category entry.</li>
   <li>parent_id (int) - The id of the parent of this entry.  -1 indicates no parent.</li>
   <li>count (int) - A count of the number of displayable articles in this category.</li>
   <li>prevdepth (int) - The depth (from the root) of the previous node.</li>
   <li>depth (int) - The (from the root category) of the current node.</li>
   <li>url (string) - A URL to a summary view of all items in this category.</li>
</ul>