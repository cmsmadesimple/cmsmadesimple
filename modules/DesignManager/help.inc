<h3>What does this do?</h3>
  <p>The &quot;DesignManger&quot; module is a mechanism to comprehensively manage the various templates, stylesheets, and &quot;designs&quot; used in your website.  It provides convenient sorting, filtering, and organization mechanisms for these various primary elements.</p>

<h3>What is a &quot;Design&quot; ?</h3>
  <p>A &quot;Design&quot; is an entity that manages in one logical unit the various templates and stylesheets that are required for a particular look and feel on a website.</p>
  <p>The design contains a list of stylesheets, and manages the order of those stylesheets.  It also contains a list of templates <em>(of various types)</em> that are associated.   Designs can be exported to XML for sharing, and can be imported from XML.</p>

<h3>Templates Explained:</h3>
<p>A template is a collection of HTML and/or smarty code.  Templates are usually re-used multiple times on a website, and can include or inherit from other templates.  CMSMS provides numerous templates for various purposes.  These purposes include defining the structure of a web page, a navigation menu, or displaying news article summaries or details.<p>
<p>Each template must have a name that must be unique across the entire installation.  Additionally, each template is capable of having a description that allows providing human readable information and notes about the characteristics of the template.  You can optionally assign each template to a category to aide in quickly finding the template when edits are required.</p>
<p>Templates can optionally be associated with one or more designs.  This helps when needing to quickly find a template to edit.  As well, when exporting a design all the templates that are assocated with the design will be exported.</p>
<p>Depending upon the template type <em>(see below)</em> A template can be set as the &quot;default&quot; for that type.  This functionality allows a module to find a template to use of an appropriate type if no template name is specified in the module tag, or via any other means.  For example, in a default installation the &quot;News Summary Sample&quot; template is the default template for the News default <em>(summary)</em> action.  Therefore calling <em>{News}</em> without specifying a template will use this template.</p>
<p>Optionally, you can select one or more admin user accounts, or admmin user groups that have the ability to edit the template.  This gives the ability for restricted users to have limited access to some templates.  This may be useful for editing seasonal messages, or for modifying API keys or RSS feed URLS.</p>
<p>When templates are edited the user(s) selected syntax highlighter module will be used, assuming that a syntax highligher module has been installed, and that the user has selected one from within his user preferences.</p>
<p>To call a template you can either specify the template name in a module call or explicitly call/include the template from another one with the <code>{include file='cms_template:&lt;template_name&gt;'}</code> syntax.  See the smarty {include} tag.  Additionally, for backwares compatibility purposes the <code>{global_content name='&lt;template_name&gt;'}</code> syntax still works.</p>

<h4>Template Types Explained:</h4>
<p>A template type loosly indicates the geneneral purpose for the template.  Template types indicate the module or code that uses them, and a subtype.  For example two common template types are Core::Page indicating a template used by the core CMSMS system to structure a web page, and News::Summary indicating a template that the News module can use to create a summary listing.</p>
<p>Modules may create new template types on installation, and delete template types when they unistall.  Most modules will delete all templates that are associated with its types when the module is uninstalled.</p>
<p>Template types can optionally contain a &quot;Prototype Template&quot;.  The prototype is used when creating a new template of that type.  For example, if you create a new template of type &quot;News::Detail&quot; the template will initially be filled with the prototype data from that type.  You can then change the template to your liking.</p>

<h3>Categories Explained:</h3>
<p>Templates can be associated with a single category merely as a method of organizing templates.  The DesignManager interface allows filtering types by category so that you can more easily find a template to edit.</p>

<h3>Stylsheets Explained:</h3>
<p>Stylesheets are text data that are stored in the database, and allow you to enter css data that is associated with zero or more designs.   Each stylesheet must have a unique name, has an optional description, and an optional media query.</p>
<p>You can associate each stylesheet with one or more designs, and specify the order of the stylesheets within the design.  When a content page is rendered, all of the stylesheets associated with the design will be combined, and output to a file to allow cashing by the browser.</p>
<p>Stylesheets can include smarty tags to allow doing logic within the stylesheet, or creating variables for reuse.  However instead of the normal { and } delimiters for smarty, the [[  and ]] delimiters are used.  For example:</p>
<pre><code><span style="color: blue;">[[$red='#f00']]</span>
div.error {
   color: <span style="color: blue;">[[$red]]</span>;
}
</code></pre>
<p><strong>Note:</strong> Due to the fact that stylesheets can be associated with zero or more designs, and/or called individually, and that stylesheets are combined and cached for all visitors, on the browser you must use caution when including smarty in templates.  Here are a few notes:</p>
  <ul>
    <li>Never put server specific conditions, state conditions, time related, or page specific conditions into a stylesheet.</li>
    <li>You should assume that each stylesheet may be called individually, therefore smarty variable initialization should be placed in each stylesheet.</li>
  </ul>
