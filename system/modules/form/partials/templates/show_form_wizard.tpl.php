<main id="app">
   <div class="row">
        <div class="columns medium-8 small-12 small-centered" style="min-height: 30vh;">
            <loading-indicator :show="is_loading"></loading-indicator>
            <div v-if="!is_loading">
                <div if="submission_status === 0" class="text-center">
                    <h3 v-if="submission_status === 0">{{ fields[index]["name"] }}</h3>
                    <textarea v-if="fields[index]['type'] === 'textarea'" ref="input" rows="4" v-model="fields[index]['value']" :placeholder="fields[index]['hint']"></textarea>
                    <select v-else-if="fields[index]['type'] === 'select'" ref="input" v-model="fields[index]['value']">
                        <option v-for="data in fields[index]['meta_data']" :value="data['value']">
                            {{ data['value'] }}
                        </option>
                    </select>
                    <input v-else-if="fields[index]['type'] === 'boolean'" type="checkbox" ref="input" v-model="fields[index]['value']">
                    <input v-else-if="submission_status === 0" ref="input" class="radius" :type="fields[index]['type']" v-model="fields[index]['value']" :placeholder="fields[index]['hint']">
                    <label v-if="show_required" class="text-left"><strong id="required">Please fill this in</strong></label>
                    <br>
                </div>
                <div v-if="submission_status === 200">
                    <h3 v-html="success_message"></h3>
                </div>
                <div v-if="submission_status !== 0 && submission_status !== 200">
                    <h3>{{ failure_message }}</h3>
                </div>
            </div>
        </div>
        <div v-if="submission_status === 0" class="columns medium-8 small-12 small-centered">
            <button v-if="index !== 0 && !is_loading"class="radius" @click="decrement">Back</button>
            <button v-if="index === fields.length - 1 && !is_loading" class="radius right success" @click="submit">Submit</button>
            <button v-else-if="!is_loading" class="radius right" @click="increment">Next</button>
        </div>
    </div>
</main>
<link rel='stylesheet' href='/system/templates/vue-components/loading-indicator.vue.css' />
<script src='/system/templates/vue-components/loading-indicator.vue.js'></script>
<script>
    const {
        createApp
    } = Vue;
    
    createApp({
        data: function() {
            return {
                form_id: '<?php echo $form_id; ?>',
                fields: <?php echo json_encode($fields); ?>,
                object_class: '<?php echo $object_class; ?>',
                object_id: '<?php echo $object_id; ?>',
                success_message: '<?php echo $success_message; ?>',
                failure_message: '<?php echo $failure_message; ?>',
                index: 0,
                is_loading: false,
                show_required: false,
                has_submitted: false,
                submission_status: 0,
            }
        },
        methods: {
            decrement: function() {
                if (app.index <= 0) {
                    app.index = 0;
                    return;
                }

                app.show_required = false;
                --app.index;
            },
            increment: function() {
                if (app.fields[app.index]["type"] !== "boolean" && app.fields[app.index]["value"].trim() === "") {
                    app.show_required = true;
                    return;
                }

                if (app.index >= app.fields.length - 1) {
                    app.index = app.fields.length - 1;
                    return;
                }

                app.show_required = false;
                ++app.index;
            },
            submit: function() {
                var _this = this;

                if (_this.is_loading) {
                    return;
                }

                _this.is_loading = true;

                var field_results = {};

                _this.fields.forEach(function(item, index) {
                    field_results[item['technical_name']] = item['value'];
                });

                axios.post("/form-instance/ajax_save_form_instance", {
                    form_id: _this.form_id,
                    field_results: field_results,
                    object_class: _this.object_class,
                    object_id: _this.object_id,
                }).then(function(response) {
                    _this.submission_status = response.status;
                    _this.is_loading = false;
                }).catch(function(error) {
                    _this.submission_status = response.status;
                    _this.is_loading = false;
                });
            },
        },
    }).mount("#app");
</script>