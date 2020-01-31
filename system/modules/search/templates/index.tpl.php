<div>
    <h3 class="subheading columns large-6">Search</h3>
    <span class="columns large-6" style="text-align: right;">
        <p style="font-size: 12px;">
        <strong>Note:</strong> Search terms must contain minimum 3 characters.
        <br>
        <strong>Tip:</strong> To search by Id, use 'id##' eg. id5.
        </p>
    </span>
</div>
<hr>
<div class="row-fluid">
<!--    <form action="<?php // echo $webroot; ?>/search/results" method="GET">-->
    <form id="search_form" class="clearfix">
        <input type="hidden" name="<?php echo CSRF::getTokenID(); ?>" value="<?php echo CSRF::getTokenValue(); ?>" />
        <div class="row-fluid">
            <div class="small-12 medium-6 columns">
                <input class="input-large" type="text" name="q" id="q" autofocus/>
            </div>
			<div class="small-12 medium-2 columns">
                <?php echo Html::select("idx", $indexes); ?>
            </div>
            <div class="small-12 medium-2 columns">
                <?php echo Html::select("tags", $tags); ?>
            </div>
            <div class="small-12 medium-2 columns">
                <button class="button tiny small-12" type="submit">Go</button>
            </div>
        </div>
    </form>


</div>

<div id="search_message" class="row hide">
    <div data-alert class="alert-box warning" id="message_box"></div>
</div>

<div id="result" class="row" style="display: none;">

</div>

<script>
    $("#search_form").submit(function(event) {
        event.preventDefault();
        $("#search_message").hide();
        $("#result").hide();

        var data = $("#search_form").serialize();

        $.getJSON("/search/results", data,
            function(response) {
                if (response.success === false) {
                    $("#message_box").html(response.data);
                    $("#search_message").show();
                } else {
                    var text_data = "<span style='padding-left: 20px;'>No results found</span>";
                    if (response.data) {
                        text_data = response.data;
                    }
                    $("#result").html(text_data).delay(100).fadeIn();
                }
            },
            function(response) {
                $("#message_box").html("Failed to receive a response from search");
                $("#search_message").show();
            }
        );

        return false;
    });

</script>
<br>
