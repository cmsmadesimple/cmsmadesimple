<script>
$(function(){
   var _url = '{cms_action_url module=News2 action=admin_ajax_relatedarticles forjs=1}&showtemplate=false'
   var _id = '#_{$def->name}'
   var _fldname = '{$def->name}[]'
   var _value = JSON.parse('{json_encode($value)}');
   var _maxn = parseInt('{$def->getExtra('maxsize')}');
   var inp  = $(_id)
   var cont = $(_id).closest('.related-cont')
   var list_zone = $('div.related-list',cont);

   var itemExists = function( val ) {
      var out = false;
      $('.related-item',cont).each(function(index,item){
         if( $(item).data('id') === val ) out = true;
      })
      return out;
   }

   var createRow = function( value, label ) {
      var el = $('<p/>').addClass('related-item').addClass('grid_12').data('id', value)
      var ch1 = $('<span/>').addClass('related-item-label').text(label)
      var ch2 = $('<span/>').addClass('related-item-del').text('X').prop('data-id',value)
      var ch3 = $('<input/>').prop('name',_fldname).prop('type','hidden').val(value);
      el.append(ch1).append(ch2).append(ch3);
      list_zone.append(el);
   }

   var setState = function() {
      var num = $('.related-item',cont).length
      var flag = num >= maxn
      inp.prop('disabled',flag)
   }

   cont.on( 'click', '.related-item-del', function(ev){
      var par = $(this).closest('.related-item')
      par.remove()
      setState()
   })

   inp.autocomplete({
     source: _url,
     minLength: 2,
     select: function( event, ui ) {
       event.preventDefault();
       // see if we have this item already
       inp.val('');
       if( ui.item.value > 0 && !itemExists( ui.item.value) ) {
           // add it
	   createRow( ui.item.value, ui.item.label )
	   setState()
       }
     }
   })

   if( _value && _value.length > 0 ) {
      // get the data via ajax
      $.ajax({
         url: '{cms_action_url module=News2 action='admin_ajax_getarticlelist' forjs=1}&showtemplate=false&list=' + _value.join(',')
      }).then(function(data){
         if( data && typeof data == 'object' && data.length > 0 ) {
	    data.forEach((item) => {
	       createRow( item.value, item.label )
            })
	    setState()
	 }
      })
   }
})
</script>
<div class="c_full cf related-cont">
   <label class="grid_3" for="_{$def->name}">{$def->label}</label>
   <div class="grid_8">
      <input class="grid_12" id="_{$def->name}"/>
      <div class="related-list"></div>
   </div>
</div>