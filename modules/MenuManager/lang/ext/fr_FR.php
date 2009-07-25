<?php
$lang['help_loadprops'] = 'Utiliser ce param&egrave;tre si des propri&eacute;t&eacute;s avanc&eacute;es sont utilis&eacute;es dans votre gabarit de gestionnaire de menu. Ce param&egrave;tre va forcer le chargement des toutes les propri&eacute;t&eacute;s des contenus de tous les n&oelig;uds (tel que extra1, image, thumbnail, etc). Ceci va augmenter tr&egrave;s fortement le nombre de requ&ecirc;tes pour construire un menu ainsi que la consommation en m&eacute;moire, mais permet de g&eacute;rer des menus plus avanc&eacute;s';
$lang['readonly'] = 'lecture seule';
$lang['error_templatename'] = 'Vous ne pouvez pas sp&eacute;cifier un nom de gabarit qui se terminent par. tpl';
$lang['this_is_default'] = 'Gabarit menu par d&eacute;faut ';
$lang['set_as_default'] = 'D&eacute;finir comme gabarit de menu par d&eacute;faut';
$lang['default'] = 'D&eacute;faut';
$lang['templates'] = 'Gabarits';
$lang['addtemplate'] = 'Ajouter un gabarit';
$lang['areyousure'] = '&Ecirc;tes-vous s&ucirc;r de vouloir supprimer&nbsp;?';
$lang['changelog'] = '	<ul>
<li>1.6.1 - Add created and modified entries on each node.</li>
<li>1.6 - Re-design admin interface, allow setting the default menu manager template.</li>
        <li>1.5.4 - Minor bugfix, now require CMS 1.5.3.</li>
        <li>1.5.3 - Support for syntax hilighter.</li>
        <li>1.5.2 - Added more fields available in each node in the template.</li>
<li>1.5 - Changement de version pour &ecirc;tre compatible avec la 1.1 seulement, et ajout du SetParameterTypes</li>
	<li>1.4.1 - Corrige un probleme sur le menu si includeprefix n&#039;est pas sp&eacute;cifi&eacute;.
	<li>1.4 - Accepte la virgule comme s&eacute;parateur de liste pour includeprefixes ou excludeprefixes</li>
	<li>1.3 - Ajout param&egrave;tres includeprefix et excludeprefix </li>
	<li>1.1 - Ajout du support de l&#039;attribut target, principalement pour le type de contenu &quot;lien&quot;</li> 
	<li>1.0 - Version initiale</li> 
	</ul> ';
