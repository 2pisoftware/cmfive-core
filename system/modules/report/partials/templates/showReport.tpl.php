<?php if (!empty($form)) {
    echo $is_multicol_form ? Html::multiColForm($form, "/report/exereport/" . $report->id, "POST", "Download", "report_partial_form") : Html::form($form);
} else {
    echo Html::alertBox('No report form data was returned', 'error');
}
?>

<script>
    $("#report_partial_form").on('submit', function() {
        var action = $(this).attr('action');
        
        action += "?dt_start=" + $("#dt_start").val() + "&dt_end=" + $("#dt_end").val() + "&format=" + $("input[name='format']:checked").val()

        $(this).attr('action', action);
    })
</script>
