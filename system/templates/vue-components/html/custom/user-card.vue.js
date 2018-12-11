/**
 * 
 */
Vue.component('user-card', {
	props: {
		id: {
			type: String,
			required: true
		}
	},
	data: function() {
		return {
			user: null,
			loaded: false
		}
	},
	methods: {
		gravatar_source: function() {
			if (this.loaded === true && this.user != null && this.user.gravatar_hash) {
				return "https://www.gravatar.com/avatar/" + this.user.gravatar_hash + "?d=identicon";
			}
		}
	},
	template: '<div class="user-card" v-if="loaded"> \
					<img v-if="this.user && this.user.gravatar_hash" class="user-card-avatar" :src="gravatar_source()" /> \
					<div class="user-card-details"> \
						<span class="user-card-name" v-html="user.name"></span> \
						<span class="user-card-email" v-html="user.email"></span> \
					</div> \
				</div>',
	created: function() {
		var _this = this;
		$.ajax('/task-ajax/user_details/' + this.id).done(function(response) {
			var _response = JSON.parse(response);
			_this.user = _response.data;
			_this.loaded = true;
		});	
	}
});

// Vue.component('user-card', {
// 	props: {
// 		id: {
// 			type: String,
// 			required: true
// 		},
// 		email: {
// 			type: String,
// 			required: true
// 		},
// 		name: {
// 			type: String,
// 			required: true
// 		},
// 		phoneNumber: String,
// 		gravatarHash: String
// 	},
// 	computed: {
// 		gravatar_source: function() {
// 			return "https://www.gravatar.com/avatar/" + this.gravatarHash + "?d=identicon";
// 		}
// 	},
// 	template: '<div class="user-card"> \
// 					<img v-if="gravatarHash" class="comment_avatar" :src="gravatar_source" /> \
// 					<p class="user-card-name" v-html="name"></p> \
// 					<p class="user-card-email" v-html="email"></p> \
// 				</div>'
// });