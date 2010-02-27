<?php
$lang['help_loadprops'] = 'Usa questo parametro quando stai usando le propriet&agrave; avanzate nel Vostro MenuManager template. Questo parametro forzer&agrave; il caricamento di tutte le propriet&agrave; di ciascun nodo (come extra1, image, thumbnail, etc) e quindi permetter&agrave; la costruzione di menu molto pi&ugrave; avanzati ma aumenter&agrave; considerevolmente il numero di query richieste per costruire un menu e quindi l&#039;ammontare di memoria richiesta';
$lang['readonly'] = 'sola lettura';
$lang['error_templatename'] = 'Voi non potete specificare un nome di template che termina con .tpl';
$lang['this_is_default'] = 'Menu template predefinito';
$lang['set_as_default'] = 'Setta come menu template predefinito';
$lang['default'] = 'Predefinito';
$lang['templates'] = 'Modelli';
$lang['addtemplate'] = 'Aggiungere Modello';
$lang['areyousure'] = 'Sei sicuri di volerlo eliminare?';
$lang['changelog'] = '<ul>
<li>1.6.1 - Add created and modified entries on each node.</li>
<li>1.6 - Re-design admin interface, allow setting the default menu manager template.</li>
        <li>1.5.4 - Minor bugfix, now require CMS 1.5.3.</li>
        <li>1.5.3 - Support for syntax hilighter.</li>
        <li>1.5.2 - Added more fields available in each node in the template.</li>
        <li>1.5 - Bump version to be compatible with 1.1 only, and add the SetParameterTypes calls</li>

	<li>1.4.1 -- Fix a problem where menus would not show if includeprefix was not specified.
	<li>1.4 -- Accept a comma separated list of includeprefixes or excludeprefixes</li>
	<li>1.3 -- Added includeprefix and excludeprefix params</li>
	<li>1.1 -- Added handling of target parameter, mainly for the Link content type</li>
	<li>1.0 -- Initial Release</li>
