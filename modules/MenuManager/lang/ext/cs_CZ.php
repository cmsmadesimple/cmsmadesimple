<?php
$lang['readonly'] = 'pouze pro čten&iacute;';
$lang['error_templatename'] = 'Nemůžete zadat jm&eacute;no &scaron;ablony konč&iacute;c&iacute; na .tpl';
$lang['this_is_default'] = 'V&yacute;choz&iacute; &scaron;ablona menu';
$lang['set_as_default'] = 'Nastavit jako v&yacute;choz&iacute; &scaron;ablonu menu';
$lang['default'] = 'V&yacute;choz&iacute;';
$lang['templates'] = '&Scaron;ablony';
$lang['addtemplate'] = 'Přidat &scaron;ablonu';
$lang['areyousure'] = 'Jsi si jist&yacute; smaz&aacute;n&iacute;m?';
$lang['changelog'] = '	<ul>
	<li>
	<p>Verze: 1.0</p>
	<p>Poč&aacute;tečn&iacute; verze.</p>
	</li> 
	</ul> ';
$lang['dbtemplates'] = 'Datab&aacute;zov&eacute; &scaron;ablony';
$lang['description'] = 'Spravovat &scaron;ablony menu pro zobrazen&iacute; menu v jak&eacute;koliv představiteln&eacute; podobě.';
$lang['deletetemplate'] = 'Smazat &scaron;ablonu';
$lang['edittemplate'] = 'Upravit &scaron;ablonu';
$lang['filename'] = 'Jm&eacute;no souboru';
$lang['filetemplates'] = '&Scaron;ablony souboru';
$lang['help_includeprefix'] = 'Přiřadit pouze ty položky, u kter&yacute;ch page alias obsahuje zadan&yacute; prefix.  Tento parametr nemůže b&yacute;t kombinov&aacute;n s parametrem excludeprefix.';
$lang['help_excludeprefix'] = 'Vyloučit v&scaron;echny položky (a jejich potomky), u kter&yacute;ch page alias obsahuje uveden&yacute; prefix. Tento parametr nesm&iacute; b&yacute;t použ&iacute;v&aacute;n v kombinaci s parametrem includeprefix.';
$lang['help_collapse'] = 'Zapni (nastav na 1) pro ukryt&iacute; položek menu, kter&eacute; se nevztahuj&iacute; k pr&aacute;vě vybran&eacute; str&aacute;nce.';
$lang['help_items'] = 'Použijte tuto položku pro vybr&aacute;n&iacute; seznamu str&aacute;nek, kter&eacute; m&aacute; toto menu zobrazit.  Hodnotou by měl b&yacute;t seznam jmen str&aacute;nek oddělen&yacute;ch č&aacute;rkami.';
$lang['help_number_of_levels'] = 'Toto nastaven&iacute; povol&iacute; zobrazen&iacute; menu pouze po určitou hloubku &uacute;rovn&iacute;.';
$lang['help_show_all'] = 'Tato volba způsob&iacute; zobrazen&iacute; v&scaron;ech položek menu i přes nastaven&iacute; nezobrazov&aacute;n&iacute; v menu. Toto i nad&aacute;le nezobraz&iacute; inaktivn&iacute; str&aacute;nky.';
$lang['help_show_root_siblings'] = 'Tato volba je užitečn&aacute; pouze pokud jsou použity elementy start_element nebo start_page.  Zobraz&iacute; sourozence po straně vybran&eacute;ho start_page/elementu.';
$lang['help_start_level'] = 'Tato možnost způsob&iacute;, že menu zobraz&iacute; pouze položky zač&iacute;naj&iacute;c&iacute; na určit&eacute; &uacute;rovni.  Snadn&yacute;m př&iacute;kladem může b&yacute;t situace, kdy m&aacute;me 1 menu na str&aacute;nce s number_of_levels=&#039;1&#039;.  Pak, jako druh&eacute; menu, m&aacute;te start_level=&#039;2&#039;.  Teď va&scaron;e druh&eacute; menu uk&aacute;že položky založen&eacute; na tom, co je vybr&aacute;no v 1. menu.';
$lang['help_start_element'] = 'Menu začne zobrazovat určen&yacute; start_element given start_element a ukazuje pouze tento element a jeho potomky.  Přeb&iacute;r&aacute; hierarchickou pozici (např.. 5.1.2).';
$lang['help_start_page'] = 'Menu začne zobrazovat určitou start_page a ukazuje pouze tento element a jeho potomky. Přeb&iacute;r&aacute; jm&eacute;no str&aacute;nky.';
$lang['help_template'] = '&Scaron;ablona pro zobrazen&iacute; menu.  &Scaron;ablony budou vytvořeny podle datab&aacute;zov&yacute;ch &scaron;ablon, poze pokud jm&eacute;no &scaron;ablony konč&iacute; na .tpl, signalizuje to um&iacute;stěn&iacute; &scaron;ablony v adres&aacute;ři &scaron;ablon MenuManageru. ';
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
$lang['importtemplate'] = 'Importovat &scaron;ablonu nebo datab&aacute;zi';
$lang['menumanager'] = 'Spr&aacute;vce menu';
$lang['newtemplate'] = 'Jm&eacute;no nov&eacute; &scaron;ablony';
$lang['nocontent'] = 'Nespecifikov&aacute;n ž&aacute;dn&yacute; obsah';
$lang['notemplatefiles'] = 'Ž&aacute;dn&eacute; soubory &scaron;ablon v %s';
$lang['notemplatename'] = '&Scaron;ablona nebyla pojmenov&aacute;na.';
$lang['templatecontent'] = 'Obsah &scaron;ablony';
$lang['templatenameexists'] = '&Scaron;ablona s t&iacute;mto jm&eacute;nem již existuje';
$lang['utma'] = '156861353.503654010.1232181160.1239786812.1239789065.25';
$lang['utmz'] = '156861353.1236079256.18.5.utmcsr=dev.cmsmadesimple.org|utmccn=(referral)|utmcmd=referral|utmcct=/project/files/6';
$lang['utmc'] = '156861353';
$lang['utmb'] = '156861353';
?>