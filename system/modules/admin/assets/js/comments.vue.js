/**
 * 
 */

vue.component('comments', {
	props: {
		objectClass: {
			type: String,
			required: true
		},
		objectId: {
			type: String,
			required: true
		},
		internalOnly: {
			type: Boolean,
			default: true
		}
	},
	data: function() {
		return {
			heading: 'Add a new Comment',
			comment_text: ''
		}
	},
	methods: {
		addRootLevelComment: function() {
			$('#' + this.modalId).foundation('reveal', 'open');
		}
	},
	computed: {
		modalId: function() {
			return 'modal_' + this.objectClass + '_' + this.objectId;
		}
	},
	template: '<div> \
					<modal :id="modalId" :modalTitle="heading"> \
						<form-row> \
							<textarea v-model="comment_text" rows="15" cols="100"></textarea> \
						</form-row> \
						<form-row> \
							<p>Help</p> \
							<p>//italics//<br/>**bold**<br/><br/>* bullet list<br/>* second item<br/>** subitem<br/><br/># numbered list<br/># second item<br/>## sub item \
								<br/><br/>[[URL|linkname]]<br/><br/>== Large Heading<br/>=== Medium Heading<br/>==== Small Heading<br/><br/>Horizontal Line:<br/>---</p> \
						</form-row> \
						<form-row> \
							<button @click="saveComment()">Save</button> \
					</modal> \
					<button @click="addRootLevelComment()">Add new comment</button> \
			   </div>'
});
