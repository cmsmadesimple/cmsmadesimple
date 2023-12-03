tinymce.PluginManager.add('nonbreaking', function(editor) {
    'use strict';
/* RUBBISH
    function option(name,editor) {
      return editor.options.get(name);
    }

    function stringRepeat(string, repeats) {
      if (repeats > 1) {
        var str = '';
        for (var index = 0; index < repeats; index++) {
          str += string;
        }
        return str;
      }
      return string;
    }

    function isVisualCharsEnabled(editor) {
      return editor.plugins.visualchars ? editor.plugins.visualchars.isEnabled() : false;
    }

    function insertNbsp(editor, times) {
      var html;
      if (option('nonbreaking_wrap', editor) || editor.plugins.visualchars) {
        var classes = isVisualCharsEnabled(editor) ? 'mce-nbsp-wrap mce-nbsp' : 'mce-nbsp-wrap';
        html = '<span class="' + classes + '" contenteditable="false">' + stringRepeat('&nbsp;',times) + '</span>';
      } else {
        html = stringRepeat('&nbsp;', times);
      }
      editor.undoManager.transact(function() {
        editor.insertContent(html);
      });
    }
*/
    function onSetupSpacer(api) {
      var nodeChanged = function() {
        api.setEnabled(editor.selection.isEditable()); // global editor
      };
      editor.on('NodeChange', nodeChanged);
      nodeChanged();
      return function() {
        editor.off('NodeChange', nodeChanged);
      };
    }
/* RUBBISH
    editor.options.register('nonbreaking_force_tab', {
      processor: function(value) {
        if (typeof value === 'boolean') {
          return {
            value: value ? 3 : 0,
            valid: true
          };
        } else if (typeof value === 'number') {
          return {
            value: value,
            valid: true
          };
        } else {
          return {
            valid: false,
            message: 'Value must be a boolean or number.'
          };
        }
      },
      default: false
    });

    editor.options.register('nonbreaking_wrap', {
      processor: 'boolean',
      default: false
    });

    editor.addCommand('mceNonBreaking', function() {
      insertNbsp(editor, 1);
    });
*/
    function nbspdoer() {
      editor.undoManager.transact(function() {
        editor.insertContent('&nbsp;'); //TODO confirm this is ok without any selection
      });
    }

    // add a menu item
    editor.ui.registry.addMenuItem('nonbreaking', {
      icon: 'non-breaking',
      text: 'Nonbreaking space',
      onAction: nbspdoer,
      onSetup: onSetupSpacer
    });

    // and a button
    editor.ui.registry.addButton('nonbreaking', {
      icon: 'non-breaking',
      tooltip: 'Nonbreaking space',
      onAction: nbspdoer,
      onSetup: onSetupSpacer
    });
/* RUBBISH
    var spaces = option('nonbreaking_force_tab',editor);
    if (spaces > 0) {
      var global = tinymce.util.Tools.resolve('tinymce.util.VK');
      editor.on('keydown', function(e) {
        if (e.keyCode === global.TAB && !e.shiftKey && !e.isDefaultPrevented()) {
          e.preventDefault();
          e.stopImmediatePropagation();
          insertNbsp(editor, spaces);
        }
      });
    }
*/
});
