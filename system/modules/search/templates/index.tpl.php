<div class="container">
    <div class="row pe-4">
        <h3 class="col-md-6">Search</h3>
        <div class="col-md-6 text-end">
            <p class="small mb-0">
                <span class="fw-bold">Note:</span> Search terms must contain minimum 3 characters.
            </p>
            <p class="small">
                <span class="fw-bold">Tip:</span> To search by Id, use 'id##' eg. id5.
            </p>
        </div>
    </div>
    <div class=""></div>
    <div class="row border-top pt-3">
        <form id="search_form" class="clearfix">
            <input type="hidden" name="<?php echo CSRF::getTokenID(); ?>" value="<?php echo CSRF::getTokenValue(); ?>" />
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="q" class="form-label mt-0">Query</label>
                    <input class="form-control" type="text" name="q" id="q" autofocus />
                </div>
                <div class="col-md-2 mb-3">
                    <label for="idx" class="form-label mt-0">Index</label>
                    <?php
                    use Html\Form\Select;

                    echo new Select([
                            "id|name" => "idx",
                            "class" => "form-select",
                            "options" => $indexes
                    ]);
                    ?>
                </div>
                <div class="col-md-2 mb-3">
                    <label for="tags" class="form-label mt-0">Tag</label>
                    <?php
                    echo new Select([
                            "id|name" => "tags",
                            "class" => "form-select",
                            "options" => $tags
                    ]);
                    ?>
                </div>
                <div class="col-md-2 mb-3 mt-4">
                    <button class="btn btn-primary w-100" type="submit">Go</button>
                </div>
            </div>
        </form>
    </div>

    <div id="search_message" class="row">
        <div class="alert alert-warning mt-3" id="message_box" style="display: none;"></div>
    </div>

    <div id="result" class="row" style="display: none;"></div>
</div>

<script>
    const setError = (str) => {
        if (!str) {
            document.querySelector("#search_message").style.display = "none";
            document.querySelector("#result").style.display = "block";
            return;
        }

        document.querySelector("#message_box").innerText = str;
        document.querySelector("#message_box").style.display = "block";
        document.querySelector("#result").style.display = "none";
    }

    document.querySelector("#search_form").addEventListener("submit", async function(event) {
        event.preventDefault();

        setError(false);

        const form = new FormData(event.target);
        const body = new URLSearchParams(form);

        try {
            const response = await fetch(`/search/results?` + body.toString());

            const json = await response.json();

            if (!json.success)
                return setError(json.data);

            document.querySelector("#result").innerHTML =
                json.data || `<span class="ps-3">No results found</span>`;
        } catch (e) {
            setError(`Failed to receive a response from search`);
        }

        return false;
    });
</script>
<br>
