/**
 * This script depends on the base tagButton.js
 * 
 * @author Robert Lockerbie, robert@lockerbie.id.au, 2015
 * @author Ged Nash <ged@2pisoftware.com>
 * @author Adam Buckley <adam@2pisoftware.com>
 */

var uniTag = uniTag || {};

uniTag.filterTagDialog = function (parent_id, term) {
	$('#' + parent_id + ' .available_tags_list .tag').each(function () {
		var tag = $(this).data('tag');
		if (tag !== undefined) {
			if (term.length == 0) {
				$(this).removeClass('hidetag');
			} else {
				var rE = new RegExp('.*' + term + '.*', 'i');
				if (tag.match(rE)) {
					$(this).removeClass('hidetag');
				} else {
					$(this).addClass('hidetag');
				}
			}
		}
	});
	if (term.length == 0) {
		$('#' + parent_id + ' .available_tags_list .custom_tag').remove();
	} else {
		var showNewTag = true;
		// Show new tag option unless there is an exact match for an existing tag
		$('#' + parent_id + ' .available_tags_list .tag:visible').each(function () {
			if ($(this).data('tag') == term.trim()) {
				showNewTag = false;
			}
		});
		if (showNewTag) {
			if ($('#' + parent_id + ' .available_tags_list .custom_tag').length == 0) {
				$('#' + parent_id + ' .available_tags_list').prepend('<div class="custom_tag"><div class="label radius success"><span class="fi-price-tag" data-tag="' + term + '">Create tag "' + term + '"</span></div></div>');
			} else {
				if ($('#' + parent_id + ' .available_tags_list .custom_tag .label').hasClass('secondary')) {
					$('#' + parent_id + ' .available_tags_list .custom_tag .label').removeClass('secondary').addClass('success');
				}
				$('#' + parent_id + ' .available_tags_list .custom_tag .fi-price-tag').text('Create tag "' + term + '"').data('tag', term);
			}
			$('#' + parent_id + ' .available_tags_list .custom_tag').unbind('click');
			$('#' + parent_id + ' .available_tags_list .custom_tag').bind('click', function () {
				//Add new tag to this object
				var url = $('#' + parent_id).data('url');
				var tagText = $(this).find('.fi-price-tag').data('tag');
				$.get(url + '&cmd=addTag&tag=' + encodeURIComponent(tagText), function (result) {
					if (result == 'Invalid request') {
						alert('Placeholder error');
					} else {
						var list = $('#' + parent_id);
						list.append('<span data-tag="' + tagText + '" class="label radius primary tag_selection"><span class="fi-price-tag">' + tagText + '</span></span>' + (list.hasClass('limited') ? '<span class="limited_count"></span> ' : ' '));
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
						$('#' + parent_id + ' .no_tags').addClass('hidetag');
						uniTag.ready(true);
					}
				});
				$(this).remove();
			});
		} else {
			$('#' + parent_id + ' .available_tags_list .custom_tag').remove();
		}
	}
};