$lang['dbtemplates'] = 'Gabarits se trouvant dans la base de donn&eacute;es';
$lang['description'] = 'Gestion de gabarits de menus pour les afficher de toutes les mani&egrave;res imaginables.';
$lang['deletetemplate'] = 'Supprimer le gabarit';
$lang['edittemplate'] = '&Eacute;diter le gabarit';
$lang['filename'] = 'Nom de fichier';
$lang['filetemplates'] = 'Gabarits sous forme de fichier';
$lang['help_includeprefix'] = 'Inclut seulement les donn&eacute;es des pages dont l&#039;alias contient le pr&eacute;fixe indiqu&eacute; (virgule comme s&eacute;parateur). Ce param&egrave;tre ne peut pas &ecirc;tre combin&eacute; avec le param&egrave;tre excludeprefix.';
$lang['help_excludeprefix'] = 'Exclut toutes les donn&eacute;es des pages (et de leurs enfants) dont l&#039;alias contient le pr&eacute;fixe indiqu&eacute; (virgule comme s&eacute;parateur). Ce param&egrave;tre ne peut pas &ecirc;tre combin&eacute; avec le param&egrave;tre includeprefix.';
$lang['help_collapse'] = '&Agrave; activer (d&eacute;finir en 1) pour que le menu cache les objets non relatifs &agrave; la page s&eacute;lectionn&eacute;e.';
$lang['help_items'] = 'Utilisez ceci pour s&eacute;lectionner la liste de pages &agrave; afficher dans le menu. La valeur entr&eacute;e doit &ecirc;tre la liste des alias, s&eacute;par&eacute;e par des virgules.';
$lang['help_number_of_levels'] = 'Ce param&egrave;tre permet au menu d&#039;afficher uniquement un certain nombre de niveaux.';
$lang['help_show_all'] = 'Cette option afichera tous les niveaux s&#039;ils sont coch&eacute;s visibles dans le menu. Il n&#039;affichera pas les pages inactives.';
$lang['help_show_root_siblings'] = 'Cette option est utile lorsque start_element ou start_page est utilis&eacute;. Les autres &eacute;l&eacute;ments du m&ecirc;me niveau que l&#039;&eacute;l&eacute;ment s&eacute;lectionn&eacute; seront affich&eacute;s.';
$lang['help_start_level'] = 'Cette option permet d&#039;afficher uniquement les &eacute;l&eacute;ments &agrave; partir d&#039;un niveau donn&eacute;. Un exemple: vous avez un menu avec number_of_levels=&#039;1&#039;.  Puis, comme second menu, vous avez start_level=&#039;2&#039;.  Le second menu affichera les &eacute;l&eacute;ments bas&eacute;s sur ce qui est s&eacute;lectionn&eacute; dans le premier menu.';
$lang['help_start_element'] = 'Cette option permet d&#039;afficher uniquement les &eacute;l&eacute;ments &agrave; partir d&#039;un &eacute;l&eacute;ment donn&eacute; (start_element), ainsi que les niveaux en-dessous de cet &eacute;l&eacute;ment.  la valeur doit &ecirc;tre &eacute;gale &agrave; la position hi&eacute;rarchique de l&#039;&eacute;l&eacute;ment (exemple : 5.1.2).';
$lang['help_start_page'] = 'Cette option permet d&#039;afficher uniquement les &eacute;l&eacute;ments &agrave; partir d&#039;une page donn&eacute;e (start_page), ainsi que les niveaux en-dessous de cet &eacute;l&eacute;ment.  la valeur doit &ecirc;tre &eacute;gale &agrave; l&#039;alias de l&#039;&eacute;l&eacute;ment.';
$lang['help_template'] = 'Le gabarit &agrave; utiliser pour l&#039;affichage du menu. Le gabarit est issu de la base de donn&eacute;es sauf si son nom fini par .tpl, auquel cas il vient du fichier du m&ecirc;me nom se trouvant dans le dossier des gabarits (templates) du module MenuManager (Par d&eacute;fault simple_navigation.tpl)';
$lang['help'] = '	<h3>Que fait ce module&nbsp;?</h3>
	<p>Le module Gestion de Menus (Menu Manager) permet de g&eacute;rer les menus dans un syst&egrave;me facile &agrave; utiliser et &agrave; personnaliser.  Il fait abstraction de la partie affichage et place cette derni&egrave;re dans des gabarits Smarty, qui peuvent &ecirc;tre modifi&eacute;s facilement pour satisfaire aux besoins de l&#039;utilisateur. Cela &eacute;tant, le module Gestion de Menus lui-m&ecirc;me est simplement un moteur qui rempli les gabarits. En personnalisant les gabarits, ou en en cr&eacute;ant de nouveaux, vous pouvez cr&eacute;er quasiment toutes les formes de menus que vous pourrez imaginer.</p>
	<h3>Comment l&#039;utiliser&nbsp;?</h3>
	<p>Ins&eacute;rez simplement la balise dans votre gabarit/page: <code>{menu}</code>.  Les param&egrave;tres possibles sont list&eacute;s plus bas.</p>
	<h3>Pourquoi m&#039;occuper de gabarits?</h3>
	<p>Le module Gestion de Menus utilise des gabarits pour son affichage. Il est install&eacute; avec 3 gabarits par d&eacute;faut, nomm&eacute;s cssmenu.tpl, minimal_menu.tpl et simple_navigation.tpl. Tous cr&eacute;ent une simple liste de pages, en utilisant diff&eacute;rentes classes et ID, afin de pouvoir leur donner un style gr&acirc;ce au CSS</p>
	<p>Notez bien que vous pouvez donner un style &agrave; vos menus par l&#039;interm&eacute;diaire du CSS. Les feuilles de style ne sont pas incluses au module Gestion de Menus, mais doivent &ecirc;tre attach&eacute;es au gabarit de pages s&eacute;par&eacute;ment. Pour que le gabarit cssmenu.tpl fonctionne sous Internet Explorer, vous devez &eacute;galement, dans la partie en-t&ecirc;te de votre gabarit de page, ins&eacute;rer un lien au JavaScript qui permet l&#039;affichage de l&#039;effet survolage dans le navigateur Internet Explorer.</p>
	<p>Si vous d&eacute;sirez cr&eacute;er une version personnalis&eacute;e d&#039;un gabarit de menu, vous pouvez facilement l&#039;importer dans la base de donn&eacute;es, puis l&#039;&eacute;diter directement dans le panneau d&#039;administration de CMSMS.  Proc&eacute;der ainsi&nbsp;:</p>
		<ol>
			<li>Cliquez sur l&#039;administration de Gestion de Menus.</li>
			<li>Cliquez sur l&#039;onglet &#039;Gabarits sous forme de fichiers&#039;, et cliquez  &#039;Importer le gabarit dans la base de donn&eacute;es&#039; &agrave; c&ocirc;t&eacute; de simple_navigation.tpl, par exemple.</li>
			<li>Donnez un nouveau nom &agrave; ce gabarit. Nous l&#039;appelerons &quot;gabarit test&quot;</li>
			<li>Vous devriez maintenant voir &quot;gabarit test&quot; dans la liste &#039;Gabarits se trouvant dans la base de donn&eacute;es&#039;.</li>
		</ol>
	
	<p>Maintenant, vous pouvez ais&eacute;ment modifier le gabarit pour le modifier &agrave; votre convenance pour le site. Ins&eacute;rez des classes, des ID et d&#039;autres balises afin que le format g&eacute;n&eacute;r&eacute; soit exactement celui que vous d&eacute;sirez. Puis, ins&eacute;rez votre menu dans le site avec&nbsp;: {menu template=&#039;gabarit test&#039;}. Notez que l&#039;extension .tpl extension doit &ecirc;tre ajout&eacute;e dans le cas d&#039;une utilisation d&#039;un gabarit sour forme de fichier.</p>
	<p>Les param&egrave;tres pour l&#039;&eacute;l&eacute;ment $node utilis&eacute;s dans un gabarit sont les suivants&nbsp;:</p>
		<ul>
			<li>$node->id -- ID de l&#039;&eacute;l&eacute;ment</li>
			<li>$node->url -- URL de l&#039;&eacute;l&eacute;ment</li>
			<li>$node->accesskey -- Access Key, si d&eacute;finie</li>
			<li>$node->tabindex -- Tab Index, si d&eacute;fini</li>
			<li>$node->titleattribute -- Attribut titre, s d&eacute;fini</li>
			<li>$node->hierarchy -- Position hi&eacute;rarchique (exemple : 1.3.3)</li>
			<li>$node->depth -- Niveau de cet &eacute;l&eacute;ment dans le menu actuel</li>
			<li>$node->prevdepth -- Niveau de l&#039;&eacute;l&eacute;ment juste avant l&#039;&eacute;l&eacute;ment actuel</li>
			<li>$node->haschildren -- Renvoie true (vrai) si cet &eacute;l&eacute;ment a des niveaux &quot;enfants&quot; qui doivent &ecirc;tre affich&eacute;s.</li>
			<li>$node->menutext -- Texte du menu</li>
