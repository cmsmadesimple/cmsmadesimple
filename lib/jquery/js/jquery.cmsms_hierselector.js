/**
 * @name cmsms.hierselector
 * @namespace cmsms.hierselector
 *
 * @example
 * $('#myinput').cmsms_hierselector();
 */
(function($){

  $.widget('cmsms.hierselector', {
      options: {
	  current: null, /* the current content item we are working on */
	  value: null,   /* the current value (content id) */
	  allowcurrent: true, /* the user is allowed to select the current page, if current is greater than -1... then also allowed to select a child of 'current' */
	  use_perms: false, /* use permissions to control what is selectable */
	  use_simple: false, /* use a simple dropdown... implied if use_perms is true */
	  allow_all: false, /* show all content entries, even those that don't have usable links */
	  for_child: false, /* we are wanting to add a child page */
	  is_manager: false, /* the current user is a content manager */
      },

      /**
       * @ignore
       */
      _create: function() {
	  if( typeof(cms_data['secure_param_name']) != 'undefined' ) this.options.secure_param = cms_data['secure_param_name'];
	  if( typeof(cms_data['user_key']) != 'undefined' ) this.options.user_key = cms_data['user_key'];
	  if( typeof(cms_data['admin_url']) != 'undefined' ) this.options.admin_url = cms_data['admin_url'];
	  if( !this.options.secure_param ) throw 'The secure_param option (string) must be set in the cmsms_lock plugin';
	  if( !this.options.user_key ) throw 'The user_key option (string) must be set in the cmsms_lock plugin';
	  if( !this.options.admin_url ) throw 'The admin_url option (string) must be set in the cmsms_lock plugin';

	  // initialization
	  var self = this;
	  this.data = {};
	  this.data.orig_val = this.element.val();
	  this.data.name = this.element.attr('name');
	  this.data.id = this.element.attr('id');
	  this.data.hidden_e = $('<input type="hidden" name="'+this.data.name+'" value="'+this.data.orig_val+'"/>').insertAfter(this.element);
	  this.data.ajax_url = this.options.admin_url + '/ajax_content.php?'+this.options.secure_param+'='+this.options.user_key;
	  this.element.val('').removeAttr('name').attr('readonly','readonly').hide();
	  $.ajax({
	      url: this.data.ajax_url+'&t='+$.now(),
	      data: { op: 'pageinfo', page: this.data.orig_val },
	      type: 'GET',
          }).pipe(function(result,textStatus,xhr){
	      if( xhr.status != 200 ) return $.Deferred().reject(result);
	      if( typeof result.status == 'undefined' || result.status != 'success' ) return $.Deferred().reject(result);
	      return result.data;
	  }).done(function(data){
	      self.data.orig_idhier = data.id_hierarchy;
	      self.data.orig_pages = data.id_hierarchy.split('.');
	  }).always(function(data){
	      self._setup_dropdowns();
	  })
      },

      _setOption: function( k, v ) {
	  this.options[k] = v;
	  this._setup_dropdowns();
      },

      _setup_dropdowns: function() {
	  if( this.options.use_simple ) {
	      this._setup_simple_dropdown();
	  }
	  else {
	      this._setup_smart_dropdowns();
	  }
      },

      _build_simple_select: function(name,data,selected_id,hilite) {
	  var self = this;
	  var sel = $('<select></select>').attr('id',name).addClass('cms_selhier').attr('title',cms_lang('hierselect_title'));
	  sel.on('change',function(){
 	      var v = $(this).val();
	      $(this).trigger('cmsms_formchange',{
		  'elem': $(this),
		  'value': v
              });
	  });
	  for( var i = 0; i < data.length; i++ ) {
	      var depth = data[i].hierarchy.split('.').length;
	      var str = '&nbsp;&nbsp;'.repeat(depth-1) + data[i].display;
              var opt = $('<option>'+str+'</option>').attr('value',data[i].content_id);
              //if( data[i].content_id == current ) opt.addclass('current');
              if( data[i].content_id == selected_id ) opt.attr('selected','selected').addClass('selected');
	      if( data[i].content_id == hilite ) opt.addClass('hilite');
	      if( data[i].content_id == this.options.value && !this.options.allowcurrent ) opt.attr('disabled','disabled');
	      if( this.options.use_perms && !data[i].can_edit ) opt.attr('disabled','disabled').addClass('nochildren');
	      //if( this.options.for_child && !data[i].has_children && !data[i].wants_children ) opt.attr('disabled','disabled').addClass('nochildren');
	      sel.append(opt);
	  }
	  return sel;
      },

      _build_smart_select: function(name,data,selected_id,parent_selectable,hilite) {
	  var self = this;
	  var sel = $('<select></select>').attr('id',name).addClass('cms_selhier').attr('title',cms_lang('hierselect_title'));
	  sel.on('change',function(){
 	      var v = $(this).val();
              if( v < 1 ) {
		  v = $(this).prev('select').val();
		  if( typeof(v) == 'undefined' ) v = -1;
	      }
              self.data.hidden_e.val(v).change();
              self._setup_smart_dropdowns();
	      $(this).trigger('cmsms_formchange',{
		  'elem': $(this),
		  'value': v
              });
	  });
	  if( parent_selectable ) {
	      var opt = $('<option>'+cms_lang('none')+'</option>').attr('value',-1);
	      sel.append(opt);
	  } else if( selected_id == -1 ) {
              // nothing selected, cannot select none... but have options
	      selected_id = data[0].content_id;
	      self.data.hidden_e.val(selected_id).change();
	  }
	  for( var i = 0; i < data.length; i++ ) {
              var opt = $('<option>'+data[i].display+'</option>').attr('value',data[i].content_id);
              //if( data[i].content_id == current ) opt.addclass('current');
              if( data[i].content_id == selected_id ) {
                  opt.attr('selected','selected').addClass('selected');
	      }
	      if( data[i].content_id == hilite ) opt.addClass('hilite');
	      if( data[i].content_id == this.options.value && !this.options.allowcurrent ) opt.attr('disabled','disabled');
	      if( !data[i].has_children && this.options.use_perms && !data[i].can_edit ) opt.attr('disabled','disabled').addClass('nochildren');
	      if( this.options.for_child && !data[i].has_children && !data[i].wants_children ) opt.attr('disabled','disabled').addClass('nochildren');
	      sel.append(opt);
	  }
	  return sel;
      },

      _setup_smart_dropdowns: function() {
	  var self = this;
	  var cur_val = this.data.hidden_e.val();
	  self.element.prevAll('select.cms_selhier').remove();
	  self.element.val('');
	  var opts = this.options;
	  opts.op = 'here_up';
	  opts.page = cur_val;
          $.ajax({
	      url: this.data.ajax_url+'&t='+$.now(),
	      data: opts,
	      type: 'GET',
          }).pipe(function(result,textStatus,xhr){
	      if( xhr.status != 200 ) return $.Deferred().reject(result);
	      if( typeof result.status == 'undefined' || result.status != 'success' ) return $.Deferred().reject(result);
	      return result.data;
	  }).done(function(data){
	      var found_cur,cur_pages = false;
	      var parent_selectable = true; // root level
	      if( self.options.for_child && self.options.use_perms && !self.options.is_manager ) parent_selectable = false;  // only managers can add child pages at top level.
	      for( var x1 = 0; x1 < data.length && !found_cur; x1++ ) {
		  for( var x2 = 0; x2 < data[x1].length && !found_cur; x2++ ) {
		      if( data[x1][x2].content_id == cur_val ) {
			  found_cur = data[x1][x2].id_hierarchy;
			  break;
		      }
		  }
	      }
	      if( found_cur ) cur_pages = found_cur.split('.');
	      for( var idx = 0; idx < data.length; idx++ ) {
		  if( data[idx] == null ) break;
		  var selected = ( idx < cur_pages.length ) ? cur_pages[idx] : -1;
		  var orig_page = ( self.data.orig_pages && idx < self.data.orig_pages.length ) ? self.data.orig_pages[idx] : -100;
		  var sel = self._build_smart_select(self.data.id+'_'+idx,data[idx],selected,parent_selectable,orig_page);
		  if( selected ) {
		      for( var x2 = 0; x2 < data[idx].length; x2++ ) {
			  if( data[idx][x2].content_id == selected ) {
			      parent_selectable = data[idx][x2].wants_children;
			      // if we are using permissions, and not a manager, and otherwise cannot edit this page
			      // then we disable the <none> item in this items select field.
			      if( !self.options.for_child && !data[idx][x2].has_usable_link ) parent_selectable = false;
			      if( self.options.for_child && self.options.use_perms && !self.options.is_manager && !data[idx][x2].can_edit ) parent_selectable = false;
			      break;
			  }
		      }
		  }
		  sel.insertBefore(self.element);
	      }
	  });
      },

      _setup_simple_dropdown: function() {
	  var self = this;
	  var cur_val = this.data.hidden_e.val();
	  var opts = this.options;
	  opts.op = 'userpages';
          $.ajax({
	      url: this.data.ajax_url+'&t='+$.now(),
	      data: opts,
	      type: 'GET',
          }).pipe(function(result,textStatus,xhr){
	      if( xhr.status != 200 ) return $.Deferred().reject(result);
	      if( typeof result.status == 'undefined' || result.status != 'success' ) return $.Deferred().reject(result);
	      return result.data;
	  }).done(function(data){
	      var sel = self._build_simple_select(self.data.id+'_0',data,cur_val,true,-1);
	      sel.insertBefore(self.element);
	  });
      },

      _noop: function() {}
  });
})(jQuery);