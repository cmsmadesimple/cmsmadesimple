/*
  $('#someelement').filepicker(options);

*/
(function($){
    $.widget('cmsms.filepicker', {
	options: {},

	_destroy: function() {
	    this._settings.button.remove();
	    this._settings.iframe.remove();
	    this._settings.iframe = this._settings.container = this._settings.popup = this._settings.button = null;
	    this.element.removeAttr('data-cmsms-i').removeAttr('readonly').removeClass('cmsfp cmsfp_elem');
	},

	_uniqid: function() {
	    return 'i'+(new Date().getTime()).toString(16)
	},

	_create: function() {
	    // todo: throw an exception if this.element is not an input
	    // basic initialization
	    this._settings = {};
	    var self = this;
	    if( typeof(this.options.start_path) == 'undefined' ) this.options.start_path = cms_data['root_url'];
	    this._settings.btn_label = (typeof(this.options.btn_label) != 'undefined') ? this.options.btn_label : cms_data['lang_choose']+'...';

	    // create the layout
	    this._settings.button = $('<button/>').text(this._settings.btn_label);
	    this.element.after(this._settings.button);
	    this._settings.inst = this.element.attr('data-cmsfp-instance');
	    if( !this._settings.inst ) {
		// make sure we have an instance
		this._settings.inst = this._uniqid();
		this.element.attr('data-cmsfp-instance',inst);
	    }
	    this.element.attr('readonly','readonly').addClass('cmsfp cmsfp_elem');

	    // make sure our iframe src url has all of the info we need.
	    this._settings.url = this.options.url;
	    if( !this._settings.url ) {
		if( !typeof(cms_data['filepicker_url']) ) throw "No filepicker_url in the cms_data class";
		this._settings.url = cms_data['filepicker_url'];
		for( var prop in this.options) {
		    if( !prop.startsWith('param_') ) continue;
		    var val = this.options[prop];
		    prop = prop.substr(6);
		    this._settings.url += '&'+prop+'='+val;
		}
	    }
	    this._settings.url += '&inst='+this._settings.inst;

	    // build the container, with an iframe to our url
	    var title = this.options.title;
	    if( !title ) {
		if( typeof(cms_data['lang_select_file']) !== 'undefined' ) title = cms_data['lang_select_file'];
		if( !title ) title = 'Select a File';
	    }
	    this._settings.iframe = $('<iframe/>').attr('src',this._settings.url).addClass('cmsfp cmsfp_frame');
	    this._settings.iframe.attr('data-cmsfp-inst',this._settings.inst).attr('name','x'+Date.now());
	    this._settings.container = $('<div/>').addClass('cmsfp cmsfp_dlg').append(this._settings.iframe).attr('title',title);
	    $('body',document).append(this._settings.container);

	    // put it in a dialog for now
	    this._settings.popup = this._settings.container.dialog({
		autoOpen: false,
		width: $(window).width() * .8,
		height: $(window).height() * .6,
		modal: true,
		draggable: true,
		dialogClass: 'cmsfp_dlg',
		resizable: false,
	    });

	    // when we click on the 'change' button
	    this.element.click(function(ev){
		ev.preventDefault();
		self.open();
	    });
	    this._settings.button.click(function(ev){
		ev.preventDefault();
		self.open();
	    });

	    // when a file is selected
	    this.element.on('cmsfp:change',function(ev,file){
		self._setOption('value', file);
		self._settings.popup.dialog('close');
		self.element.trigger('change');
	    });

	    if( this.options.value ) {
		this._setOption( 'value', this.options.value )
	    } else {
		this.render();
	    }
	},

	_setOption: function( key, value ) {
	    if( key == 'value' ) {
		// value = this._relativePath( value, this.options.start_path );
		this.element.val(value);
	    }
	    this.render();
	},

	_relativePath: function( instr, relative_to ) {
	    if( typeof(relative_to) == 'undefined' ) {
		if( instr.startsWith( cms_data['uploads_url']) ) {
		    relative_to = cms_data['uploads_url'];
		} else if( instr.startsWith(cms_data['root_url']) ) {
		    relative_to = cms_data['root_url'];
		}
	    }
	    if( !instr.startsWith( relative_to ) ) return;
	    var out = instr.substr(relative_to.length);
	    if( out.startsWith('/') ) out = out.substr(1);
	    return out;
	},

	render: function() {
	    console.debug('in render');
	},

	open: function() {
	    this._settings.popup.dialog('open');
	},

	_noop: function() {}
    }); /* end of widget */
})(jQuery);
