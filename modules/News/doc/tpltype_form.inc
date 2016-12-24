<style type="text/css">
	ul.helptext {
	  list-style-type: disc;
	  margin-left: 1em;
	  margin-bottom: 1em;
	}
</style>

<h2>News form template help</h2>

<div class="information">Templates of this type are used by the fesubmit action of the News module. <strong>Note: This action is deprecated</strong>.</div>

<h3>Assigned Variables</h3>
<ul class="helptext">
  <li><code>mod</code> (News) - A reference to the News module object.</li>
  <li><code>actionid</code> (string) - The action identifier string.</li>
  <li><code>message</code> (string) - After submission, this variable will contain a message to display to the user.</li>
  <li><code>error</code> (string) - After submission, this variable will contain any error message regarding the form submission.</li>
  <li><code>category_id</code> (int) - The category id of the selected category (if any).</li>
  <li><code>title</code> (string) - The user entered article title.  This will be empty until after form submission.</li>
  <li><code>categorylist</code> (array) - An array of category id's and names.  Suitable for use in a select list.</li>
  <li><code>extra</code> (string) - The user entered extra string.  This will be empty until after form submission.</li>
  <li><code>content</code> (string) - The user entered HTML content.  This will be empty until after form submission.</li>
  <li><code>summary</code> (string) - The user entered article summary.  This will be empty until after form submission.</li>
  <li><code>hide_summary</code> (bool) - Whether or not the summary field should be hidden.</li>
  <li><code>allow_summary_wysiwyg (bool) - Whether or not to allow the summary field to be a WYSIWYG.</li>
  <li><code>startdate</code> (int) - The unix timestamp of the user entered start time, if any.</li>
  <li><code>enddate</code> (int) - The unix timestamp of the user entered end time, if any.</li>
  <li><code>status</code> (string - The status of the entered article.</li>
  <li><code>customfields</code> (array of objects) - An array of simple objects that describe the custom fields that are eligible to be edited.</li>)
</ul>

<h3>Special Notes:</h3>
<p>In CMSMS, all forms must contain a few hidden inputs to aide in the handling process.  For that reason, all cmsms forms must start with the {form_start} tag, or it's equivalent created within PHP, and end with the {form_end} tag.  The {form_start} tag can take many parameters.</p>
<p>The name of all form input/select/textarea elements must be prefixed with the {$actionid} variable, and the name of the fields are important for the processing of the submitted information, so cannot be changed.</p>
<p>The factory default template distributed with the News module does not place any special requirements on the classes, or ids used within this template.</p>