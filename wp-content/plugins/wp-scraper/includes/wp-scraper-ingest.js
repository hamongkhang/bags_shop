(function($) {

jQuery(document).ready(function($) {
	var this_path = '';
	var html_code = '';
    var pg_imgs = [];
	var images = '';
	
	/*jQuery.fn.extend({
		getPath: function () {
			var path, node = this;
			if (node[0].id) return "#" + node[0].id;
			while (node.length) {
				var realNode = node[0], name = realNode.localName;
				if (!name) break;
				name = name.toLowerCase();
	
				var parent = node.parent();
	
				var sameTagSiblings = parent.children(name);
				if (sameTagSiblings.length > 1) { 
					allSiblings = parent.children();
					var index = allSiblings.index(realNode) + 1;
					if (index > 1) {
						name += ':nth-child(' + index + ')';
					}
				}
	
				path = name + (path ? '>' + path : '');
				node = parent;
			}
	
			return path;
		/*if (this.length != 1) throw 'Requires one element.';
		var path, node = this;
		if (node[0].id) return "#" + node[0].id;
		while (node.length) {
			var realNode = node[0],
				name = realNode.localName;
			if (!name) break;
			name = name.toLowerCase();
			var parent = node.parent();
			var siblings = parent.children(name);
			if (siblings.length > 1) {
				name += ':eq(' + siblings.index(realNode) + ')';
			}
			path = name + (path ? '>' + path : '');
			node = parent;
		}
		return path;
		}
	});*/
	
    $('a').click(function() {
        return false;
    });

    $('form').submit(function() {
        return false;
    });

    $('*').mousemove(function() {
        if ($(':hover', $(this)).length) {
            $(this).removeClass('wpscraper-hover');
			if($(this).prop('tagName').toLowerCase() == 'img') {
	            $(this).unwrap();
			}
        } else {
            $(this).addClass('wpscraper-hover');
			if($(this).prop('tagName').toLowerCase() == 'img') {
				if(!$(this).parent().hasClass('wpscraper-hover-parent')) {
	            	$(this).wrap('<div class="wpscraper-hover-parent"></div>');
				}
			}
        }
    }).mouseout(function() {
        $(this).removeClass('wpscraper-hover');
		if($(this).prop('tagName').toLowerCase() == 'img' && $(this).parent().hasClass('wpscraper-hover-parent')) {
			$(this).unwrap();
		}
    }).click(function() {
			
        if (!$(':hover', $(this)).length) {
			if($(this).find('.wpscraper-selected')) {
					$(this).find('.wpscraper-selected').each(function(index, element) {
                        $(this).removeClass('wpscraper-selected');
						if($(this).prop('tagName').toLowerCase() == 'img') {
							$(this).unwrap();
						}
                    });
			}
			if(!$(this).parent().closest('.wpscraper-selected').length) {
				if(!$(this).hasClass('wpscraper-selected')) {
            		$(this).addClass('wpscraper-selected');
					if($(this).prop('tagName').toLowerCase() == 'img') {
						if(!$(this).parent().hasClass('wpscraper-selected-parent')) {
							$(this).wrap('<div class="wpscraper-selected-parent"></div>');
						}
					}
				} else {
					$(this).removeClass('wpscraper-selected');
					if($(this).prop('tagName').toLowerCase() == 'img') {
						$(this).unwrap();
					}
				}
			}
        }
    });
	
	window.resetvars = function() {
		this_path = '';
		html_code = '';
		pg_imgs = [];
		images = '';
	}
	
	window.theSelector = function(thisObj) {
		var closestID = thisObj.closest('[id]');
		var closestClass = thisObj.closest('[class]');
		var idNum = closestID.offset();
		var classNum = closestClass.offset();
		while (closestClass.attr('class') == '') {
			closestClass.removeAttr('class');
			closestClass = closestClass.closest('[class]');
		}
		if (typeof idNum === 'undefined' && typeof classNum === 'undefined') {
			var thisParent = thisObj.parent();
			var parTag = thisParent.prop('tagName').toLowerCase();
			var likeEls = $(parTag);
			var parTotalIndex = likeEls.index( thisParent );
			var parStr = '';
			while (parTotalIndex != 0 && tagName != 'tbody') {
				var parTag = thisParent.prop('tagName').toLowerCase();
				var likeEls = $(parTag);
				var parTotalIndex = likeEls.index( thisParent );
				var thisPar = thisParent.parent();
				likeEls = thisPar.find(thisParent);
				var parIndex = likeEls.index(thisParent);
				parStr = ' > '+parTag+':eq('+parIndex+')'+parStr;
				thisParent = thisParent.parent();
			}
			var thisTag = thisObj.prop('tagName').toLowerCase();
			var thisPar = thisObj.parent();
			likeEls = thisPar.find(thisTag);
			var newIndex = likeEls.index(thisObj);
			returnStr = returnStr+parStr+' > '+thisTag+':eq('+newIndex+')';
			return returnStr;
		} else if (typeof idNum === 'undefined') {
			if (closestClass.is(thisObj)) {
				var thisClasses = thisObj.attr('class');
				thisClasses = thisClasses.split(" ");
				thisClasses = thisClasses.pop();
				var likeEls = $('.'+thisClasses);
				index = likeEls.index( closestClass );
				return '.'+thisClasses+':eq('+index+')';
			} else {
				var topClasses = closestClass.attr('class');
				var classes = topClasses.split(" ");
				classes = classes.pop();
				var likeEls = $('.'+classes);
				index = likeEls.index( closestClass );
				var returnStr = '.'+classes+':eq('+index+')';
				
				var thisParent = thisObj.parent();
				var parStr = '';
				while (!thisParent.is(closestClass)) {
					var parTag = thisParent.prop('tagName').toLowerCase();
					var thisPar = thisParent.parent();
					likeEls = thisPar.find(parTag);
					var parIndex = likeEls.index(thisParent);
					parStr = ' > '+parTag+':eq('+parIndex+')'+parStr;
					thisParent = thisParent.parent();
				}
				var thisTag = thisObj.prop('tagName').toLowerCase();
				var thisPar = thisObj.parent();
				likeEls = thisPar.find(thisTag);
				var newIndex = likeEls.index(thisObj);
				returnStr = returnStr+parStr+' > '+thisTag+':eq('+newIndex+')';
				return returnStr;
			}
		} else if (typeof classNum === 'undefined') {
			if (closestID.is(thisObj)) {
				var thisID = thisObj.attr('id');
				var likeEls = $("#"+thisID);
				index = likeEls.index( closestID );
				return '#'+thisID+':eq('+index+')';
			} else {
				var id = closestID.attr('id');
				var likeEls = $("#"+id);
				index = likeEls.index( closestID );
				var returnStr = '#'+id+':eq('+index+')';
				
				var thisParent = thisObj.parent();
				var parStr = '';
				while (!thisParent.is(closestID)) {
					var parTag = thisParent.prop('tagName').toLowerCase();
					var thisPar = thisParent.parent();
					likeEls = thisPar.find(parTag);
					var parIndex = likeEls.index(thisParent);
					parStr = ' > '+parTag+':eq('+parIndex+')'+parStr;
					thisParent = thisParent.parent();
				}
				var thisTag = thisObj.prop('tagName').toLowerCase();
				var thisPar = thisObj.parent();
				likeEls = thisPar.find(thisTag);
				var newIndex = likeEls.index(thisObj);
				returnStr = returnStr+parStr+' > '+thisTag+':eq('+newIndex+')';
				return returnStr;
				
			}
		} else {
			if (closestID.is(thisObj)) {
				var thisID = thisObj.attr('id');
				var likeEls = $("#"+thisID);
				index = likeEls.index( closestID );
				return '#'+thisID+':eq('+index+')';
			} else if (closestClass.is(thisObj)) {
				var thisClasses = thisObj.attr('class');
				thisClasses = thisClasses.split(" ");
				thisClasses = thisClasses.pop();
				var likeEls = $('.'+thisClasses);
				index = likeEls.index( closestClass );
				return '.'+thisClasses+':eq('+index+')';
			}
			if (idNum.top >= classNum.top) {
				var id = closestID.attr('id');
				var likeEls = $("#"+id);
				index = likeEls.index( closestID );
				var returnStr = '#'+id+':eq('+index+')';
				var thisParent = thisObj.parent();
				var parStr = '';
				while (!thisParent.is(closestID)) {
					var parTag = thisParent.prop('tagName').toLowerCase();
					var thisPar = thisParent.parent();
					likeEls = thisPar.find(parTag);
					var parIndex = likeEls.index(thisParent);
					parStr = ' > '+parTag+':eq('+parIndex+')'+parStr;
					thisParent = thisParent.parent();
				}
				var thisTag = thisObj.prop('tagName').toLowerCase();
				var thisPar = thisObj.parent();
				likeEls = thisPar.find(thisTag);
				var newIndex = likeEls.index(thisObj);
				returnStr = returnStr+parStr+' > '+thisTag+':eq('+newIndex+')';
				return returnStr;
				
			} else if (classNum.top > idNum.top) {
				var topClasses = closestClass.attr('class');
				var classes = topClasses.split(" ");
				var classes = classes.pop();
				var likeEls = $('.'+classes);
				index = likeEls.index( closestClass );
				var returnStr = '.'+classes+':eq('+index+')';
				var thisParent = thisObj.parent();
				var parStr = '';
				while (!thisParent.is(closestClass)) {
					var parTag = thisParent.prop('tagName').toLowerCase();
					var thisPar = thisParent.parent();
					likeEls = thisPar.find(parTag);
					var parIndex = likeEls.index(thisParent);
					parStr = ' > '+parTag+':eq('+parIndex+')'+parStr;
					thisParent = thisParent.parent();
				}
				var thisTag = thisObj.prop('tagName').toLowerCase();
				var thisPar = thisObj.parent();
				likeEls = thisPar.find(thisTag);
				var newIndex = likeEls.index(thisObj);
				returnStr = returnStr+parStr+' > '+thisTag+':eq('+newIndex+')';
				return returnStr;
				
			} else {return 'Error';}
		}
	}
	
	window.liveHtml = function() {
		if(!$('.wpscraper-selected').length) alert('Please select some content to scrape.');
		$('.wpscraper-hover').removeClass('wpscraper-hover');
		$('.wpscraper-selected').each(function (index) {
			if ($(this).prop('tagName').toLowerCase() == 'img') {
				if($(this).parent().hasClass('wpscraper-selected-parent')) {
					$(this).unwrap();	
				}
				if($(this).parent().hasClass('wpscraper-hover-parent')) {
					$(this).unwrap();	
				}
			}
			var nthis = this;
			if (!$(':hover', $(nthis)).length) {
            $(nthis).removeClass('wpscraper-selected').removeClass('wpscraper-hover');
			$('*[class=""]').removeAttr('class');
				if (this_path == '') {
					selector = theSelector( $(this) );
					this_path = selector;
					selector2 = $(this).getSelector();
					sec_path = selector2;
				} else {
					selector = theSelector( $(this) );
					this_path += ', '+selector;	
					selector2 = $(this).getSelector();
					sec_path += ', '+selector;	
				}
			
			if($(nthis).prop('tagName').toLowerCase() == 'img') {
				$(nthis).wrap('<div class="wpscraper-wrapper"></div>');
				var nthat = $(this).parent();
				nthis = $(nthat)[0];
				var wrapped = true;
			}
			
			$('img[src]', $(nthis)).each(function(index, item) {
                var image = $(item).attr('src');
                if (pg_imgs.indexOf(image) == -1) {
                    pg_imgs.push(image);
                }
            });
			
			var t_images = pg_imgs.join("\n");
			if (images == '') {
				images = t_images;
			}
			if (images != t_images) {
            	images = images+'\n'+t_images;
			}
			
			html_code += jQuery(nthis).get(0).outerHTML;
			
			
        }
		});
		
		$('*').removeClass('wpscraper-hover');
		$('*').removeClass('wpscraper-selected');
		$('.wpscraper-hover-parent').contents().unwrap();
		$('.wpscraper-selected-parent').contents().unwrap();
		$('.wpscraper-wrapper').contents().unwrap();
		window.parent.liveData(this_path, sec_path, html_code, images);
	};
	
}); 

})(jQuery);