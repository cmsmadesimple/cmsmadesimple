<h3>What does this do?</h3>
  <p>The &quot;DesignManger&quot; module is a mechanism to comprehensively manage the various templates, stylesheets, and &quot;designs&quot; used in your website.  It provides convenient sorting, filtering, and organization mechanisms for these various primary elements.</p>

<h3>What is a &quot;Design&quot; ?</h3>
  <p>A &quot;Design&quot; is an entity that manages in one logical unit the various templates and stylesheets that are required for a particular look and feel on a website.</p>
  <p>The design contains a list of stylesheets, and manages the order of those stylesheets.  It also contains a list of templates <em>(of various types)</em> that are associated.   Designs can be exported to XML for sharing, and can be imported from XML.</p>

<h3>Templates Explained:</h3>
<p>A template is a collection of HTML and/or smarty code.  Templates are usually re-used multiple times on a website, and can include or inherit from other templates.  CMSMS provides numerous templates for various purposes.  These purposes include defining the structure of a web page, a navigation menu, or displaying news article summaries or details.<p>
<p>Each template must have a name that must be unique across the entire installation.  Additionally, each template is capable of having a description that allows providing human readable information and notes about the characteristics of the template.  You can optionally assign each template to a category to aide in quickly finding the template when edits are required.</p>
<p>Templates can optionally be associated with one or more designs.  This helps when needing to quickly find a template to edit.  As well, when exporting a design all the templates that are assocated with the design will be exported.</p>
<p>Optionally, you can select one or more admin user accounts, or admmin user groups that have the ability to edit the template.  This gives the ability for restricted users to have limited access to some templates.  This may be useful for editing seasonal messages, or for modifying API keys or RSS feed URLS.</p>
<p>When templates are edited the user(s) selected syntax highlighter module will be used, assuming that a syntax highligher module has been installed, and that the user has selected one from within his user preferences.</p>
<p>To call a template you can either specify the template name in a module call or explicitly call/include the template from another one with the <code>{include file='cms_template:&lt;template_name&gt;'}</code> syntax.  See the smarty {include} tag.  Additionally, for backwares compatibility purposes the <code>{global_content name='&lt;template_name&gt;'}</code> syntax still works.</p>

<h4>Template Types Explained:</h4>
<p>A template type loosly indicates the geneneral purpose for the template.  Template types indicate the module or code that uses them, and a subtype.  For example two common template types are Core::Page indicating a template used by the core CMSMS system to structure a web page, and News::Summary indicating a template that the News module can use to create a summary listing.</p>
<p>Modules may create new template types on installation, and delete template types when they unistall.  Most modules will delete all templates that are associated with its types when the module is uninstalled.</p>

<h3>Categories Explained:</h3>
<p>Templates can be associated with a single category merely as a method of organizing templates.  The DesignManager interface allows filtering types by category so that you can more easily find a template to edit.</p>

<h3>Stylsheets Explained:</h3>
<p>Stylesheets are text data that is stored in the database, and 
<h3>The relationship between Pages, Designs, and Templates</h3>
<h3>Importing and Exporting:</h3>
<h3>Locking</h3>