<p><strong>Note:</strong> You can include other templates inside of stylesheets using the <code>[[include file='cms_template::&lt;template_name&gt;']]</code> syntax as mentioned above however you must be aware of the change in smarty delimiters when processing stylesheets.  For example:</p>

  <ul>
    <li>Stylesheet &quot;page&quot;<br/>
<pre><code><span style="color: blue;">[[include file='cms_template::my_colors']]</span>
body {
  background-color: <span style="color: blue;">[[$my_background]]</span>;
  color: <span style="color: blue;">[[$dflt_foreground]]</span>;
}
</code></pre>
    </li>
    <li>Template: &quot;my_colors&quot;<br/>
<pre><code style="color: blue;">[[$my_background='#fff']]
[[$dflt_foreground='#000']]
</code></pre>
    </li>
  </ul>

<h3>How do Content Pages Figure In?</h3>
<p>A template <em>(usually of type Core::Page)</em> is associated with each content page; this determines the content blocks and types of content blocks that are displayed in the editcontent page, and is used to determine how the content data is rendered as an HTML page. Additionally, a design is associated with each content page; this is used to determine the stylesheets that will be associated with the content page, and the order of those stylesheets.</p>
<p>It is not mandatory that the template associated with a content page be of type <em>Core::Page</em> as the template type is just used for organizational purposes.</p>

<h3>Importing and Exporting:</h3>
<p>DesignManager supports functionality to export a complete design (including templates, stylesheets, images, descriptions, and stylesheet order) to an XML format for use on another compatible CMS Made Simple website.  To export a design just click on the export icon on each design row on the Designs tab of the Design Manager admin panel.</p>
<p>The system will find all of the templates and stylesheets attached with a design, and for each template parse the template to find templates that are <strong>directly</strong> included via module calls or the include statement, and attach them.  It will also find links to local images, and include those files in the output xml file.  Similarly stylesheets are parsed for links to local images, and those images are included in the xml file.</p>

<h3>Template Locking:</h3>
<p>In order to prevent one designer or site developer from accidentally overwriting the work of another developer the DesignManager provides locking for templates.  When a site developer begins an edit session on a template the template is locked from edits from other developers.</p>
<p>Frequently, during the edit session the software pings the server to indicate that the developer is still working on the template.  If edit activity on the template ceases for a period of time, other developers can &quot;steal&quot; the lock.  Any unsaved changes by the first developer would be lost.</p>
<p><strong>Note:</strong> At this time there is no locking functionality for stylesheets.</p>

<h3>Permissions and Visibility</h3>
<p>Numerous permissions are used to control access to the DesignManager module and its visibility in the CMSMS Admin navigation:</p>
<ul>
  <li>Add Templates:
    <p>Logged in admin users with this permission have the ability to create new templates, but not to modify any existing templates <em>(that they do not otherwise have access to)</em></p>
  </li>
  <li>Modify Templates:
    <p>Logged in admin users with this permission have the ability to completely manage templates, including adding/editing/deleting.  They can also adjust the ownership, and additional editors of a template.</li>
  </li>
  <li>Manage Stylesheets:
    <p>Logged in admin users with this permission have the ability to completely manage stylesheets.  There is no functionality for ownership and additional editors of stylesheets.</p>
  </li>
  <li>Manage Designs:
    <p>Logged in admin users with this permission have the ability to manage designs, including importing and exporting designs.  When importing a design the currently logged in user is marked as the owner for all new templates and stylesheets.</p>
  </li>
  <li>Owner:
    <p>The owner of at least one template can view the module in the admin navigation, and edit or delete the templates he owns.  He can also grant additional editor rights to other users or admin groups.</p>
  </li>
  <li>Additional Editor:
    <p>An admin user who has been marked as an &quot;Additional Editor&quot; to one or more templates can view the module in the admin navigation, and edit the template including its description.  However he cannot change any associations of this template, delete the template or grant additional privileges to other users.</p>
  </li>
</ul>

<h3>History:</h3>
<p>Prior to the development of the DesignManager module in CMSMS 2.0 each separate section, and each module managed their own templates.  This meant that module management had different interfaces for each different type of template.  CMS Made Simple provided a table to store module templates, and various methods in the module API to allow interfacing with those templates.  However, each module was responsible for its own functionality for managing its templates.</p>

<h3>Compatibility:</h3>
<p>For compatibility purposes, the old method of managing module templates has been kept. <em>(This functionality is <strong>deprecated</strong> and may be removed at a future date.)</em>  Therefore until modules migrate their functionality to use the new APIs for managing template types, templates and stylesheets you may still have to visit numerous different admin pages and experience different interfaces to manage the templates of those modules.</p>