<?php
$lang['help'] = <<<EOF
	<h3>Ką jis daro?</h3>
	<p>Atvaizduoja vertikalų dhtml meniu.</p>
	<h3>Kaip jį naudoti?</h3>
	<p>Įterpkite į sukurtą šabloną/puslapį šį kodą: {cms_module module='phplayers'}</code></p>
	<h3>Kokius jis priema parametrus?</h3>
	<p>
	<ul>
		<li><em>(optional)</em> <tt>showadmin</tt> - 1/0, pasirinkta reikšmė reiškia ar bus rodoma admin nuoroda.</li>
		<li><em>(optional)</em> <tt>start_element</tt> - elementų hierarchija (pvz : 1.2 or 3.5.1). Šis parametras nustato pagrindinį menu elementą.</li>
		<li><em>(optional)</em> <tt>number_of_levels</tt> - sveikas skaičius, reikšmė reiškia kiek lygių meniu bus rodomas.</li>
		<li><em>(optional)</em> <tt>horizontal</tt> - 1/0, pasirinkta reikšmė reiškia koks bus meniu pozicionavimas: ar vertikalus ar horizontalus.</li>
		<li><em>(optional)</em> <tt>id</tt> - tekstas be specialių simbolių ir tarpų, sisteminis: menu1. Jūs turėtumėte nustatyti kitą reikšmę jeigu naudosite daugiau kaip vieną meniu.</li>
		<li><em>(optional)</em> <tt>relative</tt> - 1/0, jeigu nustatyta - meniu atvaizduos tik vaikus, priklausančius konkrečiam puslapiui.</li>
		<li><em>(optional)</em> <tt>tree</tt> - 1/0, ši reikšmė atvaizduos medžio tipo meniu.</li>

	</ul>
	</p>
EOF;
?>
