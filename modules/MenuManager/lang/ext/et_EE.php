<?php
$lang['addtemplate'] = 'Lisa Mall';
$lang['areyousure'] = 'Oled kindel, et soovid seda eemaldada?';
$lang['changelog'] = '	<ul>
	<li>1.1 -- Lisatud sihtkoha parameetri seadistamine, p&otilde;hiliselt linkide sisut&uuml;&uuml;bi haldamiseks</li>
	<li>1.0 -- Esimene versioon</li>
	</ul> ';
$lang['dbtemplates'] = 'Andmebaasi Mallid';
$lang['description'] = 'Halda men&uuml;&uuml; malle, et muuta oma men&uuml;&uuml;de v&auml;ljan&auml;gemist.';
$lang['deletetemplate'] = 'Kustuta Mall';
$lang['edittemplate'] = 'Muuda Malli';
$lang['filename'] = 'Failinimi';
$lang['filetemplates'] = 'Faili Mallid';
$lang['help_collapse'] = 'L&uuml;lita sisse (s&auml;ti 1-le), et men&uuml;&uuml; ei n&auml;itaks objekte, mis ei ole seotud avatud lehega.';
$lang['help_items'] = 'Kasuta seda parameetrit, et m&auml;&auml;rata, milliseid lehti peaks men&uuml;&uuml; kuvama. V&auml;&auml;tus peaks olema nimekiri lehtede aliastest, eraldatud komadega.';
$lang['help_number_of_levels'] = 'See seade lubab men&uuml;&uuml;l kuvada objekte ainult teatud tasemeni.';
$lang['help_show_root_siblings'] = 'Sellest valikust on kasu vaid siis, kui kasutad parameetreid <i>start_element</i> v&otilde;i <i>start_page</i>.  P&otilde;him&otilde;tteliselt kuvatakse valitud alguslehe (start_page) alla kuuluvaid lehti tema k&otilde;rval.';
$lang['help_start_level'] = 'Kuvab men&uuml;&uuml;s ainult antud tasemel asuvaid objekte.  Lihtne n&auml;ide oleks j&auml;rgmine: sul on kasutusel &uuml;ks men&uuml;&uuml;, millel on parameetriks m&auml;&auml;ratud <i>number_of_levels=&#039;1&#039;</i>.  Peale selle on sul teine men&uuml;&uuml;, millel on sama parameeter m&auml;&auml;ratud <i>start_level=&#039;2&#039;</i>. 
Sinu teine men&uuml;&uuml; n&auml;itab objekte s&otilde;ltuvalt sellest, mis on valitud esimese men&uuml;&uuml;s.';
$lang['help_start_element'] = 'Kuvab men&uuml;&uuml;s objekte alates antud <i>start_page</i>&#039;st (alguslehest) ja n&auml;itab ainult seda lehte nign tema alla kuuluvaid lehti. V&otilde;tab positsiooni saidi hierarhias (nt. 5.1.2).';
$lang['help_start_page'] = 'Kuvab men&uuml;&uuml;s objekte alates antud <i>start_page</i>&#039;st (alguslehest) ja n&auml;itab ainult seda lehte nign tema alla kuuluvaid lehti. Kasutab lehe aliast.';
$lang['help_template'] = 'Mall, mida kasutada men&uuml;&uuml; kuvamiseks.  Mallid p&auml;rinevad andmebaasi mallidest, v&auml;lja arvatud juhul, kui malli nimi l&otilde;peb .tpl-ga, mis puhul mall p&auml;rineb Men&uuml;&uuml;halduri kaustas olevast failist.';
$lang['help'] = '	<h3>What does this do?</h3>
	<p>Menu Manager is a module for abstracting menus into a system that&#039;s easily usable and customizable.  It abstracts the display portion of menus into smarty templates that can be easily modified to suit the user&#039;s needs. That is, the menu manager itself is just an engine that feeds the template. By customizing templates, or make your own ones, you can create virtually any menu you can think of.</p>
	<h3>How do I use it?</h3>
	<p>Just insert the tag into your template/page like: <code>{menu}</code>.  The parameters that it can accept are listed below.</p>
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
$lang['importtemplate'] = 'Impordi Mall Andmebaasi';
$lang['menumanager'] = 'Men&uuml;&uuml;haldur';
$lang['newtemplate'] = 'Uue Malli Nimi';
$lang['nocontent'] = 'Sisu ei ole';
$lang['notemplatefiles'] = 'Faili mallid kohas %s puuduvad';
$lang['notemplatename'] = 'Malli nime ei ole.';
$lang['templatecontent'] = 'Malli Sisu';
$lang['templatenameexists'] = 'Sellise nimega mall juba eksisteerib';
$lang['utmz'] = '156861353.1157121618.1.1.utmccn=(direct)|utmcsr=(direct)|utmcmd=(none)';
$lang['utma'] = '156861353.1526332053.1157121618.1157745950.1157896763.6';
$lang['utmc'] = '156861353';
?>