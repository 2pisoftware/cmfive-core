<div id="timelog_widget">
    <?php if (TimelogService::getInstance($w)->hasTrackingObject()) : ?>
        <span id="timelog_widget_start" data-modal-target="/timelog/starttimer" class="btn btn-primary">Start Timer</span>
    <?php endif; ?>

    <span
        class="btn btn-primary"
        style="display: none"
        id="timelog_widget_stop"
        data-bs-toggle="tooltip"
        data-bs-placement="bottom"
        title="<?php echo !empty($active_log)
                    ? $active_log->object_class . ": " . $active_log->getLinkedObject()->getSelectOptionTitle()
                    : '';
                ?>">
    </span>
</div>

<style>
    #timelog_widget_stop::after {
        content: attr(data-time);
    }

    #timelog_widget_stop:hover::after {
        content: "Stop Timer";
    }
</style>

<script>
    const start = document.getElementById("timelog_widget_start");
    const stop = document.getElementById("timelog_widget_stop");

    let start_time = <?php echo (!empty($active_log) && $active_log->dt_start)
                            ? $active_log->dt_start
                            : "false"; ?>;

    const updateClock = () => {
        var t = (new Date().getTime() / 1000) - start_time;
        var hours = Math.floor(t / 3600),
            minutes = Math.floor(t / 60 % 60),
            seconds = Math.floor(t % 60),
            arr = [];

        arr.push(hours < 10 ? '0' + hours : hours);
        arr.push(minutes < 10 ? '0' + minutes : minutes);
        arr.push(seconds < 10 ? '0' + seconds : seconds);
        stop.setAttribute("data-time", arr.join(":"));
    }

    let interval = null;
    const startTimerDisplay = () => {
        if (start_time) {
            if (start) start.style.display = "none";
            stop.style.display = "block";

            updateClock();
            interval = setInterval(updateClock, 1000);
        }
    }

    startTimerDisplay();

    var track = JSON.parse(
        <?php echo TimelogService::getInstance($w)->hasTrackingObject()
            ? json_encode(TimelogService::getInstance($w)->getJSTrackingObject())
            : '"{}"';
        ?>
    );

    const hideModal = () => {
        [...document.getElementsByClassName("modal-backdrop")].forEach(e => {
            e.remove();
        });

        document.getElementById("cmfive-modal").style.display = "none";
        document.getElementById("cmfive-modal").classList.remove("show");
    }

    document.getElementById("timelog_widget_stop").addEventListener("click", async () => {
        await fetch("/timelog/ajaxStop");
        clearInterval(interval);
        if (start) start.style.display = "block";
        stop.style.display = "none";

    })

    window.timelog_startTimer = async () => {
        const description = document.getElementById("quill_Description").innerHTML;
        const time = document.getElementById("start_time").value;

        const body = new FormData();
        body.append("start_time", time);
        body.append("description", description);

        const json = await fetch(
                `/timelog/ajaxStart/${track.class}/${track.id}`, {
                    body,
                    method: "POST",
                }
            )
            .then(x => x.json());

        start_time = json.start_time ?? (Date.now() / 1000);
        startTimerDisplay();

        hideModal();
    }
</script>