<div id="app">
    <div class="card">
        <div class="card-header">
            <h3>New Attachment</h3>
        </div>
        <form id="attachmentForm" method="POST" class="card-body">
            <input type="hidden" value="<?php echo $class; ?>" name="class" />
            <input type="hidden" value="<?php echo $class_id; ?>" name="class_id" />

            <div class="mb-3">
                <label for="file" class="form-label">File</label>
                <input type="file" class="form-control" name="file" />
            </div>
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control" name="title" />
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <input type="text" class="form-control" name="description" />
            </div>

            <div id="new_attachments_restricted" class="d-none">
                <input class="form-check-input" type="checkbox" name="is_restricted">
                <label class="form-check-label d-inline" for="is_restricted">Limit who can view this comment</label>

                <div id="new_attachments_viewers" class="pt-1"></div>
            </div>

            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>
</div>

<script>
    const can_restrict = <?php echo $can_restrict; ?>;
    const show_restrict = true;
    const viewers = <?php echo empty($viewers) ? "[]" : $viewers; ?>;
    const object_class = "<?php echo $class; ?>";
    const object_id = "<?php echo $class_id; ?>";
    const redirect_url = "<?php echo $redirect_url; ?>";

    if (can_restrict && show_restrict) {
        document.getElementById("new_attachments_restricted").classList.remove("d-none")

        const container = document.getElementById("new_attachments_viewers");

        if (!viewers.length) {
            container.innerText = "You have permission to restrict viewers, but you're the only one who can view."
        }

        for (const viewer of viewers) {
            console.log(viewer);
            const div = document.createElement("div");
            div.classList.add("form-check");

            const input = document.createElement("input");
            input.classList.add("form-check-input");
            input.setAttribute("type", "checkbox");
            input.setAttribute("name", `viewers[${viewer.id}]`);
            div.appendChild(input)

            const label = document.createElement("label");
            label.innerText = viewer.name;
            label.classList.add("form-check-label");
            div.appendChild(label);

            container.appendChild(div);
        }
    }

    document.getElementById("attachmentForm").addEventListener("submit", async (e) => {
        e.preventDefault();
        // show the loading indicator
        document.getElementById("cmfive-overlay").style.display = "flex";

        const formData = new FormData(e.target);

        formData.set(
            "viewers",
            viewers.filter(x => formData.get(`viewers[${x.id}]`) === "on")
        );

        const file = formData.get("file");
        formData.delete("file");

        const send = new FormData();
        const obj = {};
        formData.forEach((val, key) => obj[key] = val);
        send.append("file_data", JSON.stringify(obj));
        send.append("file", file);

        await fetch("/file-attachment/ajaxAddAttachment", {
            method: "POST",
            body: send,
        });

        window.history.go();
    })
</script>