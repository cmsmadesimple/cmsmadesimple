// UserGuide_admin.js
$(function() {
    $('.sortable-list').sortable({
        delay: 150,
        revert: 300,
        placeholder: 'ui-state-highlight',
        helper: function (event, ui) {
            ui.children().each(function() { // fixes width & height of dragged cells
                $(this).width($(this).width()).height($(this).height());
            });
            return ui;
        },
        stop: function (event, ui) {
            $(ui.item).parent().children().each(function( index ) {
                $(this).removeClass('row1 row2').addClass('row'+(index%2+1));
            });
            var moveItem = $(ui.item).data('id');
            var moveToAfter = $(ui.item).prev('tr').data('id');
            if (typeof moveToAfter=='undefined') moveToAfter = '0';
            var reorderUrl = $(ui.item).data('reorder').replace('_after=-1', '_after='+moveToAfter) + '&showtemplate=false';
            var loadingIcon = $('#loader').data('icon');
            if (loadingIcon!='') {
                loadingIcon = '<img src="'+loadingIcon+'" alt="updating">';
            } else {
                loadingIcon = 'updating';
            }

            $('#loader').html(loadingIcon);
            $.get( reorderUrl, function( data ) {
                if (data=="") {
                $('#loader').html('');
                reorderUserGuideTabs(moveItem, moveToAfter);
                } else { // error returned
                $('#loader').html('<span style="color:red;">'+data+'</span>');
                $('.sortable-list').sortable('cancel');
                }
            });

        }
    });

    function reorderUserGuideTabs(moveItem, moveToAfter) {
        var idPrefix = '#ugpage';
        var tabsId ='#page_tabs';
        if (moveToAfter=='0') {
            $(idPrefix+moveItem).detach().prependTo( $(tabsId) );
        } else {
            $(idPrefix+moveItem).detach().insertAfter( $(idPrefix+moveToAfter) );
        }
    };

    $('.embed-type').on('change', function() {
        var embedType = $(this).val(),
            $embedCode = $(this).closest('form').find('.embed-code'),
            $embedCodePrompt = $(this).closest('form').find('.embed-code-prompt'),
            $embedCodePrefix = $(this).closest('form').find('#embed_code_prefix'),
            $embed_code_input_group = $(this).closest('form').find('#embed_code_input_group'),
            $embed_code_input = $(this).closest('form').find('#embed_code_input'),
            $embed_code_textarea = $(this).closest('form').find('#embed_code_textarea');
        $embedCodePrompt.html( $(this).data(embedType) );
        if (embedType=='code') {
            $embed_code_input_group.removeClass('show');
            $embed_code_input.attr('name', $embed_code_input.data('name'));
            $embed_code_textarea.addClass('show').attr('name', $embed_code_textarea.data('name'));
            // $embedCode.removeClass('small');
            $embedCodePrefix.html( '' );
        } else {
            $embed_code_input_group.addClass('show');
            $embed_code_input.attr('name', $embed_code_input.data('name'));
            $embed_code_textarea.removeClass('show').attr('name', '')
            $embedCodePrefix.html( $embedCodePrefix.data(embedType) );
            // $embedCode.addClass('small');
        }
    });

    $('a.del_user_guide_page').click(function() {
        return confirm( $(this).data('confirm') + ' "' + $(this).data('title') + '"?');
    })


});

