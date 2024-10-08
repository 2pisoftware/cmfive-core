<div v-cloak id="app">
    <div class="panel">
        <h3>New Attachment</h3>
        <form method="POST" @submit.prevent="">
            <div class="small-12 medium-12 large-12">
                <label>
                    <input type="file" id="file" ref="file" @change="prepareFile()"/>
                </label>
                <label style="font-size: 18px;">Title
                    <input type="text" id="title" v-model="title"/>
                </label>
                <label style="font-size: 18px;">Description
                    <input type="text" id="description" v-model="description"/>
                </label><br>
                <div v-if="can_restrict == 'true'">
                    <label class="cmfive__checkbox-container">Restricted
                        <input type="checkbox" v-model="is_restricted">
                        <span class="cmfive__checkbox-checkmark"></span>
                    </label>
                    <div v-show="is_restricted"><strong>Select the users that can view this attachment</strong>
                        <ul class="small-block-grid-1 medium-block-grid-3 large-block-grid-3">
                            <li v-for="viewer in viewers" style="padding-bottom: 0;">
                                <label class="cmfive__checkbox-container">{{ viewer.name }}
                                    <input type="checkbox" v-model="viewer.can_view">
                                    <span class="cmfive__checkbox-checkmark" ></span>
                                </label>
                            </li>
                        </ul>
                    </div>
                </div>
            </div><br>
            <button class="small" style="margin-bottom: 0rem;" @click="uploadFile()">Save</button>
        </form>
    </div>
</div>
<script>
    const {
        createApp
    } = Vue;
    createApp({
        data: function() {
            return {
                can_restrict: "<?php echo $can_restrict; ?>",
                viewers: <?php echo empty($viewers) ? json_encode([]) : $viewers; ?>,
                title: null,
                description: null,
                file: null,
                is_restricted: false,
                max_upload_size: "<?php echo @FileService::getInstance($w)->getMaxFileUploadSize() ? : (2 * 1024 * 1024); ?>",
                class: "<?php echo $class; ?>",
                class_id: "<?php echo $class_id; ?>",
                redirect_url: "<?php echo $redirect_url; ?>",
            }
        },
        methods: {
            prepareFile: function() {
                this.file = this.$refs.file.files[0];
            },
            uploadFile: function() {
                if (this.file === null) {
                    new Toast("No file selected").show();
                    return;
                }

                if (this.file.size > this.max_upload_size) {
                    new Toast("File size is too large").show();
                    return;
                }

                toggleModalLoading();

                var file_data = {
                    title: this.title,
                    description: this.description,
                    class: this.class,
                    class_id: this.class_id,
                    is_restricted: this.is_restricted,
                    viewers: this.viewers.filter(function(viewer) {
                        return viewer.can_view;
                    })
                };

                var form_data = new FormData();
                form_data.append("file", this.file);
                form_data.append("file_data", JSON.stringify(file_data));

                axios.post("/file-attachment/ajaxAddAttachment",
                    form_data, {
                        headers: {
                            "Content-Type": "multipart/form-data"
                        }
                    }
                ).then(function(response) {
                    window.history.go();
                }).catch(function(error) {
                    new Toast("Failed to upload file").show();
                    console.log(error);
                }).finally(function() {
                    hideModalLoading();
                });
            }
        }
    }).mount("#app");
</script>