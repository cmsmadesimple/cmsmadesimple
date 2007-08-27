<table>
{foreach from=$versions item=version}
<tr>
  <td>{$version->modified_date}</td>
</tr>
{/foreach}
</table>
