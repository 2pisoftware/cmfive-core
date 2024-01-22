<!-- <form action="/timelog/move/<?php echo $timelog->id; ?><?php echo $redirect ? "?redirect=" . $redirect : ""; ?>" method="POST" name="timelog_move_form" target="_self" id="timelog_move_form" class=" small-12 columns">
    <div class="">

        <div class="">
            <div class="small-12 section-header row">
                <h4>Move timelog</h4>
            </div>
            <div class="small-block-grid-1 medium-block-grid-2 section-body row">
                <div class="col">
                    <label class="small-12 form-label" for="task_select">Module</label>
                    <?php echo (new \Html\Form\Select([
                        "id|name"            => "object_class",
                        "selected_option"    => $timelog->object_class ?: $tracking_class ?: (empty($select_indexes) ? null : $select_indexes[0][1]),
                        "options"            => $select_indexes,
                        "class"               => "form-select",
                    ])); ?>

                </div>
                <div class="col">
                    <label class="small-12 form-label" for="search">Search</label>
                    <?php
                    $usable_class = !empty($timelog->object_class) ? $timelog->object_class : (!empty($tracking_class) ? $tracking_class : (empty($select_indexes) ? null : $select_indexes[0][1]));
                    $where_clause = [];
                    if (!empty($usable_class)) {
                        if (in_array("is_deleted", (new $usable_class($w))->getDbTableColumnNames())) {
                            $where["is_deleted"] = 0;
                        }
                    }

                    echo (new \Html\Cmfive\Autocomplete([
                        "id"       => "acp_search",
                        "title"            => !empty($object) ? $object->getSelectOptionTitle() : null,
                        "value"            => !empty($timelog->object_id) ? $timelog->object_id : $tracking_id,
                        "required"        => "true",
                        "class"            => "form-input ",
                    ]))->setOptions(!empty($usable_class) ? TimelogService::getInstance($w)->getObjects($usable_class, $where) : ""); ?>
                    <small>Required</small>

                </div>
            </div>
            <div class="row">
                <?php echo (new \Html\Form\InputField(["type" => "hidden", "id|name" => "object_id", "value" => $timelog->object_id ?: $tracking_id])); ?>
            </div>
            <?php if (!empty($form)) : ?>
                <?php foreach ($form as $form_section_heading => $form_array) : ?>
                    <?php foreach ($form_array as $form_element_key => $form_elements) : ?>
                        <?php foreach ($form_elements as $form_element) : ?>
                            <ul class="small-block-grid-1 medium-block-grid-1 section-body">
                                <li>
                                    <label class="small-12 columns"><?php echo $form_element->label; ?>
                                        <?php echo $form_element; ?>
                                    </label>
                                </li>
                            </ul>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            <?php endif; ?>
            <ul class="small-block-grid-1 medium-block-grid-1 section-body">
                <li>
                    <div >
                        <button class="button small">Save</button>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</form> -->

<?php echo $form ?>
<script type="text/javascript">
    // Input values are module and search
    const search = document.getElementById('object_class')
    search.addEventListener('change', () => {
        console.log("Object class changed")
        // change the autocomplete URL here
    })

    document.addEventListener('DOMContentLoaded', () => {
        // If there is no task group selected, we disable submit
        if (document.getElementById('object_id').value == "") {
            document.getElementsByClassName('savebutton')[0].disabled = true
            document.getElementById('search').setAttribute('readonly', 'true')
        }
        var searchBaseUrl = "/timelog/ajaxSearch";

        // If there is already a value in #object_class, that is, we are
        // editing, then set the searchURL
        var searchUrl = "";
        if (document.getElementById('object_class').value !== "") {
            document.getElementsById('search').setAttribute('readonly', 'false')
            searchUrl = searchBaseUrl + "?index=" + document.getElementById('object_class').value;
        }

        document.getElementById('object_class').addEventListener('change', () => {
            console.log("Object class changed")

            //clear the search bar
            document.getElementById('search').value = ''

            var panels = document.querySelectorAll('#timelog_move_form .panel + .panel');
            panels.forEach((panel) => {
                panel.remove()
            })

            if (this.value !== "") {
                document.getElementById('search').removeAttribute('readonly')
                searchUrl = searchBaseUrl + '?index=' + this.value
            } else {
                // This fails with unknown page...
                document.getElementById("search").setAttribute('readonly', 'true')
                searchUrl = searchBaseUrl
            }
        })

        //rewrite to contain logic for autocomplete
        document.getElementById('search').addEventListener('change', () => {
            var acpUrl = searchUrl + "&term=" + this.value

            fetch(acpUrl)
                .then((response) => {
                    console.log(response.json());
                    return response.json()
                }).then((result) => {
                    let select = document.getElementById("search")
                    let control = select.tomselect
                    control.clearOptions()
                    //add new options from result
                })
        })
    });
    
</script>