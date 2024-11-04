<script setup lang="ts">
// import { defineProps, computed } from 'vue';
import { defineProps, computed, onMounted, ref, watchEffect } from '../../../../templates/base/node_modules/vue';

const props = defineProps<{
	user_json: string;
	enforce_max_length: string;
	enforce_min_length: string;
}>();

type User = {
	id: number;
	account: {
		firstname: string;
		lastname: string;
		othername: string | null;
		homephone: string | null;
		workphone: string | null;
		mobile: string | null;
		priv_mobile: string | null;
		fax: string | null;
		email: string | null;
	};
	security: {
		login: string;
		is_admin: "true" | "false",
		is_active: "true" | "false",
		is_external: "true" | "false",
		is_locked: 0 | 1,
		new_password: string;
		repeat_new_password: string;
		is_mfa_enabled: 0 | 1.;
	},
	groups: Array<unknown>;
};

const user = computed(() => JSON.parse(props.user_json) as User);

watchEffect(() => console.log(user));

onMounted(() => {
	// move over the php form since we can't generate it here.
	const userdetailsform = document.getElementById("userdetailsform");
	document.getElementById("details")!.innerHTML = userdetailsform!.innerHTML;
	userdetailsform!.remove();

	// force the cmfive helper to reattach
	// we use it's tab implementation, and when `load` triggers, we're not mounted yet
	//@ts-ignore
	window.cmfiveEventBus.dispatchEvent(new CustomEvent("dom-update", { detail: document }));
});

let is_loading = ref(false);

let mfa_qr_code_url = ref<string>(null);
let show_mfa_code = ref(false);
let show_qr_code = ref(false);
let mfa_secret = ref<string>();
let mfa_code = ref<string>();

const unlockAccount = async () => {
	is_loading.value = true;

	const response = await fetch("/admin-user/ajax_unlock_account", {
		method: "POST",
		body: JSON.stringify({
			id: user.value.id
		})
	}).catch((error) => {
		//@ts-ignore
		(new window.cmfive.toast("Failed to update")).show();
		console.log(error);
		is_loading.value = false;
	});

	if (!response) return;

	const json_response = await response.json();

	if (response.status !== 200) {
		//@ts-ignore
		(new window.cmfive.toast("Failed to unlock")).show();
		return;
	}

	//@ts-ignore
	(new window.cmfive.toast("Account unlocked")).show();
	user.value.security.is_locked = 0;

	is_loading.value = false;
};

const updatePassword = async () => {
	console.log(user.value.security);
	user.value.security.new_password = user.value.security.new_password.trim();
	user.value.security.repeat_new_password = user.value.security.repeat_new_password.trim();

	if (is_loading.value === true || user.value.security.new_password === "" || user.value.security.repeat_new_password === "") {
		return;
	}

	if (user.value.security.new_password !== user.value.security.repeat_new_password) {
		//@ts-ignore
		(new window.cmfive.toast("Passwords don't match")).show();
		return;
	}

	if (props.enforce_min_length) {
		if (user.value.security.new_password.length < Number(props.enforce_min_length)) {
			//@ts-ignore
			(new window.cmfive.toast(`Passwords must be at least ${props.enforce_min_length} characters long`)).show();
			return;
		}
	}

	is_loading.value = true;

	const response = await fetch("/auth/ajax_update_password", {
		method: "POST",
		body: JSON.stringify({
			id: user.value.id,
			new_password: user.value.security.new_password,
			repeat_new_password: user.value.security.repeat_new_password
		})
	}).catch(function (error) {
		//@ts-ignore
		(new window.cmfive.toast("Failed to update")).show();
		console.log(error);
		this.is_loading = false;
	});

	const json_response = await response!.json();

	if (response!.status !== 200) {
		//@ts-ignore
		(new window.cmfive.toast("Failed to update")).show();
		return;
	}

	user.value.security.new_password = "";
	user.value.security.repeat_new_password = "";
	//@ts-ignore
	(new window.cmfive.toast(json_response.message)).show();

	is_loading.value = false;
};

const getMfaQrCode = async () => {
	if (is_loading.value === true) {
		return;
	}

	is_loading.value = true;

	const response = await fetch(`/auth/ajax_get_mfa_qr_code?id=${user.value.id}`)
		.catch(function (error) {
			(new window.cmfive.toast("Failed to fetch QR Code")).show();
			is_loading.value = false;
			console.log(error);
		});
	const json_response = await response.json();

	if (response.status !== 200) {
		(new window.cmfive.toast("Failed to fetch QR Code")).show();
		is_loading.value = false;
		return;
	}

	mfa_qr_code_url.value = json_response.data.qr_code;
	mfa_secret.value = json_response.data.mfa_secret;

	is_loading.value = false;
};

