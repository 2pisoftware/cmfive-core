<form id="auth_form">
    <?php
    echo (new \Html\Form\InputField\Hidden([
        "name" => CSRF::getTokenID(),
        "value" => CSRF::getTokenValue(),
    ]));
    ?>

    <div
        id="errors"
        data-alert
        class="alert
        alert-warning
        fade
        show
        row
        d-none
        justify-content-between"
        role='alert'>
    </div>

    <div id="login_form">
        <label for="login" class="col-form-label">
            <?php echo Config::get('auth.login_label', 'Login'); ?>
        </label>
        <?php
        echo (new \Html\Form\InputField([
            "id|name" => "login",
            "placeholder" => Config::get('auth.login_label', 'Login'),
            "required" => true,
            "class" => "form-control-lg form-control",
        ]));
        ?>

        <label for="login" class="col-form-label">
            Password
        </label>
        <?php
        echo (new \Html\Form\InputField([
            "id|name" => "password",
            "placeholder" => "Your password",
            "required" => true,
            "class" => "form-control-lg form-control",
            "type" => "password"
        ]));
        ?>

        <div class='row d-flex justify-content-between mt-3 row-cols-1 row-cols-sm-2'>
            <div class='col'>
                <button type="submit" class="btn btn-primary w-100 h-auto">Login</button>
            </div>
            <div class='col text-center text-sm-end'>
                <a onclick="window.location.href='/auth/forgotpassword';" class="btn w-auto "><?php echo $passwordHelp; ?></a>
            </div>
        </div>
    </div>

    <div id="mfa_form" class="d-none">
        <label for='mfa_code' class='col-form-label'>MFA Code
            <?php
            echo (new \Html\Form\InputField([
                "id|name" => "mfa_code",
                "placeholder" => "Your code",
                "class" => "form-control-lg form-control",
            ]));
            ?>
        </label>

        <div class='row d-flex justify-content-between mt-3 row-cols-1 row-cols-sm-2'>
            <div class='col'>
                <button type="submit" class="btn btn-primary w-100">Confirm</button>
            </div>
            <div class='col mt-3 mt-sm-0'>
                <button class="btn btn-secondary w-100" onclick="back">Back</button>
            </div>
        </div>
    </div>
</form>

<script>
    const mfa_form = document.getElementById("mfa_form");
    const login_form = document.getElementById("login_form");
    const auth_form = document.getElementById("auth_form");
    const errors = document.getElementById("errors");

    const executeLogin = async (e) => {
        e.preventDefault();

        errors.classList.add("d-none");
        errors.classList.remove("d-flex");

        const formdata = new FormData(auth_form);

        let res;
        try {
            res = await fetch("/auth/login", {
                method: "POST",
                headers: {
                    "content-type": "application/json"
                },
                body: JSON.stringify({
                    "<?php echo CSRF::getTokenID(); ?>": "<?php echo CSRF::getTokenValue(); ?>",
                    "login": formdata.get("login"),
                    "password": formdata.get("password"),
                    "mfa_code": formdata.get("mfa_code"),
                })
            });
        } catch (e) {
            auth_form.reset();
            errors.innerText = e.message;
            errors.classList.remove("d-none");
            errors.classList.add("d-flex");
        }

        const json = await res.json();

        if (json.data.redirect_url != null)
            return window.location.href = json.data.redirect_url;

        if (json.data.is_mfa_enabled) {
            mfa_form.classList.remove("d-none");
            login_form.classList.add("d-none");

            const code_input = document.getElementById("mfa_code");
            code_input.setAttribute("required", "required");
            code_input.focus();
            return;
        }

        if (json.status == 500) {
            auth_form.reset();
            errors.innerText = json.message ?? res.statusText;
            errors.classList.remove("d-none");
            errors.classList.add("d-flex");
        }
    }

    const back = () => {
        mfa_form.classList.add("d-none");
        login_form.classList.remove("d-none");
    }

    auth_form.addEventListener("submit", executeLogin);
</script>