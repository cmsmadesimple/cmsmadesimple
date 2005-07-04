<?php
$lang['addarticle'] = 'Lisää Artikkeli';
$lang['addcategory'] = 'Lisää Aihe';
$lang['addnewsitem'] = 'Lisää uutis otsikko';
$lang['allcategories'] = 'Kaikki Aiheet';
$lang['allentries'] = 'Kaikki Entries'; // EDIT
$lang['areyousure'] = 'Oletko varma että haluat poistaa?';
$lang['articles'] = 'Artikkelit';
$lang['cancel'] = 'Peruuta';
$lang['category'] = 'Aihe';
$lang['categories'] = 'Aiheet';
$lang['content'] = 'Sisältö';
$lang['dateformat'] = '%s not in a valid yyyy-mm-dd hh:mm:ss format';
$lang['delete'] = 'Poista';
$lang['description'] = 'Lisää, muokkaa ja poista Uutisaiheita';
$lang['detailtemplate'] = 'Detail Template';  // EDIT
$lang['displaytemplate'] = 'Näytä mallipohja';
$lang['edit'] = 'Muokkaa';
$lang['enddate'] = 'Muokkaa Päivämäärä';
$lang['endrequiresstart'] = 'Jos syötät loppupäivämäärän, sinun tulee syöttää myös aloitus';
$lang['entries'] = '%s Entries';
$lang['expiry'] = 'Expriry';
$lang['filter'] = 'Suodatin';
$lang['more'] = 'Lisää';
$lang['moretext'] = 'Lisää Tekstiä';
$lang['name'] = 'Nimi';
$lang['news'] = 'Uutiset';
$lang['newcategory'] = 'Uutis Aihe';
$lang['needpermission'] = 'Tarvitset \'%s\' oikeudet jotta voit käyttää tätä toimintoa.';
$lang['nocategorygiven'] = 'Et antanut aihetta';
$lang['nocontentgiven'] = 'Et antanut sisältöä';
$lang['noitemsfound'] = '<strong>No</strong> items found for category: %s'; // EDIT
$lang['nopostdategiven'] = 'No Post Date Given'; // EDIT
$lang['note'] = '<em>Note:</em> Dates must be in a \'yyyy-mm-dd hh:mm:ss\' format.'; // EDIT
$lang['notitlegiven'] = 'No Title Given'; // EDIT
$lang['numbertodisplay'] = 'Number to Display (empty shows all records)'; // EDIT
$lang['print'] = 'Print'; // EDIT
$lang['postdate'] = 'Post Date'; // EDIT
$lang['postinstall'] = 'Make sure to set the "Modify News" permission on users who will be administering News items.'; // EDIT
$lang['rsstemplate'] = 'RSS Template'; // EDIT
$lang['selectcategory'] = 'Select Category'; // EDIT
$lang['sortascending'] = 'Sort Ascending'; // EDIT
$lang['startdate'] = 'Start Date'; // EDIT
$lang['startrequiresend'] = 'Entering a start date requires an end date also'; // EDIT
$lang['submit'] = 'Submit'; // EDIT
$lang['summary'] = 'Summary'; // EDIT
$lang['summarytemplate'] = 'Summary Template'; // EDIT
$lang['title'] = 'Title'; // EDIT
$lang['useexpiration'] = 'Use Expiration Date'; // EDIT
$lang['help'] = <<<EOF
	<h3>What does this do?</h3>
	<p>News is a module for displaying news events on your page, similar to a blog style, except with more features!.  When the module is installed, a News admin page is added to administration menu that will allow you to select or add a news category.  Once a news category is created or selected, a list of news items for that category will be displayed.  From here, you can add, edit or delete news items for that category.</p>
	<h3>Security</h3>
	<p>The user must belong to a group with the 'Modify News' permission in order to add, edit, or delete News entries.</p>
	<h3>How do I use it?</h3>
	<p>The easiest way to use it is in conjunction with the cms_module tag.  This will insert the module into your template or page anywhere you wish, and display news items.  The code would look something like: <code>{cms_module module="news" number="5" category="beer"}</code></p>
	<h3>What Parameters Exist?</h3>
	<p>
	<ul>
	<li><em>(optional)</em> number="5" - Maximum number of items to display =- leaving empty will show all items</li>
	<li><em>(optional)</em> makerssbutton="true" - Make a button to link to an RSS feed of the News items.</li>
	<li><em>(optional)</em> category="category" - Only display items for that category and it's children.  leaving empty, will show all categories</li>
	<li><em>(optional)</em> moretext="more..." - Text to display at the end of a news item if it goes over the summary length.  Defaults to "more...".</li>
	<li><em>(optional}</em> summarytemplate="sometemplate.tpl" - Use a separate template for displaying the article summary.  It have to live in modules/News/templates.
	<li><em>(optional}</em> detailtemplate="sometemplate.tpl" - Use a separate template for displaying the article detail.  It have to live in modules/News/templates.
	<li><em>(optional)</em> sortby="news_date" - Field to sort by.  Options are: "news_date", "summary", "news_data", "news_category", "news_title".  Defaults to "news_date".</li>
	<li><em>(optional)</em> sortasc="true" - Sort news items in ascending date order rather than descending.</li>
	</ul>
	</p>
EOF;
?>
