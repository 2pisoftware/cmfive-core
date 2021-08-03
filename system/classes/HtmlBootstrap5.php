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
        $buffer .= "<div class='multicolform'>";

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
     * @param Array $header
     * @param Array $data
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
            return '<div class="row paginated-table-container"><div class="col" style="margin: 5px 0px;">No results found</div></div>';
        }

        $count_items = count($data);
        $starting_item = (($page - 1) * $page_size) + 1;
        $buffer = '<div class="paginated-table-container"><div class="row">'
            . '<div class="col col-md-6 col-sm-12" style="margin-top: 5px;">Showing ' . $starting_item . ' - ' . ($starting_item + $count_items - 1) . ' of ' . $total_results . '</div>'
            . '<div class="col col-md-6 col-sm-12">';
        if ($num_results > 0) {
            $buffer .= '<div class="float-end">Page: <select onchange="location = this.value;">';
            // Build URL for dropdown pagination
            $dropdown_url_string = $url_parsed['path'];
            $dropdown_url_string .= (empty($url_parsed['query']) ? "?" : '?' . $url_parsed['query'] . '&') . $sort_query_param . '=' . $sort . '&' . $sort_direction_param . '=' . $sort_direction;

            for ($i = 1; $i <= $num_results; $i++) {
                $buffer .= '<option' . ($i == $page ? ' selected="selected"' : '') . ' value="' . $dropdown_url_string . '&' . $page_query_param . '=' . $i . (!empty($url_parsed['fragment']) ? '#' . $url_parsed['fragment'] : '') . '">' . $i . '</option>';
            }
            $buffer .= '</select></div>';
        }
        $buffer .= "</div></div>"
            . "<div data-alert class='show-for-small alert-box'>This is a responsive table, pan left to right to view data.</div>"
            . "<div class='row table-responsive'><div class='col'>"
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
        $buffer .= '<div class="pagination-centered">' . Html::pagination($page, $num_results, $page_size, $total_results, $url_string, $page_query_param, $pagesize_query_param, $total_results_query_param) . '</div></div>';
        return $buffer;
    }

    /**
     *  Filter function returns formatted form for declaring filters. Data is the same
     *  as how Html::form is used. Filter parameters can be retrieved with $w->request
     *  and it may be a good idea to prefix input names with 'filter_' to avoid naming
     *  collisions in requests
     *
     *  @param String $legend
     *  @param Array $data
     *  @param String $action
     *  @param String $method
     *  @param String $submitTitle
     *  @param String $id
     *  @param String $class
     *
     *  @return String $buf
     */
    public static function filter($legend, $data, $action = null, $method = "POST", $submitTitle = "Filter", $id = null, $class = null, $validation = null)
    {
        // This will pretty much be a redesigned Html::form layout
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
                if ((property_exists($row, "type") && $row->type !== "hidden") || !property_exists($row, "type")) {
                    $buffer .= '<li><label class=\'col\'>' . $row->label . '<div>' . $row->__toString() . '</div></label></li>';
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
            if (!empty($title)) {
                $mediumCols = 9;
                if ($type == "checkbox") {
                    $mediumCols = 6;
                }
                $buffer .= "<div class='col'><label>{$title}";
            } else {
                $buffer .= "<div class='col'>";
            }

            $size = !empty($row[4]) ? $row[4] : null;

            // Get the input that we need
            switch ($type) {
                case "text":
                case "password":
                    $buffer .= '<input' . $readonly . ' style="width:100%;"  type="' . $type . '" name="' . $name . '" value="' . htmlspecialchars($value) . '" size="' . (!empty($row[4]) ? $row[4] : null) . '" id="' . $name . '"/>';
                    break;
                case "autocomplete":
                    $minlength = !empty($row[5]) ? $row[5] : null;
                    $buffer .= Html::autocomplete($name, $size, $value, null, "width: 100%;", !empty($minlength) ? $minlength : 1, $required);
                    break;
                case "date":
                    $buffer .= Html::datePicker($name, $value, $size, $required);
                    break;
                case "datetime":
                    $buffer .= Html::datetimePicker($name, $value, $size, $required);
                    break;
                case "time":
                    $buffer .= Html::timePicker($name, $value, $size, $required);
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
                        $buffer .= Html::select($name, $items, $value, $class, $style, $allmsg);
                    } else {
                        $buffer .= $value;
                    }
                    break;
                case "multiSelect":
                    $items = $size;
                    if ($readonly == "") {
                        $buffer .= Html::multiSelect($name, $items, $value, null, "width: 100%;");
                    } else {
                        $buffer .= $value;
                    }
                    break;
                case "checkbox":
                    $buffer .= Html::checkbox($name, $value, $value, $class);
                    break;
                case "radio":
                    $group = !empty($field[4]) ? $field[4] : null;
                    $defaultValue = !empty($field[5]) ? $field[5] : null;
                    $class = !empty($field[6]) ? $field[6] : null;
                    $buffer .= Html::radio($name, $group, $value, $defaultValue, $class) . "&nbsp;" . htmlentities($title);
                    break;
                case "hidden":
                    $hidden .= "<input type=\"hidden\" name=\"" . $name . "\" value=\"" . htmlspecialchars($value) . "\"/>\n";
                    break;
                case "file":
                    $buffer .= "<input style=\"width:100%;\" type=\"" . $type . "\" name=\"" . $name . "\" size=\"" . $size . "\" id=\"" . $name . "\"/>";
                    break;
            }

            $buffer .= "</label></div></li>"; // </div>
        }

        // This is only true when the filter has one element and its a select field
        if ($should_autosubmit) {
            $buffer .= "<script>$('form" . ($id ? "#" . $id : "") . ' select\').change(function(){this.form.submit()});</script>';
        }
        // Filter button (optional... though optional is pointless)
        if (!empty($action)) {
            $button = new \Html\button();
            $buffer .= "<li><div class='small-12 columns'><label>Actions<div class='filter-button-container'>";
            if ($submitTitle !== null && !$should_autosubmit) {
                $buffer .= $button->type("submit")->text($submitTitle)->setClass('btn btn-sm btn-primary')->__toString();
            }
            if (!empty($id)) {
                $buffer .= $button->text("Reset")->id("filter_reset_{$id}")->name("filter_reset_{$id}")->value("reset")->setClass('btn btn-sm btn-secondary')->__toString();
            } else {
                $buffer .= $button->text("Reset")->name("reset")->value("reset")->setClass('btn btn-sm btn-secondary')->__toString();
            }

            $buffer .= "</div></label></div></li>";
        }
        
        $buffer .= "</ul>"; // </div>
        $buffer .= "\n</fieldset>\n";
        $buffer .= $hidden . "</form>\n";

        return $buffer;
    }
}