<?php
$lang['addtemplate'] = 'Dodaj predlogo';
$lang['areyousure'] = 'Ste prepričani, da želite izbrisati?';
$lang['changelog'] = '	<ul>
<li>1.5 - Bump version to be compatible with 1.1 only, and add the SetParameterTypes calls</li>
	<li>1.4.1 -- Fix a problem where menus would not show if includeprefix was not specified.
	<li>1.4 -- Accept a comma separated list of includeprefixes or excludeprefixes</li>
	<li>1.3 -- Added includeprefix and excludeprefix params</li>
	<li>1.1 -- Added handling of target parameter, mainly for the Link content type</li>
	<li>1.0 -- Initial Release</li>
	</ul> ';
$lang['dbtemplates'] = 'Predloge v bazi';
$lang['description'] = 'Upravljanje s predlogami menijev za kakr&scaron;en koli prikaz menijev.';
$lang['deletetemplate'] = 'Izbri&scaron;i predlogo';
$lang['edittemplate'] = 'Uredi predlogo';
$lang['filename'] = 'Datoteka';
$lang['filetemplates'] = 'Predloge v datotekah';
$lang['help_includeprefix'] = 'Vkluči samo tiste elemente, katerih psevdonim strani se ujema z eno izmed določenih (ločenih z vejico) predpon. Ta parameter ne morete kombinirati s parametrom za izločevanje predpon (excludeprefix).';
$lang['help_excludeprefix'] = 'Izključi vse elemente (in njihove podrejene), katerih psevdonim strani se ujema z eno izmed določenih (ločenih z vejico) predpon. Ta parameter ne morete kombinirati s parametrom za vključevanje predpon (includeprefix).';
$lang['help_collapse'] = 'Vklopite (nastavite na 1) če ne želite elementov, ki so skriti v meniju, povezati s trenutno izbrano stranjo.';
$lang['help_items'] = 'Uporabite ta element za izbiro seznama strani, ki naj jih prikaže ta meni. Vrednost naj bo seznam psevdonimov strani, ločenih z vejicami.';
$lang['help_number_of_levels'] = 'Ta nastavitev bo dovolila meniju prikaz elementov samo do določene globine.';
$lang['help_show_all'] = 'Ta možnost bo omogočila meniju prikaz vseh elementov, tudi če so označeni, naj se ne prikažejo v meniju. Neaktivne strani kljub temu ne bodo prikazane.';
$lang['help_show_root_siblings'] = 'Ta možnost je uporabna samo, če sta uporabljeni možnosti start_element ali start_page. Prikazal bo vse podrejene elemente poleg izbrane začetne strani/elementa.';
$lang['help_start_level'] = 'Ta možnost bo prikazala meni, ki se začne na določenem nivoju. Preprost primer je meni na eni strani, z vrednostjo number_of_levels=&#039;1&#039; in drugi meni na podstrani kjer je start_level=&#039;2&#039;. Sedaj bo drugi meni prikazal elemente glede na izbiro v prvem meniju.';
$lang['help_start_element'] = 'Začne prikaz menijev glede na določen start_element in prikazuje samo ta element in njegove podrejene elemente. Zavzema pozicijo v hierarhiji (npr. 5.1.2)';
$lang['help_start_page'] = 'Začne prikaz menijev pri določeni začetni strani (start_page) in prikazuje samo ta element in njegove podrejene elemente. Zavzema psevdonim strani.';
$lang['help_template'] = 'Predloga, ki naj bo uporabljena, za prikaz menija. Predloge bodo prikazane iz baze predlog, razen če se ime predloge ne konča z .tpl. V tem primeru bo predloga prebrana iz datoteke v MenuManager mapi za predloge (privzeto simple_navigation.tpl)';
$lang['help'] = '	<h3>What does this do?</h3>
	<p>Menu Manager is a module for abstracting menus into a system that&#039;s easily usable and customizable.  It abstracts the display portion of menus into smarty templates that can be easily modified to suit the user&#039;s needs. That is, the menu manager itself is just an engine that feeds the template. By customizing templates, or make your own ones, you can create virtually any menu you can think of.</p>
	<h3>How do I use it?</h3>
	<p>Just insert the tag into your template/page like: <code>{menu}</code>.  The parameters that it can accept are listed below.</p>
	<h3>Why do I care about templates?</h3>
	<p>Menu Manager uses templates for display logic.  It comes with three default templates called cssmenu.tpl, minimal_menu.tpl and simple_navigation.tpl. They all basically create a simple unordered list of pages, using different classes and ID&#039;s for styling with CSS.</p>
	<p>Note that you style the look of the menus with CSS. Stylesheets are not included with Menu Manager, but must be attached to the page template separately. For the cssmenu.tpl template to work in IE you must also insert a link to the JavaScript in the head section of the page template, which is necessary for the hover effect to work in IE.</p>
	<p>If you would like to make a specialized version of a template, you can easily import into the database and then edit it directly inside the CMSMS admin.  To do this:
		<ol>
			<li>Click on the Menu Manager admin.</li>
			<li>Click on the File Templates tab, and click the Import Template to Database button next to i.e. simple_navigation.tpl.</li>
			<li>Give the template copy a name.  We&#039;ll call it &quot;Test Template&quot;.</li>
			<li>You should now see the &quot;Test Template&quot; in your list of Database Templates.</li>
		</ol>
	</p>
	<p>Now you can easily modify the template to your needs for this site.  Put in classes, id&#039;s and other tags so that the formatting is exactly what you want.  Now, you can insert it into your site with {menu template=&#039;Test Template&#039;}. Note that the .tpl extension must be included if a file template is used.</p>
	<p>The parameters for the $node object used in the template are as follows:
		<ul>
			<li>$node->id -- Content ID</li>
			<li>$node->url -- URL of the Content</li>
			<li>$node->accesskey -- Access Key, if defined</li>
			<li>$node->tabindex -- Tab Index, if defined</li>
			<li>$node->titleattribute -- Description or Title Attribute (title), if defined</li>
			<li>$node->hierarchy -- Hierarchy position, (e.g. 1.3.3)</li>
			<li>$node->depth -- Depth (level) of this node in the current menu</li>
			<li>$node->prevdepth -- Depth (level) of the node that was right before this one</li>
			<li>$node->haschildren -- Returns true if this node has child nodes to be displayed</li>
			<li>$node->menutext -- Menu Text</li>
			<li>$node->alias -- Page alias</li>
			<li>$node->target -- Target for the link.  Will be empty if content doesn&#039;t set it.</li>
			<li>$node->index -- Count of this node in the whole menu</li>
			<li>$node->parent -- True if this node is a parent of the currently selected page</li>
		</ul>
	</p>';
$lang['importtemplate'] = 'Uvozi predlogo v bazo';
$lang['menumanager'] = 'Upravitelj menijev';
$lang['newtemplate'] = 'Ime nove predloge';
$lang['nocontent'] = 'Vsebina ni podana';
$lang['notemplatefiles'] = 'Ni datotek s predlogami v %s';
$lang['notemplatename'] = 'Ime predloge ni podano.';
$lang['templatecontent'] = 'Vsebina predloge';
$lang['templatenameexists'] = 'Predloga s tem imenom že obstaja';
$lang['utma'] = '156861353.587959277.1217433595.1218904938.1218966177.14';
$lang['utmz'] = '156861353.1218968039.14.7.utmcsr=google|utmccn=(organic)|utmcmd=organic|utmctr=cms made simple logo';
$lang['utmb'] = '156861353.12.10.1218966177';
$lang['utmc'] = '156861353';
?>