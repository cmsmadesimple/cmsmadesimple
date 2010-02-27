<?php
$lang['help_loadprops'] = 'Use este par&acirc;metro quando utilizar propriedades avan&ccedil;adas no Template do Gestor de Menus. Este par&acirc;metro ir&aacute; for&ccedil;ar o carregamento de todos os conte&uacute;dos propriedades de cada n&oacute; (como extra1, imagem, miniatura, etc.) e ir&aacute; aumentar dramaticamente o n&uacute;mero de consultas necess&aacute;rias para construir um menu, e aumenta os requisitos de mem&oacute;ria, mas vai permitir menus mais avan&ccedil;ados';
$lang['readonly'] = 'somente de leitura';
$lang['error_templatename'] = 'N&atilde;o pode especificar um template com um nome terminado .tpl';
$lang['this_is_default'] = 'Menu template padr&atilde;o';
$lang['set_as_default'] = 'Definir Menu template padr&atilde;o';
$lang['default'] = 'Padr&atilde;o';
$lang['templates'] = 'Templates ';
$lang['addtemplate'] = 'Adicionar Template';
$lang['areyousure'] = 'Tem a Certeza que pertende eliminar?';
$lang['changelog'] = '	<ul>
<li>1.6 - Re-design admin interface, allow setting the default menu manager template.</li>
        <li>1.5.4 - Minor bugfix, now require CMS 1.5.3.</li>
        <li>1.5.3 - Support for syntax hilighter.</li>
        <li>1.5.2 - Added more fields available in each node in the template.</li>
        <li>1.5 - Bump version to be compatible with 1.1 only, and add the SetParameterTypes calls</li>
	<li>1.4.1 -- Fix a problem where menus would not show if includeprefix was not specified.
	<li>1.4 -- Accept a comma separated list of includeprefixes or excludeprefixes</li>
	<li>1.3 -- Added includeprefix and excludeprefix params</li>
	<li>1.1 -- Added handling of target parameter, mainly for the Link content type</li>
	<li>1.0 -- Initial Release</li>
	</ul> ';
