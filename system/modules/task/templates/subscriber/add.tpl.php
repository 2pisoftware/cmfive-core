<?php echo $form; ?>

<style>
    #cmfive-modal .ts-control {
        padding: 10px;
        border: 1px solid var(--bs-body-color);
    }

    #cmfive-modal .panel {
        margin-top: 0;
    }

    #cmfive-modal .columns {
        padding: 0;
    } 
</style>

<script>
    document.getElementById("task-subscriber__add").addEventListener("submit", (e) => {
        const data = new FormData(e.target);

        if (data.get("contact")) return;    // default

        if (!data.get("firstname") || !data.get("lastname") || !data.get("email") || !data.get("work_number")) {
            alert("All fields are required when adding a new contact");
            e.preventDefault();
            return false;
        }

        return true;
    })
</script>
