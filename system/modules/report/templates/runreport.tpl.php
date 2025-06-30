<div style="width:400px">
    <?php echo (!empty($rep)) ? StringSanitiser::stripTags($rep->description) : "No Report for user"; ?>
    <?php echo (!empty($report)) ? $report : "" ?>
</div>


<script language="javascript">
    (function() {
        const format = document.getElementById("format")
        if (format) {
            format.value = "html";
        }
    })();
</script>