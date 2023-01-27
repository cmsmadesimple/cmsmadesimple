/**
 * @fileOverview plugin code for cmsms_dirtyform plugin.
 * @version 0.1
 * @author Robert Campbell <calguy1000@cmsmadesimple.org>
 */

/**
 * @name cmsms.dirtyForm
 * @namespace cmsms.dirtyForm
 *
 * @example
 * $('#myform').cmsms_dirtyForm();
 */
(function($){
  $.widget('cmsms.dirtyForm', {
    /**
     * Options.
     *
     * <dl>
     *   <dt>unloadmsg <em>(string)</em></dt>
     *   <dd>The text to display before a dirty form is unloaded.</dd>
     *
     *   <dt>disabled <em>(boolean)</em></dt>
     *   <dd>Flag indicating if the plugin behaviour is disabled.  The default value is false.</dd>
     *
     *   <dt>dirty <em>(boolean)</em></dt>
     *   <dd>Flag indicating if the form is dirty.  The default value is false.  This flag is
     *     modified on the first form change.
     *   </dd>
     *
     *   <dt>formclass <em>(string)</em></dt>
     *   <dd>A string indicating the name of a CSS class to associate with a dirty form.
     *     If the dirty flag is set to false, this class will also be removed from the form.
     *   </dd>
     *
     *   <dt>onDirty <em>(function)</em></dt>
     *   <dd>A callback function to be called when a form is first marked as dirty.</dd>
     * </dl>
     * @name $.cmsms.dirtyForm.options
     */
    options: {
	unloadmsg: 'Are you sure you want to leave this page?  some changes may be lost',
	disabled: false,
	dirty: false,
	formClass: 'dirtyForm',
	beforeUnload: null,
	unloadCancel: null,
	onUnload: null,
	onDirty: function(elem,form) {}
     },

    /**
     * @ignore
     */
    _create: function() {
      var self = this;
      this.element.on('keyup','input:not([type=submit]), select, textarea',function(e) {
  	if( self.options.disabled == false && self.options.dirty == false ) self._setOption('dirty',true);
      });
      this.element.on('change','input:not([type=submit]), select, textarea',function() {
  	if( self.options.disabled == false && self.options.dirty == false ) self._setOption('dirty',true);
      });
	// should be this.element.on...
	// should also handle cmsms_textchange
      $(document).on('cmsms_formchange',function(event){
  	if( self.options.disabled == false && self.options.dirty == false ) self._setOption('dirty',true);
      });
      $(window).on('beforeunload',function(){
          console.debug('dirtyform beforeunload')
          if( self.options.disabled ) return;
	  var res = null;
	  var msg = '';
          if( self.options.beforeUnload ) res = self.options.beforeUnload(self.options.dirty);
	  if( self.options.dirty ) msg = self.options.unloadmsg.trim();
          if( res != null && res.length > 0 ) msg = res.trim();
	  if( msg != null && msg.length > 0 ) {
  	      if( self.options.unloadCancel ) {
	          setTimeout(function(){
		      console.debug('in outer timer');
		      setTimeout(function(){
			  console.debug('dirtyform unloadCancel');
		          self.options.unloadCancel();
		      },1000);
	          },1)
	      }
	      return msg;
	  }
      });
      $(window).on('unload',function(){
          if( self.options.disabled ) return;
          console.debug('dirtyform unload')
	  if( self.options.onUnload ) self.options.onUnload(self.options.dirty);
      });
     },

    /**
     * @ignore
     */
    _setOption: function( k, v ) {
      this.options[k] = v;
      if( k == 'disabled' ) this.options.disabled = v;
      if( k == 'dirty' ) {
	if( !v ) {
          console.debug('dirtyform dirty = false')
	  this.options.dirty = false;
  	  this.element.find('form').removeClass(this.options.formClass);
	}
	else {
          console.debug('dirtyform dirty = true')
	  var form = this.element.find('form').addClass(this.options.formClass);
	  this.options.dirty = true;
	  this.options.onDirty(this,form);
	}
      }
    }
  });
})(jQuery);
