<div class="pagecontainer">
<h3>{'runuserplugin'|lang}</h3>
<form method="post" action="runuserplugin.php">
{$hidden}

<div class="pageoverflow">
  <p class="pagetext">{'name'|lang}:&nbsp;{$udt_name}</p>
</div>

<div class="pageoverflow">
  <p class="pagetext">{'code'|lang}:</p>
  <p class="pageinput">{$code}</p>
</div>

{if isset($output)}
<div class="pageoverflow">
  <p class="pagetext">{'output'|lang}:</p>
  <p class="pageinput">
     <textarea name="output" readonly="readonly">{$output}</textarea>
  </p>
</div>
{/if}

<div class="pageoverflow">
  <p class="pagetext"></p>
  <p class="pageinput">
     <input type="submit" name="submit" value="{'run'|lang}"/>
     <input type="submit" name="cancel" value="{'cancel'|lang}"/>
  </p>
</div>
</form>

</div>
