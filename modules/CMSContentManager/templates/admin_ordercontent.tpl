<script type="text/javascript">
    function parseTree(ul) {
        var tags = [];
        ul.children('li').each(function() {
            var subtree = $(this).children('ul');
            tags.push($(this).attr('id'));
            if (subtree.size() > 0) {
                tags.push(parseTree(subtree));
            }
        });
        return tags;
    }


    $(document).ready(function() {
        $(document).on('click', '#btn_submit', function(ev) {
	    ev.preventDefault();
	    var form = $(this).closest('form');
            cms_confirm('{$mod->Lang('confirm_reorder')|escape:'javascript'}').done(function(){
                var tree = $.toJSON(parseTree($('#masterlist')));
                var ajax_res = false;
                $('#orderlist').val(tree);
		form.submit();
            });
        });

        $('ul.sortable').nestedSortable({
            disableNesting : 'no-nest',
            forcePlaceholderSize : true,
            handle : 'div',
            items : 'li',
            opacity : .6,
            placeholder : 'placeholder',
            tabSize : 20,
            tolerance : 'pointer',
            listType : 'ul',
            toleranceElement : '> div'
        });
    });
</script>

{function display_tree depth=0}
	{foreach $list as $node}
		{assign var='obj' value=$node->getContent(false,true,false)}
		<li id="page_{$obj->Id()}" {if !$obj->WantsChildren()}class="no-nest"{/if}>
			<div class="label" {if !$obj->Active()}style="color: red;"{/if}>
				<span>&nbsp;</span>{$obj->Hierarchy()}:&nbsp;{$obj->Name()|cms_escape}{if !$obj->Active()}&nbsp;({$mod->Lang('prompt_inactive')}){/if} <em>({$obj->MenuText()|cms_escape})</em>
			</div>
			{if $node->has_children()}
			<ul>
				{assign var='list' value=$node->getChildren(false,true)}
				{display_tree list=$list depth=$depth+1}
			</ul>
			{/if}
		</li>
	{/foreach}
{/function}

<h3>{$mod->Lang('prompt_ordercontent')}</h3>
{form_start action='admin_ordercontent' id="theform"}
<input type="hidden" id="orderlist" name="{$actionid}orderlist" value=""/>
<div class="information">
	{$mod->Lang('info_ordercontent')}
</div>
<div class="pageoverflow">
	<p class="pagetext"></p>
	<p class="pageinput">
		<input id="btn_submit" type="submit" name="{$actionid}submit" value="{$mod->Lang('submit')}"/>
		<input type="submit" name="{$actionid}cancel" value="{$mod->Lang('cancel')}"/>
		<input id="btn_revert" type="submit" name="revert" value="{$mod->Lang('revert')}"/>
	</p>
</div>
<div class="pageoverflow">
	{assign var='list' value=$tree->getChildren(false,true)}
	<ul id="masterlist" class="sortableList sortable">
		{display_tree list=$list}
	</ul>
</div>
{form_end}