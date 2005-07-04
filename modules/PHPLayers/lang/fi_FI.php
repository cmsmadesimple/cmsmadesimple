<?php
$lang['help'] = <<<EOF
	<h3>Mit‰ t‰m‰ moduuli tekee ?</h3>
	<p>Tulostaa (dhtml pohjaisen) pystysuoran valikon</p>
	<h3>Kuinka sit‰ k‰ytet‰‰n?</h3>
	<p>Lis‰‰ seuraavanlainen koodi mallipohjaan ja/tai sivulle: <code>{cms_module module='phplayers'}</code></p>
	<h3>Mit‰ parametrej‰ moduuli hyv‰ksyy?</h3>
	<p>
	<ul>
		<li><em>(optional)</em> <tt>showadmin</tt> - 1/0, tulostetaanko "yll‰pito" linkki vai ei </li>
		<li><em>(optional)</em> <tt>start_element</tt> - Aloituselementin numero. Esim: 1.2 or 3.5.1</li>
		<li><em>(optional)</em> <tt>number_of_levels</tt> - Kuinka syv‰lle valikko hierarkiaan moduuli n‰ytt‰‰</li>
		<li><em>(optional)</em> <tt>horizontal</tt> - 1/0, Haluatko menun vaakatasossa pystysuuntaisen sijaan.</li>
		<li><em>(optional)</em> <tt>id</tt> - Uniikki tunnistejono. Syˆt‰ t‰h‰n uniikki tunniste jos k‰yt‰t useampaa menua per 1 sivu. Jokaisella menulla tulee olla oma.</li>
		<li><em>(optional)</em> <tt>relative</tt> - 1/0, tulostaa vain relatiiviset menut</li>
		<li><em>(optional)</em> <tt>tree</tt> - 1/0, tulostaako moduuli valikon puumaisesti.</li>

	</ul>
	</p>
EOF;
?>
