<?php echo !empty($createfeed) ? $createfeed : ''; ?>
<?php echo !empty($feedurl) ? $feedurl : ''; ?>

<span id="feedtext"><?php echo !empty($feedtext) ? $feedtext : ''; ?></span>

<script language="javascript">
    $.ajaxSetup ({
        cache: false
    });

    var feed_url = "/report/feedAjaxGetReportText?id="; 
    $("select[id='rid'] option").click(function() {
        $.getJSON(feed_url + $(this).val(), function(result) {
            $('#feedtext').html(result);
        });
    });
</script>

