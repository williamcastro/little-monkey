let API_KEY = 'http://api.littlemonkey:88/';

function resize_large_images() {
    jQuery('#lm_resize_large_images').change(function () {
        let data = parseInt(jQuery(this).val());

        if (data === 1)
            jQuery('.lm--width-height').fadeIn('fast');
        else
            jQuery('.lm--width-height').fadeOut('fast');
    })
}

function has_api_key() {
    /**
    setInterval(function() {
        jQuery.ajax({
            url: WP_AJAX.WP_AJAX_URL,
            data: { 'action': 'little_monkey_ajax_progress' },
            method: 'post',
            dataType: 'json'
        })
            .done(function (response) {
                console.log(response);
            })
    }, 2000);
     **/

    jQuery('#lm_settings_form').submit(function (e) {
        e.preventDefault();

        let api_key = jQuery('#lm_api_key');

        if (api_key.val() === '') {
            api_key.focus();
            alert('Please insert a API key, before bulk optimization');
        }

        jQuery.ajax({
            url: API_KEY + 'validate',
            method: 'post'
        })
        .done(function (response) {
            // Check if api key is valid
            if(response === 'success')
                return e.currentTarget.submit();

            // Not valid api key
            api_key.focus();
            alert('Your provided API KEY is not valid.')
        })
        .fail(function (error) {
            alert('We had problem contacting our API, try again minutes later please.');
        });
    })
}

jQuery(document).ready(function () {
    has_api_key();
    resize_large_images();
});
