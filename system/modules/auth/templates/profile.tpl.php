<div id="app">
    <div id="twoFAmodal" class="reveal-modal" data-reveal aria-labelledby="" aria-hidden="true" role="dialog">
        <div class="text-center">
            <?php 
                $g = new \Google\Authenticator\GoogleAuthenticator();
                $secret = $username.$salt;
                echo '<img src="'.$g->getURL($username, $_SERVER['HTTP_HOST'], $secret).'" />'; 
            ?>
        </div>
    </div>
    <?php echo $form; ?>
</div>

<script>
    new Vue({
        el: app,
        methods: {
            show_qr: function() {
                
            }
        }
    });
</script>