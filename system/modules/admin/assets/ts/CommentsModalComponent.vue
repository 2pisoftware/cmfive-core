<script setup lang="ts">
	/* @ts-ignore */
    import { ref, defineProps, onMounted, computed, watchEffect, defineModel } from 'vue';
	import Quill from "../../../../templates/base/node_modules/quill";

	type Viewer = {
		id: string;
		name: string;
		can_view: boolean;
		is_notify: boolean;
		is_original_notify: boolean;
	}

	const props = defineProps<{
		comment: string,
		comment_id: string,
		viewers: string,
		top_object_class_name: string;
		top_object_id: string;
		new_owner: { id: string, name: string },
		can_restrict: string,
		is_new_comment: string,
		is_internal_only: string,
		has_notification_selection: string,
		is_restricted: string,
		is_parent_restricted: string,
		authed_user_id: string,
	}>();

	const is_restricted = ref(false)
	is_restricted.value = props.is_restricted === "true";

	const viewers = computed(() => JSON.parse(props.viewers) as Viewer[]);

	let quill: Quill;
	onMounted(() => {
		quill = new Quill("#new_comment_modal_quill", {
			theme: "snow",
		});
		quill.setText(props.comment);
	});

	const canNotifyViewers = computed(() => viewers.value.filter(viewer => {
		if (props.is_restricted === "true" && (viewer.is_original_notify || viewer.is_notify))
			return true;
		if (props.is_restricted !== "true" && viewer.id == props.authed_user_id)
			return true;
		if (props.is_restricted === "true" && viewer.can_view)
			return true;
		
		return false;
	}))

	const canRestrictViewers = computed(() => viewers.value.filter(viewer => viewer.id != props.authed_user_id));
	const canViewViewers = computed(() => viewers.value.filter(viewer => viewer.can_view));

	const saveComment = async () => {
		await fetch("/admin/ajaxAddComment", {
			method: "POST",
			body: JSON.stringify({
				comment: quill.getSemanticHTML(),
				comment_id: props.comment_id,
				viewers: viewers.value,
				new_owner: props.new_owner,
				top_object_class_name: props.top_object_class_name,
				top_object_id: props.top_object_id,
				is_internal_only: props.is_internal_only,
				is_restricted_only: is_restricted.value,
			})
		});

		window.history.go();
	}
</script>

<template>
	<div>
        <h3>{{ props.is_new_comment == "true" ? "New Comment" : "Edit Comment" }}</h3>

		<form id="new_comment_modal_form" method="POST" @submit.prevent="saveComment">
			<div id="new_comment_modal_quill" style="height: 250px"></div>

			<div class="mt-4" v-if="props.has_notification_selection == '1'">
				<div v-if="props.comment_id != 0">
					<label for="owner">Comment Owner</label>
					<select class="form-select">
						<option id="owner" v-for="viewer in canViewViewers">
							{{ viewer.name }}
						</option>
					</select>
				</div>

				<div>
					<div v-if="props.is_new_comment == 'true'">
						<div v-if="props.viewers.length !== 0">
							<strong>Select the users that will be notified by this comment</strong>
						</div>

						<div class="form-check" v-for="viewer in canNotifyViewers">
							<input :id="'notified_' + viewer.id" class="form-check-input" type="checkbox" v-model="viewer.is_notify" />
							<label :for="'notified_' + viewer.id" class="form-check-label d-inline ms-1">{{ viewer.name }}</label>
						</div>
						<hr />
					</div>
					<div v-if="props.can_restrict === 'true' && canRestrictViewers.length">
						<div class="form-check">
							<input 
								name="is_restricted" 
								id="is_restricted"
								type="checkbox"
								class="form-check-input mt-1"
								v-model="is_restricted"
								:disabled="props.is_parent_restricted == 'true'" />
							<label for="is_restricted" class="form-check-label">Limit who can view this comment</label>
						</div>
						
						<div class="ms-4 mt-2" v-if="is_restricted">
							<div class="form-check" v-for="viewer in canRestrictViewers">
								<input :id="'restrict_' + viewer.id" class="form-check-input" type="checkbox" v-model="viewer.can_view" />
								<label :for="'restrict_' + viewer.id" class="form-check-label d-inline">{{ viewer.name }}</label>
							</div>
						</div>
					</div>
					<div v-else-if="props.can_restrict === 'true'">
						<p>You have permission to restrict viewers, but you're the only one who can view.</p>
					</div>
				</div>
			</div>
			<div class="row mt-2">
				<div class="col">
					<button class="btn btn-primary savebutton" type="submit">Save</button>
				</div>
			</div>
		</form>
	</div>
</template>