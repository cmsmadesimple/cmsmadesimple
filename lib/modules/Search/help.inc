<h3>What does this do?</h3>
<p>The "Search" is a module for searching for text within content pages, and the content of compatible modules.  Users enter a word or two and this module will return a formatted list of matching, relevant results.</p>

<h3>How do I use it?</h3>
<p>The easiest way to use it is to add the {Search} tag into a template or page content anywhere you wish.  This will, when rendered, display the search form.  The code would look something like: <code>{Search}</code></p>

<h4>How do i prevent certain content from being indexed</h4>
<p>The search module will not search any "inactive" pages. However on occasion, when you are using the CustomContent module, or other smarty logic to show different content to different groups of users, it may be advisable to prevent the entire page from being indexed even when it is live.  To do this include the following tag anywhere on the page <em>&lt;!-- pageAttribute: NotSearchable --&gt;</em> When the search module sees this tag in the page it will not index any content for that page.</p>
<p>The <em>&lt;!-- pageAttribute: NotSearchable --&gt;</em> tag can be placed in the template as well.  if this is done, none of the pages attached to that template will be indexed.  Those pages will be re-indexed if the tag is removed</p>

<h3>Frontend Actions</h3>
<ul>
    <li>default
        <p>This action is used to display a search form.  See the parameters below for the full list of possible parameters for this view.</p>
	<p>This form is automatically submitted to the dosearch action.</p>
	<ul>
	   <li>Embedding a default search form:<br/>
	       <pre><code>{Search}</code></pre>
	   </li>
	   <li>Embedding a search form that uses a different resultpage:<br/>
	       <pre><code>{Search resultpage='a-page-alias'}</code></pre>
	   </li>
	   <li>Embedding a search form to use an alternate form template, where the template is stored in /assets/module_custom/Search/templates:<br/>
	       <pre><code>{Search formtemplate='mysearchform.tpl'}</code></pre>
	   </li>
	   <li>Embedding a search form to use an alternate form template, where the template is stored in /assets/templates:<br/>
	       <pre><code>{Search formtemplate='cms_file:mysearchform.tpl'}</code></pre>
	   </li>
	   <li>Embedding a search form to use an alternate form template, where the template is stored in DesignManager:<br/>
	       <pre><code>{Search formtemplate='My Search Form'}</code></pre>
	   </li>
	</ul>
	<p>Parameters accepted:</p>
	<ul>
	   <li>formtemplate</li>
	   <li>inline</li>
	   <li>resultpage</li>
	   <li>search_method</li>
	   <li>modules</li>
	   <li>detailpage</li>
	   <li>use_like</li>
	   <li>passthru_*</li>
	</ul>
	<p>Note: unless specified below, parameters that the default action accepts are not passed to the dosearch action.  However, you can pass parameters to the dosearch action from within your form view template.</p>
    </li>

    <li>dosearch
        <p>This module action actually does the searching of the database.</p>
	<p>Parameters accepted:</p>
	<ul>
	   <li>resulttemplate</li>
	   <li>use_like</li>
	   <li>searchinput</li>
	   <li>modules</li>
	   <li>detailpage</li>
	   <li>passthru_*</li>
	</ul>
    </li>

    <li>ajax_getwords
        <p>This module action is useful with jquery autocomplete to allow searching for words that exist in the index.</p>
	<p>Request parameters accepted:</p>
	<ul>
	   <li>term - A string of one or more words, separated by spaces</li>
	   <li>limit - An integer value between 1 and 50 to limit the results to.  The default value is 10</li>
	</ul>
    </li>
</ul>

<h3>Variables available in the search form view</h3>
<ul>
    <li><code>search_actionid</code> - string <em>(deprecated)</em> - The module action id to uniquely identify this action.
       <p>Note: deprecated - use {$actionid} instead.</p>
    </li>
    <li><code>searchtext</code> - string <em>(deprecated)</em> - The text for the search field.
       <p>Note: deprecated - Place the text in your search form template.</p>
    </li>
    <li><code>destpage</code> - int - The destination page id where results will be rendered.
    </li>
    <li><code>inline</code> - bool - Whether the form results will be displayed inline, or not.
    </li>
    <li><code>label</code> - string <em>(deprecated)</em> - A text label for the search field.
       <p>Note: deprecated - Design your form template as appropriate.</p>
    </li>
    <li><code>searchprompt</code> - string <em>(deprecated)</em> - A text prompt for the search field.
       <p>Note: deprecated - Design your form template as appropriate.</p>
    </li>
    <li><code>submit</code> - string <em>(deprecated)</em> - A text string for the contents of the submit button.
       <p>Note: deprecated - Design your form template as appropriate.</p>
    </li>
    <li><code>startform</code> - string <em>(deprecated)</em> - An HTML &lt;form&lt; element to begin the search form
       <p>Note: deprecated - Use {form_start} instead.</p>
    </li>
    <li><code>endform</code> - string <em>(deprecated)</em> - An HTML &lt;/form&lt; element to terminate the search form
       <p>Note: deprecated - Use {form_end} instead.</p>
    </li>
    <li><code>hidden</code> - string <em>(deprecated)</em> - A series of HTML input elements of type hidden that may contain some data for the dosearch action.
       <p>This may be empty, and may be removed at a future date.</p>
    </li>
    <li><code>formparms</code> - array - A series of parameters that should be passed into the {form_start} method so that the dosearch action gets the required information.</li>
</ul>

<h3>Variables available in the result view</h3>
<ul>
  <li>phrase - string - The user entered search phrase.</li>
  <li>searchwords - string[] - An array of parsed, and filtered words that were searched.</li>
  <li>results - array - An array of objects representing the reslts of the search.  If this array is empty, no matches were found.</li>
  <li>itemcount - integer - The number of matches found, if any.</li>
  <li>timetook - integer - The number of milliseconds that the search operatin took.</li>
</ul>