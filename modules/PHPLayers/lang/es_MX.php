<?php
$lang['help'] = <<<EOF
	<h3>¿Que hace esto?</h3>
	<p>Crea un menu vertical en dhtml.</p>
	<h3>Como lo uso?</h3>
	<p>
	
	Solo agrega al modulo a una plantilla o pagina asi: <code>{cms_module module='phplayers'}</code></p>
	<h3>¿Que opciones acepta?</h3>
	<p>
	<ul>
		<li><em>(optional)</em> <tt>showadmin</tt> - 1/0, si quieres o no mostrar el enlace al admin.</li>
		<li><em>(optional)</em> <tt>start_element</tt> - la jerarquia de tu elemento (ejemplo : 1.2 o 3.5.1). Este parametro ajusta la raiz del menu.</li>
		<li><em>(optional)</em> <tt>number_of_levels</tt> - una integral, el numero de niveles que quieres mostrar en tu menu.</li>
		<li><em>(optional)</em> <tt>horizontal</tt> - 1/0, si quieres un menu horizontal en vez de vertical.</li>
		<li><em>(optional)</em> <tt>id</tt> - texto sin espacios o caracteres especiales, predeterminado: menu1. Debes especificarlo si quieres mas de un menu por pagina.</li>
		<li><em>(optional)</em> <tt>relative</tt> - 1/0, si esta ajustado generara solamente los hijos de la pagina actual. Esto es muy util si quieres agregar menus sensibles al contenido.</li>
		<li><em>(optional)</em> <tt>tree</tt> - 1/0, esta opcion genera un menu en arbol.</li>

	</ul>
	</p>
EOF;
?>
