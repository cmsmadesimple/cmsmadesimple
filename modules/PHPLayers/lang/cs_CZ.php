<?php
$lang['help'] = <<<EOF
	<h3>Co delá?</h3>
	<p>Zobrazuje vertikální dhtml menu.</p>
	<h3>Jak to používat?</h3>
	<p>Stačí vložit modul do Vaší šablony/stránky, jako: <code>{cms_module module='phplayers'}</code></p>
	<h3>Jaké jsou parametry?</h3>
	<p>
	<ul>
		<li><em>(volitelné)</em> <tt>showadmin</tt> - 1/0, Zobrazit nebo schovat odkaz na administraci v menu.</li>
		<li><em>(volitelné)</em> <tt>start_element</tt> - hierarchie položek (např.: 1.2 nebo 3.5.1). Toto nastaví kořen menu.</li>
		<li><em>(volitelné)</em> <tt>number_of_levels</tt> - celé číslo, počet úrovní, které chcete zobrazit v menu.</li>
		<li><em>(volitelné)</em> <tt>horizontal</tt> - 1/0, pokud chcete radsi horizontální menu místo vertikálního.</li>
		<li><em>(volitelné)</em> <tt>id</tt> - text bez mezer nebo speciálních znaků, standardně: menu1. Musíte zadat, pokud chcete mít na stránce více jak jedno menu.</li>
		<li><em>(volitelné)</em> <tt>relative</tt> - 1/0, pokud je nastaveno, v menu budou pouze podstránky té aktuální. Toto je velmi užitečné při požadavku menu závislého na obsahu.</li>
		<li><em>(volitelné)</em> <tt>tree</tt> - 1/0, tato volba generuje stromové menu.</li>

	</ul>
	</p>
EOF;
?>
