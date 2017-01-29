<h2>Favorites</h2>
<?php
	if (!empty($categorisedFavorites)) {
		foreach ($categorisedFavorites as $category => $favorites) {
			if (!empty($favorites)) :
				?>
				<h4 style='font-weight: lighter;'><?php echo $category; ?></h4>
				<?php foreach ($favorites as $object) : ?>
					<div class='row panel' style='padding: 10px; margin-bottom: 10px;'>
						<?php echo $w->Favorite->getFavoriteButton($object); ?>
						<span style='display: inline-block; padding: 8px;'><?php echo $object->toLink();?></span>
					</div>
				<?php
				endforeach;
			endif;
		}
	}
?>
<script>
	$(document).ready(function () {
		$(".favorite_flag").click(function () {
			var _this = $(this);
			$.get("/favorite/ajaxEditFavorites?class=" + $(this).data('class') + "&id=" + $(this).data('id'), {}, function (response) {
				_this.toggleClass('favorite_on');
			});
		});
	});
</script>