<?php

class HtmlBootstrap5 extends Html
{

    public static function b($href, $title, $confirm = null, $id = null, $newtab = false, $class = null, $type = null, $name = null)
    {
        $button = new \Html\button();
        $button->_class = 'btn';
        $button->href($href)->text($title)->confirm($confirm)->id($id)->setClass($class)->newtab($newtab)->type($type)->name($name);
        return $button->__toString();
    }

    public static function box($href, $title, $button = false, $iframe = false, $width = null, $height = null, $param = "isbox", $id = null, $class = null, $confirm = null, $modal_window_id = 'cmfive-modal')
    {
        $element = null;
        if ($button) {
            $element = new \Html\button();
            $element->_class = 'btn';
        } else {
            $element = new \Html\a();
        }
        $element->id($id)->setClass($class)->setAttribute('data-modal-target', $href)->text($title);
        if (!empty($confirm)) {
            $element->setAttribute('data-modal-confirm', $confirm);
        }
        return $element->__toString();
    }

    /**
     * Creates a complex form where each section can have
     * a different number of columns.
     *
     * extrabuttons = array("id"=>"title", ..)
     *
     * valid field types are:
     *  text, password, autocomplete, static, date, textarea, section,
     *  select, multiselect, checkbox, hidden
     *
     * when prefixing a fieldname with a minus sign '-' this field will be read-only
     *
     * @param <type> $data
     * @param <type> $action
     * @param <type> $method
     * @param <type> $submitTitle
     * @param <type> $id
     * @param <type> $class
     * @param <type> $extrabuttons
     * @return <type>
     */
    public static function multiColForm($data, $action = null, $method = "POST", $submitTitle = "Save", $id = null, $class = null, $extrabuttons = null, $target = "_self", $includeFormTag = true, $validation = null)
    {
        if (empty($data)) {
            return;
        }

        $buffer = "";
        $form = new \Html\FormBootstrap5();

        // If form tag is needed print it
        if ($includeFormTag) {
            $class .= " col";
            $form->id($id)->name($id)->setClass($class)->method($method)->action($action)->target($target);

            if (in_multiarray("file", $data) || objectPropertyHasValueInMultiArray("\Html\Form\InputField", "type", "file", $data)) {
                $form->enctype("multipart/form-data");
            }

            $buffer .= $form->open();
        }

        // Set up shell layout
        $buffer .= "<div class='row clearfix multicolform'>";

        // Print internals
        foreach ($data as $section => $rows) {
            // Print section header
            $buffer .= "<div class='panel clearfix'>";
            $buffer .= "<div class='row g-0 clearfix section-header'><h4 class='col'>{$section}<span style='display: none;' class='changed_status right alert radius label'>changed</span></h4></div>";

            // Loop through each row
            foreach ($rows as $row) {
                // Print each field
                $fieldCount = is_array($row) ? count($row) : 1;
                $buffer .= "<div class='row'>";

                if (empty($row)) {
                    continue;
                }

                foreach ($row as $entry) {
                    // Backwards compatibility - provide option to pass additional data
                    $field = null;
                    $tooltip = null;
                    if (is_object($entry)) {
                        $field = property_exists($entry, 'field') ? $entry->field : $entry;
                        $tooltip = property_exists($entry, 'tooltip') ? $entry->tooltip : null;
                    } else {
                        $field = array_key_exists('field', $entry) ? $entry['field'] : $entry;
                        $tooltip = array_key_exists('tooltip', $entry) ? $entry['tooltip'] : null;
                    }

                    // Check if the row is an object like an InputField
                    if (!is_array($field) && is_object($field)) {
                        if ((property_exists($field, "type") && $field->type !== "hidden") || !property_exists($field, "type")) {
                            $buffer .= '<div class="col"><label class="form-label"' . (property_exists($field, 'id') && !empty($field->id) ? ' for="' . $field->id . '"' : '' ) . '>' . $field->label . ($field->required ? " <small>Required</small>" : "") . "</label>"
                            . $field->__toString() . '</div>';
                        } else {
                            $buffer .= $field->__toString();
                        }
                        continue;
                    }

                    $title = !empty($field[0]) ? $field[0] : null;
                    $type = !empty($field[1]) ? $field[1] : null;
                    $name = !empty($field[2]) ? $field[2] : null;
                    $value = !empty($field[3]) ? $field[3] : null;

                    // Exploit HTML5s inbuilt form validation
                    $required = null;
                    if (!empty($validation[$name])) {
                        if (in_array("required", $validation[$name])) {
                            $required = "required";
                            $title .= ' <small>Required</small>';
                        }
                    }

                    $readonly = "";

                    $buffer .= ($type !== "hidden" ? "<div class='col'>" : "");

                    // Add title field
                    if (!empty($title) && $type !== "hidden") {
                        $buffer .= "<label class='form-label'>$title</div>";
                        if (!empty($tooltip)) {
                            $buffer .= " <span data-tooltip aria-haspopup='true' class='has-tip fi-info' title='" . $tooltip . "'></span>";
                        }
                    }
                    // $buffer .= ($type !== "hidden" ? "<div>" : "");

                    // handle disabled fields
                    if (substr($name, 0, 1) == '-') {
                        $name = substr($name, 1);
                        $readonly = " readonly='true' ";
                    }

                    switch ($type) {
                        case "text":
                        case "password":
                        case "email":
                        case "tel":
                            $size = !empty($field[4]) ? $field[4] : null;
                            $buffer .= '<input' . $readonly . ' class="form-control" type="' . $type . '" name="' . $name . '" value="' . htmlspecialchars($value) .
                            '" size="' . $size . '" id="' . $name . '" ' . $required . " />";
                            break;
                        case "autocomplete":
                            $options = !empty($field[4]) ? $field[4] : null;
                            $minValue = !empty($field[5]) ? $field[5] : 1;
                            $buffer .= Html::autocomplete($name, $options, $value, "form-control", "width: 100%;", $minValue, $required);
                            break;
                        case "date":
                            $size = !empty($field[4]) ? $field[4] : null;
                            $buffer .= Html::datePicker($name, $value, $size, $required);
                            break;
                        case "datetime":
                            $size = !empty($field[4]) ? $field[4] : null;
                            $buffer .= Html::datetimePicker($name, $value, $size, $required);
                            break;
                        case "time":
                            $size = !empty($field[4]) ? $field[4] : null;
                            $buffer .= Html::timePicker($name, $value, $size, $required);
                            break;
                        case "static":
                            $size = !empty($field[4]) ? $field[4] : null;
                            $buffer .= $value;
                            break;
                        case "textarea":
                            $c = !empty($field[4]) ? $field[4] : null;
                            $r = !empty($field[5]) ? $field[5] : null;
                            $custom_class = true;
                            if (isset($field[6])) {
                                $custom_class = $field[6];
                            }
                            $buffer .= '<textarea' . $readonly . ' class="form-control" style="width:100%; height: auto; " name="' . $name . '" rows="' . $r . '" cols="' . $c .
                            '" ' . (!empty($custom_class) ? ($custom_class === true ? "class='ckeditor'" : "class='$custom_class' ") : '') . ' id="' . $name
                            . '" ' . $required . '>' . $value . '</textarea>';
                            break;
                        case "select":
                            $items = !empty($field[4]) ? $field[4] : null;

                            $default = !empty($field[5]) ? ($field[5] == "null" ? null : $field[5]) : "-- Select --";
                            $sl_class = !empty($field[6]) ? $field[6] : null;
                            $buffer .= Html::select($name, $items, $value, $sl_class, "width: 100%;", $default, ($readonly ? ' disabled="disabled" ' : null) . ' ' . $required);
                            break;
                        case "multiSelect":
                            $items = !empty($field[4]) ? $field[4] : null;
                            if ($readonly == "") {
                                $buffer .= Html::multiSelect($name, $items, $value, null, "width: 100%;", $required);
                            } else {
                                $buffer .= $value;
                            }
                            break;
                        case "checkbox":
                            $defaultValue = !empty($field[4]) ? $field[4] : null;
                            $cb_class = !empty($field[5]) ? $field[5] : null;
                            $buffer .= Html::checkbox($name, $value, $defaultValue, $cb_class);
                            break;
                        case "radio":
                            $group = !empty($field[4]) ? $field[4] : null;
                            $defaultValue = !empty($field[5]) ? $field[5] : null;
                            $rd_class = !empty($field[6]) ? $field[6] : null;
                            $buffer .= Html::radio($name, $group, $value, $defaultValue, $rd_class) . "&nbsp;" . htmlentities($title);
                            break;
                        case "hidden":
                            $buffer .= '<input type="hidden" name="' . $name . '" value="' . htmlspecialchars($value) . '" id="' . $name . '"/>';
                            break;
                        case "file":
                            $size = !empty($row[4]) ? $row[4] : null;
                            $buffer .= '<input style="width:100%;"  type="' . $type . '" name="' . $name . '" size="' . $size . '" id="' . $name . '"/>';
                            break;
                        case "multifile":
                            $buffer .= Html::multiFileUpload($name);
                            break;
                    }
                    $buffer .= ($type !== "hidden" ? "</div>" : "");
                }
                $buffer .= "</div>";
            }
            $buffer .= "</div>";
        }
        // $buffer .= "<script>$(function(){try{\$('.ckeditor').each(function(){CKEDITOR.replace(this)})}catch(err){}});</script>";
        // $buffer .= "<script>$(function(){try{\$('.codemirror').each(function(){var editor = CodeMirror.fromTextArea($(this), {lineNumbers: true, mode: 'text/html', matchBrackets: true, viewportMargin: Infinity}); editor.refresh()})}catch(err){}});</script>";

        // Expermiental
        if (strpos($class, "prompt") !== false) {
            // @todo: move this to cmfive ts

            // $buffer .= "<script>"
            //     . "(function() {"
            //     . "		var confirmOnPageExit = function (e) {
            //                     console.log(e);
            //                     // If we haven't been passed the event get the window.event
            //                     e = e || window.event;

            //                     var message = 'You have unsaved changes, are you sure you want to navigate away?';

            //                     // For IE6-8 and Firefox prior to version 4
            //                     if (e) {
            //                         e.returnValue = message;
            //                     }

            //                     // For Chrome, Safari, IE8+ and Opera 12+
            //                     return message;
            //                 };"
            //     . "		$('form.prompt :input').unbind('input');"
            //     . "		$('form.prompt :input').on('input', function() {"
            //     . "			window.onbeforeunload = confirmOnPageExit;"
            //     . "			$(this).closest('form').find('.section-header h4 > .changed_status').show();"
            //     . "		});"
            //     . "})();"
            //     . "</script>";
        }

        // Finish shell div tag
        $buffer .= "</div>";

        // Close form tag if needed
        if ($includeFormTag) {
            $buffer .= $form->close($submitTitle, $extrabuttons);
        }

        return $buffer;
    }
}
