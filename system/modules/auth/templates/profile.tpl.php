<link rel="stylesheet" type="text/css" href="/system/templates/vue-components/loading-indicator.vue.css">

<div id="app">
    <?php echo $form; ?>
</div>

<script src="/system/templates/vue-components/loading-indicator.vue.js"></script>

<script>
    new Vue({
        el: "#app",

        data: {
            active_2fa: "<?php echo $active_2fa; ?>"
        },

        watch: {
            active_2fa: function(v) {
                var barcode = document.getElementById("barcode");
                
                if (v) {
                    barcode.innerHTML = "<loading-indicator :show='true'></loading-indicator>";
                    
                    $.get("/auth/gettwofactorbarcode", function(data, status){
                        barcode.innerHTML = data;
                    });
                }

                if (!v) {
                    barcode.innerHTML = "";
                }
            }
        }
    });
</script>