<?php
$lang['addarticle'] = 'Agregar Noticia';
$lang['addcategory'] = 'Agregar Categoria';
$lang['addnewsitem'] = 'Agregar Objeto Noticia';
$lang['allcategories'] = 'Todas las Categorias';
$lang['allentries'] = 'Todas las Noticias';
$lang['areyousure'] = 'Are you sure you want to delete?';
$lang['articles'] = 'Articulos';
$lang['cancel'] = 'Cancelar';
$lang['category'] = 'Categoria';
$lang['categories'] = 'Categorias';
$lang['content'] = 'Contenido';
$lang['dateformat'] = '%s no es el formato yyyy-mm-dd hh:mm:ss correcto';
$lang['delete'] = 'Borrar';
$lang['description'] = 'Agregar, editar y quitar Noticias';
$lang['detailtemplate'] = 'Detalle de Plantilla';
$lang['displaytemplate'] = 'Mostrar Plantilla';
$lang['edit'] = 'Editar';
$lang['enddate'] = 'Fecha Final';
$lang['endrequiresstart'] = 'Agregar una fecha final requiere que tambien exista una fecha de inicio';
$lang['entries'] = '%s Noticias';
$lang['expiry'] = 'Expria';
$lang['filter'] = 'Filtrar';
$lang['more'] = 'Mas';
$lang['moretext'] = 'Mas Texto';
$lang['name'] = 'Nombre';
$lang['news'] = 'Noticias';
$lang['newcategory'] = 'Nueva Categoria';
$lang['needpermission'] = 'Necesitas el permiso de \'%s\' para hacer esta funcion.';
$lang['nocategorygiven'] = 'No Diste una Categoria';
$lang['nocontentgiven'] = 'No Diste Contenido';
$lang['noitemsfound'] = '<strong>No</strong> entontramos nada en la categoria: %s';
$lang['nopostdategiven'] = 'No Diste Fecha de Escritura';
$lang['note'] = '<em>Nota:</em> Las fechas deben tener el formato \'yyyy-mm-dd hh:mm:ss\'.';
$lang['notitlegiven'] = 'No Diste un Titulo';
$lang['numbertodisplay'] = 'Numero a mostrar (vacio muestra todos)';
$lang['print'] = 'Imprimir';
$lang['postdate'] = 'Fecha Escrito';
$lang['postinstall'] = 'Asegurate de haber puesto los permisos de "Modificar Noticias" para los usuarios que administraran las noticias.';
$lang['rsstemplate'] = 'Plantilla de RSS';
$lang['selectcategory'] = 'Seleccionar Categoria';
$lang['sortascending'] = 'Organizar Ascendientemente';
$lang['startdate'] = 'Fecha de Inicio';
$lang['startrequiresend'] = 'Una fecha de inicio requiere una fecha final tambien';
$lang['submit'] = 'Enviar';
$lang['summary'] = 'Summary';
$lang['summarytemplate'] = 'Plantilla de resumen';
$lang['title'] = 'Titulo';
$lang['useexpiration'] = 'Use Expiration Date';
$lang['help'] = <<<EOF
	<h3>Que hace esto?</h3>
	<p>El modulo de noticias sirve para mostrar eventos de noticias en tu pagina, similar al estilo blog, excepto que tiene mas opciones! Cuandos e instala el modulo, se agrega una pagina de administracion de noticias al menu de abajo que te permitira seleccionar o agregar una categoria de noticias. Ya que una categoria es ceada o seleccionada, se mostrara una lista de noticias relacionadas con la categoria. Desde aqui, puedes agregar, editar o borrar noticias en la categoria correspondiente.</p>
	<h3>Seguridad</h3>
	<p>El usuario debe pertenecer al grupo con los permisos de 'Modificar Noticias' para poder agregar, editar o borrar las noticias.</p>
	<h3>¿Como lo uso?</h3>
	<p>
	
	La manera mas sencilla de usarlo es junto con la etiqueta cms_module. Esta insertara al modulo a tu plantilla o pagina en cualquier parte que quieras, y mostrara noticias. El codigo se veria asi: <code>{cms_module module="news" number="5" category="cerveza"}</code></p>
	<h3>Que opciones existen?</h3>
	<p>
	<ul>
	<li><em>(optional)</em> number="5" - Maximo numero de noticias a mostrar =- si lo dejas vacio muestra todos</li>
	<li><em>(optional)</em> makerssbutton="true" - Haz un boton para enlazar a un RSS FEED relacionado con la noticia.</li>
	<li><em>(optional)</em> category="category" - Solo muestra noticias de esta categoria y sus hijos. si la dejas vacia mostrara todas las categorias</li>
	<li><em>(optional)</em> moretext="more..." - Texto a mostrar al final de la noticia si supera el largo del resumen. Predeterminado es  "mas...".</li>
	<li><em>(optional}</em> summarytemplate="sometemplate.tpl" - Usa una plantilla aparte para mostrar el resumen de la noticia. Debe estar en modules/News/templates.
	<li><em>(optional}</em> detailtemplate="sometemplate.tpl" - Usa una plantilla aparte para mostrar el detalle del articulo. Debe estar en  modules/News/templates.
	<li><em>(optional)</em> sortby="news_date" - Ordenar con este criterio.  Opciones son: "news_date" (Fecha), "summary" (Resumen), "news_data" (Datos de la noticia), "news_category" (Categoria), "news_title" (Titulo).  Predeterminado es "news_date" (Fecha).</li>
	<li><em>(optional)</em> sortasc="true" - Ordenar noticias en orden ascendiente en vez de descendiente.</li>
	</ul>
	</p>
EOF;
?>