$lang['dbtemplates'] = 'Base de Dados Templates';
$lang['description'] = 'Gerir os templates do menu para exibir menus de qualquer forma imagin&aacute;vel.';
$lang['deletetemplate'] = 'Eliminar Template';
$lang['edittemplate'] = 'Editar Template';
$lang['filename'] = 'Nome do Ficheiro';
$lang['filetemplates'] = 'Arquivo Templates';
$lang['help_includeprefix'] = 'Incluir apenas os itens que a p&aacute;gina &#039;alias&#039; coincidir com um dos especificados (separados por v&iacute;rgula) prefixos. Este par&acirc;metro n&atilde;o pode ser combinada com o &#039;excludeprefix&#039; par&acirc;metro.';
$lang['help_excludeprefix'] = 'Excluir todos os itens (e seus filhos), que o &#039;alias&#039; da p&aacute;gina coincidir com um dos especificados (separados por v&iacute;rgula) prefixos. Esse par&acirc;metro n&atilde;o deve ser utilizado em conjunto com o &#039;includeprefix&#039; par&acirc;metro.';
$lang['help_collapse'] = 'Ligue (definir a 1) para ter o menu de itens escondidos, que n&atilde;o est&atilde;o relacionadas com a actual p&aacute;gina seleccionada.';
$lang['help_items'] = 'Use este item para seleccionar uma lista de p&aacute;ginas que este menu deve exibir. O valor deve ser uma lista de p&aacute;gina apelidos separados por v&iacute;rgulas.';
$lang['help_number_of_levels'] = 'Esta defini&ccedil;&atilde;o s&oacute; ir&aacute; permitir o menu a exibir apenas um determinado n&uacute;mero de n&iacute;veis profundos.';
$lang['help_show_all'] = 'Essa op&ccedil;&atilde;o far&aacute; com que o menu mostre todos os &#039;nodes&#039;, mesmo que n&atilde;o estejam definidas para mostrar no menu. Ela ainda n&atilde;o ir&aacute; exibir p&aacute;ginas inativas no entanto.';
$lang['help_show_root_siblings'] = 'Esta op&ccedil;&atilde;o s&oacute; se torna &uacute;til se start_element ou start_page forem utilizados. &Eacute; basicamente exibir&aacute; os irm&atilde;os ao lado dos seleccionados start_page/elemento.';
$lang['help_start_level'] = 'Esta op&ccedil;&atilde;o ter&aacute; o menu a exibir apenas os itens ao iniciar um determinado n&iacute;vel. Um exemplo seria f&aacute;cil se tivesse um menu na p&aacute;gina com number_of_levels =&#039;1 &#039;. Ent&atilde;o, como um segundo menu,  ter&aacute; start_level =&#039;2 &#039;. Agora, o sue segundo menu ir&aacute; mostrar itens com base naquilo que for seleccionado no primeiro menu.';
$lang['help_start_element'] = 'Inicia o menu a exibir um determinado elemento start_page e mostrando as filhas s&oacute;. Retorna uma hierarquia de posi&ccedil;&atilde;o (ex. 5.1.2).';
$lang['help_start_page'] = 'Inicia o menu a exibir um determinado elemento start_page e mostrando as filhas s&oacute;. Retorna uma p&aacute;gina alias.';
$lang['help_template'] = 'O template a utilizar para exibir o menu. Template ser&atilde;o provenientes da base de dados, a menos que o nome do modelo termine  .tpl, nesse caso em vir&atilde;o a partir de um arquivo no diret&oacute;rio templates do MenuManager (padr&atilde;o para simple_navigation.tpl)';
$lang['help'] = '<h3>What does this do?</h3>
	<p>Menu Manager is a module for abstracting menus into a system that&#039;s easily usable and customizable.  It abstracts the display portion of menus into smarty templates that can be easily modified to suit the user&#039;s needs. That is, the menu manager itself is just an engine that feeds the template. By customizing templates, or make your own ones, you can create virtually any menu you can think of.</p>
	<h3>How do I use it?</h3>
	<p>Just insert the tag into your template/page like: <code>{menu}</code>.  The parameters that it can accept are listed below.</p>
	<h3>Why do I care about templates?</h3>
	<p>Menu Manager uses templates for display logic.  It comes with three default templates called cssmenu.tpl, minimal_menu.tpl and simple_navigation.tpl. They all basically create a simple unordered list of pages, using different classes and ID&#039;s for styling with CSS.</p>
	<p>Note that you style the look of the menus with CSS. Stylesheets are not included with Menu Manager, but must be attached to the page template separately. For the cssmenu.tpl template to work in IE you must also insert a link to the JavaScript in the head section of the page template, which is necessary for the hover effect to work in IE.</p>
	<p>If you would like to make a specialized version of a template, you can easily import into the database and then edit it directly inside the CMSMS admin.  To do this:
		<ol>
			<li>Click on the Menu Manager admin.</li>
			<li>Click on the File Templates tab, and click the Import Template to Database button next to i.e. simple_navigation.tpl.</li>
			<li>Give the template copy a name.  We&#039;ll call it &quot;Test Template&quot;.</li>
			<li>You should now see the &quot;Test Template&quot; in your list of Database Templates.</li>
		</ol>
	</p>
	<p>Now you can easily modify the template to your needs for this site.  Put in classes, id&#039;s and other tags so that the formatting is exactly what you want.  Now, you can insert it into your site with {menu template=&#039;Test Template&#039;}. Note that the .tpl extension must be included if a file template is used.</p>
	<p>The parameters for the $node object used in the template are as follows:
		<ul>
			<li>$node->id -- Content ID</li>
			<li>$node->url -- URL of the Content</li>
			<li>$node->accesskey -- Access Key, if defined</li>
			<li>$node->tabindex -- Tab Index, if defined</li>
			<li>$node->titleattribute -- Description or Title Attribute (title), if defined</li>
			<li>$node->hierarchy -- Hierarchy position, (e.g. 1.3.3)</li>
			<li>$node->depth -- Depth (level) of this node in the current menu</li>
			<li>$node->prevdepth -- Depth (level) of the node that was right before this one</li>
			<li>$node->haschildren -- Returns true if this node has child nodes to be displayed</li>
			<li>$node->menutext -- Menu Text</li>
			<li>$node->alias -- Page alias</li>
			<li>$node->target -- Target for the link.  Will be empty if content doesn&#039;t set it.</li>
			<li>$node->index -- Count of this node in the whole menu</li>
			<li>$node->parent -- True if this node is a parent of the currently selected page</li>
		</ul>
	</p>';
$lang['importtemplate'] = 'Importar Modelo da Base de dados';
$lang['menumanager'] = 'Gestor de Menu';
$lang['newtemplate'] = 'Novo nome do template';
$lang['nocontent'] = 'Nenhum conte&uacute;do dado ';
$lang['notemplatefiles'] = 'Nenhum arquivo de templates em %s';
$lang['notemplatename'] = 'Nenhum nome do template dado.';
$lang['templatecontent'] = 'Template Conte&uacute;do';
$lang['templatenameexists'] = 'Um Template com esse nome j&aacute; existe';
$lang['utma'] = '156861353.3089450601847726600.1241711306.1242653258.1242681672.52';
$lang['utmz'] = '156861353.1242681672.52.18.utmcsr=patriciavaz.nunodev.homelinux.com|utmccn=(referral)|utmcmd=referral|utmcct=/site/';
$lang['utmc'] = '156861353';
$lang['utmb'] = '156861353.1.10.1242681672';
?>