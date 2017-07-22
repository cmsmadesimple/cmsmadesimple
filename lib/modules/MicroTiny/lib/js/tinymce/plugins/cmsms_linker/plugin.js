tinymce.PluginManager.add('cmsms_linker', function(editor, url) {
    function cmsms_showDialog() {
        
        var data = {}, 
            selection = editor.selection, 
            dom = editor.dom,
            selectedElm, 
            anchorElm, 
            initialText,
            win,
            pageField,
            aliasField,
            textField,
            targetOptions,
            classnameField,
            relField,
            r;
        
        // build target attribute dropdown
        function buildTargetList(targetValue) {
            var targetListItems = [{
                text: cmsms_tiny.target_none, 
                value: ''
            }];
            
            if (!editor.settings.target_list) {
                targetListItems.push({
                    text: cmsms_tiny.target_new_window, 
                    value: '_blank'
                });
            }
            
            tinymce.each(editor.settings.target_list, function(target) {
                targetListItems.push({
                    text: target.text || target.title,
                    value: target.value,
                    selected: targetValue === target.value
                });
            });
            
            return targetListItems;
        }
        
        // run jQueryUI autocomplete and set values
        function initAutoComplete(targetElement) {
            
            $('.ui-autocomplete').css('z-index', 70000);
            $('.ui-helper-hidden-accessible').hide();
            
            var el = document.getElementById(targetElement);
            
            $(el).autocomplete({
                minLength: 2,
                source: function(request, response) {
                    $.ajax({
                        url: cmsms_tiny.linker_autocomplete_url,
                        dataType: 'json',
                        data: {
                            term: request.term
                        },
                        success: function(data) {
                            response(data);
                        }
                    });
                },
                focus: function(event, ui) {
                    event.preventDefault();
                },
                select: function(event, ui) {
                    $(el).val(ui.item.label);
                    $('.mce-cmsms-linker-alias').val(ui.item.value);
                    $('.mce-cmsms-linker-href').val("{cms_selflink href='" + ui.item.value + "'}");
                    event.preventDefault();
                }
            });
        }
        
        // insert all selected values to submitted form
        function onSubmitForm() {
            
            var data = win.toJSON(),
                href = data.href,
                page = data.page;

                function insertLink() {
                    if (data.text !== initialText) {
                        if (anchorElm) {
                            editor.focus();
                            anchorElm.innerHTML = data.text;
                            
                            dom.setAttribs(anchorElm, {
                                href: href,
                                target: data.target ? data.target : null,
                                rel: data.rel ? data.rel : null,
                                class: data.classname ? data.classname : null
                            });
                            
                            selection.select(anchorElm);
                        } else {
                            editor.insertContent(dom.createHTML('a', {
                                href: href,
                                target: data.target ? data.target : null,
                                rel: data.rel ? data.rel : null,
                                class: data.classname ? data.classname : null
                            }, data.text));
                        }
                    } else {
                        editor.execCommand('mceInsertLink', false, {
                            href: href,
                            target: data.target,
                            rel: data.rel ? data.rel : null,
                            class: data.classname ? data.classname : null
                        });
                    }
                }
                
                if (!href || !page) {
                    editor.execCommand('unlink');
                    return;
                }
                
                insertLink();
        }
        
        // set default values for fields
        selectedElm    = selection.getNode();
        anchorElm      = dom.getParent(selectedElm, 'a[href]');
        
        data.page      = '';
        data.alias     = '';
        data.text      = initialText = anchorElm ? (anchorElm.innerText || anchorElm.textContent) : selection.getContent({format: 'text'});
        data.href      = anchorElm ? dom.getAttrib(anchorElm, 'href') : '';
        data.target    = anchorElm ? dom.getAttrib(anchorElm, 'target') : '';
        data.classname = anchorElm ? dom.getAttrib(anchorElm, 'class') : '';
        data.rel       = anchorElm ? dom.getAttrib(anchorElm, 'rel') : '';
        
        // grab page information if href is cms_selflink
        if(data.href.indexOf('cms_selflink') !== -1 ) {
            r = data.href.match(/href=(.*)[\s\}]/);
            
            if(r.length >= 2) {
                // parsed the cms_selflink for the page alias
                // fill in the alias field.
                data.alias = r[1].replace (/'/g, '');
                // default value for page field
                data.page = cmsms_tiny.loading_info;
                $.ajax({
                    url: cmsms_tiny.linker_autocomplete_url,
                    dataType: 'json',
                    data: {
                        alias: data.alias
                    },
                    success: function(res) {
                        // update values for alias and page.
                        data.page = res.label;
                        data.href= "{cms_selflink href='" + data.alias + "'}";
                        $('.mce-cmsms-linker-page').val(data.page);
                        $('.mce-cmsms-linker-alias').val(data.alias);
                        $('.mce-cmsms-linker-href').val(data.href);
                    }
                });
            }
        }

        // reset text field if it's image'
        if (selectedElm.nodeName === 'IMG') {
            data.text = initialText = ' ';
        }
        
        // set target list option values
        if (editor.settings.target_list !== false) {
            targetOptions = {
                name: 'target',
                type: 'listbox',
                label: cmsms_tiny.prompt_target,
                values: buildTargetList(data.target)
            };
        }
        
        // set defaults for page field
        pageField = {
            name: 'page',
            type: 'textbox',
            size: 40,
            classes: 'cmsms-linker-page',
            tooltip: cmsms_tiny.prompt_page_info,
            label: cmsms_tiny.prompt_page,
            onkeyup: function() {
                initAutoComplete(this._id);
            },
            onchange: function() {
                initAutoComplete(this._id);
            }
        };
        // set default for alias field
        aliasField = {
            name: 'alias', 
            disabled: true,
            type: 'textbox', 
            size: 40, 
            label: cmsms_tiny.prompt_alias,
            tooltip: cmsms_tiny.prompt_alias_info,
            classes: 'cmsms-linker-alias'
        };
        //set defaults for text field
        textField = {
            name: 'text', 
            type: 'textbox', 
            size: 40, 
            label: cmsms_tiny.prompt_text, 
            onchange: function() {
                data.text = this.value();
            },
            onkeyup: function() {
                data.text = this.value();
            }
            
        };
        // set classname defaults
        classnameField = {
            name: 'classname',
            type: 'textbox',
            size: 40,
            label: cmsms_tiny.prompt_class,
            onchange: function() {
                data.classname = this.value();
            },
            onkeyup: function() {
                data.classname = this.value();
            }
        };
        // set defaults for rel field
        relField = {
            name: 'rel', 
            type: 'textbox', 
            size: 40, 
            label: cmsms_tiny.prompt_rel,
            onchange: function() {
                data.rel = this.value();
            },
            onkeyup: function() {
                data.rel = this.value();
            }
        };
        
        // run tinymce window and build form
        win = editor.windowManager.open({
            title: cmsms_tiny.linker_text,
            data: data,
            bodyType: 'tabpanel',
            body: [{
                title: cmsms_tiny.tab_general,
                type: 'form',
                items: [
                    pageField,
                    aliasField,
                    textField,
                {
                    name: 'href', 
                    type: 'textbox', 
                    classes: 'cmsms-linker-href',
                    size: 100, 
                    hidden: true,
                    value: "{cms_selflink href='" + data.alias + "'}"
                }
             ]},
             {
                 title: cmsms_tiny.tab_advanced,
                 type: 'form',
                 items: [
                    targetOptions,
                    classnameField,
                    relField
                 ]
             }],
             onSubmit: onSubmitForm
        });
    }

    // add a button
    editor.addButton('cmsms_linker', {
        title: cmsms_tiny.linker_title,
        icon: 'link',
        image: cmsms_tiny.linker_image,
        onclick: cmsms_showDialog,
        stateSelector: 'a[href]'
    });
    
    // and a menu item
    editor.addMenuItem('cmsms_linker', {
        text: cmsms_tiny.linker_text,
        title: cmsms_tiny.linker_title,
        image: cmsms_tiny.linker_image,
        stateSelector: 'a[href]',
        context: 'insert',
        prependToContext: true,
        onclick: cmsms_showDialog
    });
});