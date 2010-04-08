<div class="pagecontainer">
  <div class="pageoverflow">
  {$theme_object->show_header('editcontent')}
  </div>

  <div id="Edit_Content_Result"></div>

  {if isset($error)}
  <div class="pageerrorcontainer"><p class="pageerror">{$error}</p></div>
  {else}

  {$javascript}

  {$theme_object->start_tab_headers()}
  {foreach from=$tabnames key='name' item='label'}
    {$theme_object->set_tab_header($name,$label)}
  {/foreach}
  {$theme_object->end_tab_headers()}

  {$formstart}
  {$theme_object->start_tab_content()}
    {foreach from=$tabnames key='name' item='label'}
    {$theme_object->start_tab($name)}
      {if isset($tabelements.$name)}
        {assign var='elements' value=$tabelements.$name}
        {foreach from=$elements item='element'}
          <div class="pageoverflow">
            <p class="pagetext">{$element[0]}</p>
            <p class="pageinput">{$element[1]}</p>
          </div>
        {/foreach}
      {elseif isset($tab_contents.$name)}
        {$tab_contents.$name}
      {/if}
    {$theme_object->end_tab()}
    {/foreach}
  {$theme_object->end_tab_content()}
  {$formend}

  {/if}
</div>{* pagecontainer *}
