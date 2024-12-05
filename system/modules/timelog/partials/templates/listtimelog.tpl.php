
<div class="d-flex align-items-center justify-content-between">
    <?php echo HtmlBootstrap5::box("/timelog/edit?class={$class}&id={$id}" . (!empty($redirect) ? "&redirect=$redirect" : ''), "Add new timelog", true, false, null, null, "isbox", null, "bg-primary"); ?>
    <h4> <?php echo TaskService::getInstance($w)->getFormatPeriod($total); ?> </h4>
</div>

<?php
    $header = ["Name", "From", "To", "Duration", "Time Type", "Description", "Actions"];

    echo HtmlBootstrap5::table(array_map(function ($val) use ($w, $redirect) {
        $row = [
            $val->getFullName(),
            formatDate($val->dt_start, "d-m-Y H:i:s"),
            formatDate($val->dt_end, "d-m-Y H:i:s"),
            $val->isRunning ? "See Timer" : $val->getHoursWorked() . ':' . str_pad($val->getMinutesWorked(), 2, '0', STR_PAD_LEFT),
            $val->time_type,
            "<pre class='break-pre text-truncate d-block' style='width: 250px;'>" . strip_tags($val->getComment()->comment) . "</pre>",
        ];

        $actions = [];
        if ($val->canEdit(AuthService::getInstance($w)->user())) {
            $actions[] = HtmlBootstrap5::box('/timelog/edit/' . $val->id . (!empty($redirect) ? "?redirect=$redirect" : ''), 'Edit', true, false, null, null, "isbox", null, "bg-primary btn-sm");
            $actions[] = HtmlBootstrap5::box('/timelog/move/' . $val->id . (!empty($redirect) ? "?redirect=$redirect" : ''), 'Move', true, false, null, null, "isbox", null, "bg-primary btn-sm");
        }

        if ($val->canDelete(AuthService::getInstance($w)->user())) {
            $confirmation_message = implode("", $w->callHook("timelog", "before_display_timelog", $val));
            $actions[] = HtmlBootstrap5::b('/timelog/delete/' . $val->id . (!empty($redirect) ? "?redirect=$redirect" : ''), 'Delete', empty($confirmation_message) ? 'Are you sure you want to delete this timelog?' : $confirmation_message, null, false, "bg-danger btn-sm");
        }

        $row[] = HtmlBootstrap5::buttonGroup(implode("", $actions));

        return $row;
    }, $timelogs), null, "tablesorter", $header);
    ?>