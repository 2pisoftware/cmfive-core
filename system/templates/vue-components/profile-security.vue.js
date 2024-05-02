Vue.component('profile-security', {
  props: {
    userId: {
      type: String,
      required: true
    },
    isMfaEnabled: {
      type: Boolean,
      required: true
    }
  },
  template: `
    <div class="row-fluid body panel clearfix">
      <div class="small-12 medium-6 columns">
        <h3>Update Password</h3>
        <form>
          <label>New Password
            <input name="password" type="password" v-model="password" required />
          </label>
          <label>Confirm New Password
            <input name="confirm-password" type="password" v-model="passwordConfirmation" required />
          </label>
          <button class="btn btn-secondary" style="margin-left: 0;" @click.prevent="updatePassword">Change Password</button>
        </form>
      </div>
      <div class="small-12 medium-6 columns end">
        <div class="large-12">
          <h3>Two Factor Authentication</h3>
        </div>
        <div class="large-12">
          <label>Google Authenticator</label>
          <div>
            <button v-if="!isMfaEnabled && mfa_qr_code_url === null" class="btn btn-success" @click="getMfaQrCode" :disabled="is_loading">Enable</button>
            <button v-if="isMfaEnabled" class="btn btn-error" @click="disableMfa" :disabled="is_loading">Disable</button>
          </div>
          <div v-if="mfa_qr_code_url !== null">
            <div v-if="show_qr_code">
              <img :src="mfa_qr_code_url" width="250" height="250">
              <div>
                <p style="margin-top: 4px;">Can't scan the code? Add it manually. <strong>{{ mfa_secret }}</strong></p>
              </div>
            </div>
            <button v-else class="btn btn-secondary" @click="show_qr_code = true">Show QR Code</button>
            <form>
              <label>Code
                <input id="code" name="code" type="text" v-model="mfa_code" required />
              </label>
              <br>
              <br>
              <button class="btn btn-success" @click.prevent="confirmMfaCode" :disabled="is_loading">Confirm Code</button>
              <button class="btn btn-secondary" @click.prevent="cancel">Cancel</button>
            </form>
          </div>
        </div>
      </div>
    </div>`,
  data: function () {
    return {
      password: '',
      passwordConfirmation: '',
      mfa_code: "",
      mfa_secret: "",
      mfa_qr_code_url: null,
      show_qr_code: false,
      is_confirming_code: false,
      is_loading: false,
    }
  },
  methods: {
    updatePassword: function () {
      var _this = this;
      _this.password = _this.password.trim();
      _this.passwordConfirmation = _this.passwordConfirmation.trim();

      if (_this.is_loading === true || _this.password === "" || _this.passwordConfirmation === "") {
        return;
      }

      if (_this.password !== _this.passwordConfirmation) {
        (new Toast("Passwords don't match")).show();
        return;
      }

      _this.is_loading = true;

      axios.post("/auth/ajax_update_password", {
        id: _this.userId,
        new_password: _this.password,
        repeat_new_password: _this.passwordConfirmation
      }).then(function (response) {
        if (response.status !== 200) {
          (new Toast("Failed to update password")).show();
          return;
        }

        _this.password = "";
        _this.passwordConfirmation = "";
        (new Toast("Password successfully updated")).show();
      }).catch(function (error) {
        (new Toast("Failed to update password")).show();
        console.log(error);
      }).finally(function () {
        _this.is_loading = false;
      });
    },
    getMfaQrCode: function () {
      var _this = this;

      if (_this.is_loading === true) {
        return;
      }

      _this.is_loading = true;

      axios.get("/auth/ajax_get_mfa_qr_code", {
        params: {
          id: _this.userId
        }
      }).then(function (response) {
        if (response.status !== 200) {
          (new Toast("Failed to fetch QR Code")).show();
          return;
        }

        const { data } = response.data;
        _this.mfa_qr_code_url = data.qr_code;
        _this.mfa_secret = data.mfa_secret;
      }).catch(function (error) {
        (new Toast("Failed to fetch QR Code")).show();
        console.log(error);
      }).finally(function () {
        _this.is_loading = false;
      });
    },
    confirmMfaCode: function () {
      var _this = this;
      _this.mfa_code = _this.mfa_code.trim();

      if (_this.is_loading === true || _this.mfa_code === "") {
        return;
      }

      _this.is_loading = true;

      axios.post("/auth/ajax_confirm_mfa_code", {
        id: _this.userId,
        mfa_code: _this.mfa_code
      }).then(function (response) {
        if (response.status !== 200) {
          (new Toast("Failed to confirm 2FA Code")).show();
          return;
        }

        _this.mfa_qr_code_url = null;
        _this.mfa_secret = null;
        _this.isMfaEnabled = true;
        (new Toast("2FA enabled")).show();
      }).catch(function (error) {
        (new Toast("Failed to confirm 2FA Code")).show();
        console.log(error);
      }).finally(function () {
        _this.is_loading = false;
      });
    },
    disableMfa: function () {
      if (!confirm("Are you sure you want to disable 2FA? This will greatly decrease the security of your account.")) {
        return;
      }

      var _this = this;

      if (_this.is_loading === true) {
        return;
      }

      _this.is_loading = true;

      axios.post("/auth/ajax_disable_mfa", {
        id: _this.userId
      }).then(function (response) {
        if (response.status !== 200) {
          (new Toast("Failed to disable 2FA")).show();
          return;
        }

        _this.isMfaEnabled = false;
        (new Toast("2FA disabled")).show();
      }).catch(function (error) {
        (new Toast("Failed to disable 2FA")).show();
        console.log(error);
      }).finally(function () {
        _this.is_loading = false;
      });
    },
    cancel: function () {
      this.mfa_qr_code_url = null;
      this.show_qr_code = false;
    }
  }
});
