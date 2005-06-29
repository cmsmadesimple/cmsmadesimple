<?php
$lang['addacomment'] = 'Agregar Comentario';
$lang['cancel'] = 'Cancelar';
$lang['comment'] = 'Comentario';
$lang['error'] = 'Error';
$lang['errorenterauthor'] = 'Agrega un Autor';
$lang['errorentercomment'] = '¿Agrega un Comentario (Para eso estoy no?)';
$lang['submit'] = 'Enviar';
$lang['yourname'] = 'Tu nombre';
$lang['help'] = <<<EOD
	<h3>¿Que hace esto?</h3>
	<p>El modulo de comentarios esta basado en etiquetas. Es usado para agregar comentarios a paginas individuales y puede ser leido por los usuarios que despues visitan la pagina. La razon practica de este modulo es para la documentacion de paginas, para que los usuarios puedan agregar comentarios adicionales e informacion a la pagina.</p>
	<h3>Como la uso?</h3>
	<p>Comments es un modulo basado en etiqueta. Se agega a tu pagina o plantilla al usar  cms_module tag.  Por ejemplo: <code>{cms_module module="comments"}</code></p>
	<h3>¿Que opciones tiene?</h3>
	<p>
	<ul>
		<li><em>(optional)</em> number="5" - Numero maximo de comentarios a mostrar -- vacio muestra todos</li>
		<li><em>(optional)</em> dateformat - Formato de Fecha/Hora usando parametros de la funcion strftime. Ve <a href="http://php.net/strftime" target="_blank">here</a> para consultar una lista de opciones e informacion.</li>
	</ul>
EOD;
?>
