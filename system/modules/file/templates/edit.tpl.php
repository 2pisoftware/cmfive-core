<div id="app">
    <div class="card">
        <div class="card-header">
            <h3>Edit Attachment</h3>
        </div>
        <form id="attachmentForm" method="POST" class="card-body">
            <input type="hidden" value="<?php echo $id; ?>" name="id" />

            <div class="mb-3">
                <label for="file" class="form-label">File</label>
                <input type="file" class="form-control" name="file" id="file" />
            </div>
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control" name="title" value="<?php echo $title ?>" />
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <input type="text" class="form-control" name="description" value="<?php echo $description; ?>" />
            </div>


            <div id="new_attachments_restricted" class="d-none">
                <input class="form-check-input" type="checkbox" name="is_restricted" id="is_restricted" <?php echo $is_restricted === "true" ? "checked" : "" ?>>
                <label class="form-check-label d-inline" for="is_restricted">Limit who can view this attachment</label>

                <div id="new_attachments_viewers" class="pt-1 d-none"></div>

                <div class="mb-3 d-none" id="owner_select">
                    <label class="form-label" for="new_owner">Owner</label>
                    <select class="form-select" name="new_owner" id="owner">

                    </select>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>
</div>

<script>
    const can_restrict = <?php echo $can_restrict; ?>;
    const show_restrict = true;
    const viewers = <?php echo empty($viewers) ? "[]" : $viewers; ?>;
    const redirect_url = "<?php echo $redirect_url; ?>";
    const authed_user_id = <?php echo AuthService::getInstance($w)->user()->id; ?>;
    const file_dir = "<?php echo $file_directory; ?>";
    const file_name = "<?php echo $file_name ?>";

    if (file_dir && file_name) {
        // NOTE: currently, sending an empty file is no-op
        // so we can abuse this to get a nicer UI
        let file = new File([], file_name);
        let container = new DataTransfer();
        container.items.add(file);
        document.getElementById("file").files = container.files;
    }

    const owner_select = document.getElementById("owner");
    for (const viewer of viewers.filter(x => x.can_view)) {
        const option = document.createElement("option");
        option.value = viewer.id;
        option.innerText = viewer.name;
    }

    if (can_restrict && show_restrict) {
        document.getElementById("new_attachments_restricted").classList.remove("d-none")

        const onchange = async () => {
            const c1 = document.getElementById("new_attachments_viewers");
            const c2 = document.getElementById("owner_select");
            const box = document.getElementById("is_restricted");
            if (box.checked) {
                c1.classList.remove("d-none");
                c2.classList.remove("d-none");
            }
            else {
                c1.classList.add("d-none");
                c2.classList.add("d-none");
            }
        };

        onchange();
        document.getElementById("is_restricted").addEventListener("change", onchange)

        const container = document.getElementById("new_attachments_viewers");

        if (!viewers.length) {
            container.innerText = "You have permission to restrict viewers, but you're the only one who can view."
        }

        for (const viewer of viewers) {
            if (viewer.id === authed_user_id) continue;

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
            JSON.stringify(viewers.filter(x => formData.get(`viewers[${x.id}]`) === "on"))
        );

        const file = formData.get("file");
        formData.delete("file");

        const send = new FormData();
        const obj = {};
        formData.forEach((val, key) => obj[key] = val);
        send.append("file_data", JSON.stringify(obj));
        send.append("file", file);

        await fetch("/file-attachment/ajaxEditAttachment", {
            method: "POST",
            body: send,
        });

        window.history.go();
    })
</script>