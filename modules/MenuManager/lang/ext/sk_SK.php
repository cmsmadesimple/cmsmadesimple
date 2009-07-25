<?php
$lang['help_loadprops'] = 'Use this parameter when using advanced properties in your menu manager template.  This parameeter will force the loading of all content properties for each node (such as extra1, image, thumbnail, etc).  and will dramatically increase the number of queries required to build a menu, and increase memory requirements, but will allow for much more advanced menus';
$lang['readonly'] = 'iba na č&iacute;tanie';
$lang['error_templatename'] = 'Nem&ocirc;žete zadať  n&aacute;zov &scaron;abl&oacute;ny s koncovkou tpl.';
$lang['this_is_default'] = 'Prednastaven&aacute; &scaron;abl&oacute;na pre menu';
$lang['set_as_default'] = 'Nastaviť ako prednastaven&uacute; &scaron;abl&oacute;nu pre menu';
$lang['default'] = 'Prednastaven&aacute;';
$lang['templates'] = '&Scaron;abl&oacute;ny';
$lang['addtemplate'] = 'Pridať &scaron;abl&oacute;nu';
$lang['areyousure'] = 'Ste si ist&yacute;, že to chcete odstr&aacute;niť?';
$lang['changelog'] = '	<ul>
        <li>1.5.3 - Podpora pre syntax hilighter.</li>
        <li>1.5.2 - Pridan&aacute; podpora pre viac hodn&uacute;ť pre objekt node &scaron;abl&oacute;na.</li>
        <li>1.5 - Bump version to be compatible with 1.1 only, and add the SetParameterTypes calls</li>
	<li>1.4.1 -- Fix a problem where menus would not show if includeprefix was not specified.
	<li>1.4 -- Accept a comma separated list of includeprefixes or excludeprefixes</li>
	<li>1.3 -- Added includeprefix and excludeprefix params</li>
	<li>1.1 -- Added handling of target parameter, mainly for the Link content type</li>
	<li>1.0 -- Initial Release</li>
	</ul> ';
$lang['dbtemplates'] = '&Scaron;abl&oacute;ny v datab&aacute;ze';
$lang['description'] = 'Spr&aacute;va &scaron;abl&oacute;n pre menu a zobrazovanie menu na str&aacute;nkach.';
$lang['deletetemplate'] = 'Odstr&aacute;niť &scaron;abl&oacute;nu';
$lang['edittemplate'] = 'Upraviť &scaron;abl&oacute;nu';
$lang['filename'] = 'N&aacute;zov s&uacute;boru';
$lang['filetemplates'] = '&Scaron;abl&oacute;ny zo  s&uacute;boru';
$lang['help_includeprefix'] = 'Zobraz&iacute; iba tie položky, ktor&eacute; obsahuj&uacute; jeden zo zadan&yacute;ch,  čiarkov oddelen&yacute;ch prefixov. Tento parameter nem&ocirc;že byť použit&yacute; v kombin&aacute;cii s excludeprefix.';
$lang['help_excludeprefix'] = 'Nezobraz&iacute; položky, ktor&eacute; obsahuj&uacute; jeden zo zadan&yacute;ch, čiarkov oddelen&yacute;ch prefixov. Tento parameter nem&ocirc;že byť použit&yacute; v kombin&aacute;cii s  includeprefix .
';
$lang['help_collapse'] = 'Povoľte (nastavte na 1) pre skrytie položiek, ktor&eacute; nie s&uacute; s&uacute;visiace s akt&iacute;vnou položkou.';
$lang['help_items'] = 'Použite pre zoznam str&aacute;nok, ktor&eacute; maj&uacute; byť  vmenu zobrazen&eacute;.   Hodnota parametra mus&iacute; obsahovať čiarkov oddelen&eacute; aliasy str&aacute;nok.';
$lang['help_number_of_levels'] = 'Nastavenie povol&iacute; zobrazenie menu iba do určitej hĺbky.';
$lang['help_show_all'] = 'Nastavuje zobrazenie pre v&scaron;etky str&aacute;nky, vr&aacute;tane str&aacute;nok, ktor&eacute; s&uacute; označen&eacute; ako &quot;nezobrazovať v menu&quot;. Av&scaron;ak str&aacute;le nebud&uacute; zobrazen&eacute; neakt&iacute;vne str&aacute;nky.';
$lang['help_show_root_siblings'] = 'Nastavenie sa použ&iacute;va iba pri použit&iacute; start_element alebo  start_page. Zobrazuje iba podriaden&eacute; str&aacute;nky.';
$lang['help_start_level'] = 'Nastavenie zobraz&iacute; menu iba od určitej &uacute;rovne. Napr&iacute;klad menu prvej &uacute;rovne zobraz&iacute;te ak nastav&iacute;te number_of_levels=&#039;1&#039;.  Menu druhej &uacute;rovne nastav&iacute;te pridan&iacute;m parametra start_level=&#039;2&#039;.';
$lang['help_start_element'] = 'Začiatok menu od start_element, vr&aacute;tane tejto položky a jeho podraden&yacute;ch položiek. Vypĺňajte poz&iacute;ciu v hierarchii  (napr. 5.1.2)';
$lang['help_start_page'] = 'Začiatok menu od start_page, vr&aacute;tane tejto položky a jeho podraden&yacute;ch položiek.';
$lang['help_template'] = '&Scaron;abl&oacute;na pre zobrazenie menu. &Scaron;abl&oacute;nu si m&ocirc;žete vytvoriť, alebo naimportovať zo predpripraven&yacute;ch s&uacute;borov a potom modifikovať.  Prednastaven&aacute; &scaron;abl&oacute;na je simple_navigation.tpl.';
$lang['help'] = '<h3>Čo to rob&iacute;?</h3>
	<p>Menu gener&aacute;tor  je modul pre spr&aacute;vu a zobrazenie menu zo str&aacute;nok v syst&eacute;me.</p>
	<h3>Ako sa použ&iacute;va?</h3>
	<p>Vložen&iacute;m značky: <code>{menu}</code> do str&aacute;nok.  Parametre pre &scaron;pecifick&eacute; zobrazenie menu si pozrite niž&scaron;ie.</p>
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
$lang['importtemplate'] = 'Imporotvať &scaron;abl&oacute;ny do datab&aacute;zy';
$lang['menumanager'] = 'Menu gener&aacute;tor';
$lang['newtemplate'] = 'Nov&yacute; n&aacute;zov &scaron;abl&oacute;ny';
$lang['nocontent'] = 'Nezadan&yacute; obsah';
$lang['notemplatefiles'] = 'Žiadny s&uacute;bor &scaron;abl&oacute;n v  %s';
$lang['notemplatename'] = 'Nezadan&yacute; n&aacute;zov &scaron;abl&oacute;ny.';
$lang['templatecontent'] = 'Obsah &scaron;abl&oacute;ny';
$lang['templatenameexists'] = '&Scaron;abl&oacute;na s t&yacute;mto n&aacute;zvom existuje';
$lang['utmz'] = '156861353.1242823757.727.119.utmcsr=dev.cmsmadesimple.org|utmccn=(referral)|utmcmd=referral|utmcct=/';
$lang['utma'] = '156861353.4300742689275601400.1213256953.1242818813.1242823757.727';
$lang['utmc'] = '156861353';
$lang['utmb'] = '156861353.1.10.1242823757';
?>