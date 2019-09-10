/**
 * 
 */

Vue.component('html-pagination', {
	props: {
		numItems: {
			type: Number,
			required: false,
			default: 0
		},
		perPage: {
			type: Number,
			required: false,
			default: 20
		}
	},
	data: function() {
		return {
			current_page: 1
		}
	},
	computed: {
		num_pages: function() {
			var _num_pages = Math.ceil(this.numItems / this.perPage);
			if (_num_pages) {
				return _num_pages
			} else {
				return 0;
			}
		}
	},
	methods: {
		changePage: function(page) {
			if (page > 0 && page <= this.num_pages) {
				this.current_page = page
				this.$emit('paginate', this.current_page)
			}
		},
		incrementPage: function(increment) {
			this.changePage(this.current_page + increment);
		}
	},
	onCreate: function() {
		this.changePage(1)
	},
	template:  '<div class="pagination-centered"> \
					<ul class="pagination"> \
						<li class="arrow" :class="{\'unavailable\': current_page <= 1}"><a @click.prevent="incrementPage(-1)">&laquo;</a></li> \
						<li v-for="page in num_pages" :class="{\'current\': page == current_page}"> \
							<a href="#" @click.prevent="changePage(page)">{{ page }}</a> \
						</li> \
						<li class="arrow" :class="{\'unavailable\': current_page >= num_pages}"><a @click.prevent="incrementPage(1)">&raquo;</a></li> \
					</ul> \
				</div>'
})