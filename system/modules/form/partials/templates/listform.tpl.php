<div class='row-fluid'>
	<h4><?php echo $form->title; ?></h4>
	<?php echo $display_only !== true ? Html::box("/form-instance/edit?form_id=" . $form->id . "&redirect_url=" . $redirect_url . "&object_class=" . get_class($object) . "&object_id=" . $object->id, "Add new " . $form->title, true) : ''; ?>

	<div id="form_list_<?php echo $form->id; ?>">

		<table class='small-12'>
			<thead>
				<tr><?php echo $form->getTableHeaders(); ?><?php if ($display_only !== true) : ?><td>Actions</td><?php endif; ?></tr>
			</thead>
			<tbody>
				<tr v-if='!instances'><td colspan="2">No form instances found</td></tr>
				<tr v-if='instances' v-for="instance in instances" v-html="instance"></tr>
				<?php echo $form->getSummaryRow($object); ?>
			</tbody>
		</table>

		<ul class="pagination text-center">
			<li class="arrow" :class="{unavailable: page <= 1}"><a v-on:click="setPage(page - 1)">&laquo;</a></li>
			<li v-for="p in max_page" :class="{current: p == page}"><a v-on:click="setPage(p)">{{ p }}</a></li>
			<li class="arrow" :class="{unavailable: page >= max_page}"><a v-on:click="setPage(page + 1)">&raquo;</a></li>
		</ul>
	</div>

	<script>

		var form_list_<?php echo $form->id; ?> = new Vue({
			el: '#form_list_<?php echo $form->id; ?>',
			data: {
				instances: [],
				page: 1,
				pagesize: 10,
				total_size: <?php echo $form->countFormInstancesForObject($object); ?>
			},
			computed: {
				max_page: function() {
					return Math.ceil(this.total_size / this.pagesize);
				}
			},
			methods: {
				setPage: function(page) {
					if (page == 0 || page == (this.max_page + 1)) {
						return;
					} else {
						this.page = page;
					}
				},
				getInstances: function() {
					var _this = this;
					$.get('/form-vue/get_form_instance_rows/<?php echo $form->id; ?>/<?php echo get_class($object); ?>/<?php echo $object->id; ?>?page=' + this.page + '&pagesize=' + this.pagesize + '&display_only=<?php echo $display_only ? 1 : 0; ?>&redirect_url=<?php echo $redirect_url; ?>').done(function(response) {
						var _response = JSON.parse(response);
						if (_response.success) {
							_this.instances = _response.data;
						} else {
							alert(_response.error);
						}
					});
				}
			},
			watch: {
				page: function() {
					this.getInstances();
				}
			},
			created: function() {
				this.getInstances();
			}
		});

	</script>
</div>