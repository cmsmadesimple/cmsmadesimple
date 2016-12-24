<style type="text/css">
	ul.helptext {
	  list-style-type: disc;
	  margin-left: 1em;
	  margin-bottom: 1em;
	}
</style>


<h2>Navigator menu template help</h2>

<p>The Navigator template outputs an array of Nodes representing pages that match the criteria specified on the call to the Navigator module.  This is a nested array of nodes that should be displayed using a recursive algorithhm.</p>

<h3>Nodes:</h3>
<p>Each node is an object of type NavigatorNode. Each NavigatorNode object represents a single content page, and potentially its children.  It has the following read-only properties:</p>

<ul class="helptext">
  <li><code>id</code> (int) - The integer id of the content item being represented.</li>
  <li><code>url</code> (string) - The public, canonical URL of the content item (if any).</li>
  <li><code>accesskey</code> (string) - The access key specified for the content item.</li>
  <li><code>type</code> (string) - A string representing the type of content item.</li>
  <li><code>tabindex</code> (int) - The tab index (if any) associated with the content item.</li>
  <li><code>titleattribute</code> (string) - A string that may contain a description that can be used as the title attribute of a generated link.</li>
  <li><code>modified</code> (string) - The date the content item was last modified.</li>
  <li><code>created</code> (string) - The date the content item was initially created.</li>
  <li><code>depth</code> (string) - An integer representing the depth of the content item within the current resultset (note, the depth is not necessarily from the root, but only from the current resultset.</li>
  <li><code>menutext</code> (string) - The string to use in the text portion of the navigation.  This string has been converted to HTML entities and is suitable for display in a web page.</li>
  <li><code>raw_menutext</code> (string) - The raw menu text (not entity encoded) of the content item.</li>
  <li><code>alias</code> (string) - The human readable alias of the content item (if any).</li>
  <li><code>current</code> (bool) - Whether or not this is the content item currently being displayed.</li>
  <li><code>default</code> (bool) - Whether or not this is the content item that is set as the &quot;default&quot; page, or the home page.</li>
  <li><code>parent</code> (bool) - Whether or not this node represents a parent of the content item currently being displayed.</li>
  <li><code>children_exist</code> (bool) - Whether or not the content item represented by this node has any children what-so-ever.</li>
  <li><code>has_children</code> (bool) Whether or not this node has children that can be displayed.</li>
  <li><code>children</code> (NavigatorNode[]) - An array of Navigator node objects represnenting the children of this node that are displayable.  May not exist, or be empty.</li>
</ul>

<p>The following properties are only ever available if &quot;deep&quot; mode is enabled:</p>
<ul class="helptext">
  <li><code>target</code> (string) - The target attribute of the content item.</li>
  <li><code>extra1</code> (string) - The extra1 field associated with the content item.</li>
  <li><code>extra2</code> (string) - The extra2 field associated with the content item.</li>
  <li><code>extra3</code> (string) - The extra3 field associated with the content item.</li>
  <li><code>image</code> (string) - The complete URL to the image specified in the image field (if any).</li>
  <li><code>thumbnail</code> (string) - The complete URL to the thumbnail specified in the thumbnail  field (if any).</li>
</ul>