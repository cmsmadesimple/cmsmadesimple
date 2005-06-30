<?php
$lang['help'] = <<<EOF
	<h3>Hvad gør dette modul?</h3>
	<p>Viser en lodret dhtml menu.</p>
	<h3>Hvordan bruges det?</h3>
	<p>Indsæt blot modulet i din side eller skabelon sådan her:: <code>{cms_module module='phplayers'}</code></p>
	<h3>Hvilke parametre findes der?</h3>
	<p>
	<ul>
		<li><em>(valgfrit)</em> <tt>showadmin</tt> - kan være 1 eller 0, angiver om du vil ønsker et link til administrationen vist.</li>
		<li><em>(valgfrit)</em> <tt>start_element</tt> - hierakiet for det element du ønsker skal være roden for menuen (for eksempel: 1.2 or 3.5.1).</li>
		<li><em>(valgfrit)</em> <tt>number_of_levels</tt> - et tal der angiver hvor mange niveau'er du ønsker der skal vises i menuen.</li>
		<li><em>(valgfrit)</em> <tt>horizontal</tt> - kan være 1 eller 0, angiver om du vil ha en vandret menu i stedet for en lodret.</li>
		<li><em>(valgfrit)</em> <tt>id</tt> - tekst uden mellemrum eller specielle tegn, standard: menu1. Dette skal angives hvis du ønskermere en en menu på din side.</li>
		<li><em>(valgfrit)</em> <tt>relative</tt> - kan være 1 eller 0, angiver om menuen kun skal vise "børn" til den aktuelle side. Dette er smart til at vise kontekst sensitive menuer.</li>
		<li><em>(valgfrit)</em> <tt>tree</tt> - kan være 1 eller0, angiver om et helt menu-træ skal vises.</li>

	</ul>
	</p>
EOF;
?>
