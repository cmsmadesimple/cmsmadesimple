{validation_errors for=$user_object}
{$header_name}
<form method="post" action="{$action}">		
{if $user_object->id > 0}
	<input type="hidden" name="user_id" value="{$user_object->id}" />
{/if}
	<div id="page_tabs">
		<ul>
			<li><a href="#account"><span>Account Details</span></a></li>
		</ul>
	    <div id="account">
			{admin_input type='input' label='username' id='username' name='user[name]' value=$user_object->name}
			{admin_input type='password' label='password' id='password' name='user[password]' value=$user_object->password}
			{admin_input type='password' label='passwordagain' id='passwordagain' name='user[passwordagain]' value=$user_object->passwordagain}		
			{admin_input type='input' label='firstname' id='firstname' name='user[firstname]' value=$user_object->firstname}			
			{admin_input type='input' label='lastname' id='lastname' name='user[lastname]' value=$user_object->lastname}						
			{admin_input type='input' label='email' id='email' name='user[email]' value=$user_object->email}
			{admin_input type='input' label='openid' id='openid' name='user[openid]' value=$user_object->openid}						
			{admin_input type='checkbox' label='active' id='active' name='user[active]' selected=$user_object->active}
		</div>
	</div>
	{include file='elements/buttons.tpl'}	
</form>
<script type="text/javascript">
<!--
	$('#page_tabs').tabs('account');
//-->
</script>