(function($){
    $.widget('cmsms.autoRefresh', {
	options: {
	    url: null,
	    data: null,
	    interval: 30,
	    start_handler: null,
	    done_handler: null,
	},

	settings: {
	    timer: null,
	    focus: -1,
	    lastrefresh: null,
	},

	_create: function() {
	    var self = this;
	    if( !this.options.url ) throw 'A URL must be specified for the autoRefresh plugin';
	    var v = this.options.interval;
	    if( v < 1 || v > 3600 ) throw 'autoRefresh interval must be between 1 and 3600';
	    $(this.element).on('click',':input',function(){
		self.start();
	    });
	    $(this.element).on('click','a',function(){
		self.start();
	    });
	    $(window).focus(function(){
		if( self.settings.focus < 1 ) {
		    self.settings.focus = 1;
		    var v = Date.now() / 1000;
		    var n = v - self.settings.lastrefresh;
		    if( n >= self.options.interval ) {
			self.start();
			self.refresh();
		    }
		}
	    });
	    $(window).blur(function(){
		if( self.settings.focus == 1 ) self.settings.focus = 0;
	    });
	    this.start();
	    this.refresh();
	},

	_setOption: function( key, val ) {
	    if( key == 'url' ) {
		if( typeof val === 'string' && val.length > 0 ) this.options.url = val;
		this.start();
	    } else if( key == 'DATA' ) {
		this.options.data = val;
		this.start();
		return this.refresh();
	    } else if( key == 'interval' ) {
		var v = parseInt(val);
		if( v > 0 ) this.options.url = Math.min(v,3600);
		this.start();
	    } else if( key == 'start_handler' ) {
		this.options.start_handler = null;
		if( typeof val === 'function' ) this.options.start_handler = val;
	    } else if( key == 'done_handler' ) {
		this.options.done_handler == null;
		if( typeof val === 'function' ) this.options.done_handler = val;
	    }
	},

	stop: function() {
	    var self = this;
	    if( self.settings.timer ) {
		clearInterval(this.settings.timer);
		self.settings.timer = null;
	    }
	},

	start: function() {
	    var self = this;
	    self.stop();
	    self.settings.timer = setInterval( function() {
		self.refresh();
	    }, self.options.interval * 1000 );
	},

	reset: function() {
	    // alias for start
	    this.start();
	},

	refresh: function() {
	    var self = this;
	    if( !self.settings.focus ) return;
	    var v = Date.now() / 1000;
	    self.settings.lastrefresh = v;
	    if( self.options.start_handler ) self.options.start_handler();
	    cms_busy();
	    return $.ajax({
		url: self.options.url,
		data: self.options.data,
		cache: false,
	    }).then(function(data){
		cms_busy(false);
		return data;
	    }).fail(function(res,textStatus,xhr){
		console.debug('autorefresh failed');
	    }).done(function(data){
		console.debug('autorefresh success');
		$(self.element).html(data);
		if( typeof self.options.done_handler == 'function' ) self.options.done_handler(data);
	    });
	}
    });
})(jQuery);
