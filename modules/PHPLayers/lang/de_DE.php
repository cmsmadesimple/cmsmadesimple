<?php
$lang['help'] = <<<EOF
	<h3>Was kann dieses Modul?</h3>
	<p>Es kann ein vertikales oder horizontales Men&uuml; darstellen.</p>
	<p>Die Men&uuml;eintr&auml;ge werden automatisch aus den vorhandenen aktiven Inhalten gebildet.</p>
	<p>Dieses System verwendet zur Darstellung eine eigene CSS - Datei:modules/PHPlayers/layersmenu-cms.css .<br/>
        Wird eine &Auml;nderung der Optik gew&uuml;scht, muss diese Datei angepa&szlig;t werden.  </p>
	<h3>Wie wird es eingesetzt?</h3>
	<p>Es muss lediglich nachfolgender Tag in eine individuelle Seite oder global in das Template eingesetzt werden:<br /><br />
           Beispiel: <code>{cms_module module='phplayers'}</code></p>
	<h3>Welche Parameter gibt es?</h3>
	<p>
	<ul>
		<li><em>(optional)</em> <tt>showadmin</tt> - 1/0, 1 wenn ein Link zur Adminseite eingesetzt werden soll, 0 wenn nicht.</li>
		<li><em>(optional)</em> <tt>start_element</tt> - Damit wird die erste Seite festgelegt, als Parameter muss die Hierarchie des Elements eingesetzt werden.(z.B. : 1.2 or 3.5.1 ).</li>
		<li><em>(optional)</em> <tt>number_of_levels</tt> - Ein ganzzahliger Wert (nicht der Hirarchiewert), der die Anzahl der Untermen&uuml;s (Level) bestimmt, die dargestellt werden.</li>
		<li><em>(optional)</em> <tt>horizontal</tt> - 1/0, 1 f&uuml;r horizontale, 0 f&uuml;r vertikale Darstellung des Men&uuml;s.</li>
	</ul>
	</p>
EOF;
?>
