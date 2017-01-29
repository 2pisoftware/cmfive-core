/**
 * 
 * @author Robert Lockerbie, robert@lockerbie.id.au, 2015
 * 
 *  amended by Ged Nash November 2015 to allow multiple objects to have tags
 *  on page. 
 *  
 **/
var uniTag = {
	buf: '',
	ready: function (reload) {
		// There can be multiple tag lists on the page, so bind tags for
		// each individually
		

		$('.tag_list').each(function (index) {
			uniTag.bindTags($(this).get()[0].id);

		});

		if (reload) {
			//$('.tag_selection:visible:first').trigger('click');
		}
	},
	/*
	 * Builds the tag dialog
	 * This is a list of all available tags and highlights which ones are already attached
	 */
	loadTagDialog: function (tags, parent_id) {
		//console.log("test load dialog");
		// Clicking tags for current dialog will close the dialog
		$("#" + parent_id + ' .tag_selection').unbind('click');
		$("#" + parent_id + ' .tag_selection').bind('click', function (e) {
			$('.tag_list_dialog').hide();
			uniTag.bindTags(parent_id);
		});
		var url = $('#' + parent_id).data('url');
		$.get(url + '&cmd=getAll', function (availableTags) {
			uniTag.buf = '<div class="available_tags"><div class="available_tags_list">';
			if (availableTags.length == 0) {
				uniTag.buf += '<div class="custom_tag"><div class="label radius secondary"><span class="fi-price-tag">No tags found</span></div></div>';
			} else {
				for (var tagId in availableTags) {
					if (availableTags.hasOwnProperty(tagId)) {
						var tag = availableTags[tagId];
						var cl = 'secondary';
						//tags here is the list of attached tags
						for (var selectedTagId in tags) {
							if (tags.hasOwnProperty(selectedTagId)) {
								if (tags[selectedTagId].tag == tag.tag) {
									cl = 'primary';
									break;
								}
							}
						}
						uniTag.buf += '<div class="tag" data-id="' + tag.id + '" data-tag="' + tag.tag + '"><div class="label radius ' + cl + '"><span ';
						if (tag.tag_color != '') {
							uniTag.buf += 'style="color:' + tag.tag_color + ';"';
						}
						uniTag.buf += ' class="fi-price-tag">' + tag.tag + '</span></div></div> ';
					}
				}
			}
			uniTag.buf += '</div>';
			uniTag.buildTagDialog(parent_id);
		});
		return uniTag.buf;
	},
	buildTagDialog: function (parent_id) {

		$('#' + parent_id + ' .tag_list_dialog .tag_list_body').html(uniTag.buf);
		$('#' + parent_id + ' .tag_list_dialog').show();
		$('body').bind('click.taglisthide', uniTag.hideTagsOnLoseFocus);
		if ($('#' + parent_id + ' .tag_list_dialog').offset().left < 0) {
			$('#' + parent_id + ' .tag_list_dialog').css('left', Math.abs($('#' + parent_id + ' .tag_list_dialog').offset().left / 2));
			$('#' + parent_id + ' .tag_list_dialog').css('right', 'auto');
		}
		$('#' + parent_id + ' .tag_list_dialog .tag').bind('click', function (e) {
			uniTag.setTag(this, parent_id);
		});
		$('#' + parent_id + ' .tag_list_header .search_tags').keyup(function () {
			uniTag.filterTagDialog(parent_id, $(this).val());
		});
	},
	/*
	 * Filters the list of available tags
	 * No option is provided to create new tags here
	 */
	filterTagDialog: function (parent_id, term) {
		$('#' + parent_id + ' .available_tags_list .tag').each(function () {
			var tag = $(this).data('tag');
			if (tag !== undefined) {
				if (term.length == 0) {
					$(this).show();
				} else {
					var rE = new RegExp('.*' + term + '.*', 'i');
					if (tag.match(rE)) {
						$(this).show();
					} else {
						$(this).hide();
					}
				}
			}
		});
		if ($('#' + parent_id + ' .available_tags_list .tag:visible').length == 0) {
			if ($('#' + parent_id + ' .available_tags_list .custom_tag').length == 0) {
				$('#' + parent_id + ' .available_tags_list').prepend('<div class="custom_tag"><div class="label radius secondary"><span class="fi-price-tag">No tags found</span></div></div>');
			}
		} else {
			$('#' + parent_id + ' .available_tags_list .custom_tag').remove();
		}
	},
	/*
	 * Adds or removes a tag
	 */
	setTag: function (obj, parent_id) {
		//label refers to the label for the tag within the tag dialog
		var label = $(obj).find('.label');
		var list = $(obj).closest('.tag_list'); // $('#'+parent_id)
		var url = $('#' + parent_id).data('url');
		var tagId = $(obj).data('id');
		var tag = $(obj).data('tag');
		// check to see if the tag is allready selected
		if (label.hasClass('primary')) {
			//hide the tag and change class
			$('#' + parent_id + ' .tag_selection[data-tag="' + tag + '"]').addClass('hidetag');
			// If there are no more tags then show no tag
			if ($('#' + parent_id + ' .tag_selection:visible').length == 0) {
				$('#' + parent_id + ' .no_tags').removeClass('hidetag');
			}

			label.removeClass('primary').addClass('secondary');
			$.get(url + '&cmd=removeTag&tagId=' + tagId);
		} else {
			if ($('#' + parent_id + ' .tag_selection[data-tag="' + tag + '"]').length == 0)
			{
				// include information for hidden tags in all tags that are generated here
				$('#' + parent_id).append('<span data-tag="' + tag + '" class="label radius primary tag_selection"><span class="fi-price-tag">' + tag + '</span></span>' + (list.hasClass('limited') ? '<span class="limited_count"></span> ' : ' '));
				// Bind click action to tag    
				uniTag.bindTags(parent_id);
			} else {
				$('#' + parent_id + ' .tag_selection[data-tag="' + tag + '"]').removeClass('hidetag');
			}
			$('#' + parent_id + ' .no_tags').addClass('hidetag');
			$.get(url + '&cmd=setTag&tagId=' + tagId);
			label.removeClass('secondary').addClass('primary');
		}
		
		// how many hidden tags are there?
		if (list.hasClass('limited')) {
			$('.first', list).removeClass('first');
			var tags = $('.tag_selection.primary', list).not('.hidetag');
			var numTags = tags.length - 1;
			tags.first().addClass('first');
			$('.limited_count', list).text(" +" + numTags);
			if (numTags >= 1)
				list.addClass('show_num_limited');
			else
				list.removeClass('show_num_limited');
		}
	},
	hideTagsOnLoseFocus: function (e) {
		if ($(e.target).hasClass('tag_list_dialog') || $(e.target).parents('.tag_list_dialog').length > 0) {

		} else {
			$('.tag_list_dialog').hide();
			$('body').unbind('click.taglisthide');
		}
	},
	bindTags: function (parent_id) {
		$("#" + parent_id + ' .tag_selection').unbind('click');
		$("#" + parent_id + ' .tag_selection').bind('click', function (e) {
			// First, hide any other open dialog
			$('.tag_list_dialog').hide();
			$.get($("#" + parent_id).data('url') + '&cmd=get', function (result) {
				uniTag.loadTagDialog(result, parent_id);
			});
			e.preventDefault();
			e.stopImmediatePropagation();
			return false;
		});
		
		// bind click to close icon AND body 
		$("#"+parent_id+' .hide_tag_list ').bind('click.taglisthide', function() {
			$('.tag_list_dialog').hide();
			$('body').unbind('click.taglisthide');
		});
//		$('body').bind('click.taglisthide', uniTag.hideTagsOnLoseFocus);

//		$("#" + parent_id + ' .hide_tag_list').bind('click', function (e) {
//			$("#" + parent_id + ' .tag_list_dialog').hide();
//		});
		$('body').bind('click.taglisthide', uniTag.hideTagsOnLoseFocus);
	}
};
$(document).ready(function () {
	uniTag.ready(false);
});
