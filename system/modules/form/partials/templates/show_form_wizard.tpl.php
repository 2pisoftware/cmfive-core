<main id="app">
   <div class="row">
        <div class="columns medium-8 small-12 small-centered" style="min-height: 30vh;">
            <div class="text-center">
                <h3>{{ fields[index]["name"] }}</h3>
                <textarea v-if="fields[index]['type'] === 'textarea'" rows="4" v-model="fields[index]['value']" :placeholder="fields[index]['hint']"></textarea>
                <input v-else class="radius" :type="fields[index]['type']" v-model="fields[index]['value']" :placeholder="fields[index]['hint']">
                <br>
            </div>
        </div>
        <div class="columns medium-8 small-12 small-centered">
            <button class="radius" @click="decrement">Back</button>
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
            }
        },
        methods: {
            decrement: function() {
                if (app.index <= 0) {
                    app.index = 0;
                    return;
                }

                --app.index;
            },
            increment: function() {
                if (app.index >= app.fields.length - 1) {
                    app.index = app.fields.length - 1;
                    return;
                }

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
                    // TODO: Redirect.
                    _this.is_loading = false;
                }).catch(function(error) {
                    // TODO: Redirect.
                    _this.is_loading = false;
                });
            },
        },
    })
</script>