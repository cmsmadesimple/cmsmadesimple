<?php
/**
 * Smarty {setlist} compiler function plugin
 *
 * Purpose:  populate an array from a JSON-like string
 */
function smarty_function_setlist($params, &$template) {
  $smarty = $template->smarty;
	$newlist = array();
	if (!isset($params['var']))
		{
		$params['var']='var';
		}
	if (substr($params['value'],0,1) != '{')
		{
		$params['value'] = '{'.$params['value'];
		}
	$opens = substr_count($params['value'], '{');
	$closes =  substr_count($params['value'], '}');
	while ($closes < $opens)
		{
		$params['value'] = $params['value'].'}';
		$closes += 1;
		}
	$newlist = json_decode($params['value'],true);
	$smarty->assign($params['var'],$newlist);
}

function smarty_cms_help_function_setlist() {
	?>
	<p>Populate arrays directly in your template, e.g.:</p>
	<pre>
		{setlist var='varname' value='"red":"#f00","green":"#0f0","blue":"#00f","violet":"#f0f","yellow":"#ff0"'}
		{foreach from=$varname key=color item=colorcode}
		    {$color} is {$colorcode}<br />
		{/foreach}
	</pre>
	<p>It uses JSON syntax (with implicit curly-brace wrappers), so you can do crazy stuff if you choose to:</p>
	<pre>
		{capture assign="json_sample_struct"}"layered":{ldelim}"bar":"baz"{rdelim},"flat":"blank","layered2":{ldelim}"qux":"quux","crox":"bagg"{rdelim}{/capture}
		{setlist var='nested' value=$json_sample_struct}
	</pre>
	<p>This is useful for setting up lists of similar structure in your CSS.</p>
	<?php
}

function smarty_cms_about_function_setlist() {
?>
<p>Author: SjG &lt;sjg@cmsmodules.com&gt;</p>
<p>Version: 1.0</p>
<?php
}

?>