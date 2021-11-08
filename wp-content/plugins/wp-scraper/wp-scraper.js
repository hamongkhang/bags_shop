(function($) {

jQuery(document).ready(function($) {
	
	
	$('input[name=fix]').change(function(){
        if ($(this).is(':checked')) {
			$('input[name=title]').addClass('fixes');
			$('input[name=title_prefix]').show();
			$('input[name=title_suffix]').show();
		} else {
			$('input[name=title]').removeClass('fixes');
			$('input[name=title_prefix]').hide();
			$('input[name=title_suffix]').hide();
		}
	});
	
	
	$('.admin_page_wp-scraper-add-menu #toplevel_page_wp-scraper').removeClass('wp-not-current-submenu');
	$('.admin_page_wp-scraper-add-menu #toplevel_page_wp-scraper').addClass('wp-has-current-submenu');
	$('.admin_page_wp-scraper-add-menu #toplevel_page_wp-scraper a.wp-not-current-submenu').addClass('wp-has-current-submenu');
	$('.admin_page_wp-scraper-add-menu #toplevel_page_wp-scraper a.wp-not-current-submenu').removeClass('wp-not-current-submenu');
	$('.admin_page_wp-scraper-add-menu #toplevel_page_wp-scraper ul li:nth-child(4n)').addClass('current');

	$('.wpsf-selector').focus(function() {
			$('<p class="wpsf-notice description">Changing this field affects how data is pulled from your list of urls. Please only change this value if you understand how to use CSS selectors to choose the data for each field. Thank you!</p>').insertAfter(this).delay(10000).fadeOut();
	});
	
	$('.wpsf-selector:not(#live_selector)').change(function() {
		var url = $('#wpsf-url').val().trim();
		var downloader = jQuery('#wpsf-downloader-url').val().trim();
		var this_path = $(this).val();
		
		if (!url) {
			alert('URL not set');
			return false;
			} else {
			var this_id = $(this).attr('id');
			
				if(this_id == 'title_selector') {
					var title_selector = $('#title_selector').val();
					jQuery.post(ajax_object.ajax_url,{
							"action": "wpsf_live_scrape_action",
							"url": url,
							"downloader": downloader,
							"selector": this_path,
						},function(response){
							var title = jQuery(response).text();
							jQuery('#title').val(title);
						})
					.error(function() {
						jQuery('#title').val('There was an error scraping. Please try again.');
	var newSrc = jQuery('#wpsf-content-extractor-url').val()+'&blockUrl='+encodeURIComponent(jQuery('#wpsf-url').val())+'&down='+down+js;
	jQuery('#content-extractor-iframe').attr('src', newSrc);
					});
				} else if(this_id == 'body_selector') {
					$('#wpsf-data .inside').addClass('loading');
					var body_selector = $('#body_selector').val();
					jQuery.post(ajax_object.ajax_url,{
								"action": "wpsf_live_scrape_action",
								"url": url,
								"downloader": downloader,
								"selector": this_path,
							},function(response){
								  body_parsed = response;
								  $('#wpsf-html').val(body_parsed);
								$('#wpsf-data .inside').removeClass('loading');
							})
						.error(function() {
							jQuery('#wpsf-html').val('There was an error scraping. Please try again.');
	var newSrc = jQuery('#wpsf-content-extractor-url').val()+'&blockUrl='+encodeURIComponent(jQuery('#wpsf-url').val())+'&down='+down+js;
	jQuery('#content-extractor-iframe').attr('src', newSrc);
						});
				} else if(this_id == 'cat_selector') {
					var cat_selector = $('#cat_selector').val();
					jQuery.post(ajax_object.ajax_url,{
								"action": "wpsf_live_scrape_action",
								"url": url,
								"downloader": downloader,
								"selector": this_path,
							},function(response){
								  cat_parsed = jQuery(response).text();
									jQuery('#newcategory').val(response);
									jQuery('#category-add-submit').click();
							})
						.error(function() {
							jQuery('#newcategory').val('There was an error scraping. Please try again.');
	var newSrc = jQuery('#wpsf-content-extractor-url').val()+'&blockUrl='+encodeURIComponent(jQuery('#wpsf-url').val())+'&down='+down+js;
	jQuery('#content-extractor-iframe').attr('src', newSrc);
						});
				} else if(this_id == 'tags_selector') {
					var tags_selector = $('#tags_selector').val();
						jQuery.post(ajax_object.ajax_url,{
								"action": "wpsf_live_scrape_action",
								"url": url,
								"downloader": downloader,
								"selector": tags_selector,
							},function(response){
								$('#new-tag-post_tag').val(tags_now);
								$('.tagadd').click(); 
							})
						.error(function() {
							jQuery('#new-tag-post_tag').val('There was an error scraping. Please try again.');
	var newSrc = jQuery('#wpsf-content-extractor-url').val()+'&blockUrl='+encodeURIComponent(jQuery('#wpsf-url').val())+'&down='+down+js;
	jQuery('#content-extractor-iframe').attr('src', newSrc);
						});
				} else if(this_id == 'fi_selector') {
					var fi_selector = $('#fi_selector').val();
					var fi_parsed = '';
					var fi_html = '';
					var fi_images = '';
					jQuery.post(ajax_object.ajax_url,{
							"action": "wpsf_live_scrape_action",
							"url": url,
							"downloader": downloader,
							"selector": this_path,
						},function(response){
							var new_html = jQuery.liveHtml(response);
							var new_image = jQuery(response).find('img').attr('src');
							fi_images = new_image;
							jQuery('.wpsf_featured').attr('src', fi_images);
							jQuery('#wpsf_featured_image').val(fi_images);
							jQuery('#set-featured-thumbnail').addClass('remove');
							jQuery('#set-featured-thumbnail').html('Remove Featured Image');
							jQuery('#choose_image_content').hide();
						})
					.error(function() {
						jQuery('.wpsf_featured').append('<p>There was an error scraping. Please try again.</p>');
	var newSrc = jQuery('#wpsf-content-extractor-url').val()+'&blockUrl='+encodeURIComponent(jQuery('#wpsf-url').val())+'&down='+down+js;
	jQuery('#content-extractor-iframe').attr('src', newSrc);
					});
				} 
			};
		});

	$('.save-post-status').click(function(e) {
		e.preventDefault();
		var status = $( "#post_status" ).val();
		$('#hidden_post_status').val(status);
		if (status == 'draft') var display = 'Draft';
		if (status == 'publish') var display = 'Published';
		if (status == 'pending') var display = 'Pending Review';
		$('#post-status-display').html(display);
	});
	
	$('.edit-post-type').click(function(e) {
		e.preventDefault();
		$('#post-type-select').show();
	});
	
	$('.cancel-post-type').click(function(e) {
		e.preventDefault();
		$('#post-type-select').hide();
	});
	
	$('.save-post-type').click(function(e) {
		e.preventDefault();
		var type = $( "#post_type" ).val();
		$('#hidden_post_type').val(type);
		var display = type.substr(0,1).toUpperCase()+type.substr(1);
		$('#post-type-display').html(display);
		$('#post-type-select').hide();
		
		jQuery.post(ajax_object.ajax_url,{
				"action": "wpsf_custom_fields",
				"post_type": type,
			},function(response){
				$metaArr = response.split(', ');
				if ($metaArr[0] == 0) {
					$('#titlediv').hide();
				} else $('#titlediv').show();
				if ($metaArr[1] == 0) {
					$('#wpsf-data').hide();
				} else $('#wpsf-data').show();
				if ($metaArr[2] == 0) {
					$('#postimagediv').hide();
				} else $('#postimagediv').show();
				if ($metaArr[3] == 0) {
					$('#tagsdiv-post_tag').hide();
				} else $('#tagsdiv-post_tag').show();
				if ($metaArr[4] == 0) {
					$('#categorydiv').hide();
				} else $('#categorydiv').show();
			}
		);
	});
	
	var custom_uploader1;
 
    $('#set-featured-thumbnail').click(function(e) {
        e.preventDefault();
		var set = false;
 		if($(this).hasClass('remove')) {
			$('.wpsf_featured').attr('src', '');
			$('.wpsf_featured').hide();
            $('#wpsf_featured_image').val('');
			$('#set-featured-thumbnail').removeClass('remove');
			$('#set-featured-thumbnail').html('Set Featured Image');
			$('#choose_image_content').show();
			set = true;
		}
		if(set == false) {
        //If the uploader object has already been created, reopen the dialog
        if (custom_uploader1) {
            custom_uploader1.open();
            return;
        }
 
        //Extend the wp.media object
        custom_uploader1 = wp.media.frames.file_frame = wp.media({
            title: 'Featured Image',
            button: {
                text: 'Set Featured Image'
            },
            multiple: false
        });
 
        //When a file is selected, grab the URL and set it as the text field's value
        custom_uploader1.on('select', function() {
            attachment = custom_uploader1.state().get('selection').first().toJSON();
			$('.wpsf_featured').attr('src', attachment.url);
			$('.wpsf_featured').show();
            $('#wpsf_featured_image').val(attachment.id);
			$('#set-featured-thumbnail').addClass('remove');
			$('#set-featured-thumbnail').html('Remove Featured Image');
			$('#choose_image_content').hide();
        });
 
        //Open the uploader dialog
        custom_uploader1.open();
		}
 
    });
	
	$('#choose_body_content, #choose_title_content, #choose_tags_content, #choose_cat_content, #choose_image_content').click(function () {
	var url = $('#wpsf-url').val().trim();
	if (!url) {
		alert('URL not set');
		return false;
	} else {
		if($(this).attr('id') == 'choose_body_content') {
			$('#content-extractor-iframe').addClass('wpsbody');
			$('#content-extractor-iframe').removeClass('wpstitle');
			$('#content-extractor-iframe').removeClass('wpstags');
			$('#content-extractor-iframe').removeClass('wpscat');
			$('#content-extractor-iframe').removeClass('wpsimage');
			$('#wpsf-html-html').click();
		}
		if($(this).attr('id') == 'choose_title_content') {
			$('#content-extractor-iframe').addClass('wpstitle');
			$('#content-extractor-iframe').removeClass('wpsbody');
			$('#content-extractor-iframe').removeClass('wpstags');
			$('#content-extractor-iframe').removeClass('wpscat');
			$('#content-extractor-iframe').removeClass('wpsimage');
		}
		if($(this).attr('id') == 'choose_tags_content') {
			$('#content-extractor-iframe').addClass('wpstags');
			$('#content-extractor-iframe').removeClass('wpsbody');
			$('#content-extractor-iframe').removeClass('wpstitle');
			$('#content-extractor-iframe').removeClass('wpscat');
			$('#content-extractor-iframe').removeClass('wpsimage');
		}
		if($(this).attr('id') == 'choose_cat_content') {
			$('#content-extractor-iframe').addClass('wpscat');
			$('#content-extractor-iframe').removeClass('wpsbody');
			$('#content-extractor-iframe').removeClass('wpstitle');
			$('#content-extractor-iframe').removeClass('wpstags');
			$('#content-extractor-iframe').removeClass('wpsimage');
		}
		if($(this).attr('id') == 'choose_image_content') {
			$('#content-extractor-iframe').addClass('wpsimage');
			$('#content-extractor-iframe').removeClass('wpsbody');
			$('#content-extractor-iframe').removeClass('wpstitle');
			$('#content-extractor-iframe').removeClass('wpstags');
			$('#content-extractor-iframe').removeClass('wpscat');
		}
		$('#TB_window').css({ 'bottom': '30px', 'left': '30px', 'position': 'fixed', 'right': '30px', 'top': '30px', 'z-index': '160000' });
		if($("input[name=down]").prop('checked') == true){
			var down = 1;
		} else {
			var down = 0;
		}
		if($("input[name=js]").prop('checked') == true){
			var js = '&js=true';
		} else {
			var js = '';
		}
		
		var newSrc = $('#wpsf-content-extractor-url').val()+'&blockUrl='+encodeURIComponent($('#wpsf-url').val())+'&down='+down+js;
		if($('#content-extractor-iframe').attr('src') != newSrc) {
			$('#content-extractor-iframe').attr('src', newSrc);
		}
		$('#TB_overlay').fadeIn();
		$('#TB_window').fadeIn();
	}
	});
	

$('#auto_submit').click(function(e) {
	e.preventDefault();
	$('.wpsf-form').addClass('loading');
		var error = false;
		jQuery('#wpsf-html-html').click();
		if($('#title').val() == '') {
			$('<p class="wpsf-error-notice">Title is a required field.</p>').insertAfter('#title');
			error = true;
		}
		if($('#wpsf-url').val() == '') {
			$('<p class="wpsf-error-notice">Page Url is a required field.</p>').insertAfter('#wpsf-url');
			error = true;
		}
		if($('#wpsf-html').val() == '') {
			$('<p class="wpsf-error-notice">Page Content is a required field.</p>').insertAfter('#wpsf-html');
			error = true;
		}
		if (error == false) {
        	$('#wpsf-add-multi-post-form').submit();
		} else { 
        $('.wpsf-form').removeClass('loading');
		}
        return false;
});

$('#wpsf-add-post-form').submit(function() {
        $('.wpsf-form').addClass('loading');
		var error = false;
		jQuery('#wpsf-html-html').click();
		if($('#title').val() == '') {
			$('<p class="wpsf-error-notice">Title is a required field.</p>').insertAfter('#title');
			error = true;
		}
		if($('#wpsf-url').val() == '') {
			$('<p class="wpsf-error-notice">Page Url is a required field.</p>').insertAfter('#wpsf-url');
			error = true;
		}
		if($('#wpsf-html').val() == '') {
			$('<p class="wpsf-error-notice">Page Content is a required field.</p>').insertAfter('#wpsf-html');
			error = true;
		}
		if (error == false) {
        tinyMCE.triggerSave();
        $.ajax({
			type: "POST",
            url: $(this).attr('action'),
            data: $(this).serialize(),
			complete: function(data) {
				var s = data.responseText;
				s = s.substring(s.indexOf('{'));
				var response = $.parseJSON(s);
				window.location = response.redirect_url;
            },
            dataType: 'json'
        });
		} else { 
        $('.wpsf-form').removeClass('loading');
		}
        return false;
    }); 

}); 

$('#wpsf-select-html').click(function() {
	jQuery('#TB_window').addClass('loading');
	window.frames['wpsf-extractor'].resetvars();
	window.frames['wpsf-extractor'].liveHtml();
});

})(jQuery);

