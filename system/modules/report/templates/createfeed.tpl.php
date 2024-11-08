<?php echo !empty($createfeed) ? $createfeed : ''; ?>
<?php echo !empty($feedurl) ? $feedurl : ''; ?>

<span id="feedtext"><?php echo !empty($feedtext) ? $feedtext : ''; ?></span>

<script>
    const feed_url = "/report/feedAjaxGetReportText?id="; 

    document.getElementById("rid").addEventListener("change", async e => {
        const res = await fetch(`${feed_url}${e.target.value}`);
        const body = await res.text();
        document.getElementById("feedtext").innerHTML = body.replaceAll("\"", "").replaceAll("\\", "");
    });
</script>

