/**
 * JS Helper file
 * 
 * @author Steve Ryan, steve@2pisoftware.com, 2015
 * @author Adam Buckley, adam@2pisoftware.com, 2015
 **/

//function toggleFavorite(linked_class, linked_id) {
//	$.get("/favorite/ajaxEditFavorites?class=" + linked_class + "&id=" + linked_id, {}, function(response) {
//		$(".favorite_flag").toggleClass('favorite_on');
//	});
//}

$(document).ready(function() {
	$(".favorite_flag").click(function() {
		var _this = $(this);
		$.get("/favorite/ajaxEditFavorites?class=" + $(this).data('class') + "&id=" + $(this).data('id'), {}, function(response) {
			_this.toggleClass('favorite_on');
		});
	});
});