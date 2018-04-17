<style>
    #cmfive_V_logo {
        background-image: url("/system/templates/img/cmfive_V_logo.png");
        background-repeat: no-repeat;
        background-size: contain;
        background-position: center;
        height: 9em;
    }
    
    #submit {
        background-color: #2A2869;
    }
    
    #loginForm {
        
    }
</style>

<div class="row">
    <div class="columns large-4 medium-4 small-12 small-centered medium-centered large-centered">
        <form method="POST" action="/auth/login" id="loginForm">
            <input type="hidden" name="<?php echo CSRF::getTokenID(); ?>" value="<?php echo CSRF::getTokenValue(); ?>" />
            <div id="cmfive_V_logo"></div>
            <div style="height: 2em;"></div>
            <input class="radius" id="login" name="login" type="text" placeholder="Username or Email" style="height: 3em;"/>
            <input class="radius" id="password" name="password" type="password" placeholder="Password" style="height: 3em;"/>
            <div style="height: 1.5em;"></div>
            <div class="text-center">
                <button id="submit" type="submit" class="button round large-8 small-12">LOG IN</button>
            </div>

            <!--<button type="button" onclick="window.location.href='/auth/forgotpassword';" class="button alert large-5 small-12 right">Forgot Password</button>-->
        </form>
    </div>
</div>