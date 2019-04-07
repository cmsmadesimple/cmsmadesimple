{if $user->firstname && $user->lastname}
   {$user->firstname|ucwords} {$user->lastname|ucwords}
{else}
   {$user->username}
{/if}