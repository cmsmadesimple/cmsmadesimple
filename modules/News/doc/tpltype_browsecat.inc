<style type="text/css">
	ul.helptext {
	  list-style-type: disc;
	  margin-left: 1em;
	  margin-bottom: 1em;
	}
</style>

<h2>Browse category template help</h2>

<h3>Assigned Variables</h3>
<ul class="helptext">
  <li><code>cats</code> (array) - A flat array of records (each record is itself an array) that contains information about that category.
     <h4>Record Details:</h4>
     <ul>
       <li><code>news_category_id</code> (int) - The numeric id of this category.</li>
       <li><code>news_category_name</code> (string) - The name of this category.</li>
       <li><code>parent_id</code> (int) - The numeric id of this category's parent.  A value less than one indicates no parent.</li>
       <li>item_order (int) - A numeric representation of this category's order amongst its peers.</li>
       <li><code>index</code> (int) - An increasing index of the current category information.</li>
       <li><code>count</code> (int) - The number of valid, displayable articles in this category.</li>
       <li><code>url</code> (string) - A URL that will generate a summary view of all of the valid, displayable articles in this category.</li>
     </ul>
  </li>
</ul>