</ul>';
$lang['dbtemplates'] = 'Database dei Modelli';
$lang['description'] = 'Gestione dei Modelli per visualizzare i menu in ogni maniera.';
$lang['deletetemplate'] = 'Elimina Modello';
$lang['edittemplate'] = 'Modifica Modello';
$lang['filename'] = 'Nome file';
$lang['filetemplates'] = 'Modelli file';
$lang['help_includeprefix'] = 'Include solo quei termini in cui l&#039;alias coincide con almeno uno dei specificati (separati da virgola) prefissi. Questo parametro non pu&ograve; essere combinato con il parametro excludeprefix.';
$lang['help_excludeprefix'] = 'Esclude tutti gli elementi (e i loro figli) i cui alias di pagina coincidono con almeno uno dei prefissi specificati (separati da virgola). Questo parametro non pu&ograve; essere combinato con il parametro includeprefix.';
$lang['help_collapse'] = 'Attiva (imposta a 1) perch&egrave; il menu nasconda gli elementi non relativi alla pagina selezionata corrente.';
$lang['help_items'] = 'Usa questo termine per selezionare una lista di pagine che questo menu dovrebbe visualizzare. Il valore sar&agrave; una lista di alias delle pagine separate da virgole.';
$lang['help_number_of_levels'] = 'Questa configurazione permette al menu di visualizzare solo un certo numero di livelli di profondit&agrave;.';
$lang['help_show_all'] = 'Questa opzione visualizza il menu con tutti i nodi anche se configurati da non essere visualizzati nel menu. Tuttavia non visualizza le pagine inattive.';
$lang['help_show_root_siblings'] = 'Questa opzione diventa utile se sono usati start_element o start_page. Semplicemente visualizza i &quot;fratelli&quot; della pagina/elemento selezionato.';
$lang['help_start_level'] = 'Questa opzione visualizzer&agrave; solo gli elementi che partono da un dato livello. Un esempio dovrebbe essere se avete un menu con number_of_levels=&#039;1&#039; ed un secondo menu con start_level=&#039;2&#039;.  Ora, il secondo menu visualizzer&agrave; gli elementi basati su quello che &egrave; selezionato nel primo menu.';
$lang['help_start_element'] = 'Il menu inizia a visualizzare gli elementi a partire dal dato start_element mostrando solo gli elementi discendenti. Il parametro prende una posizione della gerarchia (p.e. 5.1.2).';
$lang['help_start_page'] = 'Il menu inizia a visualizzare gli elementi a partire dalla data start_page mostrando solo gli elementi discendenti. Il parametro prende un alias di pagina.';
$lang['help_template'] = 'Il Modello da usare per visualizzare il menu. Modelli vengono dal database dei Modelli a meno che il nome non finisca con .tpl, in questo caso verr&agrave; da un file nella directory dei Modelli di MenuManager';
$lang['help'] = '	<h3>Che cosa fa?</h3>
	<p>Menu Manager &egrave; un modulo per l&#039;astrazione di menu in un sistema facilmente utilizzabile e personalizzabile. Esso astrae la porzione di visualizzazione del menu in Modelli smarty che possono essere facilmente modificati a seconda della bisogna. In questo modo, menu manager &egrave; un motore. Personalizzando i Modelli, o creando uno tuo, puoi creare virtualmente qualsiasi menu tu voglia.</p>
	<h3>Come usarlo?</h3>
	<p>Basta inserire il tag nel Modello/pagina come: <code>{menu}</code>. I parametri accettati sono indicati pi&ugrave; sotto.</p>
	<h3>Perch&egrave; curare i Modelli?</h3>
	<p>Menu Manager usa i Modelli per la logica di visualizzazione. Viene con tre Modelli predefiniti chiamati cssmenu.tpl, minimal_menu.tpl e simple_navigation.tpl. Essi creano una semplice lista non ordinata delle pagine, usando differenti classi e ID degli Stili CSS.</p>
	<p>Nota che utilizzi per l&#039;estetica dei menu gli Stili CSS. Questi non sono inclusi con Menu Manager, ma devono essere attaccati separatamente alla pagina Modello. Affinch&egrave; funzioni cssmenu.tpl con IE devi anche inserire un link al JavaScript nella sezione head della pagina Modello, il quale &egrave; necessario perl&#039;effetto hover in IE.</p>
	<p>Se volete fare una versione speciale di questo Modello, potete importarlo facilmente nel database e modificarlo direttamente dal pannello di amministrazione di CMSMS. Per fare questo:
		<ol>
			<li>Click sull&#039;amministrazione di Menu Manager.</li>
			<li>Click sul Modelli di file e click su Importa Modello al database relativo (es. simple_navigation.tpl).</li>
			<li>Date un nome al Modello copiato. Chiamiamo ad esempio &quot;Modello test&quot;.</li>
			<li>Dovreste vedere adesso il &quot;Modello test&quot; nella lista dei Database dei Modelli.</li>
		</ol>
	</p>
	<p>Ora potete facilmente modificare il Modello a seconda dei vostri bisogni. Mettete le classi, id e altri tag cos&igrave; che la formattazione sia esattamente come la volete. Ora, potete inserire nel vostro sito con {menu template=&#039;Modello test&#039;}. L&#039;estensione .tpl deve essere inclusa solo se usate il file Modello e non da database.</p>
	<p>I parametri per l&#039;oggetto $node (usato nel Modello) sono i seguenti:
		<ul>
			<li>$node->id -- ID del contenuto</li>
			<li>$node->url -- Indirizzo URL del contenuto</li>
			<li>$node->accesskey -- Chiave di accessibilit&agrave;, se definita</li>
			<li>$node->tabindex -- L&#039;indice di Tabulazione, se definito</li>
			<li>$node->titleattribute -- Attributo titolo (titolo), se definito</li>
			<li>$node->hierarchy -- Posizione gerarchica, (p.e. 1.3.3)</li>
			<li>$node->depth -- Profondit&agrave; (livello) del nodo nel menu corrente</li>
			<li>$node->prevdepth -- Profondit&agrave; (livello) del nodo prima di questo</li>
			<li>$node->haschildren -- Ritorna  true se questo nodo ha un nodo/i figli da visualizzare</li>
			<li>$node->menutext -- Menu testuale</li>
			<li>$node->raw_menutext -- Menu Text without having html entities converted</li>
			<li>$node->alias -- Page alias</li>
			<li>$node->extra1 -- Applicable only when the loadprops parameter is supplied on the menu tag, this field contains the value of the extra1 page property.</li>
			<li>$node->extra2 -- Applicable only when the loadprops parameter is supplied on the menu tag, this field contains the value of the extra2 page property.</li>
			<li>$node->extra3 -- Applicable only when the loadprops parameter is supplied on the menu tag, this field contains the value of the extra3 page property.</li>
			<li>$node->image -- Applicable only when the loadprops parameter is supplied on the menu tag, this field contains the value of the image page property (if non empty)</li>
			<li>$node->thumbnail -- Applicable only when the loadprops parameter is supplied on the menu tag, this field contains the value of the thumbnail page property (if non empty)</li>
			<li>$node->target -- Target for the link.  Will be empty if content doesn&#039;t set it.</li>
			<li>$node->created -- Item creation date</li>
			<li>$node->modified -- Item modified date</li>
			<li>$node->index -- Contatore di questo nodo nell&#039;intero menu</li>
			<li>$node->parent -- True (vero) se questo nodo &egrave; un parent (genitore) della pagina correntemente selezionata</li>
		</ul>
	</p>';
$lang['importtemplate'] = 'Importa Modello nel database';
$lang['menumanager'] = 'Gestore menu';
$lang['newtemplate'] = 'Nuovo nome di Modello';
$lang['nocontent'] = 'Nessun contenuto fornito';
$lang['notemplatefiles'] = 'Nessun modello per file in %s';
$lang['notemplatename'] = 'Nessun nome di Modello fornito.';
$lang['templatecontent'] = 'Contenuto del Modello';
$lang['templatenameexists'] = 'Un Modello con questo nome esiste gi&agrave;';
$lang['utma'] = '156861353.1700396481762948400.1243519364.1246260148.1246263667.20';
$lang['utmz'] = '156861353.1246263667.20.16.utmccn=(referral)|utmcsr=forum.cmsmadesimple.org|utmcct=/index.php|utmcmd=referral';
$lang['utmc'] = '156861353';
$lang['utmb'] = '156861353';
?>