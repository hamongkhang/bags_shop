(function($) {

jQuery(document).ready(function($) {

	$('.hndle, .handlediv').click(function() {
        $(this).parent().toggleClass('closed');
		if ($(this).parent().hasClass('closed')) {
			$('.wpsf-settings .handlediv .dashicons').removeClass('dashicons-arrow-up');
			$('.wpsf-settings .handlediv .dashicons').addClass('dashicons-arrow-down');
		} else {
			$('.wpsf-settings .handlediv .dashicons').removeClass('dashicons-arrow-down');
			$('.wpsf-settings .handlediv .dashicons').addClass('dashicons-arrow-up');
		}
    });
	
	$('#wpsf-crawl-submit').click(function(e) {
		e.preventDefault();
		$('.wpsf-url-form').addClass('loading');
		$('.hndle, .handlediv').parent().removeClass('closed');
		var url = $('#wpsf-url').val();
		var fol = $('input[name=wpsf_follow]:checked').val();
		var num = $('input[name=wpsf_number]:checked').val();
		var error = false;
		if(url == '') {
			$('<p class="wpsf-error-notice">Url is a required field.</p>').insertAfter('#wpsf-urld');
			error = true;
		}
		if(!fol) {
			$('<p class="wpsf-error-notice">Domain Pattern is a required field.</p>').insertAfter('#wpsf-pattern');
			error = true;
		}
		if(!num) {
			$('<p class="wpsf-error-notice">Number of Pages is a required field.</p>').insertAfter('#wpsf-num');
			error = true;
		}
		if (error == false) {
		$.ajax({
			type: "POST",
			url: ajax_object.ajax_url,
			data: {
				"action": "wpsf_ajax_scrape",
				"url":url,
				"fol":fol,
				"num":num,
				"skp":$('#wpsf-skip').val(),
				"dep":$('#wpsf-depth').val(),
				"del":$('#wpsf-delay').val(),
				"typ":$('#wpsf-typematch option:selected').val(),
				"mat":$('input[name=wpsf_pattern]').val(),
			},
			timeout: 600000,
			success: function(data) {
				$('#wpsf-url-list').val(data);
				$('.hndle, .handlediv').parent().addClass('closed');
				$('.wpsf-url-form').removeClass('loading');
				//location.reload(true);
				//window.location = self.location;
            },
			error: function() {
				$('#wpsf-url-list').val('Something went wrong. Most likely, your server timeout limits have been reached. Set the number of pages to a smaller number and try again.');
				$('.hndle, .handlediv').parent().addClass('closed');
				$('.wpsf-url-form').removeClass('loading');
			}
		})
		} else { $('.wpsf-url-form').removeClass('loading'); }
	});
	
	$('#wpsf-continue-submit').click(function(e) {
		var error = false;
		if($('#wpsf-url-list').val() == '') {
			$('<p class="wpsf-error-notice">Webpages to Scrape is a required field.</p>').insertAfter('#wpsf-continue-submit');
			error = true;
		}
		if (error == true) {
			e.preventDefault();
		}
		if (error == false) {
			return true;
		}
	});

});

})(jQuery);