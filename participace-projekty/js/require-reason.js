jQuery(document).ready(function($) {

    // Check if category dropdown exists -> you are in the new issue page
    if (document.getElementById('imccategory')) {

        var initial_cat_value = $('#imc-select-category-dropdown').val();
        var initial_status_value = $('#imc-select-status-dropdown').val();

        var current_cat_val = null;
        var current_status_val = null;
        var status_textbox_val = null;
        var category_textbox_val = null;

        if(initial_cat_value === null && initial_status_value === null) {
            //////////////////////
            // ADD NEW ISSUE MODE

            $( "#imc-select-category-dropdown" ).change(function () {
                $('#cat_notice').remove();
                /*$('#cat_reason_notice').remove();*/
                current_cat_val = $( "#imc-select-category-dropdown").val();
                /*$('#cat_reason_box').show();*/
            });

            $( "#imc-select-status-dropdown" ).change(function () {
                $('#status_notice').remove();
                current_status_val = $( "#imc-select-status-dropdown").val();
            });

            $('#post').submit(function(event) {

                /*$('#cat_reason_notice').remove();*/

                if (current_cat_val === null) {
                    event.preventDefault();
                    $('#ajax-loading').hide();
                    $('#publish').removeClass('button-primary-disabled');
                    $('<div id="cat_notice" class="error below-h2"><p>'+requireReasonVars.categoryAlert+'</p></div>').insertAfter('#imc-select-category-dropdown');

                }

                if (current_status_val === null) {
                    event.preventDefault();
                    $('#ajax-loading').hide();
                    $('#publish').removeClass('button-primary-disabled');
                    $('<div id="status_notice" class="error below-h2"><p>'+requireReasonVars.statusAlert+'</p></div>').insertAfter('#imc-select-status-dropdown');

                }

                /*category_textbox_val = $( "#imc-category-reason-textarea").val();

                if (category_textbox_val === '') {
                    event.preventDefault();
                    $('#ajax-loading').hide();
                    $('#publish').removeClass('button-primary-disabled');
                    $('<div id="cat_reason_notice" class="error below-h2"><p>Please make sure that you have entered a reason!</p></div>').insertAfter('#imc-category-reason-textarea');
                }*/

            });

        } else {
            //////////////////////
            // EDIT ISSUE MODE

            $( "#imc-select-category-dropdown" ).change(function () {

                $('#cat_reason_notice').remove();
                current_cat_val = $( "#imc-select-category-dropdown").val();

                if (current_cat_val !== initial_cat_value) {
                    $('#cat_reason_box').show();
                } else {
                    $('#cat_reason_box').hide();
                }

            });

            $( "#imc-select-status-dropdown" ).change(function () {

                $('#status_reason_notice').remove();
                current_status_val = $( "#imc-select-status-dropdown").val();
                if (current_status_val !== initial_status_value) {
                    $('#status_reason_box').show();
                } else {
                    $('#status_reason_box').hide();
                }

            });


            $('#post').submit(function(event) {

                $('#cat_reason_notice').remove();
                $('#status_reason_notice').remove();

                category_textbox_val = $( "#imc-category-reason-textarea").val();
                status_textbox_val = $( "#imc-status-reason-textarea").val();

                if ($('#cat_reason_box').is(':visible') && category_textbox_val === '') {
                    event.preventDefault();
                    $('#ajax-loading').hide();
                    $('#publish').removeClass('button-primary-disabled');
                    $('<div id="cat_reason_notice" class="error below-h2"><p>'+requireReasonVars.reasonAlert+'</p></div>').insertAfter('#imc-category-reason-textarea');
                }

                if ($('#status_reason_box').is(':visible') && status_textbox_val === '') {
                    event.preventDefault();
                    $('#ajax-loading').hide();
                    $('#publish').removeClass('button-primary-disabled');
                    $('<div id="status_reason_notice" class="error below-h2"><p>'+requireReasonVars.reasonAlert+'</p></div>').insertAfter('#imc-status-reason-textarea');
                }

            });

        }

    }

});