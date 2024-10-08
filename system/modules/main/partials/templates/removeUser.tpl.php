<div v-cloak id="app">
    <div v-show="!new_owner_set && owner_links.length > 0">
        <h3>Main</h3>
        <p>This user has ownership of the following restricted object(s). Select a new user to take over ownership. <strong>If a new User is not selected these restricted objects will be unretrievable</strong></p>
        <ul>
            <li v-for="restricted_object_class in restricted_object_classes">
                {{ restricted_object_class.count + " " + restricted_object_class.name + (restricted_object_class.count > 1 ? "s" : "") }}
            </li>
        </ul>
        <form method="POST" @submit.prevent="">
            <select @change="updateSelectedOwner">
                <option v-for="user in users" :value="JSON.stringify(user)">
                    {{ user.name }}
                </option>
            </select>
            <br><br><button class="small warning" @click="setNewOwner">Save</button>
        </form>
    </div>
</div>
<script>
    cosnt {
        createApp
    } = Vue;
    createApp({
        data: function() {
            return {
                deleting_user_id: "<?php echo $deleting_user_id; ?>",
                users: <?php echo empty($users) ? json_encode([]) : $users; ?>,
                owner_links: <?php echo empty($owner_links) ? json_encode([]) : $owner_links; ?>,
                restricted_object_classes: <?php echo empty($restricted_object_classes) ? json_encode([]) : $restricted_object_classes; ?>,
                new_owner: null,
                new_owner_set: false,
            }
        },
        methods: {
            updateSelectedOwner: function(event) {
                this.new_owner = JSON.parse(event.target.value);
            },
            setNewOwner: function() {
                if (!confirm("Are you sure you want to set this user as the new owner of these restricted object(s)?")) {
                    return;
                }

                toggleModalLoading();
                const response = await fetch('/main/ajaxSetNewOwner', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        deleting_user_id: this.deleting_user_id,
                        new_owner_id: this.new_owner.id,
                        owner_links: this.owner_links,
                    }),
                }).catch(function(error) {
                    new Toast("Failed to set new owner").show();
                    console.log(error);
                    window.history.go();
                });
                
                this.new_owner_set = true;
                new Toast("New owner successfully set").show();
                window.history.go();
            }
        },
        mounted: function() {
            this.$nextTick(function() {
                this.new_owner = this.users[0];
            })
        }
    }).mount("#app");
</script>