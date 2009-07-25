<?php
$lang['help_loadprops'] = 'Use este parametro cuando se utilicen las propiedadas avanzadas en la plantilla de su Administrador de Menus. Este par&aacute;metro forzar&aacute; que se carguen todas las propiedades de cada nodo (tales como extra1, imagen, thumbnail, etc) y aumentar&aacute; dramaticamente el numero de consultas requeridas para construir un menu, asi como el requerimiento de memoria pero permite que se construyan menus avanzados.';
$lang['readonly'] = 'solo lectura';
$lang['error_templatename'] = 'No se puede especificar una plantilla cuyo nombre termine en  .tpl';
$lang['this_is_default'] = 'Plantilla de menu por defecto';
$lang['set_as_default'] = 'Usar como plantilla de menu por defecto';
$lang['default'] = 'Por defecto';
$lang['templates'] = 'Plantillas';
$lang['addtemplate'] = 'A&ntilde;adir Plantilla';
$lang['areyousure'] = '&iquest;Seguro que quiere borrar esto?';
$lang['changelog'] = '	<ul>
	<li>
	<p>Versi&oacute;n: 1.0</p>
	<p>Realizaci&oacute;n Inicial.</p>
	</li> 
	</ul> ';
$lang['dbtemplates'] = 'Plantillas de la Base de Datos';
$lang['description'] = 'Gestiona plantillas de menu que mostrar&aacute;n menus de cualquier clase.';
$lang['deletetemplate'] = 'Borrar Plantilla';
$lang['edittemplate'] = 'Editar Plantilla';
$lang['filename'] = 'Nombre de Archivo';
$lang['filetemplates'] = 'Archivo de Plantillas';
$lang['help_includeprefix'] = 'Incluir s&oacute;lo aquellos elementos cuyo alias de p&aacute;gina coincide con alguno de los prefijos  (separados por coma) especificados.  Este par&aacute;metro no se puede combinar con el par&aacute;metro de excluir prefijo.';
$lang['help_excludeprefix'] = 'Excluir todos los elementos (y sub-elementos) cuyo alias dep&aacute;gina coincide con alguno de los prefijos (separados por coma) especificados.  Este par&aacute;metro no se puede usar junto con el par&aacute;metro de incluir prefijo.';
$lang['help_collapse'] = 'Activarlo (poner a 1) para tener los items de men&uacute; escondidos no relacionados con la p&aacute;gina actual seleccionada.';
$lang['help_items'] = 'Usa esto opci&oacute;n para seleccoinar una lista de p&aacute;ginas a mostrar en este men&uacute;. El valor debe ser una lista de alias de p&aacute;ginas separadas con coma.';
$lang['help_number_of_levels'] = 'Esta opci&oacute;n permitir&aacute; al menu mostrar s&oacute;lo un cierto n&uacute;mero de niveles de despliegue.';
$lang['help_show_all'] = 'Esta opci&oacute;n mostrar&aacute; todos los nodos del men&uacute;, incluso si est&aacute;n configurados para no mostrarse en el men&uacute;.';
$lang['help_show_root_siblings'] = 'Esta opci&oacute;n es v&aacute;lida s&oacute;lo si se usan start_element or start_page.  B&aacute;sicamente, mostrar&aacute; los sub-menus al lado de cada elemento start_page seleccionado.';
$lang['help_start_level'] = 'Esta opci&oacute;n har&aacute; que el men&uacute; solo muestre items que comienzan en el nivel dado.  Un ejemplo muy f&aacute;cil ser&iacute;a si usted tiene un men&uacute; en la p&aacute;gina con number_of_levels=&#039;1&#039;.  Entonces como segundo men&uacute;, usted tiene start_level=&#039;2&#039;.  Entonces, su segundo men&uacute; va a mostrar items basados en aquello que es elegido en el primer men&uacute;.';
$lang['help_start_element'] = 'Comienza el men&uacute; mostrando en el dado start_element y solo exhibiendo ese elemento y su descendencia.  Toma una posici&oacute;n de jerarqu&iacute;a (p.ej. 5.1.2).';
$lang['help_start_page'] = 'Comienza el men&uacute; mostrando en la dada start_page y solo exhibiendo ese elemento y su descendencia.  Toma un alias de p&aacute;gina.';
$lang['help_template'] = 'La plantilla a usar para mostrar el menu.  Las plantillas se toman de la tabla de plantillas a menos que el nombre de la misma termine en .tpl, en cuyo caso se tomar&aacute; del directorio de plantillas del Gestor de Menus.';
$lang['help'] = '	<h3>&iquest;Qu&eacute; hace esto?</h3>
	<p>El Gestor de Men&uacute;s es un m&oacute;dulo para abstraer los men&uacute;s en un sistema que sea de f&aacute;cil uso y se pueda arreglar a gusto.  Hace una abstracci&oacute;n de la porci&oacute;n a mostrar de los men&uacute;s dentro de plantillas smarty que pueden ser f&aacute;cilmente modificadas para adaptarse a las necesidades del usuario. Entonces, el gestor de men&uacute;s en si es un motor que alimenta a la plantilla. Adaptando las plantillas, o haciendo las suyas propias, usted puede crear virtualmente cualquier men&uacute; que se le pueda ocurrir.</p>
	<h3>&iquest;C&oacute;mo uso esto?</h3>
	<p>Simplemente inserte una tag similar a la indicada abajo en su plantilla/p&aacute;gina: <code>{cms_module module=&#039;menumanager&#039;}</code>.  Los par&aacute;metros que la tag acepta se listan abajo.</p>
	<h3>&iquest;Porqu&eacute; preocuparse de las plantillas?</h3>
	<p>El Gestor de Men&uacute;s usa plantillas para mostrar su l&oacute;gica.  Viene acompa&ntilde;ado de tres plantillas por defecto llamadas bulletmenu.tpl, cssmenu.tpl y ellnav.tpl. Las tres crean una simple y sin orden lista de p&aacute;ginas, usando diferentes clases e IDs para dar estilo con CSS.  Son similares al sistema de men&uacute;s incluido en versiones anteriores: bulletmenu, CSSMenu y EllNav.</p>
	<p>Debe notar que usted va a dar estilo al aspecto de los men&uacute;s con CSS. Las Hojas de Estilo no est&aacute;n incluidas en el Gestor de Men&uacute;s, pero deben ser adjuntadas a la plantilla de la p&aacute;gina en forma separada. Para que la plantilla cssmenu.tpl trabaje en IE usted debe tambi&eacute;n incluir un enlace al JavaScript en la secci&oacute;n head de la plantilla de la p&aacute;gina, lo cual es necesario para que funcione el efecto hover en IE.</p>
	<p>Si usted desea hacer una versi&oacute;n especializada de la plantilla, puede importarla f&aacute;cilmente a la base de datos para luego editarla directamente en la administraci&oacute;n de CMSMS.  Para hacerlo:
		<ol>
			<li>Haga click en admin de Gestor de Men&uacute;s.</li>
			<li>Haga click en la solapa Archivo de Plantillas, y luego click en el bot&oacute;n Importar Plantilla a Base de Datos cercano a bulletmenu.tpl.</li>
			<li>De a la copia de la plantilla un nombre.  La vamos a llamar &quot;Plantilla Prueba&quot;.</li>
			<li>Usted deber&aacute; ahora pasar a ver &quot;Plantilla Prueba&quot; en su lista de Plantillas de la Base de Datos.</li>
		</ol>
	</p>
	<p>Ahora usted va a poder modificar f&aacute;cilmente las plantillas de acuerdo a sus necesidades para este sitio web.  Ponga en ella clases, ids i otras tags de manera tal que el formato sea aquel que usted quiere.  Una vez logrado, usted puede insertarla en su sitio utilizando a {cms_module module=&#039;menumanager&#039; template=&#039;Plantilla Prueba&#039;}. Note que la extensi&oacute;n .tpl debe ser incluida si lo que se utiliza es la plantilla.</p>
	<p>Los par&aacute;metros para el objeto $node usados en la plantilla son los siguientes:
		<ul>
			<li>$node->id -- ID Contenido</li>
			<li>$node->url -- URL del Contenido</li>
			<li>$node->accesskey -- Llave de Acceso, si definida</li>
			<li>$node->tabindex -- Tab Index, si definida</li>
			<li>$node->titleattribute -- Title Attribute (t&iacute;tulo), si definido</li>
			<li>$node->hierarchy -- Posici&oacute;n jer&aacute;rquica, (e.g. 1.3.3)</li>
			<li>$node->depth -- Profundidad (nivel) de este nodo en el actual men&uacute;</li>
			<li>$node->prevdepth -- Profundidad (nivel) de este nodo que estaba justo antes del actual</li>
			<li>$node->haschildren -- Devuelve un true si este nodo tiene nodos por debajo para ser mostrados</li>
			<li>$node->menutext -- Texto del Men&uacute;</li>
			<li>$node->index -- Cuenta de este nodo en el total del men&uacute;</li>
			<li>$node->parent -- True si el nodo esta por arriba de la p&aacute;gina actual seleccionada</li>
		</ul>
	</p>';
$lang['importtemplate'] = 'Importar Plantilla a Base de Datos';
$lang['menumanager'] = 'Gestor de Menu';
$lang['newtemplate'] = 'Nombre de Nueva Plantilla';
$lang['nocontent'] = 'Sin Contenido';
$lang['notemplatefiles'] = 'Sin plantillas en %s';
$lang['notemplatename'] = 'No has dado un nombre a la plantilla';
$lang['templatecontent'] = 'Contenido de la Plantilla';
$lang['templatenameexists'] = 'Ya existe una plantilla con este nombre';
$lang['utma'] = '156861353.661434885.1227567621.1241554232.1242674634.67';
$lang['utmz'] = '156861353.1241473702.63.28.utmccn=(referral)|utmcsr=forum.cmsmadesimple.org|utmcct=/index.php/board,30.0.html|utmcmd=referral';
$lang['utmb'] = '156861353';
$lang['utmc'] = '156861353';
?>