{* this is a search form template for the Simplex theme *}
{* note the script type below:
   Because the simplex theme uses {cms_queue_script} and {cms_render_scripts} to handle javascript
   We can use this special script type to defer execution of the javascript within until after
   the other scripts are loaded.

   The Simplex theme also enables the jquery, and jquery-ui libraries.  So we can utilize them here.
*}
<div class='five-col search noprint' role='search'>
<script type="text/cms_javascript">
$(function(){
   function split( val ) {
      return val.split( /[ ]+/ );
   }
   function extractLast( term ) {
      return split( term ).pop();
   }

   $('#simplex-searchinput').autocomplete({
        source: function( request, response ) {
          console.log('in source');
          $.getJSON( '{cms_action_url action=ajax_getwords forjs=1}&showtemplate=false', {
            term: extractLast( request.term )
          }, response );
        },
        search: function() {
          var term = extractLast( this.value );
          if ( term.length < 2 ) return false;
        },
        focus: function() {
          return false;
        },
        select: function( event, ui ) {
          var terms = split( this.value );
          terms.pop();
          terms.push( ui.item.value );
          terms.push( "" );
          this.value = terms.join( " " );
          return false;
        }
   });
});
</script>

{* this is the form itself *}
{form_start action=dosearch method=$form_method returnid=$destpage inline=$inline id='simplex-searchform' use_like=1}
   <label for='simplex-searchinput' class='visuallyhidden'>{$searchprompt}:</label>
   <input type='search' class='search-input' id='simplex-searchinput' name='{$search_actionid}searchinput' size='20' maxlength='50' value='' placeholder='{$searchtext}' required/><i class='icon-search' aria-hidden='true'></i>
   {if isset($hidden)}{$hidden}{/if}
{form_end}
</div>