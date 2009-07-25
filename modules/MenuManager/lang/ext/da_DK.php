<?php
$lang['addtemplate'] = 'Tilf&oslash;j skabelon';
$lang['areyousure'] = 'Er du sikker p&aring; dette skal slettes?';
$lang['changelog'] = '	<ul>
<li>1.5 - Bump version to be compatible with 1.1 only, and add the SetParameterTypes calls</li>
	<li>1.4.1 -- Fix a problem where menus would not show if includeprefix was not specified.
	<li>1.4 -- Accept a comma separated list of includeprefixes or excludeprefixes</li>
	<li>1.3 -- Added includeprefix and excludeprefix params</li>
	<li>1.1 -- Added handling of target parameter, mainly for the Link content type</li>
	<li>1.0 -- Initial Release</li>
	</ul> ';
$lang['dbtemplates'] = 'Database skabeloner';
$lang['description'] = 'H&aring;ndt&eacute;r menu skabeloner s&aring; menu&#039;er kan vises p&aring; enhver t&aelig;nkeligt m&aring;de.';
$lang['deletetemplate'] = 'Slet skabelon';
$lang['edittemplate'] = 'Redig&eacute;r skabelon';
$lang['filename'] = 'Filnavn';
$lang['filetemplates'] = 'Fil skabelon';
$lang['help_includeprefix'] = 'Medtag kun de menupunkter hvis side-alias passer med et af de angivne (komma-sepererede) prefikser. Dette parameter kan ikke kombineres med excludeprefix parametret.';
$lang['help_excludeprefix'] = 'Spring alle menupunkter (og deres b&oslash;rn) over, hvis side-alias passer med et af de angivne (komma-sepererede) prefikser. Dette parameter kan ikke kombineres med includeprefix parametret.';
$lang['help_collapse'] = 'Sl&aring; til (s&aelig;t til 1) for at lade menu&#039;en skjule punkter der ikke er relaterede til den valgte side.';
$lang['help_items'] = 'Brug dette punkt til at v&aelig;lge en liste af sider som denne menu skal vise. V&aelig;rdien skal v&aelig;re en list af side-alias&#039;er adskilt af kommaer.';
$lang['help_number_of_levels'] = 'Denne indstilling vil g&oslash;re at menu&#039;en kun viser et bestemt antal niveau&#039;er.';
$lang['help_show_all'] = 'Dette parameter vil tvinge menuen til at vise alle menupunkter selvom de er sat til ikke at blive vist. Inaktive menupunkter vil dog stadig ikke blive vist.';
$lang['help_show_root_siblings'] = 'Denne indstilling er nyttig hvis start_element eller start_page benyttes. Der kan angives at &quot;s&oslash;skende&quot;, dvs. punkter p&aring; samme niveau, skal vises ved sidenaf det valgte start_element/start_page.';
$lang['help_start_level'] = 'Denne indstilling g&oslash;r at menu&#039;et kun viser punkter startende fra det angivne niveau. Et hurtigt eksempel kunne v&aelig;re hvis du havde en menu p&aring; siden med number_of_levels=&#039;1&#039;. Og som en anden menu havde du start_leve=&#039;2&#039;. S&aring; ville din anden menu vise menupunkter baseret p&aring; hvad du havde valgt i f&oslash;rste menu.';
$lang['help_start_element'] = 'Lad menu&#039;en starte ved menu-punktet start_element, og vis kun dette og det&#039;s underpunkter. skal v&aelig;re en gyldig hierarkisk position (f.eks. 5.1.2).';
$lang['help_start_page'] = 'Lad menu&#039;en starte ved den angivne menu-punkt start_page og vis kun dette og dets underpunkter. SKal v&aelig;re et gyldgt side-alias.';
$lang['help_template'] = 'Den skabelon der skal bruge til visning af menuen. Skabeloner hentes fra database-skabeloner med mindre skabelon-navnet ender p&aring; &quot;.tpl&quot;, hvor de i stedet hentes fra en fil i underfolderen &quot;templates&quot; der ligger i MenuManager&#039;eren folder.';
$lang['help'] = '	<h3>What does this do?</h3>
	<p>Menu Manager is a module for abstracting menus into a system that&#039;s easily usable and customizable.  It abstracts the display portion of menus into smarty templates that can be easily modified to suit the user&#039;s needs. That is, the menu manager itself is just an engine that feeds the template. By customizing templates, or make your own ones, you can create virtually any menu you can think of.</p>
	<h3>How do I use it?</h3>
	<p>Just insert the tag into your template/page like: <code>{cms_module module=&#039;menumanager&#039;}</code>.  The parameters that it can accept are listed below.</p>
	<h3>Why do I care about templates?</h3>
	<p>Menu Manager uses templates for display logic.  It comes with three default templates called bulletmenu.tpl, cssmenu.tpl and ellnav.tpl. They all basically create a simple unordered list of pages, using different classes and ID&#039;s for styling with CSS.  They are similar to the menu systems included in previous versions: bulletmenu, CSSMenu and EllNav.</p>
	<p>Note that you style the look of the menus with CSS. Stylesheets are not included with Menu Manager, but must be attached to the page template separately. For the cssmenu.tpl template to work in IE you must also insert a link to the JavaScript in the head section of the page template, which is necessary for the hover effect to work in IE.</p>
	<p>If you would like to make a specialized version of a template, you can easily import into the database and then edit it directly inside the CMSMS admin.  To do this:
		<ol>
			<li>Click on the Menu Manager admin.</li>
			<li>Click on the File Templates tab, and click the Import Template to Database button next to bulletmenu.tpl.</li>
			<li>Give the template copy a name.  We&#039;ll call it &quot;Test Template&quot;.</li>
			<li>You should now see the &quot;Test Template&quot; in your list of Database Templates.</li>
		</ol>
	</p>
	<p>Now you can easily modify the template to your needs for this site.  Put in classes, id&#039;s and other tags so that the formatting is exactly what you want.  Now, you can insert it into your site with {cms_module module=&#039;menumanager&#039; template=&#039;Test Template&#039;}. Note that the .tpl extension must be included if a file template is used.</p>
	<p>The parameters for the $node object used in the template are as follows:
		<ul>
			<li>$node->id -- Content ID</li>
			<li>$node->url -- URL of the Content</li>
			<li>$node->accesskey -- Access Key, if defined</li>
			<li>$node->tabindex -- Tab Index, if defined</li>
			<li>$node->titleattribute -- Title Attribute (title), if defined</li>
			<li>$node->hierarchy -- Hierarchy position, (e.g. 1.3.3)</li>
			<li>$node->depth -- Depth (level) of this node in the current menu</li>
			<li>$node->prevdepth -- Depth (level) of the node that was right before this one</li>
			<li>$node->haschildren -- Returns true if this node has child nodes to be displayed</li>
			<li>$node->menutext -- Menu Text</li>
			<li>$node->index -- Count of this node in the whole menu</li>
			<li>$node->parent -- True if this node is a parent of the currently selected page</li>
		</ul>
	</p>';
$lang['importtemplate'] = 'Import&eacute;r skabelon til databasen';
$lang['menumanager'] = 'Menu H&aring;ndtering';
$lang['newtemplate'] = 'Nyt skabelon navn';
$lang['nocontent'] = 'Intet indhold angivet';
$lang['notemplatefiles'] = 'Ingen fil skabeloner i %s';
$lang['notemplatename'] = 'Intet skabelon navn angivet.';
$lang['templatecontent'] = 'Skabelon indhold';
$lang['templatenameexists'] = 'En skabelon med dette navn eksisterer allerede';
$lang['utma'] = '156861353.873058978.1204880321.1216499186.1216503463.6';
$lang['utmz'] = '156861353.1216499186.5.2.utmccn=(referral)|utmcsr=localhost|utmcct=/cms/admin/editcontent.php|utmcmd=referral';
$lang['utmb'] = '156861353';
$lang['utmc'] = '156861353';
?>