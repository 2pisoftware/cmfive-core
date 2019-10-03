<main id="app">
   <div class="row">
        <div class="columns medium-8 small-12 small-centered" style="min-height: 30vh;">
            <div v-if="submission_status === 0" class="text-center">
                <h3>{{ fields[index]["name"] }}</h3>
                <label v-if="show_required" class="text-left alert" style="color: red;">Required</label>
                <textarea v-if="fields[index]['type'] === 'textarea'" ref="input" rows="4" v-model="fields[index]['value']" :placeholder="fields[index]['hint']"></textarea>
                <input v-else ref="input" class="radius" :type="fields[index]['type']" v-model="fields[index]['value']" :placeholder="fields[index]['hint']">
                <br>
            </div>
            <div v-else-if="submission_status === 200">
                <h3>Thanks for that, someone will be in touch soon.</h3>
            </div>
            <div v-else>
                <h3>Looks like something went wrong. Try submitting the form again, but if the problem persits please contact us.</h3>
            </div>
        </div>
        <div v-if="submission_status === 0" class="columns medium-8 small-12 small-centered">
            <button v-if="index !== 0"class="radius" @click="decrement">Back</button>
            <button v-if="index === fields.length - 1" class="radius right success" @click="submit">Submit</button>
            <button v-else class="radius right" @click="increment">Next</button>
        </div>
    </div>
</main>
<script>
    var app = new Vue({
        el: "#app",
        data: function() {
            return {
                form_id: '<?php echo $form_id; ?>',
                fields: <?php echo json_encode($fields); ?>,
                object_class: '<?php echo $object_class; ?>',
                object_id: '<?php echo $object_id; ?>',
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
                    this.setFocus();
                    return;
                }

                app.show_required = false;
                --app.index;
                this.setFocus();
            },
            increment: function() {
                if (app.fields[app.index]["value"].trim() === "") {
                    app.show_required = true;
                this.setFocus();
                    return;
                }

                if (app.index >= app.fields.length - 1) {
                    app.index = app.fields.length - 1;
                this.setFocus();
                    return;
                }

                app.show_required = false;
                ++app.index;
                this.setFocus();
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
            setFocus: function() {
                this.$nextTick(() => {
                    this.$refs.input.focus();
                })
            },
        },
        created: function() {
            window.addEventListener("keydown", (e) => {
                if (e.key === "Enter") {
                    app.increment();
                }
            });
        },
        mounted: function() {
            this.setFocus();
        },
    })
</script>