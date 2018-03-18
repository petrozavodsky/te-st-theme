jQuery(function ($) {

    var te_st_theme_form = {
        run: function () {

            this.form_ajax("[action$='action=TeStTheme_insert_post']");
        },
        form_ajax: function (selector) {


            $(selector).on('submit', function (event) {
                event.preventDefault();

                var form_action = $(this).attr('action');
                var form_elem = $(this);
                var form_data = $(this).serialize();
                var form_method = $(this).attr('method');


                $.ajax({
                    type: form_method,
                    url: form_action,
                    data: form_data,
                    beforeSend: function (jqXHR, status) {
                        form_elem.find('.alert').slideUp(900, function () {
                           $(this).remove();
                        });
                        form_elem.find('.loader').addClass('active');
                    },
                    success: function (json) {
                        form_elem.prepend(json.data.message);
                    },
                    complete: function (jqXHR, status) {

                        form_elem.find('.loader').removeClass('active');
                    }

                });

            });
        }
    };


    $(document).ready(function () {
        te_st_theme_form.run();
    });


});