function liveData(this_path, sec_path, html, images)
{
	var url = jQuery('#wpsf-url').val();
	var multiple = false;
	if(jQuery('#wpsf_is_mult').val() == 'true') {
		multiple = true;
		jQuery('.overlay-loading').show();
	}
	
    if(jQuery('#content-extractor-iframe').hasClass('wpsbody')) {
		jQuery('#wpsf-images').val(images);
		jQuery('#wpsf-data').show();
		if (multiple == true) {
			jQuery('#body_selector').val(this_path);
			jQuery('#body_selector').show();
			if(jQuery("input[name=down]").prop('checked') == true){
				var downloader = jQuery('#wpsf-downloader-url').val().trim();
			} else { var downloader = 'false';}
			jQuery.post(ajax_object.ajax_url,{
						"action": "wpsf_live_scrape_action",
						"url": url,
						"downloader": downloader,
						"selector": this_path,
					},function(response){ 
							jQuery('#wpsf-html').val(response);
							jQuery('#content-extractor-iframe').removeClass('wpsbody');
						jQuery('.overlay-loading').hide();
						jQuery('.overlay-loading').hide();			jQuery('body.modal-open').css('overflow', 'scroll');
							
						jQuery('#wpsf-html-tmce').click();
						}
				)
				.error(function() {
					if (this_path.indexOf("tbody") >= 0) {
						var selectorArr = this_path.split(', ');
						var newSelector = '';
						jQuery.each(selectorArr, function(k, v) {
							if (k > 0) {
								newSelector = newSelector+', ';
							}
							var elemArr = v.split(' > ');
							jQuery.each(elemArr, function(key, value) {
							if (typeof value != "undefined") {
								if (value.indexOf("tbody") >= 0) {
									//tbody is removed
								} else {
									if (newSelector == '') {
										newSelector = value;
									} else {
										newSelector += ' > '+value;
									}
								}
							}
							});
						});
						jQuery.post(ajax_object.ajax_url,{
							"action": "wpsf_live_scrape_action",
							"url": url,
							"downloader": downloader,
							"selector": newSelector,
						},function(response){ 
							jQuery('textarea#wpsf-html').val(response);
							jQuery('#content-extractor-iframe').removeClass('wpsbody');
						jQuery('.overlay-loading').hide();
						jQuery('.overlay-loading').hide();			jQuery('body.modal-open').css('overflow', 'scroll');
							
						jQuery('#wpsf-html-tmce').click();
						})
						.error(function() {
							jQuery('textarea#wpsf-html').val('There was an error scraping. Please try again.');
	var newSrc = jQuery('#wpsf-content-extractor-url').val()+'&blockUrl='+encodeURIComponent(jQuery('#wpsf-url').val())+'&down='+down+js;
	jQuery('#content-extractor-iframe').attr('src', newSrc);
							jQuery('#content-extractor-iframe').removeClass('wpsbody');
						jQuery('.overlay-loading').hide();
						jQuery('.overlay-loading').hide();			jQuery('body.modal-open').css('overflow', 'scroll');
							
						jQuery('#wpsf-html-tmce').click();
						});
					} else {
						jQuery('textarea#wpsf-html').val('There was an error scraping. Please try again.');
	var newSrc = jQuery('#wpsf-content-extractor-url').val()+'&blockUrl='+encodeURIComponent(jQuery('#wpsf-url').val())+'&down='+down+js;
	jQuery('#content-extractor-iframe').attr('src', newSrc);
							jQuery('#content-extractor-iframe').removeClass('wpsbody');
						jQuery('.overlay-loading').hide();			jQuery('body.modal-open').css('overflow', 'scroll');
							
						jQuery('#wpsf-html-tmce').click();

					}
				});
		} else {
			jQuery('textarea#wpsf-html').val(html);
			jQuery('#content-extractor-iframe').removeClass('wpsbody');
			jQuery('body.modal-open').css('overflow', 'scroll');
				
			jQuery('#wpsf-html-tmce').click();
		}
		
	} else if(jQuery('#content-extractor-iframe').hasClass('wpstitle')) {
		if (multiple == true) {
			jQuery('#title_selector').val(this_path);
			jQuery('#title_selector').show();
			if(jQuery("input[name=down]").prop('checked') == true){
				var downloader = jQuery('#wpsf-downloader-url').val().trim();
			} else { var downloader = 'false';}
			jQuery.post(ajax_object.ajax_url,{
						"action": "wpsf_live_scrape_action",
						"url": url,
						"downloader": downloader,
						"selector": this_path,
					},function(response){ 
							var title = jQuery(response).text();
							jQuery('#title').val(title);
						jQuery('.overlay-loading').hide();
						}
				)
				.error(function() {
					if (this_path.indexOf("tbody") >= 0) {
						var selectorArr = this_path.split(', ');
						var newSelector = '';
						jQuery.each(selectorArr, function(k, v) {
							if (k > 0) {
								newSelector = newSelector+', ';
							}
							var elemArr = v.split(' > ');
							jQuery.each(elemArr, function(key, value) {
							if (typeof value != "undefined") {
								if (value.indexOf("tbody") >= 0) {
									//tbody is removed
								} else {
									if (newSelector == '') {
										newSelector = value;
									} else {
										newSelector += ' > '+value;
									}
								}
							}
							});
						});
						jQuery.post(ajax_object.ajax_url,{
							"action": "wpsf_live_scrape_action",
							"url": url,
							"downloader": downloader,
							"selector": newSelector,
						},function(response){ 
							var title = jQuery(response).text();
							jQuery('#title').val(title);
						jQuery('.overlay-loading').hide();
						})
						.error(function() {
							jQuery('#title').val('There was an error scraping. Please try again.');
	var newSrc = jQuery('#wpsf-content-extractor-url').val()+'&blockUrl='+encodeURIComponent(jQuery('#wpsf-url').val())+'&down='+down+js;
	jQuery('#content-extractor-iframe').attr('src', newSrc);
						jQuery('.overlay-loading').hide();
						});
					} else {
						jQuery('#title').val('There was an error scraping. Please try again.');
	var newSrc = jQuery('#wpsf-content-extractor-url').val()+'&blockUrl='+encodeURIComponent(jQuery('#wpsf-url').val())+'&down='+down+js;
	jQuery('#content-extractor-iframe').attr('src', newSrc);
						jQuery('.overlay-loading').hide();
					}
				});
		} else { 
			var title = jQuery(html).text();
			jQuery('#title').val(title);
		}
		jQuery('#content-extractor-iframe').removeClass('wpstitle');
		jQuery('body.modal-open').css('overflow', 'scroll');
	} else if (jQuery('#content-extractor-iframe').hasClass('wpstags')) {
		if (multiple == true) {
			jQuery('#tags_selector').val(this_path);
			jQuery('#tags_selector').show();
			if(jQuery("input[name=down]").prop('checked') == true){
				var downloader = jQuery('#wpsf-downloader-url').val().trim();
			} else { var downloader = 'false';}
			jQuery.post(ajax_object.ajax_url,{
						"action": "wpsf_live_scrape_action",
						"url": url,
						"downloader": downloader,
						"selector": this_path,
					},function(response){ 
							var tags = jQuery(response).text();
							jQuery('#new-tag-post_tag').val(tags);
							jQuery('.tagadd').click();
							jQuery('.overlay-loading').hide();
						}
				)
				.error(function() {
					if (this_path.indexOf("tbody") >= 0) {
						var selectorArr = this_path.split(', ');
						var newSelector = '';
						jQuery.each(selectorArr, function(k, v) {
							if (k > 0) {
								newSelector = newSelector+', ';
							}
							var elemArr = v.split(' > ');
							jQuery.each(elemArr, function(key, value) {
							if (typeof value != "undefined") {
								if (value.indexOf("tbody") >= 0) {
									//tbody is removed
								} else {
									if (newSelector == '') {
										newSelector = value;
									} else {
										newSelector += ' > '+value;
									}
								}
							}
							});
						});
						jQuery.post(ajax_object.ajax_url,{
							"action": "wpsf_live_scrape_action",
							"url": url,
							"downloader": downloader,
							"selector": newSelector,
						},function(response){ 
							var tags = jQuery(response).text();
							jQuery('#new-tag-post_tag').val(tags);
							jQuery('.tagadd').click();
							jQuery('.overlay-loading').hide();
						})
						.error(function() {
							jQuery('#new-tag-post_tag').val('There was an error scraping. Please try again.');
	var newSrc = jQuery('#wpsf-content-extractor-url').val()+'&blockUrl='+encodeURIComponent(jQuery('#wpsf-url').val())+'&down='+down+js;
	jQuery('#content-extractor-iframe').attr('src', newSrc);
						jQuery('.overlay-loading').hide();
						});
					} else {
						jQuery('#new-tag-post_tag').val('There was an error scraping. Please try again.');
	var newSrc = jQuery('#wpsf-content-extractor-url').val()+'&blockUrl='+encodeURIComponent(jQuery('#wpsf-url').val())+'&down='+down+js;
	jQuery('#content-extractor-iframe').attr('src', newSrc);
						jQuery('.overlay-loading').hide();
					}
				});
		} else { 
			var tags = jQuery(html).text();
			jQuery('#new-tag-post_tag').val(tags);
			jQuery('.tagadd').click();
		}
		jQuery('#content-extractor-iframe').removeClass('wpstags');
		jQuery('body.modal-open').css('overflow', 'scroll');
	} else if(jQuery('#content-extractor-iframe').hasClass('wpscat')) {
		if (multiple == true) {
			jQuery('#cat_selector').val(this_path);
			jQuery('#cat_selector').show();
			if(jQuery("input[name=down]").prop('checked') == true){
				var downloader = jQuery('#wpsf-downloader-url').val().trim();
			} else { var downloader = 'false';}
			jQuery.post(ajax_object.ajax_url,{
						"action": "wpsf_live_scrape_action",
						"url": url,
						"downloader": downloader,
						"selector": this_path,
					},function(response){ 
							var cat = jQuery(response).text();
							jQuery('#newcategory').val(cat);
							jQuery('#category-add-submit').click();
						jQuery('.overlay-loading').hide();
						}
				)
				.error(function() {
					if (this_path.indexOf("tbody") >= 0) {
						var selectorArr = this_path.split(', ');
						var newSelector = '';
						jQuery.each(selectorArr, function(k, v) {
							if (k > 0) {
								newSelector = newSelector+', ';
							}
							var elemArr = v.split(' > ');
							jQuery.each(elemArr, function(key, value) {
							if (typeof value != "undefined") {
								if (value.indexOf("tbody") >= 0) {
									//tbody is removed
								} else {
									if (newSelector == '') {
										newSelector = value;
									} else {
										newSelector += ' > '+value;
									}
								}
							}
							});
						});
						jQuery.post(ajax_object.ajax_url,{
							"action": "wpsf_live_scrape_action",
							"url": url,
							"downloader": downloader,
							"selector": newSelector,
						},function(response){ 
							var cat = jQuery(response).text();
							jQuery('#newcategory').val(cat);
							jQuery('#category-add-submit').click();
						jQuery('.overlay-loading').hide();
						})
						.error(function() {
							alert('There was an error scraping. Please try again.');
	var newSrc = jQuery('#wpsf-content-extractor-url').val()+'&blockUrl='+encodeURIComponent(jQuery('#wpsf-url').val())+'&down='+down+js;
	jQuery('#content-extractor-iframe').attr('src', newSrc);
						jQuery('.overlay-loading').hide();
						});
					} else {
						alert('There was an error scraping. Please try again.');
	var newSrc = jQuery('#wpsf-content-extractor-url').val()+'&blockUrl='+encodeURIComponent(jQuery('#wpsf-url').val())+'&down='+down+js;
	jQuery('#content-extractor-iframe').attr('src', newSrc);
						jQuery('.overlay-loading').hide();
					}
				});
		} else { 
			var cat = jQuery(html).text();
			jQuery('#newcategory').val(cat);
			jQuery('#category-add-submit').click();
		}
		jQuery('#content-extractor-iframe').removeClass('wpscat');
		jQuery('body.modal-open').css('overflow', 'scroll');
	} else if(jQuery('#content-extractor-iframe').hasClass('wpsimage')) {
		if(images == '') {
			var new_html = jQuery.parseHTML(html);
			var new_image = jQuery(new_html).find('img').attr('src');
			images = new_image;
		}
		
		if (multiple == true) {
			jQuery('#fi_selector').val(this_path);
			jQuery('#fi_selector').show();
			if(jQuery("input[name=down]").prop('checked') == true){
				var downloader = jQuery('#wpsf-downloader-url').val().trim();
			} else { var downloader = 'false';}
			jQuery.post(ajax_object.ajax_url,{
						"action": "wpsf_live_scrape_action",
						"url": url,
						"downloader": downloader,
						"selector": this_path,
					},function(response){ 
							var new_html = jQuery.parseHTML(response);
							var new_image = jQuery(new_html).find('img').attr('src');
							if(new_image == '' || typeof new_image == 'undefined') {
								new_html = jQuery(new_html).wrap('<div class="wpscraper-wrapper"></div>');
								new_image = jQuery(new_html).find('img').attr('src');
								if(new_image == '' || typeof new_image == 'undefined') {
									new_image = jQuery(new_html).attr('src');
								}
							}
							images = new_image;
							jQuery('.wpsf_featured').attr('src', images);
							jQuery('.wpsf_featured').show();
							jQuery('#wpsf_featured_image').val(images);
							jQuery('#set-featured-thumbnail').addClass('remove');
							jQuery('#set-featured-thumbnail').html('Remove Featured Image');
							jQuery('#choose_image_content').hide();
							jQuery('#content-extractor-iframe').removeClass('wpsimage');
							jQuery('.overlay-loading').hide();
						}
				)
				.error(function() {
					if (this_path.indexOf("tbody") >= 0) {
						var selectorArr = this_path.split(', ');
						var newSelector = '';
						jQuery.each(selectorArr, function(k, v) {
							if (k > 0) {
								newSelector = newSelector+', ';
							}
							var elemArr = v.split(' > ');
							jQuery.each(elemArr, function(key, value) {
							if (typeof value != "undefined") {
								if (value.indexOf("tbody") >= 0) {
									//tbody is removed
								} else {
									if (newSelector == '') {
										newSelector = value;
									} else {
										newSelector += ' > '+value;
									}
								}
							}
							});
						});
						jQuery.post(ajax_object.ajax_url,{
							"action": "wpsf_live_scrape_action",
							"url": url,
							"downloader": downloader,
							"selector": newSelector,
						},function(response){ 
							var new_html = jQuery.parseHTML(response);
							var new_image = jQuery(new_html).find('img').attr('src');
							images = new_image;
							jQuery('.wpsf_featured').attr('src', images);
							jQuery('.wpsf_featured').show();
							jQuery('#wpsf_featured_image').val(images);
							jQuery('#set-featured-thumbnail').addClass('remove');
							jQuery('#set-featured-thumbnail').html('Remove Featured Image');
							jQuery('#choose_image_content').hide();
							jQuery('#content-extractor-iframe').removeClass('wpsimage');
						jQuery('.overlay-loading').hide();
						})
						.error(function() {
							alert('There was an error scraping. Please try again.');
	var newSrc = jQuery('#wpsf-content-extractor-url').val()+'&blockUrl='+encodeURIComponent(jQuery('#wpsf-url').val())+'&down='+down+js;
	jQuery('#content-extractor-iframe').attr('src', newSrc);
						jQuery('.overlay-loading').hide();
						});
					} else {
						alert('There was an error scraping. Please try again.');
	var newSrc = jQuery('#wpsf-content-extractor-url').val()+'&blockUrl='+encodeURIComponent(jQuery('#wpsf-url').val())+'&down='+down+js;
	jQuery('#content-extractor-iframe').attr('src', newSrc);
						jQuery('.overlay-loading').hide();
					}
				});
		} else { 
			jQuery('.wpsf_featured').attr('src', images);
			jQuery('.wpsf_featured').show();
			jQuery('#wpsf_featured_image').val(images);
			jQuery('#set-featured-thumbnail').addClass('remove');
			jQuery('#set-featured-thumbnail').html('Remove Featured Image');
			jQuery('#choose_image_content').hide();
			jQuery('#content-extractor-iframe').removeClass('wpsimage');
						jQuery('.overlay-loading').hide();
		}
		
		jQuery('body.modal-open').css('overflow', 'scroll');
	} 
	jQuery('#TB_overlay').fadeOut();
	jQuery('#TB_window').fadeOut();
	//jQuery('#content-extractor-iframe').removeAttr('src').empty();
    
}