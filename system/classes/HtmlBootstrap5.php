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
        $element->id($id)->setClass($class)->setAttribute('data-modal-target', $href)->text($title)->setAttribute("role", "button");
        if (!empty($confirm)) {
            $element->setAttribute('data-modal-confirm', $confirm);
        }
        return $element->__toString();
    }

    public static function buttonGroup(string $content): string
    {
        return '<div class="btn-group btn-group-sm" role="group">' . $content . '</div>';
    }

    public static function dropdownButton($title, $contents, $class)
    {
        $content = '';
        foreach ($contents as $item) {
            $content .= '<li>' . $item . '</li>';
        }

        return '<div class="dropdown">
            <button class="' . $class . ' dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">'
            . $title .
            '</button>
            <ul class="dropdown-menu">'
            . $content .
            '</ul>
        </div>';
    }

/**
     * creates a simple one column form from the following array:
     * array(
     *       array("title","type","fieldname","value",{size | array(select options) | cols, rows}),
     *       ...
     * )
     *
     * valid field types are:
     *  text, password, autocomplete, static, date, textarea, section,
     *  select, multiselect, checkbox, hidden
     *
     * Field type auto uses ui hints from a DbObject.
     *
     * when prefixing a fieldname with a minus sign '-' this field will be read-only
     */
    public static function form($data, $action = null, $method = "POST", $submitTitle = "Save", $id = null, $class = null, $target = "_self", $enctype = null)
    {
        if (empty($data)) {
            return;
        }

        $buffer = "";

        if (null !== $action) {
            $form = new \Html\FormBootstrap5();

            // If form tag is needed print it
            $class .= " col";
            $form->id($id)->setClass($class)->method($method)->action($action)->target($target);

            if (in_modified_multiarray("file", $data, 1)) {
                $form->enctype("multipart/form-data");
            }

            $buffer .= $form->open();
        }

        foreach ($data as $row) {
            $buffer .= "<div class='row'><div class='col-12'>";

            // Backwards compatibility - provide option to pass additional data
            $field = null;
            $tooltip = null;
            if (is_object($row)) {
                $field = property_exists($row, 'field') ? $row->field : $row;
                $tooltip = property_exists($row, 'tooltip') ? $row->tooltip : null;
            } else {
                $field = array_key_exists('field', $row) ? $row['field'] : $row;
                $tooltip = array_key_exists('tooltip', $row) ? $row['tooltip'] : null;
            }

            // Check if the row is an object like an InputField
            if (!is_array($field) && is_object($field)) {
                $label_class = 'form-label';
                $field->setClass(str_replace(['small-12', 'columns', 'column'], '', $field->class ?? ''));
                switch (get_class($field)) {
                    case 'Html\Form\Select':
                    case 'Html\Cmfive\SelectWithOther':
                        $field->setClass($field->class . ' form-select');
                        break;
                    case 'Html\Form\InputField\Checkbox':
                    case 'Html\Form\InputField\Radio':
                        $field->setClass($field->class . ' form-check-control');
                        // $label_class = 'form-check-label';
                        break;
                    case 'Html\Form\InputField\Text':
                    case 'Html\Form\InputField\Date':
                    case 'Html\Form\InputField\File':
                    case 'Html\Form\InputField\Number':
                    default:
                        $field->setClass($field->class . ' form-control');
                        break;
                }
                if ((property_exists($field, "type") && $field->type !== "hidden") || !property_exists($field, "type")) {
                    $buffer .= '<div class="col"><label class="' . $label_class . '"'
                        . (property_exists($field, 'id') && !empty($field->id) ? ' for="' . $field->id . '"' : '')
                        . '>'
                        . $field->label
                        . (property_exists($field, "required") && $field->required ? " <small>Required</small>" : "")
                        . "</label>"
                        . $field->__toString() . '</div>';
                } else {
                    $buffer .= $field->__toString();
                }
                continue;
            }

            $title = !empty($field[0]) ? $field[0] : '';
            $type = !empty($field[1]) ? $field[1] : '';
            $name = !empty($field[2]) ? $field[2] : '';
            $value = !empty($field[3]) ? $field[3] : '';
            $readonly = "";

            // handle disabled fields
            if (substr(($name ?? ""), 0, 1) == '-') {
                $name = substr(($name ?? ""), 1);
                $readonly = " readonly='true' ";
            }
            // Add title field
            if ("section" === $type) {
                $buffer .= "<h4>{$title}</h4></div></div>";
                continue;
            }

            if (!empty($title) && "static" !== $type && "hidden" !== $type) {
                $buffer .= "<label class='col-12'>$title";
            }

            switch ($type) {
                case "text":
                case "password":
                    $size = !empty($field[4]) ? $field[4] : '';
                    $required = !empty($field[5]) ? $field[5] : '';
                    $buffer .= '<input' . $readonly . ' style="width:100%;" type="' . $type . '" name="' . $name . '" value="' . $value . '" size="' . $size . '" id="' . $name . '"  ' . $required . '/>';
                    break;
                case "autocomplete":
                    $options = !empty($field[4]) ? $field[4] : '';
                    $minValue = !empty($field[5]) ? $field[5] : 1;
                    $required = !empty($field[6]) ? $field[6] : '';
                    $buffer .= HtmlBootstrap5::autocomplete($name, $options, $value, null, "width: 100%;", $minValue, $required);
                    break;
                case "date":
                    $size = !empty($field[4]) ? $field[4] : '';
                    $buffer .= HtmlBootstrap5::datePicker($name, $value, $size);
                    break;
                case "datetime":
                    $size = !empty($field[4]) ? $field[4] : '';
                    $buffer .= HtmlBootstrap5::datetimePicker($name, $value, $size);
                    break;
                case "time":
                    $size = !empty($field[4]) ? $field[4] : '';
                    $buffer .= HtmlBootstrap5::timePicker($name, $value, $size);
                    break;
                case "static":
                    $size = !empty($field[4]) ? $field[4] : '';
                    $buffer .= "<div class='col-6 col-md-3'>{$title}</div><div class='col-6 col-md-9'>{$value}</div>";
                    break;
                case "textarea":
                    $c = !empty($field[4]) ? $field[4] : '';
                    $r = !empty($field[5]) ? $field[5] : '';
                    $custom_class = true;
                    if (isset($field[6])) {
                        $custom_class = $field[6];
                    }
                    $buffer .= '<textarea' . $readonly . ' style="width:100%; height:auto; " name="' . $name . '" rows="' . $r . '" cols="' . $c . '" ' .
                        (!empty($custom_class) ? ($custom_class === true ? "class='ckeditor'" : "class='$custom_class' ") : '') . ' id="' . $name . '">' . $value . '</textarea>';
                    break;
                case "select":
                    $items = !empty($field[4]) ? $field[4] : '';
                    $default = !empty($field[5]) ? ($field[5] == "null" ? '' : $field[5]) : "-- Select --";
                    $class = !empty($field[6]) ? $field[6] : '';
                    if ($readonly == "") {
                        $buffer .= HtmlBootstrap5::select($name, $items, $value, $class, "width: 100%;", $default, $readonly != "");
                    } else {
                        $buffer .= $value;
                    }
                    break;
                case "multiSelect":
                    $items = !empty($field[4]) ? $field[4] : '';
                    if ($readonly == "") {
                        $buffer .= HtmlBootstrap5::multiSelect($name, $items, $value, null, "width: 100%;");
                    } else {
                        $buffer .= $value;
                    }
                    break;
                case "checkbox":
                    $defaultValue = !empty($field[4]) ? $field[4] : '';
                    $class = !empty($field[5]) ? $field[5] : '';
                    $buffer .= HtmlBootstrap5::checkbox($name, $value, $defaultValue, $class);
                    break;
                case "radio":
                    $group = !empty($field[4]) ? $field[4] : '';
                    $defaultValue = !empty($field[5]) ? $field[5] : '';
                    $class = !empty($field[6]) ? $field[6] : '';
                    $buffer .= HtmlBootstrap5::radio($name, $group, $value, $defaultValue, $class) . "&nbsp;" . htmlentities($title);
                    break;
                case "hidden":
                    $buffer .= '<input type="hidden" name="' . $name . '" value="' . htmlspecialchars($value) . '" id="' . $name . '"/>';
                    break;
                case "file":
                    $size = !empty($field[4]) ? $field[4] : '';
                    $buffer .= '<input style="width:100%;"  type="' . $type . '" name="' . $name . '" size="' . $size . '" id="' . $name . '"/>';
                    break;
                case "multifile":
                    $buffer .= HtmlBootstrap5::multiFileUpload($name);
                    break;
            }
            if (!empty($title) && "static" !== $type && "hidden" !== $type) {
                $buffer .= "</label>";
            }
            $buffer .= "</div></div>";
        }

        if (null !== $action) {
            $buffer .= $form->close($submitTitle);
        }
        return $buffer;
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
    public static function multiColForm($data, $action = null, $method = "POST", $submitTitle = "Save", $id = null, $class = null, $extrabuttons = null, $target = "_self", $includeFormTag = true, $validation = null, $displayOverlay = true)
    {
        if (empty($data)) {
            return;
        }

        $buffer = "";
        $form = new \Html\FormBootstrap5();

        // If form tag is needed print it
        if ($includeFormTag) {
            $class .= " col";
            $form->id($id)->name($id)->setClass($class)->method($method)->action($action)->target($target)->displayOverlay($displayOverlay);

            if (in_multiarray("file", $data) || objectPropertyHasValueInMultiArray("\Html\Form\InputField", "type", "file", $data)) {
                $form->enctype("multipart/form-data");
            }

            $buffer .= $form->open();
        }

        // Set up shell layout
        $buffer .= "<div class='multicolform'>";

        // Print internals
        foreach ($data as $section => $rows) {
            // Print section header
            $buffer .= "<div class='panel clearfix'>";
            $buffer .= "<div class='row g-0 clearfix section-header'><h4 class='col'>{$section}<span class='changed_status position-absolute bg-danger rounded p-1 d-none' style='right: 1rem; top: 0.5rem; font-size: 1rem'>Changed</span></h4></div>";

            // Loop through each row
            foreach ($rows as $row) {
                if (empty($row)) {
                    continue;
                }

                // Print each field
                $buffer .= "<div class='row'>";

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
                        $label_class = 'form-label';
                        $field->setClass(str_replace(['small-12', 'columns', 'column'], '', $field->class ?? ''));
                        switch (get_class($field)) {
                            case 'Html\Form\Select':
                            case 'Html\Cmfive\SelectWithOther':
                                $field->setClass($field->class . ' form-select');
                                break;
                            case 'Html\Form\InputField\Checkbox':
                            case 'Html\Form\InputField\Radio':
                                $field->setClass($field->class . ' form-check-control');
                                // $label_class = 'form-check-label';
                                break;
                            case 'Html\Form\InputField\Text':
                            case 'Html\Form\InputField\Date':
                            case 'Html\Form\InputField\File':
                            case 'Html\Form\InputField\Number':
                            default:
                                $field->setClass($field->class . ' form-control');
                                break;
                        }
                        if ((property_exists($field, "type") && $field->type !== "hidden") || !property_exists($field, "type")) {
                            $buffer .= '<div class="col"><label class="' . $label_class . '"'
                                . (property_exists($field, 'id') && !empty($field->id) ? ' for="' . $field->id . '"' : '')
                                . '>'
                                . $field->label
                                . (property_exists($field, "required") && $field->required ? " <small>Required</small>" : "")
                                . "</label>"
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
                        $buffer .= "<div class='col'><label class='form-label'>$title</div>";
                        if (!empty($tooltip)) {
                            $buffer .= " <span data-tooltip aria-haspopup='true' class='has-tip fi-info' title='" . $tooltip . "'></span>";
                        }
                    }
                    // $buffer .= ($type !== "hidden" ? "<div>" : "");

                    // handle disabled fields
                    if (!empty($name) && substr($name, 0, 1) == '-') {
                        $name = substr($name, 1);
                        $readonly = " readonly='true' ";
                    }

                    switch ($type) {
                        case "text":
                        case "password":
                        case "email":
                        case "tel":
                            $size = !empty($field[4]) ? $field[4] : null;
                            $buffer .= '<input' . $readonly . ' class="form-control" type="' . $type . '" name="' . $name . '" value="' . (empty($value) ? '' : $value) .
                                '" size="' . $size . '" id="' . $name . '" ' . $required . " />";
                            break;
                        case "autocomplete":
                            $options = !empty($field[4]) ? $field[4] : null;
                            $minValue = !empty($field[5]) ? $field[5] : 1;
                            $buffer .= HtmlBootstrap5::autocomplete($name, $options, $value, "form-control", "width: 100%;", $minValue, $required);
                            break;
                        case "date":
                            $size = !empty($field[4]) ? $field[4] : null;
                            $buffer .= HtmlBootstrap5::datePicker($name, $value, $size, $required);
                            break;
                        case "datetime":
                            $size = !empty($field[4]) ? $field[4] : null;
                            $buffer .= HtmlBootstrap5::datetimePicker($name, $value, $size, $required);
                            break;
                        case "time":
                            $size = !empty($field[4]) ? $field[4] : null;
                            $buffer .= HtmlBootstrap5::timePicker($name, $value, $size, $required);
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
                            $sl_class = !empty($field[6]) ? $field[6] : "form-select";
                            $buffer .= HtmlBootstrap5::select($name, $items, $value, $sl_class, "width: 100%;", $default, ($readonly ? ' disabled="disabled" ' : null) . ' ' . $required);
                            break;
                        case "multiSelect":
                            $items = !empty($field[4]) ? $field[4] : null;
                            if ($readonly == "") {
                                $buffer .= HtmlBootstrap5::multiSelect($name, $items, $value, null, "width: 100%;", $required);
                            } else {
                                $buffer .= $value;
                            }
                            break;
                        case "checkbox":
                            $defaultValue = !empty($field[4]) ? $field[4] : null;
                            $cb_class = !empty($field[5]) ? $field[5] : null;
                            $buffer .= HtmlBootstrap5::checkbox($name, $value, $defaultValue, $cb_class);
                            break;
                        case "radio":
                            $group = !empty($field[4]) ? $field[4] : null;
                            $defaultValue = !empty($field[5]) ? $field[5] : null;
                            $rd_class = !empty($field[6]) ? $field[6] : null;
                            $buffer .= HtmlBootstrap5::radio($name, $group, $value, $defaultValue, $rd_class) . "&nbsp;" . htmlentities($title);
                            break;
                        case "hidden":
                            $buffer .= '<input type="hidden" name="' . $name . '" value="' . (empty($value) ? '' : htmlspecialchars($value)) . '" id="' . $name . '"/>';
                            break;
                        case "file":
                            $size = !empty($row[4]) ? $row[4] : null;
                            $buffer .= '<input style="width:100%;"  type="' . $type . '" name="' . $name . '" size="' . $size . '" id="' . $name . '"/>';
                            break;
                        case "multifile":
                            $buffer .= HtmlBootstrap5::multiFileUpload($name);
                            break;
                    }
                    $buffer .= ($type !== "hidden" ? "</div>" : "");
                }
                $buffer .= "</div>";
            }
            $buffer .= "</div>";
        }

        // Finish shell div tag
        $buffer .= "</div>";

        // Close form tag if needed
        if ($includeFormTag) {
            $buffer .= $form->close($submitTitle, $extrabuttons);
        }

        return $buffer;
    }

    /**
     * Creates an html table from an array like
     * (
     *   ("one","two","three"),
     *   ("hello","world","bla")
     * )
     *
     * @param array $array is the array of data
     * @param string $id is a css id
     * @param string $class is a css class
     * @param boolean|string[] $header use first row as <th> if true
     *
     */
    public static function table($data, $id = null, $class = "tablesorter", $header = null)
    {
        if (empty($data)) {
            return null;
        }
        $buffer = "";

        // Opening tags
        $buffer .= "<table class='{$class} d-none d-md-table'>";
        if (empty($header)) {
            $header = array_shift($data);
        }

        $buffer .= "<thead><tr>";
        if (is_array($header)) {
            foreach ($header as $h) {
                if (!is_array($h)) {
                    $buffer .= "<th>{$h}</th>";
                } else {
                    $buffer .= "<th " . ($h[1] === true ? "class='show-for-medium-up'" : "") . ">{$h[0]}</th>";
                }
            }
        } else {
            // Backwards capability!
            foreach ($data[0] as $h) {
                $buffer .= "<th>{$h}</th>";
            }
            array_shift($data);
        }
        $buffer .= "</tr></thead>";

        $buffer .= "<tbody>";
        foreach ($data as $key => $row) {
            // add a data-id attribute to each table row
            $rowId = ' data-id="' . $key . '" ';
            $buffer .= "<tr " . $rowId . ">";
            foreach ($row as $column) {
                if (!is_array($column)) {
                    $buffer .= "<td>{$column}</td>";
                } else {
                    $buffer .= "<td class='" . ($column[1] === true ? "show-for-medium-up" : (is_scalar($column[1]) ? $column[1] : '')) . "'>{$column[0]}</td>";
                }
            }
            $buffer .= "</tr>";
        }
        $buffer .= "</tbody></table>";

        $buffer .= "<div class='d-block d-md-none pt-4'>";
        if (!empty($data) && is_array($data)) {
            if (!is_array($header)) {
                $header = array_shift($data);
            }

            foreach ($data as $key => $row) {
                $buffer .= '<div class="card d-block mb-4"><ul class="list-group list-group-flush">';
                foreach ($row as $index => $column) {
                    $buffer .= '<li class="list-group-item">';
                    if (!empty($header) && is_array($header) && array_key_exists($index, $header)) {
                        $buffer .= "<strong class='me-3'>" . (is_array($header[$index]) ? $header[$index][0] : $header[$index]) . "</strong>";
                    }
                    $buffer .= "<span>" . (is_array($column) ? $column[0] : $column) . "</span></li>";
                }
                $buffer .= "</ul></div>";
            }
        }
        $buffer .= '</div>';

        return $buffer;
    }

    /**
     * This function invokes multiColForm with default parameters
     * to remove unnecessary html when displaying data
     *
     * @param Array $data
     * @return String html
     */
    public static function multiColTable($data)
    {
        if (empty($data)) {
            return;
        }

        // Set up shell layout
        $buffer = "<div class='row'>";
        $buffer .= "<div class='col'>";
        foreach ($data as $section => $rows) {
            $buffer .= "<div class='item " . toSlug($section) . "'><div class='panel flat'><h4>{$section}</h4><table class='table'>";
            foreach ($rows as $row) {
                foreach ($row as $field) {
                    $title = !empty($field[0]) ? $field[0] : null;
                    $type = !empty($field[1]) ? $field[1] : null;
                    $name = !empty($field[2]) ? $field[2] : null;
                    $value = !empty($field[3]) ? $field[3] : null;

                    // Can I do this?
                    if (empty($title) and empty($value)) {
                        continue;
                    }

                    // Add title field
                    $buffer .= "<tr class='" . toSlug($title) . "' >";
                    if (!empty($title)) {
                        $buffer .= "<td width='20%'><strong>{$title}</strong></td>";
                    }

                    $buffer .= "<td type_" . toSlug($type) . "'>{$value}</td></tr>";
                }
            }
            $buffer .= "</table></div></div>";
        }
        $buffer .= "</div></div>";

        return $buffer;
    }

    /**
     * This function creates a DataTables table, ID to identify the table,
     * an array of headers and the source data url are required
     *
     * If a table column is to be sortable, you must provide each header element
     * in the following format:
     *    [0 => "<sort column>", 1 => "<title>"]
     *
     * @param array $header
     * @param array $data
     * @param int $page
     * @param int $page_size
     * @param int $total_results
     * @param string $base_url
     * @param string|optional $sort
     * @param string|optional $sort_direction
     * @param string|optional $page_query_param
     * @param string|optional $pagesize_query_param
     * @param string|optional $total_results_query_param
     * @param string|optional $sort_query_param
     */
    public static function paginatedTable(
        $header,
        $data,
        $page,
        $page_size,
        $total_results,
        $base_url,
        $sort = null,
        $sort_direction = 'asc',
        $page_query_param = "page",
        $pagesize_query_param = "page_size",
        $total_results_query_param = "total_results",
        $sort_query_param = "sort",
        $sort_direction_param = "sort_direction"
    ) {
        // Build URL for pagination
        $url_parsed = parse_url($base_url);
        $url_string = $url_parsed['path'];
        $url_string .= (empty($url_parsed['query']) ? "?" : '?' . $url_parsed['query'] . '&') . $sort_query_param . '=' . $sort . '&' . $sort_direction_param . '=' . $sort_direction;
        $url_string .= (!empty($url_parsed['fragment']) ? '#' . $url_parsed['fragment'] : '');

        // Generate the table
        $num_results = $total_results;
        if ($page_size > 0) {
            $num_results = ceil($total_results / $page_size);
        }

        if ($total_results == 0) {
            $total_results = count($data);
            if ($total_results == 0) {
                return '<div class="row paginated-table-container"><div class="col" style="margin: 5px 0px;">No results found</div></div>';
            }
        }

        $count_items = count($data);
        $starting_item = (($page - 1) * $page_size) + 1;
        $buffer = '<div class="paginated-table-container"><div class="row">'
            . '<div class="col-md-6 col-sm-12" style="margin-top: 5px;">Showing ' . $starting_item . ' - ' . ($starting_item + $count_items - 1) . ' of ' . $total_results . '</div>'
            . '<div class="col-md-6 col-sm-12">';
        if ($num_results > 0) {
            $buffer .= '<div class="float-md-end">Page: <select class="form-select mb-4 mb-md-0" onchange="location = this.value;">';
            // Build URL for dropdown pagination
            $dropdown_url_string = $url_parsed['path'];
            $dropdown_url_string .= (empty($url_parsed['query']) ? "?" : '?' . $url_parsed['query'] . '&') . $sort_query_param . '=' . $sort . '&' . $sort_direction_param . '=' . $sort_direction;

            for ($i = 1; $i <= $num_results; $i++) {
                $buffer .= '<option' . ($i == $page ? ' selected="selected"' : '') . ' value="' . $dropdown_url_string . '&' . $page_query_param . '=' . $i . (!empty($url_parsed['fragment']) ? '#' . $url_parsed['fragment'] : '') . '">' . $i . '</option>';
            }
            $buffer .= '</select></div>';
        }
        $buffer .= "</div></div>"
            . "<div class='row table-responsive d-none d-md-block'><div class='col'>"
            . "<table>";
        if (!empty($header) && is_array($header)) {
            // Print table header
            $buffer .= "<thead><tr>";
            foreach ($header as $title) {
                // Build optional sort url
                $sort_asc_string = '';
                $sort_desc_string = '';
                if (is_array($title)) {
                    $sort_direction_asc_query = "{$sort_query_param}={$title[0]}&{$sort_direction_param}=asc";
                    $sort_direction_desc_query = "{$sort_query_param}={$title[0]}&{$sort_direction_param}=desc";

                    $sort_asc_string = $url_parsed['path'] . (empty($url_parsed['query']) ? '?' : '?' . $url_parsed['query'] . '&') . $sort_direction_asc_query . (!empty($url_parsed['fragment']) ? '#' . $url_parsed['fragment'] : '');
                    $sort_desc_string = $url_parsed['path'] . (empty($url_parsed['query']) ? '?' : '?' . $url_parsed['query'] . '&') . $sort_direction_desc_query . (!empty($url_parsed['fragment']) ? '#' . $url_parsed['fragment'] : '');
                }
                $buffer .= '<th' . (is_array($title) && $title[0] === $sort ? ' class="sorted_column"' : '') . '>' . (is_array($title) ? '<a href="' . ($title[0] === $sort && $sort_direction === 'asc' ? $sort_desc_string : $sort_asc_string) . '">' . $title[1] . '</a>' : $title)
                    . (is_array($title) ? '<div class="float-end">'
                        . ($title[0] !== $sort || ($title[0] === $sort && $sort_direction !== 'asc') ? '<a class="sort-ascending" href="' . $sort_asc_string . '"><i class="bi bi-chevron-up"></i></a>' : '')
                        . ($title[0] !== $sort || ($title[0] === $sort && $sort_direction !== 'desc') ? '<a class="sort-descending" href="' . $sort_desc_string . '"><i class="bi bi-chevron-down"></i></a>' : '')
                        . '</div></th>' : '');
            }
            $buffer .= "</tr></thead>";
        }

        // Print table body
        if (!empty($data) && is_array($data)) {
            $buffer .= "<tbody>";
            foreach ($data as $key => $row) {
                $buffer .= '<tr data-id="' . $key . '">';
                foreach ($row as $column) {
                    if (is_array($column)) {
                        $buffer .= '<td class="' . $column[1] . '">' . $column[0] . '</td>';
                    } else {
                        $buffer .= "<td>{$column}</td>";
                    }
                }
                $buffer .= "</tr>";
            }
            $buffer .= "</tbody>";
        }
        $buffer .= "</table></div></div>";
        $buffer .= "<div class='d-block d-md-none'>";
        if (!empty($data) && is_array($data)) {
            foreach ($data as $key => $row) {
                $buffer .= '<div class="card d-block mb-4"><ul class="list-group list-group-flush">';
                foreach ($row as $index => $column) {
                    $buffer .= '<li class="list-group-item">';
                    if (array_key_exists($index, $header)) {
                        $buffer .= "<strong class='me-3'>" . (is_array($header[$index]) ? $header[$index][1] : $header[$index]) . "</strong>";
                    }
                    $buffer .= "<span>{$column}</span></li>";
                }
                $buffer .= "</ul></div>";
            }
        }
        $buffer .= '</div>';
        $buffer .= '<div class="pagination-centered">' . self::pagination($page, $num_results, $page_size, $total_results, $url_string, $page_query_param, $pagesize_query_param, $total_results_query_param) . '</div></div>';
        return $buffer;
    }

    /**
     *  Filter function returns formatted form for declaring filters. Data is the same
     *  as how HtmlBootstrap5::form is used. Filter parameters can be retrieved with $w->request
     *  and it may be a good idea to prefix input names with 'filter_' to avoid naming
     *  collisions in requests
     *
     *  @param string $legend
     *  @param array $data
     *  @param string $action
     *  @param string $method
     *  @param string $submitTitle
     *  @param string $id
     *  @param string $class
     *
     *  @return string $buf
     */
    public static function filter($legend, $data, $action = null, $method = "POST", $submitTitle = "Filter", $id = null, $class = null, $validation = null)
    {
        // This will pretty much be a redesigned HtmlBootstrap5::form layout
        if (empty($data)) {
            return;
        }

        $form = new \Html\FormBootstrap5();
        // If form tag is needed print it
        $form->id($id)->setClass($class)->method($method)->action($action);

        $buffer = "";
        $buffer .= $form->open();

        // Set up vars
        $hidden = "";
        $buffer .= "<fieldset>";
        $buffer .= "<legend>" . $legend . "</legend>";
        $buffer .= "<ul id='filter-grid' class='small-block-grid-1 medium-block-grid-3 large-block-grid-4'>";

        $should_autosubmit = false;
        if (count($data) === 1 && is_array($data[0]) && $data[0][1] === "select") {
            $should_autosubmit = true;
        }

        // Loop through data
        foreach ($data as $row) {
            // Check if the row is an object like an InputField
            if (!is_array($row) && is_object($row)) {
                switch (get_class($row)) {
                    case 'Html\Form\Select':
                    case 'Html\Cmfive\SelectWithOther':
                        $row->setClass($row->class . ' form-select form-select-sm');
                        break;
                    case 'Html\Form\Checkbox':
                    case 'Html\Form\Radio':
                        $row->setClass($row->class . ' form-check-control');
                        break;
                    case 'Html\Form\InputField\Text':
                    case 'Html\Form\InputField\Date':
                    case 'Html\Form\InputField\File':
                    case 'Html\Form\InputField\Number':
                    default:
                        $row->setClass($row->class . ' form-control form-control-sm');
                        break;
                }
                if ((property_exists($row, "type") && $row->type !== "hidden") || !property_exists($row, "type")) {
                    $buffer .= '<li><label class="col">' . $row->label . '<div>' . $row->__toString() . '</div></label></li>';
                } else {
                    $buffer .= $row->__toString();
                }
                continue;
            }

            $buffer .= "<li>";

            // Get row parameters
            $title = !empty($row[0]) ? $row[0] : null;
            $type = !empty($row[1]) ? $row[1] : null;
            $name = !empty($row[2]) ? $row[2] : null;
            $value = !empty($row[3]) ? $row[3] : null;

            $readonly = "";

            $required = null;
            if (!empty($validation[$name])) {
                if (in_array("required", $validation[$name])) {
                    $required = "required";
                }
            }

            // handle disabled fields
            if (substr($name, 0, 1) == '-') {
                $name = substr($name, 1);
                $readonly = " readonly='true' ";
            }

            // span entry fields that have no title
            $buffer .= "<div class='col'>" . (!empty($title) ? "<label>{$title}" : '');

            $size = !empty($row[4]) ? $row[4] : null;

            // Get the input that we need
            switch ($type) {
                case "text":
                case "password":
                    $buffer .= '<input' . $readonly . ' style="width:100%;"  type="' . $type . '" name="' . $name . '" value="' . (empty($value) ? '' : htmlspecialchars($value)) . '" size="' . (!empty($row[4]) ? $row[4] : null) . '" id="' . $name . '"/>';
                    break;
                case "autocomplete":
                    $minlength = !empty($row[5]) ? $row[5] : null;
                    $buffer .= HtmlBootstrap5::autocomplete($name, $size, $value, null, "width: 100%;", !empty($minlength) ? $minlength : 1, $required);
                    break;
                case "date":
                    $buffer .= HtmlBootstrap5::datePicker($name, $value, $size, $required);
                    break;
                case "datetime":
                    $buffer .= HtmlBootstrap5::datetimePicker($name, $value, $size, $required);
                    break;
                case "time":
                    $buffer .= HtmlBootstrap5::timePicker($name, $value, $size, $required);
                    break;
                case "static":
                    $buffer .= $value;
                    break;
                case "textarea":
                    // Columns is the size variable
                    $cols = $size;
                    $rows = !empty($row[5]) ? $row[5] : null;
                    $buffer .= '<textarea name="' . $name . '" rows="' . $rows . '" cols="' . $cols . '" id="' . $name . '">' . $value . '</textarea>';
                    break;
                case "section":
                    $buffer .= htmlentities($title);
                    break;
                case "select":
                    $items = $size;
                    $class = !empty($row[5]) ? $row[5] : null;
                    $style = !empty($row[6]) ? $row[6] : null;
                    $allmsg = !empty($row[7]) ? $row[7] : "-- Select --";
                    // $name, $items, $value=null, $class=null, $style=null, $allmsg = "-- Select --", $required = null
                    if ($readonly == "") {
                        $buffer .= HtmlBootstrap5::select($name, $items, $value, $class, $style, $allmsg);
                    } else {
                        $buffer .= $value;
                    }
                    break;
                case "multiSelect":
                    $items = $size;
                    if ($readonly == "") {
                        $buffer .= HtmlBootstrap5::multiSelect($name, $items, $value, null, "width: 100%;");
                    } else {
                        $buffer .= $value;
                    }
                    break;
                case "checkbox":
                    $buffer .= HtmlBootstrap5::checkbox($name, $value, $value, $class);
                    break;
                case "radio":
                    $group = !empty($field[4]) ? $field[4] : null;
                    $defaultValue = !empty($field[5]) ? $field[5] : null;
                    $class = !empty($field[6]) ? $field[6] : null;
                    $buffer .= HtmlBootstrap5::radio($name, $group, $value, $defaultValue, $class) . "&nbsp;" . htmlentities($title);
                    break;
                case "hidden":
                    $hidden .= "<input type=\"hidden\" name=\"" . $name . "\" value=\"" . (empty($value) ? '' : htmlspecialchars($value)) . "\"/>\n";
                    break;
                case "file":
                    $buffer .= "<input style=\"width:100%;\" type=\"" . $type . "\" name=\"" . $name . "\" size=\"" . $size . "\" id=\"" . $name . "\"/>";
                    break;
            }

            $buffer .= "</label></div></li>"; // </div>
        }

        // This is only true when the filter has one element and its a select field
        if ($should_autosubmit) {
            $selector = $id ? "form#" . $id . " select" : "form select";
            $buffer .= "<script>document.querySelectorAll('" . $selector . "').forEach(function(select) { select.addEventListener('change', function() { this.form.submit(); }); });</script>";
        }

        // Filter button (optional... though optional is pointless)
        if (!empty($action)) {
            $button = new \Html\button();
            $buffer .= "<li><label>Actions<div class='filter-button-container'>";
            if ($submitTitle !== null && !$should_autosubmit) {
                $buffer .= $button->type("submit")->text($submitTitle)->setClass('btn btn-sm btn-primary')->__toString();
            }
            if (!empty($id)) {
                $buffer .= $button->text("Reset")->id("filter_reset_{$id}")->name("filter_reset_{$id}")->value("reset")->setClass('btn btn-sm btn-secondary')->__toString();
            } else {
                $buffer .= $button->text("Reset")->name("reset")->value("reset")->setClass('btn btn-sm btn-secondary')->__toString();
            }

            $buffer .= "</div></label></li>";
        }

        $buffer .= "</ul>"; // </div>
        $buffer .= "\n</fieldset>\n";
        $buffer .= $hidden . "</form>\n";

        return $buffer;
    }

    public static function pagination($currentpage, $numpages, $pagesize, $totalresults, $baseurl, $pageparam = "p", $pagesizeparam = "ps", $totalresultsparam = "tr", $tab = null): string
    {
        // Prepare buffer
        $buf = '';
        if ((isNumber($currentpage) && isNumber($numpages)) && ((!empty($tab)) || (isNumber($pagesize) && isNumber($totalresults)))) {
            // Check that we're within range
            if ($currentpage > 0 && $currentpage <= $numpages && $numpages > 1) {
                $buf = "<nav aria-label='pagination'><ul class='pagination justify-content-center flex-wrap'" . ((!empty($tab)) ? " id='$tab-pagination-controls'" : "") . ">";

                for ($page = 1; $page <= $numpages; $page++) {
                    $buf .= "<li class='page-item" . ($currentpage == $page ? " active disabled' aria-current='page'" : "'") . ">";

                    if (!empty($tab)) { // Tabbed pagination
                        $buf .= "<a class='page-link' data-tab='$tab' data-tabbed-pagination-page='$page'>$page</a>";
                    } else { // Standard pagination
                        $url_parsed = parse_url($baseurl);

                        $url_string = $url_parsed['path'];
                        $url_string .= (empty($url_parsed['query']) ? '?' : '?' . $url_parsed['query'] . '&') . $pageparam . '=' . $page . '&' . $pagesizeparam . '=' . $pagesize . '&' . $totalresultsparam . '=' . $totalresults;
                        $url_string .= (!empty($url_parsed['fragment']) ? '#' . $url_parsed['fragment'] : '');

                        $buf .= '<a class="page-link" href=\'' . $url_string . '\'>' . $page . '</a>';
                    }

                    $buf .= "</li>";
                }

                $buf .= "</ul></nav>";
            }
        }

        return $buf;
    }

    /**
     * Display a message inside an alert box.
     *
     * @param string $msg
     * @param string $class
     * @return string
     */
    public static function alertBox($msg, $type = "alert-info", $include_close = true): string
    {
        if ($type !== "alert-info" && $type !== "alert-warning" && $type !== "alert-danger" && $type !== "alert-success") {
            $type = "alert-info";
        }

        return "<div data-alert class='alert alert-box {$type}'>{$msg}" . (!!$include_close ? "<a href='#' class='close'>&times;</a>" : '') . "</div>";
    }

    public static function dataCard(string $header, array $data)
    {
        $buffer = '<div class="row panel flex-fill"><div class="col-sm-12">';
        $buffer .= '<h4>' . $header . '</h4>';
        foreach ($data as $row_header => $row_data) {
            $buffer .= '<p><strong>' . $row_header . '</strong><br/>' . $row_data . '</p>';
        }
        return $buffer . '</div></div>';
    }

    
    public static function datePicker($name, $value = null, $size = null, $required = null)
    {
        return '<input class="form-control" type="date" name="' . $name . '" value="' . $value . '" size="' . $size . '" id="' . $name . '" ' . $required . ' />';
    }

    public static function datetimePicker($name, $value = null, $size = null, $required = null)
    {
        return '<input class="form-control" type="datetime-local" name="' . $name . '" value="' . $value . '" size="' . $size . '" id="' . $name . '" ' . $required . ' />';
    }

    public static function timePicker($name, $value = null, $size = null, $required = null)
    {
        return '<input class="form-control" type="time" name="' . $name . '" value="' . $value . '" size="' . $size . '" id="' . $name . '" ' . $required . ' />';
    }

    /**
     * Create a single select autocomplete widget
     *
     * @param <type> $data
     * @param <type> $value
     * @param <type> $class
     */
    public static function autocomplete($name, $options, $value = null, $class = null, $style = null, $minLength = 1, $required = null)
    {
        return (new \Html\Form\Html5Autocomplete([
            "id|name" => "title",
            "class" => "form-control " . $class,
            "label" => "Title",
            "maxItems" => 1,
            "value" => $value,
            "required" => !!$required,
            "style" => $style,
            "minLength" => $minLength,
            "options" => $options,
        ]))->__toString();
    }
}
