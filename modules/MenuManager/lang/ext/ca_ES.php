<?php
$lang['addtemplate'] = 'Afegeix plantilla';
$lang['areyousure'] = 'N&#039;est&agrave;s segur d&#039;esborrar aix&ograve;?';
$lang['changelog'] = '	<ul>
	<li>1.1 -- Afegida la gesti&oacute; del par&agrave;metre objectiu, b&agrave;sicament pel tipus de contingut d&#039;Enlla&ccedil;</li>
	<li>1.0 -- Versi&oacute; inicial</li>
	</ul> ';
$lang['dbtemplates'] = 'Plantilla de base de dades';
$lang['description'] = 'Gestiona plantilles de men&uacute; per mostrar els men&uacute;s de la manera que imaginis';
$lang['deletetemplate'] = 'Esborra plantilla';
$lang['edittemplate'] = 'Edita plantilla';
$lang['filename'] = 'Nom d&#039;arxiu';
$lang['filetemplates'] = 'Plantilles d&#039;arxiu';
$lang['help_includeprefix'] = 'Inclou nom&eacute;s aquells elements pels quals l&#039;&agrave;lies de p&agrave;gina encaixa amb un dels prefixos indicats (separats per comes).  Aquest par&egrave;metre no es pot combinar amb el par&agrave;metre excludeprefix.';
$lang['help_excludeprefix'] = 'Exclou tots els elements (i els seus fills) els &agrave;lies dels quals encaixen amb un dels prefixos indicats (separats per comes).  Aquest par&agrave;metre no s&#039;ha d&#039;utilitzar a la vegada que el par&agrave;metre includeprefix.';
$lang['help_collapse'] = 'Activa-ho (posa a 1) per tal que el men&uacute; oculti els elements no relacionats amb la p&agrave;gina selcccionada ';
$lang['help_items'] = 'Utilitza aquest element per triar una llista de p&agrave;gines que aquest men&uacute; hauria d&#039;ensenyar. El valor hauria de ser una llista d&#039;&agrave;lies de p&agrave;gina separats per comes.';
$lang['help_number_of_levels'] = 'Aquest valor de configuraci&oacute; limitar&agrave; els nivells de profunditat del men&uacute;';
$lang['help_show_all'] = 'Aquesta opci&oacute; provocar&agrave; que el men&uacute; mostri tots els nodes fins i tot si s&#039;han marcat com a no mostrables. Malgrat aix&ograve;, no es mostraran les p&agrave;gines inactives.';
$lang['help_show_root_siblings'] = 'Aquesta opci&oacute; nom&eacute;s &eacute;s &uacute;til si s&#039;utilitzen start_element o start_page. B&agrave;sicament, mostrar&agrave; els fills del start_page/element triat.';
$lang['help_start_level'] = 'This option will have the menu only display items starting a the given level.  An easy example would be if you had one menu on the page with number_of_levels=&#039;1&#039;.  Then as a second menu, you have start_level=&#039;2&#039;.  Now, your second menu will show items based on what is selected in the first menu.';
$lang['help_start_element'] = 'Starts the menu displaying at the given start_element and showing that element and it&#039;s children only.  Takes a hierarchy position (e.g. 5.1.2).';
$lang['help_start_page'] = 'Starts the menu displaying at the given start_page and showing that element and it&#039;s children only.  Takes a page alias.';
$lang['help_template'] = 'La plantilla a utilitzar per mostrar el men&uacute;. Les plantilles provenen de plantilles a la base de dades a menys que el seu nom acabi en .tpl, cas en el qual provindr&agrave; d&#039;un arxiu en el directori de plantilles del ManuManager ';
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
$lang['importtemplate'] = 'Imports Plantilla a la base de ades';
$lang['menumanager'] = 'Gestor de menus';
$lang['newtemplate'] = 'Nou nom de plantilla';
$lang['nocontent'] = 'No s&#039;ha proporcionat contingut';
$lang['notemplatefiles'] = 'No hi ha plantilles a %s';
$lang['notemplatename'] = 'No s&#039;ha donat un nom de plantilla.';
$lang['templatecontent'] = 'Contingut de plantilla';
$lang['templatenameexists'] = 'Ja existeix una plantilla amb aquest nom';
$lang['utmz'] = '156861353.1220258661.19.2.utmccn=(referral)|utmcsr=dev.cmsmadesimple.org|utmcct=/project/admin/|utmcmd=referral';
$lang['utma'] = '156861353.1195346509.1193818854.1220338326.1220362831.25';
$lang['utmc'] = '156861353';
$lang['utmb'] = '156861353';
?>