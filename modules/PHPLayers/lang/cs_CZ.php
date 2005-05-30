<?php
$lang['help'] = <<<EOF
	<h3>Co dělá tento modul?</h3>
	<p>Slouží pro zobrazování dhtml menu.</p>
	<h3>Jak se používá?</h3>
	<p>Stačí vložit modul cms_module do Vaší šablony nebo stránky. Příklad: <code>{cms_module module='phplayers'}</code></p>
	<h3>Jaké jsou parametry?</h3>
	<p>
	<ul>
		<li><em>(volitelné)</em> <tt>showadmin</tt> - 1/0, Zobrazit nebo schovat odkaz na administraci v menu.</li>
		<li><em>(volitelné)</em> <tt>start_element</tt> - hierarchie položek (např.: 1.2 nebo 3.5.1). Toto nastaví kořen menu.</li>
		<li><em>(volitelné)</em> <tt>number_of_levels</tt> - celé číslo, počet úrovní, které chcete zobrazit v menu.</li>
		<li><em>(volitelné)</em> <tt>horizontal</tt> - 1/0, pokud chcete radši horizontální menu místo vertikálního.</li>
		<li><em>(volitelné)</em> <tt>id</tt> - text bez mezer nebo speciálních znaků, standardně: menu1. Musíte zadat, pokud chcete mít na stránce více jak jedno menu.</li>
		<li><em>(volitelné)</em> <tt>relative</tt> - 1/0, pokud je nastaveno, v menu budou pouze podstránky té aktuální. Toto je velmi užitečné při požadavku menu závislého na obsahu.</li>
		<li><em>(volitelné)</em> <tt>tree</tt> - 1/0, tato volba generuje stromové menu.</li>

	</ul>
	</p>
EOF;
?>
