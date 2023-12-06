tinymce.PluginManager.add('cmsms_linker', function(editor) {

  function linker_showDialog() {

    var data = {},
      selection = editor.selection,
      dom = editor.dom,
      selectedElm,
      anchorElm,
      initialText, //selected-<a/> text or non-<a/> selected text or empty if no selection
      win,
      pageinput, //jQuery selection inside dialog
      pagehref, //fake url like {cms_selflink href="a-page-alias"}
      pagevalue = '';//, //auto-completion value
//    compinit = false; //auto-completion status

    // setup content for target attribute dropdown
    function buildTargetList(targetValue) {
      var targetListItems = [{
        text: cmsms_tiny.target_none,
        value: ''
      }];

      if (editor.options.isRegistered('target_list') && editor.options.isSet('target_list')) {
        tinymce.each(editor.options.get('target_list'), function(target) {
          targetListItems.push({
            text: target.text || target.title,
            value: target.value,
            selected: targetValue === target.value
          });
        });
      } else {
        targetListItems.push({
          text: cmsms_tiny.target_new_window,
          value: '_blank'
        });
      }
      return targetListItems;
    }

    // run jQueryUI autocomplete and set values
    function AutoComplete() {

//      if (!compinit) {
        $('.ui-autocomplete').css('z-index', 70000); //this should be in a stylesheet
        $('.ui-helper-hidden-accessible').hide(); //TODO check this
//        compinit = true;
//      }

      pageinput.autocomplete({
        minLength: 2,
        source: function(request, response) {
          $.ajax({
            url: cmsms_tiny.linker_autocomplete_url,
            dataType: 'json',
            data: {
              term: request.term
            }
          }).done(function(data) {
            response(data);
          });
        },
        focus: function(event, ui) {
          event.preventDefault();
        },
        select: function(event, ui) {
          event.preventDefault();
          if (typeof ui.item !== 'undefined') {
            win.setData({
              'page': ui.item.label,
              'alias': ui.item.value
            });
            pagehref = "{cms_selflink href='" + ui.item.value + "'}";
          }
        }
      });
    } //AutoComplete

    // insert selected values into the content
    function SubmitForm(dialogApi) {
      var data = dialogApi.getData(); //value and/or state of dialog’s components
      var newtext;

      if (!data.page || !pagehref) {
        editor.execCommand('unlink');
        return;
      }

      if (data.text !== initialText) {
        if (anchorElm) {
          editor.trigger('focus'); //OR tinymce.activeEditor.focus() ?
          anchorElm.innerHTML = data.text || data.page;

          dom.setAttribs(anchorElm, {
            href: pagehref,
            target: data.target ? data.target : null,
            rel: data.rel ? data.rel : null,
            class: data.classname ? data.classname : null
          });
          selection.select(anchorElm);
        } else {
          newtext = data.text || initialText || data.page;
          anchorElm = dom.createHTML('a', {
            href: pagehref,
            target: data.target ? data.target : null,
            rel: data.rel ? data.rel : null,
            class: data.classname ? data.classname : null
          }, newtext);
          editor.insertContent(anchorElm);
        }
      } else {
        newtext = data.text || data.page || 'EDITME';
        anchorElm = dom.createHTML('a', {
          href: pagehref,
          target: data.target ? data.target : null,
          rel: data.rel ? data.rel : null,
          class: data.classname ? data.classname : null
        }, newtext);
        editor.insertContent(anchorElm);
      }
      win.close();
    } //SubmitForm

    // skanky hack forced by V5 non-support for component classes or other easy identifier
    function FindComponents() {
      // the element used for auto-completion
      pageinput = $('.tox-dialog').find('input[placeholder="FINDMEpage"]');
      pageinput.attr({
        placeholder: cmsms_tiny.prompt_page_place,
        title: cmsms_tiny.prompt_page_info
      });
    }

    // setup intial values for dialog fields
    selectedElm = selection.getNode();
    anchorElm = dom.getParent(selectedElm, 'a[href]');

    data.page = '';
    data.alias = '';
    data.text = initialText = anchorElm ? (anchorElm.innerText || anchorElm.textContent) : selection.getContent({
      format: 'text'
    });
    data.target = anchorElm ? dom.getAttrib(anchorElm, 'target') : '';
    data.classname = anchorElm ? dom.getAttrib(anchorElm, 'class') : '';
    data.rel = anchorElm ? dom.getAttrib(anchorElm, 'rel') : '';
    pagehref = anchorElm ? dom.getAttrib(anchorElm, 'href') : ''; //might be incompatible format

    // grab page information if href includes 'cms_selflink'
    if (pagehref.indexOf('cms_selflink') !== -1) {
      var r = pagehref.match(/href=(.*)[\s\}]/);

      if (r.length >= 2) {
        // parsed the cms_selflink for the page alias
        // fill in the alias field.
        data.alias = r[1].replace(/'/g, '');
        // default value for page field
        data.page = cmsms_tiny.loading_info;
        $.ajax({
          url: cmsms_tiny.linker_autocomplete_url,
          dataType: 'json',
          data: {
            alias: data.alias
          }
        }).done(function(res) {
          // update values for page and alias
          if (res && res.label) {
            data.page = res.label;
            data.alias = res.value;
            pagehref = "{cms_selflink href='" + data.alias + "'}";
          } else {
            data.page = data.alias = '';
            pagehref = '';
          }
          win.setData({'page':data.page, 'alias':data.alias});
        });
      }
    } else {
      pagehref = '';
    }

    // reset text field if it's 'image'
    if (selectedElm.nodeName === 'IMG') {
      data.text = initialText = ' ';
    }

    // run tinymce dialog
    win = editor.windowManager.open({
      title: cmsms_tiny.linker_heading,
      initialData: {
        page: data.page,
        alias: data.alias,
        text: data.text,
        target: data.target,
        classname: data.classname,
        rel: data.rel
      },
      body: {
        type: 'tabpanel',
        tabs: [{
          title: cmsms_tiny.tab_general,
          name: 'general',
          type: 'form',
          items: [
            { // formerly class 'cmsms-linker-page'
              name: 'page',
              type: 'input',
              inputMode: 'text',
              label: cmsms_tiny.prompt_page,
              size: 40,
//            tooltip: cmsms_tiny.prompt_page_info, unsupported in V5
              placeholder: 'FINDMEpage' // later replaced by cmsms_tiny.prompt_page_info etc
            },
            { // formerly class 'cmsms-linker-alias'
              name: 'alias',
              type: 'input',
              inputMode: 'text',
              label: cmsms_tiny.prompt_alias,
              disabled: true,
              size: 40,
//            tooltip: cmsms_tiny.prompt_alias_info, unsupported in V5
              placeholder: cmsms_tiny.prompt_alias_info
            },
            {
              name: 'text',
              type: 'input',
              inputMode: 'text',
              label: cmsms_tiny.prompt_text,
              size: 40
            }
          ]
        },
        {
          title: cmsms_tiny.tab_advanced,
          name: 'advanced',
          type: 'form',
          items: [
            {
              name: 'target',
              type: 'listbox',
              label: cmsms_tiny.prompt_target,
              items: buildTargetList(data.target)
            },
            {
              name: 'classname',
              type: 'input',
              inputMode: 'text',
              label: cmsms_tiny.prompt_class,
              size: 40
            },
            {
              name: 'rel',
              type: 'input',
              inputMode: 'text',
              label: cmsms_tiny.prompt_rel,
              size: 40
            }
          ]
        }]
      },
      //array of footerbuttons https://www.tiny.cloud/docs/ui-components/dialog/#footerbuttons
      buttons: [{
          type: 'cancel',
          text: 'Cancel' //translated if possible
        },
        {
          type: 'submit',
          text: 'Save',
          primary: true
        }
      ],
//    onCancel: function(dialogApi) {},
      onChange: function(dialogApi, changeData) {
        switch (changeData.name) {
          case 'page':
            var dlgdata = dialogApi.getData(); //value and/or state of dialog’s components
            if (dlgdata.page.length > 2) {
              //TODO c.f. current pagevalue e.g. all different or same prefix but now longer
              pagevalue = dlgdata.page;
              AutoComplete();
            }
            break;
          default:
            var here = 1;
        }
      },
      onTabChange: function(dialogApi, details) {
        if (details.newTabName === 'general') {
          FindComponents();
        }
      },
      onSubmit: SubmitForm
    });
    FindComponents();
  } //showDialog

  function getAnchorElement(editor, selectedElm) {
    selectedElm = selectedElm || editor.selection.getNode();
    if (selectedElm) {//TODO && some relevant test applied to selectedElm
      return editor.dom.select('a[href*="cms_selflink"]', selectedElm)[0]; //? 'a[href*="cms_selflink"]' OR just 'a[href]'
    } else {
      return editor.dom.getParent(selectedElm, 'a[href*="cms_selflink"]'); //? 'a[href*="cms_selflink"]' OR just 'a[href]'
    }
  }

  function toggleState(editor, toggler) {
    editor.on('NodeChange', toggler);
    return function() {
      return editor.off('NodeChange', toggler);
    };
  }

  function toggleLinkerState(api) {
    // Do stuff here on component render
/* TODO something to replace TMCE4 'a[href*="cms_selflink"]' stateSelector handling c.f.
      var nodeChangeHandler = function() {
        var selectedNode = editor.selection.getNode();
        return api.setActive(selectedNode.id === constants.id);
      }
      editor.on('NodeChange', nodeChangeHandler);
*/
    return function(api) {
      // Do stuff here on component teardown
      var updateState = function() {
        return api.setEnabled(getAnchorElement(editor, editor.selection.getNode()) !== null);
      };
      updateState();
      return toggleState(editor, updateState);
    };
  }

  // add a menu item
  editor.ui.registry.addMenuItem('cmsms_linker', {
    icon: 'pagelink',
    onAction: linker_showDialog,
    onSetup: toggleLinkerState,
    text: cmsms_tiny.linker_text + '...'
  });
  // and a button
  editor.ui.registry.addButton('cmsms_linker', {
    icon: 'pagelink',
    onAction: linker_showDialog,
    onSetup: toggleLinkerState,
    tooltip: cmsms_tiny.linker_title
  });
});