const confirmMfaCode = async () => {
	mfa_code.value = mfa_code.value.trim();

	if (is_loading.value === true || mfa_code.value === "") {
		return;
	}

	is_loading.value = true;

	const response = await fetch("/auth/ajax_confirm_mfa_code", {
		method: "POST",
		body: JSON.stringify({
			id: user.value.id,
			mfa_code: mfa_code.value
		})
	}).catch(function (error) {
		(new window.cmfive.toast("Failed to confirm 2FA Code")).show();
		is_loading.value = false;
		console.log(error);
	});

	const json_response = await response.json();
	if (response.status !== 200) {
		(new window.cmfive.toast("Failed to confirm 2FA Code")).show();
		is_loading.value = false;
		return;
	}

	mfa_qr_code_url.value = null;
	mfa_secret.value = null;
	user.value.security.is_mfa_enabled = true;
	(new window.cmfive.toast("2FA enabled")).show();

	is_loading.value = false;
};

const disableMfa = async () => {
	if (!confirm("Are you sure you want to disable 2FA? This will greatly decrease the security of your account.")) {
		return;
	}

	if (is_loading.value === true) {
		return;
	}

	is_loading.value = true;

	const response = await fetch("/auth/ajax_disable_mfa", {
		method: "POST",
		body: JSON.stringify({
			id: user.value.id
		})
	}).catch(function (error) {
		(new window.cmfive.toast("Failed to disable 2FA")).show();
		is_loading.value = false;
	});
	const json_response = response.json();
	if (response.status !== 200) {
		(new window.cmfive.toast("Failed to disable 2FA")).show();
		is_loading.value = false;
		return;
	}

	user.value.security.is_mfa_enabled = false;
	(new window.cmfive.toast("2FA disabled")).show();

	is_loading.value = false;
};

const cancel = () => {
	mfa_qr_code_url.value = null;
	show_qr_code.value = false;
};
</script>

<template>
	<h3>
		Edit - {{
			`${user.account.firstname} ${user.account.lastname} ${user.account.othername ? user.account.othername : ""}`
		}}
	</h3>

	<div class="tabs">
		<div class="tab-head">
			<a href="#details">User Details</a>
			<a href="#security">Security</a>
			<a href="#groups">Groups</a>
		</div>

		<div class="tab-body">
			<div id="details"><!-- populated via onMount --></div>

			<div id="security">
				<div class="row">
					<div class="col-6">
						<form>
							<div v-if="user.account.is_locked === 1">
								This account is locked
								<input class="btn btn-danger" value="Unlock"
									style="font-size: 0.8rem; display: inline; float: middle; width: 100px; margin-top: -8px; margin-left: 15px;"
									@click.prevent="unlockAccount" :disabled="is_loading">
							</div>
						</form>
						<div class="panel mt-0">
							<div class="row g-0 section-header">
								<h4 class="col">Update Password</h4>
							</div>

							<div class="row">
								<form class="col">
									<div class="mb-3">
										<label for="password" class="form-label">New Password</label>
										<input id="password" name="password" type="password" required="required"
											v-model="user.security.new_password"
											:minlength="props.enforce_min_length ? props.enforce_min_length : null"
											class="form-control" />
									</div>

									<div class="mb-3">
										<label for="repeatpassword" class="form-label">Repeat New Password</label>
										<input id="repeatpassword" name="repeatpassword" type="password"
											required="required" v-model="user.security.repeat_new_password"
											:minlength="props.enforce_min_length ? props.enforce_min_length : null"
											class="form-control" />
									</div>

									<button class="btn btn-primary btn-sm m-0" type="submit"
										@click.prevent="updatePassword" :disabled="is_loading">Update Password</button>
								</form>
							</div>
						</div>
					</div>

					<div class="col-6">
						<div class='panel clearfix'>
							<div class='row g-0 clearfix section-header'>
								<h4 class='col'>Google Authenticator</h4>
							</div>
							<button v-if="user.security.is_mfa_enabled == 0 && mfa_qr_code_url === null"
								class="btn btn-success" @click="getMfaQrCode" :disabled="is_loading">Enable</button>
							<button v-if="!!user.security.is_mfa_enabled" class="btn btn-warning" @click="disableMfa"
								:disabled="is_loading">Disable</button>
							<div v-if="mfa_qr_code_url !== null">
								<div v-if="show_qr_code">
									<img :src="mfa_qr_code_url" width="250" height="250">
									<label style="display: block; margin-top: 4px;">Can't scan the code? Add it
										manually.</label>
									<label>{{ mfa_secret }}</label>
								</div>
								<button v-else class="btn btn-primary" @click="show_qr_code = true">Show QR
									Code</button>
								<form>
									<div class="mb-3">
										<label>Code</label>
										<input id="code" name="code" type="text" required="required" v-model="mfa_code"
											class="form-control" />
									</div>
									<button class="btn btn-primary" @click.prevent="confirmMfaCode"
										:disabled="is_loading">Confirm Code</button>
									<button class="btn btn-secondary" @click.prevent="cancel">Cancel</button>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div id='groups'>
                <div class="row-fluid body panel clearfix">
                    <h3>Groups</h3>
                    <ul>
                        <li v-for="group in user.groups" :key="group.title">
                            <a :href="group.url" target="_blank">{{ group.title }}</a>
                        </li>
                    </ul>
                </div>
            </div>
		</div>
	</div>
</template>