<li>$node->raw_menutext -- Texte du menu sans convertir les entit&eacute;s html</li>
			<li>$node->alias -- Alias de la page</li>

			<li>$node->extra1 -- Applicable uniquement lorsque le param&egrave;tre loadprops est dans la balise (tag) menu, ce champ contient la valeur de la propri&eacute;t&eacute; de page extra1.</li>
			<li>$node->extra2 -- Applicable uniquement lorsque le param&egrave;tre loadprops est dans la balise (tag) menu, ce champ contient la valeur de la propri&eacute;t&eacute; de page extra2.</li>
			<li>$node->extra3 -- Applicable uniquement lorsque le param&egrave;tre loadprops est dans la balise (tag) menu, ce champ contient la valeur de la propri&eacute;t&eacute; de page extra3.</li>
			<li>$node->image -- Applicable uniquement lorsque le param&egrave;tre loadprops est dans la balise (tag) menu, ce champ contient la valeur de la propri&eacute;t&eacute; de page de l&#039;image(si non vide).</li>
			<li>$node->thumbnail -- Applicable uniquement lorsque le param&egrave;tre loadprops est dans la balise (tag) menu, ce champ contient la valeur de la propri&eacute;t&eacute; de page de la vignette del&#039;image(si non vide).</li>
			<li>$node->target -- Applicable uniquement lorsque le param&egrave;tre loadprops est dans la balise (tag) menu, ce champ contient la cible du lien. Sera vide si le contenu n&#039;est pas d&eacute;fini.</li>

			<li>$node->index -- Position de cet &eacute;l&eacute;ment dans le menu</li>
			<li>$node->parent -- Renvoie true (vrai) si cet &eacute;l&eacute;ment est le parent de la page actuelle</li>
		</ul>
	';
$lang['importtemplate'] = 'Importer le gabarit dans la base de donn&eacute;es';
$lang['menumanager'] = 'Gestion de Menu';
$lang['newtemplate'] = 'Nom du nouveau gabarit&nbsp;';
$lang['nocontent'] = 'Aucun contenu entr&eacute;';
$lang['notemplatefiles'] = 'Aucun gabarit sous forme de fichier dans %s';
$lang['notemplatename'] = 'Aucun nom de gabarit entr&eacute;';
$lang['templatecontent'] = 'Contenu du gabarit&nbsp;';
$lang['templatenameexists'] = 'Un gabarit du m&ecirc;me nom existe d&eacute;j&agrave;';
$lang['utmz'] = '156861353.1246471583.1.1.utmccn=(direct)|utmcsr=(direct)|utmcmd=(none)';
$lang['utma'] = '156861353.655614565.1246471583.1246471583.1246475143.2';
$lang['utmc'] = '156861353';
$lang['utmb'] = '156861353';
?>