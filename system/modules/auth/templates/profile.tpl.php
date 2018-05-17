<div id="app">
    <div id="twoFAmodal" class="reveal-modal" data-reveal aria-labelledby="" aria-hidden="true" role="dialog">
    </div>
    <?php echo $form; ?>
</div>

<script>
    new Vue({
        el: "#app",

        data: {
            active_2fa: "<?php echo $active_2fa; ?>"
        },

        methods: {
        },

        watch: {
            /*active_2fa: function(v) {
                if (v === true)
                    $('#twoFAmodal').foundation('reveal', 'open');
            }*/
        },

        created: function() {
            
        }
    });
</script>