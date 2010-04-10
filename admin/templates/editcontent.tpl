<div class="pagecontainer">
  <div class="pageoverflow">
  {$theme_object->show_header('editcontent')}
  </div>

  {$javascript}

  <div id="Edit_Content_Result">
  {if isset($error)}
  {$theme_object->show_errors($error)}
  {/if}
  </div>

  {$formstart}
  {$theme_object->start_tab_headers()}
  {foreach from=$tabnames key='name' item='label'}
    {$theme_object->set_tab_header($name,$label)}
  {/foreach}
  {$theme_object->end_tab_headers()}

  {$theme_object->start_tab_content()}
    {foreach from=$tabnames key='name' item='label'}
    {$theme_object->start_tab($name)}
      {if !empty($tabelements.$name)}
        {assign var='elements' value=$tabelements.$name}
        {foreach from=$elements item='element'}
          <div class="pageoverflow">
            <p class="pagetext">{$element[0]}</p>
            <p class="pageinput">{$element[1]}</p>
          </div>
        {/foreach}
      {elseif isset($tabcontents.$name)}
        {$tabcontents.$name}
      {/if}
    {$theme_object->end_tab()}
    {/foreach}
  {$theme_object->end_tab_content()}
  {$formend}

</div>{* pagecontainer *}
