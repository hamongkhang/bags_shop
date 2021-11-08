(function($) {

jQuery(document).ready(function($) {
	
		var form_data = $('#wpsf-add-multi-post-form').serialize();
		var urls = $('#wpsf-url-list').val();
		var newurls = $.trim(urls);
		if (newurls.match(",\n$")) {
			newurls = newurls.slice(0,-3);
		}
		if (newurls.match(",$")) {
			newurls = newurls.slice(0,-1);
		}
		var url_array = newurls.split(',');
		url_array.shift();
		$.ajax({
			type: "POST",
            url: $('#wpsf-add-multi-post-form').attr('action'),
            data: form_data,
			complete: function(data) {
				var title = $('#title_prefix').val()+$('#title').val()+$('#title_suffix').val();
				var s = data.responseText;
				s = s.substring(s.indexOf('{'));
				var response = $.parseJSON(s);
				var html = $('#wpsf-scraper-results tbody').html();
				$('#wpsf-scraper-results tbody').html(html+'<tr class="iedit author-self level-0 type-post format-standard hentry"><td class="title column-title has-row-actions column-primary page-title" data-colname="Title">'+title+'</td><td class="view column-view has-row-actions column-primary page-view"><a style="padding-left: 5px;" target="_blank" href="'+response.view+'">View</a></td> <td class="edit column-edit has-row-actions column-primary page-edit"><a style="padding-left: 5px;" target="_blank" class="post-edit-link" href="'+response.edit+'">Edit</a></td></tr>');
				$('#wpsf-scraper-results').show();
				var totalUrls = url_array.length;
				var completeUrls = 0;
				var next_limit = $('input[name=limit]').val();
				var index = 0;
				if (next_limit < 10) {
					var sliced = false;
					$.each(url_array, function(key, id) {
						if($.trim(id) == 'sliced') {
							sliced = true;
							completeUrls = completeUrls + 1;
						} else {
						setTimeout(function() {
							$.ajax({
								type: "POST",
								url: ajax_object.ajax_url,
								data: form_data + '&ThisUrl=' + id + '&action=wpsf_multi_scrape_action',
								dataType: "json",
								success: function(response) {
								completeUrls = completeUrls + 1;
								var entry = response;
								if (entry == "ERROR" || entry == null || entry.title == 'undefined' || entry == 0) {
									var html = $('#wpsf-scraper-results tbody').html();
									$('#wpsf-scraper-results tbody').html(html+'<tr class="iedit author-self level-0 type-post format-standard hentry"><td colspan="3" class="title column-title has-row-actions column-primary page-title" data-colname="Title">There was an error scraping the url <a href="'+id+'" target="_blank">'+id+'</a>. Please try again.</td></tr>');
								} else {
								var html = $('#wpsf-scraper-results tbody').html();
								$('#wpsf-scraper-results tbody').html(html+'<tr class="iedit author-self level-0 type-post format-standard hentry"><td class="title column-title has-row-actions column-primary page-title" data-colname="Title">'+entry.title+'</td><td class="view column-view has-row-actions column-primary page-view"><a style="padding-left: 5px;" target="_blank" href="'+entry.view+'">View</a></td> <td class="edit column-edit has-row-actions column-primary page-edit"><a style="padding-left: 5px;" target="_blank" class="post-edit-link" href="'+entry.edit+'">Edit</a></td></tr>');
								}
								if (completeUrls >= totalUrls) {
									$('.wpsf-form').hide('slow');
									$('.wpsf-form').removeClass('loading');
									if ($('#message').length == 0 ) {
										if (sliced == true) {
											$('#wpsf-scraper-results').prepend('<div id="message" class="updated notice"><p>You are limited to 10 posts or pages with the multiple scraper. <a href="http://wpscraper.com/purchase/">You can upgrade to WP Scraper Pro to unlock unlimited posts or pages.</a></p><p>If you permanently delete the posts you have already created, you can use the multiple scraper for another ten posts. To permanently delete posts, you must also empty your trash.</p></div>');
										} else {
											$('#wpsf-scraper-results').prepend('<div id="message" class="updated notice is-dismissible"><p>Scraping Complete!</p></div>');
										}
									}
								}
								},
								error: function(response) {
								completeUrls = completeUrls + 1;
								var html = $('#wpsf-scraper-results tbody').html();
								$('#wpsf-scraper-results tbody').html(html+'<tr class="iedit author-self level-0 type-post format-standard hentry"><td colspan="3" class="title column-title has-row-actions column-primary page-title" data-colname="Title">There was an error scraping the url <a href="'+id+'" target="_blank">'+id+'</a>. Please try again.</td></tr>');
								if (completeUrls >= totalUrls) {
									$('.wpsf-form').hide('slow');
									$('.wpsf-form').removeClass('loading');
									if ($('#message').length == 0 ) {
										if (sliced == true) {
											$('#wpsf-scraper-results').prepend('<div id="message" class="updated notice"><p>You are limited to 10 posts or pages with the multiple scraper. <a href="http://wpscraper.com/purchase/">You can upgrade to WP Scraper Pro to unlock unlimited posts or pages.</a></p><p>If you permanently delete the posts you have already created, you can use the multiple scraper for another ten posts. To permanently delete posts, you must also empty your trash.</p></div>');
										} else {
											$('#wpsf-scraper-results').prepend('<div id="message" class="updated notice is-dismissible"><p>Scraping Complete!</p></div>');
										}
									}
								}
								},
								});
						}, index*10000);
						index++;
						}
					});
				} else {
					$('.wpsf-form').hide('slow');
					if(!$('#wpsf-scraper-results > #message').length) {
						$('#wpsf-scraper-results').prepend('<div id="message" class="updated notice"><p>You are limited to 10 posts or pages with the multiple scraper. <a href="http://wpscraper.com/purchase/">You can upgrade to WP Scraper Pro to unlock unlimited posts or pages.</a></p><p>If you permanently delete the posts you have already created, you can use the multiple scraper for another ten posts. To permanently delete posts, you must also empty your trash.</p></div>');	
					}
				}
			},
            dataType: 'json'
        });
		
});
		
			
	

})(jQuery);