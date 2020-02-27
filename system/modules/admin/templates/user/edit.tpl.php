<div id="app">
<div class="tabs">
    <div class="tab-head">
        <a class="active" href="#general">General</a>
        <a href="#security">Security</a>
        <a href="#groups">Groups</a>
    </div>
    <div class="tab-body">
        <div id="general">
            <div class="row">
                <div class="small-12 medium-6 large-4 columns">
                    <label for="login">Login</label>
                    <input id="login" type="text" v-model="user.login" required>
                    <br>
                    <button class="tiny">Save</button>
                    <button class="info tiny">Cancel</button>
                </div>
            </div>
        </div>
        <div id="security">
        </div>
        <div id="groups">
        </div>
    </div>
</div>
</div>
<script>
    var app = new Vue({
        el: "#app",
        data: function() {
            return {
                user: "<?php echo json_encode($user); ?>"
            }
        },
        methods: {

        }
    })
</script>