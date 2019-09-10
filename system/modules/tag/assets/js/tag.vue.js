/**
 *
 * @depends ajax-modal vue component
 */

Vue.component('tag', {
	data: function() {
		return {
			loading: true,
			display_tags: [],
			hidden_tags: [],
			tags: []
		}
	},
	props: {
		objectClass: {
			required: true,
			type: String
		},
		objectId: {
			required: true,
			type: String
		},
		preloadTags: Array,
		characterLimit: {
			required: false,
			type: Number,
			default: 10
		},
		prefetch: {
			type: Boolean,
			required: false,
			default: false
		},
		canEdit: {
			type: Boolean,
			required: false,
			default: false
		}
	},
	methods: {
		filterTags: function() {
			var character_count = 0;

			var _tag_copy = this.tags;
			if (this.tags) {
				for(var i in this.tags) {
					console.log(this.tags[i]);
					if (i > 0 && this.tags[i].tag.length + character_count > this.characterLimit) {
						this.display_tags = _tag_copy.splice(0, i);
						this.hidden_tags = _tag_copy;
						return;
					}
				}
			}

			// If we get here we didn't hit the given character limit
			this.display_tags = this.tags;
		},
		getTags: function() {
			console.log("LOADING TAGS");

			var _this = this;
			_this.loading = true;
			$.ajax({
				url: '/tag/ajaxGetTags/' + this.objectClass + '/' + this.objectId,
				method: 'GET',
				success: function(response) {
					var tags = JSON.parse(response);
					_this.display_tags = tags.display;
					_this.hidden_tags = tags.hover;
				},
				complete: function(response) {
					_this.loading = false;
				}
			});
		},
		openModal: function() {
			if (!this.canEdit) {
				return;
			}

			$('#' + this.modalId).foundation('reveal', 'open', this.modalUrl);
		}
	},
	computed: {
		containerId: function() {
			return 'tag_container_' + this.objectClass + '_' + this.objectId;
		},
		modalId: function() {
			return 'modal_' + this.containerId;
		},
		modalUrl: function() {
			return '/tag/changeTags/' + this.objectClass + '/' + this.objectId;
		},
		hoverClass: function() {
			return 'show_hover_' + this.objectClass + '_' + this.objectId;
		}
	},
	mounted: function() {
		if (this.preloadTags) {
			this.tags = this.preloadTags;
		}

		// Prefetch tags if needed
		if (this.prefetch === true) {
			this.updateTags();
		} else {
			this.loading = false;
		}

		this.filterTags();
	},
	template:  '<div> \
					<div class="tag_container" :id="containerId" @click="openModal()"> \
						<div class="tag_show_container" v-if="!loading && display_tags && display_tags.length"> \
							<span class="info label" v-for="tag in display_tags">{{ tag.tag }}</span> \
							<span class="count_hover_tags" v-if="hidden_tags && hidden_tags.length">+ {{ hidden_tags.length }}</span> \
						</div> \
						<div :class="hoverClass" v-if="!loading && hidden_tags && hidden_tags.length"> \
							<span class="info label" v-for="tag in hidden_tags">{{ tag.tag }}</span> \
						</div> \
						<span class="secondary label" v-if="!loading && (!display_tags || !display_tags.length)">No tags</span> \
						<div v-if="loading" class="loader"></div> \
					</div> \
					<ajax-modal :id="modalId" v-on:modalclose="getTags()"></ajax-modal> \
				</div>'
});
