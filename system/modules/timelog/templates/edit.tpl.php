<!--
I did rewrite this into a HtmlBootstrap5::multiColForm,
but it turns out that function doesn't support having multiple elements within a column,
so I had to scrap it. How fun!
-->

<h1><?php

use Html\Cmfive\QuillEditor;
use Html\Form\Html5Autocomplete;
use Html\Form\InputField;
use Html\Form\InputField\Date;
use Html\Form\InputField\Hidden;
use Html\Form\InputField\Number;
use Html\Form\InputField\Radio;
use Html\Form\InputField\Time;
use Html\Form\Select;
use Html\Form\Textarea;

 echo !empty($timelog->id) ? "Update" : "Create" ?> Timelog</h1>
<form
    action="/timelog/edit/<?php echo ($timelog->id ?? '') . ($redirect ? "?redirect={$redirect}" : '') ?>"
    method="POST"
    name="timelog_edit_form"
    target="_self"
    id="timelog_edit_form"
>
    <div class="mb-3">
        <label for="user_id" class="form-label m-0">
            Assigned user
            <small>Required</small>
        </label>
        <?php
        if (AuthService::getInstance($w)->user()->is_admin && !empty($options)) {
            echo (new Select([
                "id|name" => "user_id",
                "class" => "form-select",
                "required" => true,
                "options" => $options
            ]))->setSelectedOption(empty($timelog->id)
            ? AuthService::getInstance($w)->user()->id
            : (!empty($timelog->user_id) ? $timelog->user_id : null));
        } else {
            echo new Hidden([
                "name" => "user_id",
                "value" => empty($timelog->id) ? AuthService::getInstance($w)->user()->id : $timelog->user_id
            ]);
        }
        ?>
    </div> <!-- group 1 assigned user -->

    <div class="mb-3 row">
        <div class="col">
            <label for="object_class" class="form-label">
                Module
                <small>Required</small>
            </label>
            <?php
            echo new Select([
                "id|name" => "object_class",
                "class" => "form-select",
                "options" => $select_indexes,
                "selected_option" => $timelog->object_class
                    ?: $tracking_class
                    ?: (empty($select_indexes ? null : $select_indexes[0][1]))
            ]);
            ?>
        </div> <!-- module select -->

        <div class="col">
            <label for="search" class="form-label">
                Search
                <small>Required</small>
            </label>
            <?php
            $usable_class = !empty($timelog->object_class) ? $timelog->object_class : (!empty($tracking_class) ? $tracking_class : (empty($select_indexes) ? null : $select_indexes[0][1]));
            $where_clause = [];
            if (!empty($usable_class))
            {
                if (in_array('is_deleted', (new $usable_class($w))->getDbTableColumnNames()))
                {
                    $where['is_deleted'] = 0;
                }
            }

            echo new Html5Autocomplete([
                "id|name" => "object_id",
                "class" => "form-control",
                "title" => !empty($object) ? $object->getSelectOptionTitle() : null,
                "value" => $timelog->object_id ?: $tracking_id,
                "required" => "required",
                "source" => $w->localUrl("/timelog/ajaxSearch?index={$timelog->object_class}"),
                "options" => !empty($usable_class) ? TimelogService::getInstance($w)->getObjects($usable_class, $where) : '',
                "maxItems" => 1,
            ]);
            ?>
        </div> <!-- task search -->
    </div> <!-- end row -->

    <div class="mb-3 row">
        <div class="col">
            <label for="date_start" class="form-label">
                Date
                <small>Required</small>
            </label>
            <?php
            echo new Date([
                "id|name" => "date_start",
                "class" => "form-control",
                "value" => $timelog->getDateStart(),
                "required" => true
            ])
            ?>
        </div> <!-- date_start -->

        <div class="col">
        <label for="date_start" class="form-label">
                Time started
                <small>Required</small>
            </label>
            <?php
            echo new Time([
                "id|name" => "time_start",
                "class" => "form-control",
                "value" => $timelog->getTimeStart(),
                "pattern"        => "^(0?[0-9]|1[0-9]|2[0-3]):[0-5][0-9](\s+)?(AM|PM|am|pm)?$",
                "placeholder"    => "12hr format: 11:30pm or 24hr format: 23:30",
                "required" => true
            ])
            ?>
        </div> <!-- time start -->
    </div> <!-- start value row -->

    <?php if (!$timelog->isRunning()) : ?>
        <div class="mb-3 row">
            <div class="col">
                <span class="form-check d-inline ps-0">
                    <?php
                        echo new Radio([
                            "id" => "select_end_method_time",
                            "name" => "select_end_method",
                            "class" => "",
                            "select" => "form-check-input",
                            "value"        => "time",
                            "checked"    => "true",
                            "tabindex"    => -1
                        ]);
                    ?>
                </span>

                <span class="d-inline">
                    <label class="form-label d-inline-flex" for="time_end">
                        End time
                    </label>
                    <?php
                    echo new Time([
                        "id|name" => "time_end",
                        "class" => "form-control",
                        "value"            => $timelog->getTimeEnd(),
                        "pattern"        => "^(0?[0-9]|1[0-9]|2[0-3]):[0-5][0-9](\s+)?(AM|PM|am|pm)?$",
                        "placeholder"    => "12hr format: 11:30pm or 24hr format: 23:30",
                        "required"        => "true"
                    ])
                    ?>
                </span>
            </div> <!-- time_end -->

            <div class="col">
                <span class="form-check d-inline ps-0">
                    <?php
                        echo new Radio([
                            "id" => "select_end_method_hours",
                            "name" => "select_end_method",
                            "class" => "",
                            "select" => "form-check-input",
                            "value"        => "hours",
                            "tabindex"    => -1
                        ]);
                    ?>
                </span>

                <span class="d-inline">
                    <label class="form-label d-inline-flex" for="hours_worked">
                        Hours/Minutes worked
                    </label>
                    <div class="d-flex gap-2">
                        <?php
                        echo new Number([
                            "id|name" => "hours_worked",
                            "class" => "form-control",
                            "value" => $timelog->getHoursWorked(),
                            "min" => 0,
                            "max" => 23,
                            "step" => 1,
                            "placeholder" => "Hours: 0-23",
                            "disabled" => "true"
                        ]);

                        echo new Number([
                            "id|name" => "minutes_worked",
                            "class" => "form-control",
                            "value" => $timelog->getMinutesWorked(),
                            "min" => 0,
                            "max" => 59,
                            "step" => 1,
                            "placeholder" => "Mins: 0-59",
                            "disabled" => "true"
                        ])
                        ?>
                    </div>
                </span>
            </div> <!-- hours/minutes -->
        </div> <!-- end time inputs -->

        <div class="mb-3 row">
            <div class="col">
                <label for="description" class="form-label">Description</label>
                <?php
                echo new QuillEditor([
                    "id|name" => "description",
                    "class" => "form-control",
                    "value" => !empty($timelog->id) ? $timelog->getComment()->comment : null,
                    "rows" => 8,
                ])
                ?>
            </div>
        </div>
    <?php endif; ?> <!-- timelog isrunning -->

    <?php if (!empty($form)) : ?>
        <?php foreach ($form as $form_heading => $form_array) : ?>
            <div class="mb-3 row">
                <h2><?php echo $form_heading ?></h2>
                <?php foreach ($form_array as $form_element_key => $form_elements) : ?>
                    <?php foreach ($form_elements as $form_element) : ?>
                        <div class="col">
                            <label for="<?php echo $form_element->id ?>"><?php echo $form_element->label ?></label>
                            <?php echo $form_element ?>
                        </div>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?> <!-- additional fields -->

    <button type="submit" class="btn btn-primary ms-0">Submit</button>
</form>

<script>
    const handleRadioChange = (e) => {
        const value = e.target.value;

        const time_end = document.getElementById("time_end");
        const hours_worked = document.getElementById("hours_worked");
        const minutes_worked = document.getElementById("minutes_worked");

        if (value === "time") {
            hours_worked.setAttribute("disabled", "disabled");
            minutes_worked.setAttribute("disabled", "disabled");
            time_end.removeAttribute("disabled");
        }
        else {
            hours_worked.removeAttribute("disabled");
            minutes_worked.removeAttribute("disabled");
            time_end.setAttribute("disabled", "disabled");
        }
    }

    document.getElementById("select_end_method_hours").addEventListener("change", handleRadioChange);
    document.getElementById("select_end_method_time").addEventListener("change", handleRadioChange);
</script>