<main id="app">
   <div class="row">
        <div class="columns medium-8 small-12 small-centered" style="min-height: 30vh;">
            <div class="text-center">
                <h3>{{ fields[index]["name"] }}</h3>
                    <input class="radius" type="text" v-model="fields[index]['value']" :placeholder="fields[index]['hint']">
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
                fields: <?php echo json_encode($fields); ?>,
                index: 0,
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
                location.reload();
            },
        },
    })
</script>