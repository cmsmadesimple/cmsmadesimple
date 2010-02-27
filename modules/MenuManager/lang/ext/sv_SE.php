<?php
$lang['readonly'] = 'Skrivskyddad';
$lang['error_templatename'] = 'Du kan inte specificera en mall med fil&auml;ndelsen .tpl';
$lang['this_is_default'] = 'Grund menymall';
$lang['set_as_default'] = 'S&auml;tt som grund menymall';
$lang['default'] = 'Grundinst&auml;llning';
$lang['templates'] = 'Mallar';
$lang['addtemplate'] = 'L&auml;gg till mall';
$lang['areyousure'] = '&Auml;r du s&auml;ker p&aring; att du vill radera denna?';
$lang['changelog'] = '	<ul>
	<li>1.1 -- La till hanterande av parametern target, fr&auml;mst f&ouml;r inneh&aring;llstypen Link.>/li>
	<li>1.0 --  F&ouml;rsta utg&aring;van</li> 
	</ul> ';
$lang['dbtemplates'] = 'Databaslagrade mallar';
$lang['description'] = 'Hantera menymallar f&ouml;r att visa menyer p&aring; alla t&auml;nkbara s&auml;tt';
$lang['deletetemplate'] = 'Ta bort mall';
$lang['edittemplate'] = 'Redigera mall';
$lang['filename'] = 'Filnamn';
$lang['filetemplates'] = 'Mallar fr&aring;n fil';
$lang['help_includeprefix'] = 'Inkludera bara de poster vars sidalias inneh&aring;ller det angivna prefixet. Den h&auml;r parametern kan inte kombineras med parametern excludeprefix';
$lang['help_excludeprefix'] = 'Exkudera alla poster (och dess undernoder) vars sidalias inneh&aring;ller det angivna prefixet. Den h&auml;r parametern kan inte anv&auml;ndas tillsammans med parametern includeprefix';
$lang['help_collapse'] = 'S&auml;tt till 1 f&ouml;r att menyn ska g&ouml;mma poster/sidor som inte &auml;r relaterade till den aktuella sidan.';
$lang['help_items'] = 'Anv&auml;nd denna f&ouml;r att v&auml;lja en lista med sidor som den h&auml;r menyn ska visa. V&auml;rdet ska anges som en lista med sidalias, separerade med kommatecken.';
$lang['help_number_of_levels'] = 'V&auml;lj det antal niv&aring;er som menyn ska visa.';
$lang['help_show_all'] = 'Detta alternativ g&ouml;r att menyn visar alla noder, &auml;ven om de &auml;r satta att inte visas i menyn. Den visar dock inte ainaktiva sidor.';
$lang['help_show_root_siblings'] = 'Den h&auml;r parametern kan bara anv&auml;ndas om start_element eller start_page anv&auml;nds. Den visar sidor som &auml;r p&aring; samma niv&aring; som den valda start_page/start_element.';
$lang['help_start_level'] = 'Med den h&auml;r parametern visar menyn endast sidor fr.om. den niv&aring;n som anges och ner&aring;t. Ett enkelt exempel: du har en meny p&aring; sidan med number_of_levels=&#039;1&#039;. Som andra meny kan du ha start_level=&#039;2&#039;. Din andra meny visar sidor som baseras p&aring; vad som &auml;r valt i den f&ouml;rsta menyn.';
$lang['help_start_element'] = 'B&ouml;rjar visa menyn fr&aring;n angett start_element och visar enbart den sidan/det elementet och dess undersidor. Anges som positionsnummer i hierarkin (t.ex. 5.1.2).';
$lang['help_start_page'] = 'B&ouml;rjar visa menyn fr&aring;n sidan som anges med start_page och visar enbart den sidan och dess undersidor. Anges som sidalias.';
$lang['help_template'] = 'Mallen som anv&auml;nds f&ouml;r att visa menyn. Mallar kommer fr&aring;n databaslagrade mallar om inte mallnamnet anges med fil&auml;ndelsen .tpl. I det senare fallet anv&auml;nds mallar fr&aring;n filer i templates-katalogen i MenuManager-mappen.';
$lang['help'] = '	<h3>Vad g&ouml;r den h&auml;r modulen?</h3>
	<p>Menyhanteraren (Menu Manager) &auml;r en modul f&ouml;r att abstrahera menyer till ett system som &auml;r enkelt att anv&auml;nda och anpassa. Genom Smarty-mallar kan anv&auml;ndaren best&auml;mma hur menyn ska visas. Dvs Menyhanteraren &auml;r bara en motor som s&aring; att s&auml;ga matar mallen med uppgifter. Genom att anpassa mallarna, eller g&ouml;ra egna, kan du skapa i princip vilken meny som helst.</p>
	<h3>Hur anv&auml;nder jag den?</h3>
	<p>Anv&auml;nd taggen i en mall/p&aring; en sida enligt f&ouml;ljande: <code>{menu}</code>.  De parametrar som modulen tar listas l&auml;ngre ner.</p>
	<h3>Varf&ouml;r ska jag bry mig om mallar?</h3>
	<p>Menyhanteraren anv&auml;nder mallar som best&auml;mmer hur menyn ska visas. Modulen kommer med tre standardmallar som heter cssmenu.tpl, minimal_menu.tpl och simple_navigation.tpl. I princip skapar de en enkel lista av sidorna, och anv&auml;nder olika klasser och ID&#039;s som kan anpassas genom CSS.</p>
	<p>Observera att du st&auml;ller in stilen - menyns utseende - med CSS. Stilmallar inkluderas inte med Menyhanteraren, utan m&aring;ste kopplas till sidans mall separat. F&ouml;r att mallarna cssmenu.tpl och cssmenu-accessible.tpl ska fungera i IE m&aring;ste du ocks&aring; l&auml;gga till en l&auml;nk till ett JavaScript i head-delen av sidmallen. Det kr&auml;vs f&ouml;r att hover-effekten ska fungera i IE.</p>
	<p>Om du vill anv&auml;nda en specialversion av en mall kan du enkelt importera mallen till databasen och sedan redigera mallen direkt i CMSMS-administrationen.  G&ouml;r d&aring; s&aring; h&auml;r:
		<ol>
			<li>Klicka p&aring; Layout/Menyhanterare (Menu Manager).</li>
			<li>Klicka p&aring; tabben Mallar fr&aring;n fil, och klicka p&aring; knappen Importera mall till databas bredvid simple_navigation.tpl.</li>
			<li>Ge kopian av mallen ett namn.  I exemplet kallar vi den &quot;Testmall&quot;.</li>
			<li>Du ser nu &quot;Testmall&quot; i listan &ouml;ver Databaslagrade mallar</li>
		</ol>
	</p>
	<p>Nu kan du enkelt modifiera mallen efter dina behov. L&auml;gg till klasser, id&#039;s och andra taggar s&aring; att formateringen &auml;r precis som du vill. Nu kan du anv&auml;nda taggen p&aring; din sida s&aring; h&auml;r: {menu template=&#039;Testmall&#039;}. Observera att fil&auml;ndelsen .tpl m&aring;ste anv&auml;ndas om en filbaserad mall anv&auml;nds.</p>
	<p>Parametrarna f&ouml;r $node-objektet som anv&auml;nds i mallen &auml;r f&ouml;ljande:
		<ul>
			<li>$node->id -- Inneh&aring;lls-ID (f&ouml;r sidan)</li>
			<li>$node->url -- URL f&ouml;r Inneh&aring;llet</li>
			<li>$node->accesskey -- Access Key, om den &auml;r definierad</li>
			<li>$node->tabindex -- Tabb-index, om det &auml;r definierat</li>
			<li>$node->titleattribute -- Title-attributet, om det &auml;r definierat</li>
			<li>$node->hierarchy -- Position i hierarkin, (t.ex. 1.3.3)</li>
			<li>$node->depth -- Djupet (niv&aring;n) f&ouml;r den h&auml;r noden i den aktuella menyn</li>
			<li>$node->prevdepth -- Djupet (niv&aring;n) f&ouml;r noden som var just f&ouml;re den h&auml;r</li>
			<li>$node->haschildren -- Returnerar true (sant) om noden har undernoder som ska visas</li>
			<li>$node->menutext -- Menytexten</li>
                        <li>$node->alias -- Sidalias</li>
			<li>$node->target -- M&aring;let (target) f&ouml;r l&auml;nken.  &Auml;r tomt om inte satt till n&aring;got.</li>
			<li>$node->index -- Ordningsnumret f&ouml;r den h&auml;r noden i hela menyn</li>
			<li>$node->parent -- True (sant) om noden &auml;r f&ouml;r&auml;lder till den aktuella sidan</li>
		</ul>
	</p>';
$lang['importtemplate'] = 'Importera mall till databas';
$lang['menumanager'] = 'Menyhanterare (Menu Manager)';
$lang['newtemplate'] = 'Nytt mallnamn';
$lang['nocontent'] = 'Inget inneh&aring;ll angivet';
$lang['notemplatefiles'] = 'Inga filbaserade mallar i %s';
$lang['notemplatename'] = 'Inget mallnamn angivet';
$lang['templatecontent'] = 'Mallinneh&aring;ll';
$lang['templatenameexists'] = 'En mall med detta namnet finns redan';
$lang['utma'] = '156861353.120607462.1233740298.1239218099.1239222156.7';
$lang['utmz'] = '156861353.1239218099.6.5.utmccn=(referral)|utmcsr=umearugby.se|utmcct=/admin/moduleinterface.php|utmcmd=referral';
$lang['utmc'] = '156861353';
$lang['utmb'] = '156861353';
?>