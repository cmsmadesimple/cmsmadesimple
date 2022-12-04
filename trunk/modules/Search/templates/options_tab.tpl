{$formstart}
<div class="pageoverflow">
  <p class="pagetext">{$prompt_stopwords}:</p>
  <p class="pageinput">{$input_stopwords|html_entity_decode}</p>
  <p class="pagetext">{$prompt_resetstopwords}:</p>
  <p class="pageinput">{$input_resetstopwords}</p>
</div>
<div class="pageoverflow">
  <p class="pagetext">{$prompt_stemming}:</p>
  <p class="pageinput">{$input_stemming}</p>
</div>
<div class="pageoverflow">
  <p class="pagetext">{$prompt_searchtext}:</p>
  <p class="pageinput">{$input_searchtext}</p>
</div>
<div class="pageoverflow">
  <p class="pagetext">{$prompt_savephrases}:</p>
  <p class="pageinput">{$input_savephrases}</p>
</div>
<div class="pageoverflow">
  <p class="pagetext">{$prompt_alpharesults}:</p>
  <p class="pageinput">{$input_alpharesults}</p>
</div>
<div class="pageoverflow">
  <p class="pagetext">{$prompt_resultpage}:</p>
  <p class="pageinput">{page_selector name="{$actionid}resultpage" value=$mod->GetPreference('resultpage')}</p>
</div>
<div class="pageoverflow">
  <p class="pagetext"></p>
  <p class="pageinput">{$submit}
    <input type="submit" name="{$actionid}reindex" value="{$mod->Lang('reindexallcontent')}" onclick="return confirm('{$mod->Lang('confirm_reindex')|escape:'javascript'}')"/>
  </p>
</div>
{$formend}