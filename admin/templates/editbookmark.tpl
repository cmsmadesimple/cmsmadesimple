{if $error_msg}
<div class="error">
		<div class="pageerrorcontainer"><ul style="list-style:none" class="pageerror">{$error_msg}</ul></div>
	</div>
{/if}
<div class="pagecontainer">
	<div class="pageoverflow">
    {$header_name}
	</div><!-- pageoverflow -->

<form method="post" action="editbookmark.php">

			{admin_input type='input' label='title' name='title' id='title' value=$title maxlength='255'}        
            {admin_input type='input' label='url' name='url' id='url' value=$url class='standard'}
             <div class="input-hidden">
            {admin_input type='hidden' label='noneLabel' name='bookmark_id' id='bookmark_id' value=$bookmark_id}
            {admin_input type='hidden' label='noneLabel' name='editbookmark' id='editbookmark' value='true'}
            {admin_input type='hidden' label='noneLabel' name='userid' id='userid' value=$userid}
            </div> <!--input-hidden-->
            {include file='elements/buttons.tpl'}
</form>    
</div><!-- pagecontainer -->
<p class="pageback"><a class="pageback" href="{$back_url}">&#171; {lang string='back'}</a></p>
