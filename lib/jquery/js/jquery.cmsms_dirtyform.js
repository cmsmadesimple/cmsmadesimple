(function($){

  $.widget('cmsms.dirtyForm', {
    options: {
      disabled: false,
      dirty: false,
      formClass: 'dirtyForm',
      onDirty: function(elem,form) {}
     },

    _create: function() {
      this.element.find('form').data('cmsms.dirtyForm',this);
      this.element.find('input:not([type=submit]), select, textarea').on('change',function() {
        var form = $(this).closest('form');
        var ob = $(form).data('cmsms.dirtyForm');
  	if( ob.options.disabled == false && ob.options.dirty == false ) {
          ob._setOption('dirty',true);
	}
      });
      $(window).bind('beforeunload',function(){
	var msg = '';
        $('form').each(function(){
	  var ob = $(this).data('cmsms.dirtyForm');
          if( typeof(ob) == 'object' ) {
	    if( ob.options.dirty ) msg = 'got it';
	  }
        });
	if( msg ) return msg;
      });
     },

    _setOption: function( k, v ) {
      this.options[k] = v;
      if( k == 'disabled' ) {
        this.options.disabled = v;
      }
      if( k == 'dirty' ) {
	if( !v ) {
	  this.options.dirty = false;
  	  this.element.find('form').removeClass(this.options.formClass);
	}
	else {
	  var form = this.element.find('form').addClass(this.options.formClass);
	  this.options.dirty = true;
	  this.options.onDirty(this,form);
	}
      }
    }
  });
})(jQuery);
