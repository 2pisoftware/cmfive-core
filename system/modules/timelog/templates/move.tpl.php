<form action="/timelog/move/<?php echo $timelog->id; ?><?php echo $redirect ? "?redirect=" . $redirect : ""; ?>"
    method="POST" name="timelog_move_form" target="_self" id="timelog_move_form" class=" small-12 columns">
    <div class="row-fluid clearfix small-12 multicolform">

        <div class="panel clearfix">
            <div class="small-12 columns section-header">
                <h4>Move timelog</h4>
            </div>
            <ul class="small-block-grid-1 medium-block-grid-2 section-body">
                <li>
                    <label class="small-12 columns">Module
                        <?php echo (new \Html\Form\Select([
                            "id|name"            => "object_class",
                            "selected_option"    => $timelog->object_class ?: $tracking_class ?: (empty($select_indexes) ? null : $select_indexes[0][1]),
                            "options"            => $select_indexes
                        ])); ?>
                    </label>
                </li>
                <li>
                    <label class="small-12 columns">Search
                        <small>Required</small>
                        <?php
                        $usable_class = !empty($timelog->object_class) ? $timelog->object_class : (!empty($tracking_class) ? $tracking_class : (empty($select_indexes) ? null : $select_indexes[0][1]));
$where_clause = [];
if (!empty($usable_class)) {
    if (in_array("is_deleted", (new $usable_class($w))->getDbTableColumnNames())) {
        $where["is_deleted"] = 0;
    }
}

echo (new \Html\Form\Autocomplete([
    "id|name"       => "search",
    "title"            => !empty($object) ? $object->getSelectOptionTitle() : null,
    "value"            => !empty($timelog->object_id) ? $timelog->object_id : $tracking_id,
    "required"        => "true"
]))->setOptions(!empty($usable_class) ? TimelogService::getInstance($w)->getObjects($usable_class, $where) : ""); ?>
                    </label>
                </li>
                <?php echo (new \Html\Form\InputField(["type" => "hidden", "id|name" => "object_id", "value" => $timelog->object_id ?: $tracking_id])); ?>
            </ul>
            <ul class="small-block-grid-1 medium-block-grid-1 section-body">
                <li>
                    <label class="small-12 columns">Description
                        <?php echo (new \Html\Form\Textarea([
                            "id|name"        => "description",
                            "value"            => !empty($timelog->id) ? $timelog->getComment()->comment : null,
                            "rows"            => 8
                        ])); ?>
                    </label>
                </li>
            </ul>
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
                    <div class="small-12 columns">
                        <button class="button small">Save</button>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</form>
<script type="text/javascript">
    // Input values are module, search and description
    $(document).ready(function() {
        // If there is no task group selected, we disable submit
        if ($("#object_id").val() == "") {
            $(".savebutton").prop("disabled", true);
            $("#acp_search").attr("readonly", "true");
        }
        var searchBaseUrl = '/timelog/ajaxSearch';

        // If there is already a value in #object_class, that is, we are
        // editing, then set the searchURL
        var searchUrl = "";
        if ($("#object_class").val !== "") {
            $("#acp_search").removeAttr("readonly");
            searchUrl = searchBaseUrl + "?index=" + $("#object_class").val();
        }
        $("#object_class").change(function() {
            console.log("object class changed");
            $("#acp_search").val("");
            $("#timelog_move_form .panel + .panel").remove();
            if ($(this).val() !== "") {
                $("#acp_search").removeAttr("readonly");
                searchUrl = searchBaseUrl + "?index=" + $(this).val();
            } else {
                // This fails with unknown page...
                $("#acp_search").attr("readonly", "true");
                searchUrl = searchBaseUrl;
            }
        });

        $("#acp_search").autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: searchUrl + "&term=" + request.term,
                    success: function(result) {
                        response(JSON.parse(result));
                    }
                });
            },
            // When the have selected a search value then do the ajax call
            select: function(event, ui) {
                $("#object_id").val(ui.item.id);
                // Task is chosen, allow submit
                $(".savebutton").prop("disabled", false);
                $("#timelog_move_form .panel + .panel").remove();
                $.get("/timelog/ajaxGetExtraData/" + $("#object_class").val() + "/" + $("#object_id").val())
                    .done(function(response) {
                        if (response != "") {
                            var append_panel = "<div class='panel'><div class='row-fluid section-header'><h4>Additional Fields" + $("#object_class").val() +
                                "</h4></div><ul class='small-block-grid-1 medium-block-grid-1 section-body'><li>" + response + "</li></ul></div>";
                            $("#timelog_move_form .panel").after(append_panel);
                        }
                    });

            },
            minLength: 3
        });

        $("#timelogForm").on("submit", function() {
            $.ajax({
                url: "/timelog/ajaxStart",
                method: "POST",
                data: {
                    "object": $("#object_class").val(),
                    "object_id": $("#object_id").val(),
                    "description": $("#description").val()
                },
                success: function(result) {
                    alert(result);
                }
            });
            return false;
        });

        // Need to simulate change to module type to set url

    });
</script>
