/**
 * @fileOverview plugin code for cmsms_dirtyform plugin.
 * @version 0.1
 * @author Robert Campbell <calguy1000@cmsmadesimple.org>
 */

/**
 * @name cmsms.dirtyForm
 * @namespace cmsms
 *
 * @example
 * $.cmsms_lock();
 */

(function($){
  $.widget('cmsms.lockManager', {
    options: {
      touch_handler: null,
      lostlock_handler: null,
      error_handler: null,
      change_noticed: 0,
      lock_timeout: 60,
      lock_refresh: 60
    },

    _settings: { 'locked': 0, 'lock_id': -1, 'lock_expires': -1 },

    _error_handler: function(error) {
       if( typeof(error) == 'string' ) {
         var key = 'error_lock_'+error;
         var msg = 'Unknown Error';
         if( typeof this._settings.lang[key] != 'undefined' ) msg = this._settings.lang[key];
         error = { 'type': error, 'msg': msg }
       }
       if( typeof(this.options.error_handler) === 'function' ) {
	 this.options.error_handler(error);
       }
       else {
	 console.debug('Error: '+error.type+' - '+error.msg);
       }
    },

    _lostlock_handler: function(error) {
      if( typeof(this.options.lostlock_handler) === 'function' ) {
	this.options.lostlock_handler(error);
      }
      console.debug('Error: '+error.type+' - '+error.msg);
    },

    _create: function() {
      // do initial error checking (user key)
      this.options.lock_refresh = Math.max(this.options.lock_refresh,30);
      this.options.lock_refresh = Math.min(this.options.lock_refresh,3600);
      if( typeof(cms_data['secure_param_name']) != 'undefined' ) this.options.secure_param = cms_data['secure_param_name'];
      if( typeof(cms_data['user_key']) != 'undefined' ) this.options.user_key = cms_data['user_key'];
      if( typeof(cms_data['admin_url']) != 'undefined' ) this.options.admin_url = cms_data['admin_url'];

      if( !this.options.secure_param ) throw 'The secure_param option (string) must be set in the cmsms_lock plugin';
      if( !this.options.user_key ) throw 'The user_key option (string) must be set in the cmsms_lock plugin';
      if( !this.options.admin_url ) throw 'The admin_url option (string) must be set in the cmsms_lock plugin';

      if( !this.options.type ) throw 'The type option (string) must be set in a cmsms_lock plugin';
      if( !this.options.oid ) throw 'The oid option (integer) must be set in a cmsms_lock plugin';
      if( !this.options.uid ) throw 'The uid option (string) must be set in the cmsms_lock plugin';

      // do initial ajax connection to fill settings
      var self = this;
      var ajax_url = this.options.admin_url+'/ajax_lock.php?showtemplate=false';
      var opts = {};
      opts.opt = 'setup';
      opts[this.options.secure_param] = this.options.user_key;
      opts.uid = this.options.uid;
      $.post(ajax_url, opts, function(data,textStatus,jqXHR) {
        if( textStatus != 'success' ) throw 'Problem communicating with ajax url '+ajax_url;
	if( data.status == 'error' ) {
	    self._error_handler(data.error);
        }

	self._settings = data;
	self._settings.ajax_url = ajax_url;
	self.options.change_noticed = 0;
	if( typeof(data.uid) == 'undefined' || self.options.uid != data.uid ) {
	  // for the first time, we can use the onError callback
	  self._error_handler('useridmismatch');
          return;
	}

	if( self.options.lock_timeout ) {
	    // setup our event handlers
	    self._setup_handlers();
  	    // do our initial lock.
	    self._lock();
	}
      });
    },

    _setup_touch: function() {
	var self = this;
	var interval = self.options.lock_refresh;
	interval = Math.min(3600,Math.max(5,interval));
	if( typeof(self._settings.touch_timer) != 'undefined' ) clearTimeout(self._settings.touch_timer);
	self._settings.touch_timer = setTimeout(function(){
            self._touch();
	},interval * 1000);
    },

    _setup_handlers: function() {
	var self = this;
	this._settings.touch_skipped = 0;
	this.element.on('change','input:not([type=submit]), select, textarea',function(){
            self.options.change_noticed = 1;
	    if( self._settings.touch_skipped ) {
		self._touch();
	    }
	});
	if( this.options.lock_refresh > 0 ) this._setup_touch();
    },

    _touch: function() {
	var self = this;
	if( self.options.change_noticed && self._settings.locked && self._settings.lock_id > 0 ) {
            // do ajax touch
  	    console.debug('lockmanager: touching lock');
	    this._settings.touch_skipped = 0;
            var opts = {};
            opts.opt = 'touch';
            opts[this.options.secure_param] = this.options.user_key;
            opts.type = this.options.type;
            opts.oid  = this.options.oid;
            opts.uid  = this.options.uid;
	    opts.lock_id = this._settings.lock_id;
            $.post(self._settings.ajax_url, opts, function(data,textStatus,jqXHR) {
		if( textStatus != 'success' ) throw 'Problem communicating with ajax url '+self._settings.ajax_url;
		if( data.status == 'error' ) {
		    if( data.error.type == 'cmsnolockexception' ) {
			self._lostlock_handler(data.error);
		    }
		    else {
			self._error_handler(data.error);
		    }
		    // assume we are no longer locked...
		    self._settings.locked = 0;
		    self._settings.lock_id = -1;
		    self._settings.lock_expires = -1;
		    return;
		}
		if( self.options.touch_handler ) self.options.touch_handler();
		self._settings.lock_expires = data.lock_expires;
		self.options.change_noticed = 0;
            });
	}
	else {
	    this._settings.touch_skipped = 1;
	}
	this._setup_touch();
    },

    _lock: function() {
      var self = this;
      if( !self._settings.locked ) {
        // do ajax lock
        var opts = {};
        opts.opt = 'lock';
        opts[this.options.secure_param] = this.options.user_key;
        opts.type = this.options.type;
        opts.oid  = this.options.oid;
        opts.uid  = this.options.uid;
        opts.lifetime = this.options.lock_timeout;
        $.post(self._settings.ajax_url, opts, function(data,textStatus,jqXHR) {
          if( textStatus != 'success' ) throw 'Problem communicating with ajax url '+self._settings.ajax_url;
	  if( data.status == 'error' ) {
	    // todo: here handle the type of error.
	    self._error_handler(data.error);
            return;
          }
	  if( self.options.lock_handler ) self.options.lock_handler();
	  self._settings.lock_id = data.lock_id;
	  self._settings.lock_expires = data.lock_expires;
          self._settings.locked = 1;
        });
      }
    },

    relock: function() {
	this._lock();
    },

    unlock: function(ajax) {
	var self = this;
	if( self._settings.locked && self._settings.lock_id > 0 ) {
            // do ajax unlock
            var opts = {};
            opts.opt = 'unlock';
            opts[this.options.secure_param] = this.options.user_key;
            opts.type = this.options.type;
            opts.oid = this.options.oid;
            opts.uid = this.options.uid;
            opts.lock_id = this._settings.lock_id;
	    if( !ajax && navigator.sendBeacon ) {
		// woot, we can try a beacon
		var data = new FormData();
		for( var o in opts ) {
		    data.append(o,opts[o]);
		}
		var r = navigator.sendBeacon(self._settings.ajax_url,data);
		if( r ) {
		    self._settings.locked = 0;
		    self._settings.lock_id = -1;
		    self._settings.lock_expires = -1;
		    data = { status: 'success' }
    		    return $.Deferred().resolve(data,'');
		}
		console.debug('unlock beacon failed... fallback to ajax');
	    }

	    var _jqxhr = $.ajax({
		type: 'POST',
		url: self._settings.ajax_url,
		cache: false,
		data: opts
	    }).pipe(function(data,textStatus,xhr) {
		if( xhr.status != 200 ) {
		    return $.Deferred().reject(data,textStatus);
		}
		if( textStatus != 'success' ) {
		    return $.Deferred().reject(data,textStatus);
		}
  		if( data.status == 'error' ) {
		    return $.Deferred().reject(data,textStatus);
		}
	    }).done(function(data,textStatus){
		if( self.options.unlock_handler ) self.options.unlock_handler();
		self._settings.locked = 0;
		self._settings.lock_id = -1;
		self._settings.lock_expires = -1;
	    }).fail(function(xhr, textStatus, errorThrown){
		console.debug('unlock failed: '+textStatus+' // '+errorThrown + ' // '+xhr.status);
		setTimeout(function(){
		    // nothing here
		},3000)
	    });
	    return _jqxhr;
        } else {
	    data = { status: 'success' }
    	    return $.Deferred().resolve(data,'');
	}
    },

    // mark the end of the functions
    _noop: function() {}
  });
})(jQuery);
