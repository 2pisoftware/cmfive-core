<?php

require_once "classes/html/GlobalAttributes.php";
require_once "classes/html/a.php";
require_once "classes/html/button.php";
require_once "classes/html/form.php";

class Html
{

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
     * @param boolean $header use first row as <th> if true
     *
     */
    public static function table($data, $id = null, $class = "tablesorter", $header = null)
    {
        if (empty($data)) {
            return null;
        }
        $buffer = "";

        // Opening tags
        $buffer .= "<table class='{$class}'>";
        if (!empty($header)) {
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
        }

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
        return $buffer;
    }

    public static function dataTable($data = [])
    {
        if (empty($data)) {
            return '';
        }

        $row_count = 0;
        $buffer = '<dl class="accordion" data-accordion>';
        foreach ($data as $row) {
            $buffer .= '<dd class="accordion-navigation">';
            $buffer .= '<a href="#panel' . (++$row_count) . '">' . $row[0] . '</a>';
            $buffer .= '<div id="panel' . $row_count . '" class="content">';

            // Content here
            foreach ($row[1] as $entry) {
                $buffer .= '<div class="row-fluid">' . $entry . '</div>';
            }

            // End content

            $buffer .= '</div></dd>';
        }
        $buffer .= "</dl>";

        return $buffer;
    }

    /**
     * Html function to draw a chart, see: http://www.chartjs.org/docs/ for how
     * the data structure and options should be put together for each
     *
     * @param string $id
     * @param string $type
     * @param array $data
     * @param array $options
     * @param mixed $height
     * @param mixed $width
     * @return string
     */
    public static function chart($id = "chartjs", $type = "line", $data = [], $options = [], $height = null, $width = null, $class = null)
    {
        // Set default values
        if (empty($height)) {
            $height = "300px";
        }
        if (empty($width)) {
            $width = "300px";
        }
        // Create the canvas
        $buffer = "<canvas id='{$id}' class='{$class}' width='{$width}' height='{$height}' style='display: block; margin: 0 auto; height: {$height}; width: {$width};'></canvas>\n";
        $buffer .= "<script type='text/javascript'>\n";
        // Get canvas context via jQuery
        $buffer .= "\tvar ctx = jQuery(\"#{$id}\").get(0).getContext(\"2d\");\n";

        // Create the chart
        $buffer .= "var chart{$id} = new Chart(ctx, {type: '";
        switch (strtolower($type)) {
            case "line":
                $buffer .= "line";
                break;
            case "bar":
                $buffer .= "bar";
                break;
            case "radar":
                $buffer .= "radar";
                break;
            case "polar":
                $buffer .= "polarArea";
                break;
            case "pie":
                $buffer .= "pie";
                break;
            case "doughnut":
                $buffer .= "doughnut";
                break;
            case "bubble":
                $buffer .= "bubble";
                break;
            default:
                $buffer .= "Line";
        }
        $buffer .= "', data: " . json_encode($data) . ", options: " . (!empty($options) ? json_encode($options) : "{}") . "});";
        $buffer .= "</script>";
        return $buffer;
    }

    /**
     * creates a html link
     * Side note: $alt is an "illegal" parameter for a links
     */
    public static function a($href, $title, $alt = null, $class = null, $confirm = null, $target = null)
    {
        $a = new \Html\a();
        $a->href($href)->text($title)->setClass($class)->confirm($confirm)->target($target);
        return $a->__toString();
    }

    public static function b($href, $title, $confirm = null, $id = null, $newtab = false, $class = null, $type = null, $name = null)
    {
        $button = new \Html\button();
        $button->href($href)->text($title)->confirm($confirm)->id($id)->setClass($class)->newtab($newtab)->type($type)->name($name);
        return $button->__toString();
    }
    /**
     * Create an a link styled as a button
     * */
    public static function ab($href, $title, $class = "", $id = "", $confirm = "")
    {
        $classParam = ' button tiny ';
        if (strlen($class) > 0) {
            $classParam .= $class;
        }
        $classParam = " class='" . $classParam . "' ";
        $idParam = '';
        if (strlen($id) > 0) {
            $idParam = " id='" . $id . "' ";
        }
        $confirmParam = '';
        if (strlen($confirm) > 0) {
            $confirmParam = " onclick=\"return confirm('" . $confirm . "')\" ";
        }

        return '<a href="' . $href . '" ' . $classParam . ' ' . $idParam . ' ' . $confirmParam . '>' . $title . '</a>';
    }

    /**
     * Create an a link styled as a button that pops up a reveal dialog
     * */
    public static function abox($href, $title, $class = "", $id = "", $confirm = "")
    {
        $classParam = ' button tiny ';
        if (strlen($class) > 0) {
            $classParam .= $class;
        }
        $classParam = " class='" . $classParam . "' ";
        $idParam = '';
        if (strlen($id) > 0) {
            $idParam = " id='" . $id . "' ";
        }
        $confirmParam = '';
        if (strlen($confirm) > 0) {
            $confirmParam = " onclick='return(\"" . $confirm . "\");' ";
        }

        return '<a href="' . $href . '" data-reveal-id="cmfive-modal" data-reveal-ajax="true" ' . $classParam . ' ' . $idParam . ' ' . $confirmParam . '>' . $title . '</a>';
    }


    /**
     * Creates a link (or button) which will pop up a colorbox
     * containing the contents of the url
     *
     */
    public static function box($href, $title, $button = false, $iframe = false, $width = null, $height = null, $param = "isbox", $id = null, $class = null, $confirm = null, $modal_window_id = 'cmfive-modal')
    {
        // $onclick = Html::boxOnClick($href, $iframe, $width, $height, $param, $confirm, false, $modal_window_id);
        $element = null;
        if ($button) {
            // $tag = "button";
            $element = new \Html\button();
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
     * Returns onclick event for inline html attribute
     *
     * This should no longer be used as JS click binding is the best way to do this
     *
     * @deprecated v4.0.x
     */
    public static function boxOnClick($href, $iframe = false, $width = null, $height = null, $param = "isbox", $confirm = null, $include_tag = true, $modal_window_id = 'cmfive-modal')
    {
        if ($iframe) {
            $width = ", innerWidth:" . $width;
            $height = ", innerHeight:" . $height;
        }
        // add parameter to indicate that this request is shown inside a box
        $prefix = stripos($href, "?") ? "&" : "?";
        $href .= $prefix . $param . "=1";
        $iframe = $iframe ? "true" : "false";

        $confirm_str = '';
        if ($confirm) {
            $confirm_str = "if(confirm('" . $confirm . "')) { ";
        }
        if ($include_tag) {
            $tag_start = "onclick=\"";
            $tag_end = "\"";
        } else {
            $tag_start = "";
            $tag_end = "";
        }

        return $tag_start . "openModal(&quot;{$href}&quot;);return false;" . ($confirm ? "}" : "") . $tag_end;
    }

    /**
     * creates a ul from an array structure:
     * ("1","2", array("2.1","2.2"),"3")
     */
    public static function ul($array, $id = null, $class = null, $subclass = null, $type = "ul")
    {
        if (!$array || sizeof($array) < 1) {
            return "";
        }

        $id = $id ? ' id="' . $id . '"' : null;
        $class = $class ? ' class="' . $class . '"' : null;
        $buf = "<{$type}" . $id . $class . ">\n";
        for ($i = 0; $i < sizeof($array); $i++) {
            $cur = $array[$i];
            $next = $i < sizeof($array) - 1 ? $array[$i + 1] : null;
            $buf .= "<li>" . $cur;
            if (is_array($next)) {
                $buf .= self::ul($next, null, $subclass);
            }
            $buf .= "</li>\n";
        }
        $buf .= "</{$type}>\n";
        return $buf;
    }

    /**
     * creates a ol from an array structure:
     * ("1","2", array("2.1","2.2"),"3")
     */
    public static function ol(&$array, $id = null, $class = null, $subclass = null)
    {
        return Html::ul($array, $id, $class, $subclass, "ol");
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
            $form = new \Html\form();

            // If form tag is needed print it
            $class .= " small-12 columns";
            $form->id($id)->setClass($class)->method($method)->action($action)->target($target);

            if (in_modified_multiarray("file", $data, 1)) {
                $form->enctype("multipart/form-data");
            }

            $buffer .= $form->open();
        }
        // Set up shell layout
        $buffer .= "<div class='row-fluid small-12'>";

        foreach ($data as $field) {
            $buffer .= "<div class='row-fluid'><div class='small-12'>";

            // Check if the row is an object like an InputField
            if (!is_array($field) && is_object($field)) {
                if ((property_exists($field, "type") && $field->type !== "hidden") || !property_exists($field, "type")) {
                    $buffer .= '<label class=\'small-12 columns\'>' . $field->label . ($field->required ? " <small>Required</small>" : "") . '<div>' . $field->__toString() . '</div></label>';
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
                $buffer .= "<label class='small-12 columns'>$title";
            }

            switch ($type) {
                case "text":
                case "password":
                    $size = !empty($field[4]) ? $field[4] : '';
                    $required = !empty($field[5]) ? $field[5] : '';
                    $buffer .= '<input' . $readonly . ' style="width:100%;" type="' . $type . '" name="' . $name . '" value="' . htmlspecialchars($value) . '" size="' . $size . '" id="' . $name . '"  ' . $required . '/>';
                    break;
                case "autocomplete":
                    $options = !empty($field[4]) ? $field[4] : '';
                    $minValue = !empty($field[5]) ? $field[5] : 1;
                    $required = !empty($field[6]) ? $field[6] : '';
                    $buffer .= Html::autocomplete($name, $options, $value, null, "width: 100%;", $minValue, $required);
                    break;
                case "date":
                    $size = !empty($field[4]) ? $field[4] : '';
                    $buffer .= Html::datePicker($name, $value, $size);
                    break;
                case "datetime":
                    $size = !empty($field[4]) ? $field[4] : '';
                    $buffer .= Html::datetimePicker($name, $value, $size);
                    break;
                case "time":
                    $size = !empty($field[4]) ? $field[4] : '';
                    $buffer .= Html::timePicker($name, $value, $size);
                    break;
                case "static":
                    $size = !empty($field[4]) ? $field[4] : '';
                    $buffer .= "<div class='small-6 medium-3 columns'>{$title}</div><div class='small-6 medium-9 columns'>{$value}</div>";
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
                        $buffer .= Html::select($name, $items, $value, $class, "width: 100%;", $default, $readonly != "");
                    } else {
                        $buffer .= $value;
                    }
                    break;
                case "multiSelect":
                    $items = !empty($field[4]) ? $field[4] : '';
                    if ($readonly == "") {
                        $buffer .= Html::multiSelect($name, $items, $value, null, "width: 100%;");
                    } else {
                        $buffer .= $value;
                    }
                    break;
                case "checkbox":
                    $defaultValue = !empty($field[4]) ? $field[4] : '';
                    $class = !empty($field[5]) ? $field[5] : '';
                    $buffer .= Html::checkbox($name, $value, $defaultValue, $class);
                    break;
                case "radio":
                    $group = !empty($field[4]) ? $field[4] : '';
                    $defaultValue = !empty($field[5]) ? $field[5] : '';
                    $class = !empty($field[6]) ? $field[6] : '';
                    $buffer .= Html::radio($name, $group, $value, $defaultValue, $class) . "&nbsp;" . htmlentities($title);
                    break;
                case "hidden":
                    $buffer .= '<input type="hidden" name="' . $name . '" value="' . htmlspecialchars($value) . '" id="' . $name . '"/>';
                    break;
                case "file":
                    $size = !empty($field[4]) ? $field[4] : '';
                    $buffer .= '<input style="width:100%;"  type="' . $type . '" name="' . $name . '" size="' . $size . '" id="' . $name . '"/>';
                    break;
                case "multifile":
                    $buffer .= Html::multiFileUpload($name);
                    break;
            }
            if (!empty($title) && "static" !== $type && "hidden" !== $type) {
                $buffer .= "</label>";
            }
            $buffer .= "</div></div>";
        }
        $buffer .= "</div>";
        // $buffer .= "<script>$(function(){try{\$('textarea.ckeditor').each(function(){CKEDITOR.replace(this)})}catch(err){}});</script>";

        $buffer .= "<script>document.addEventListener('DOMContentLoaded', () => { [...document.querySelectorAll('textarea.ckeditor')].forEach(x => { CKEDITOR.replace(x) }) })</script>";

        if (null !== $action) {
            $buffer .= $form->close($submitTitle);
        }
        return $buffer;
    }

    public static function datePicker($name, $value = null, $size = null, $required = null)
    {
        $firstDay = Config::get('main.datepicker_first_day');
        $buf = '<input class="date_picker" type="text" name="' . $name . '" value="' . $value . '" size="' . $size . '" id="' . $name . '" ' . $required . ' />';
        $buf .= "<script>$('#$name').datepicker({dateFormat: 'dd/mm/yy', changeMonth: true, changeYear: true, firstDay: $firstDay});$('#$name').keyup( function(event) { $(this).val('');}); </script>";
        return $buf;
    }

    public static function datetimePicker($name, $value = null, $size = null, $required = null)
    {
        $firstDay = Config::get('main.datepicker_first_day');
        $buf = '<input class="date_picker" type="text" name="' . $name . '" value="' . $value . '" size="' . $size . '" id="' . $name . '" ' . $required . ' />';
        $buf .= "<script>$('#$name').datetimepicker({ampm: true, dateFormat: 'dd/mm/yy', changeMonth: true, changeYear: true, firstDay: $firstDay});$('#$name').keyup( function(event) { $(this).val('');}); </script>";
        return $buf;
    }

    public static function timePicker($name, $value = null, $size = null, $required = null)
    {
        $buf = '<input class="date_picker" type="text" name="' . $name . '" value="' . $value . '" size="' . $size . '" id="' . $name . '" ' . $required . ' />';
        $buf .= "<script>$('#$name').timepicker({ampm: true, dateFormat: 'dd/mm/yy'});$('#$name').keyup( function(event) { $(this).val('');}); </script>";
        return $buf;
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

        $buffer = "";

        // Set up shell layout
        $buffer .= "<div class='row-fluid small-12 multicolform'>";
        $buffer .= "<div class='row-fluid'>";
        foreach ($data as $section => $rows) {
            $buffer .= "<div class='item " . toSlug($section) . "'><div class='panel'><h4>{$section}</h4><table>";
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

                    // Exploit HTML5s inbuilt form validation
                    $required = null;
                    if (!empty($validation[$name])) {
                        if (in_array("required", $validation[$name])) {
                            $required = "required";
                        }
                    }

                    //                $buffer .= "<li class='display-row'>";

                    // Add title field
                    $buffer .= "<tr class='" . toSlug($title) . "' >";
                    if (!empty($title)) {
                        $buffer .= "<td class='small-6 large-4'>{$title}</td>";
                    }

                    $buffer .= "<td class='small-6 large-8 type_" . toSlug($type) . "'>{$value}</td></tr>";
                }
            }
            $buffer .= "</table></div></div>";
        }
        $buffer .= "</div></div>";

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
    public static function multiColForm($data, $action = null, $method = "POST", $submitTitle = "Save", $id = null, $class = null, $extrabuttons = null, $target = "_self", $includeFormTag = true, $validation = null)
    {
        if (empty($data)) {
            return;
        }

        $buffer = "";
        $form = new \Html\form();

        // If form tag is needed print it
        if ($includeFormTag) {
            $class .= " small-12 columns";
            $form->id($id)->name($id)->setClass($class)->method($method)->action($action)->target($target);

            if (in_multiarray("file", $data) || objectPropertyHasValueInMultiArray("\Html\Form\InputField", "type", "file", $data)) {
                $form->enctype("multipart/form-data");
            }

            $buffer .= $form->open();
        }

        // Set up shell layout
        $buffer .= "<div class='row-fluid clearfix small-12 multicolform'>";

        // Print internals
        foreach ($data as $section => $rows) {
            // Print section header
            $buffer .= "<div class='panel clearfix'>";
            $buffer .= "<div class='row-fluid clearfix section-header'><h4>{$section}<span style='display: none;' class='changed_status right alert radius label'>changed</span></h4></div>";

            // Loop through each row
            foreach ($rows as $row) {
                // Print each field
                $fieldCount = is_array($row) ? count($row) : 1;
                $buffer .= "<ul class='small-block-grid-1 medium-block-grid-{$fieldCount} section-body'>";

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
                            $buffer .= '<li><label class=\'small-12 columns\'>' . $field->label . ($field->required ? " <small>Required</small>" : "") .
                                $field->__toString() . '</label></li>';
                        } else {
                            $buffer .= $field->__toString();
                        }
                        continue;
                    }

                    $title = !empty($field[0]) ? $field[0] : "";
                    $type = !empty($field[1]) ? $field[1] : "";
                    $name = !empty($field[2]) ? $field[2] : "";
                    $value = (key_exists(3, $field) && ($field[3] === 0 || !empty($field[3]))) ? $field[3] : "";

                    // Exploit HTML5s inbuilt form validation
                    $required = null;
                    if (!empty($validation[$name])) {
                        if (in_array("required", $validation[$name])) {
                            $required = "required";
                            $title .= ' <small>Required</small>';
                        }
                    }

                    $readonly = "";

                    $buffer .= ($type !== "hidden" ? "<li>" : "");

                    // Add title field
                    if (!empty($title) && $type !== "hidden") {
                        $buffer .= "<label class='small-12 columns'>$title";
                        if (!empty($tooltip)) {
                            $buffer .= " <span data-tooltip aria-haspopup='true' class='has-tip fi-info' title='" . $tooltip . "'></span>";
                        }
                    }
                    $buffer .= ($type !== "hidden" ? "<div>" : "");

                    // handle disabled fields
                    if (substr(($name ?? ""), 0, 1) == '-') {
                        $name = substr(($name ?? ""), 1);
                        $readonly = " readonly='true' ";
                    }

                    switch ($type) {
                        case "text":
                        case "password":
                        case "email":
                        case "tel":
                            $size = !empty($field[4]) ? $field[4] : null;
                            $buffer .= '<input' . $readonly . ' style="width:100%;" type="' . $type . '" name="' . $name . '" value="' . htmlspecialchars($value ?? '') .
                                '" size="' . $size . '" id="' . $name . '" ' . $required . " />";
                            break;
                        case "autocomplete":
                            $options = !empty($field[4]) ? $field[4] : null;
                            $minValue = !empty($field[5]) ? $field[5] : 1;
                            $buffer .= Html::autocomplete($name, $options, $value, null, "width: 100%;", $minValue, $required);
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
                            $buffer .= '<textarea' . $readonly . ' style="width:100%; height: auto; " name="' . $name . '" rows="' . $r . '" cols="' . $c .
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
                            $buffer .= Html::radio($name, $group, $value, $defaultValue, $rd_class) . "&nbsp;" . htmlentities($title ?? '');
                            break;
                        case "hidden":
                            $buffer .= '<input type="hidden" name="' . $name . '" value="' . htmlspecialchars($value ?? '') . '" id="' . $name . '"/>';
                            break;
                        case "file":
                            $size = !empty($row[4]) ? $row[4] : null;
                            $buffer .= '<input style="width:100%;"  type="' . $type . '" name="' . $name . '" size="' . $size . '" id="' . $name . '"/>';
                            break;
                        case "multifile":
                            $buffer .= Html::multiFileUpload($name);
                            break;
                    }
                    $buffer .= ($type !== "hidden" ? "</div></label></li>" : "");
                }
                $buffer .= "</ul>";
            }
            $buffer .= "</div>";
        }
        $buffer .= "<script>$(function(){try{\$('.ckeditor').each(function(){CKEDITOR.replace(this)})}catch(err){}});</script>";
        $buffer .= "<script>$(function(){try{\$('.codemirror').each(function(){var editor = CodeMirror.fromTextArea($(this), {lineNumbers: true, mode: 'text/html', matchBrackets: true, viewportMargin: Infinity}); editor.refresh()})}catch(err){}});</script>";

        // Expermiental
        if (strpos($class ?? "", "prompt") !== false) {
            $buffer .= "<script>"
                . "$(function() {"
                . "		var confirmOnPageExit = function (e) {
                                console.log(e);
                                // If we haven't been passed the event get the window.event
                                e = e || window.event;

                                var message = 'You have unsaved changes, are you sure you want to navigate away?';

                                // For IE6-8 and Firefox prior to version 4
                                if (e) {
                                    e.returnValue = message;
                                }

                                // For Chrome, Safari, IE8+ and Opera 12+
                                return message;
                            };"
                . "		$('form.prompt :input').unbind('input');"
                . "		$('form.prompt :input').on('input', function() {"
                . "			window.onbeforeunload = confirmOnPageExit;"
                . "			$(this).closest('form').find('.section-header h4 > .changed_status').show();"
                . "		});"
                . "});"
                . "</script>";
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
     * Creates a checkbox input element
     *
     * @param <type> $name
     * @param <type> $value
     * @return <type>
     */
    public static function checkbox($name, $value, $default_value = '1', $class = null, $required = null)
    {
        $default_value = $default_value === null ? '1' : $default_value;
        $checked = ($value == $default_value ? 'checked = "checked"' : "");
        $buf = "<input type=\"checkbox\" name=\"" . $name . "\" value=\"" . $default_value . "\" $checked  id=\"" . $name . "\" class=\"" . $class . "\" " . $required . " />";
        return $buf;
    }

    /**
     * Creates a radiobutton input element
     *
     * @param <type> $id
     * @param <type> $name
     * @param <type> $value
     * @return <type>
     */
    public static function radio($name, $group, $value, $default_value = '1', $class = null, $required = null)
    {
        $default_value = $default_value === null ? '1' : $default_value;
        $checked = $value == $default_value ? "checked" : "";
        $buf = "<input type=\"radio\" name=\"" . $group . "\" value=\"" . $default_value . "\" $checked  id=\"" . $name . "\" class=\"" . $class . "\" " . $required . " />";
        return $buf;
    }

    /**
     * Create just a single select input widget to be used
     * in a custom form.
     *
     * @param <type> $data
     * @param <type> $value
     * @param <type> $class
     */
    public static function select($name, $items, $value = null, $class = null, $style = null, $allmsg = "-- Select --", $required = null)
    {
        $buf = '<select id="' . $name . '"  name="' . $name . '" class="' . $class . '" style="' . $style . '" ' . $required . '>';
        $buf .= $allmsg ? "<option value=''>" . $allmsg . "</option>" : '';
        if (!empty($items) && is_array($items)) {
            foreach ($items as $item) {
                if (is_scalar($item)) {
                    $selected = $value == $item ? ' selected = "true" ' : "";
                    $buf .= '<option value="' . htmlspecialchars($item) . '"' . $selected . '>' . htmlentities($item) . '</option>';
                } elseif ($item instanceof DbObject) {
                    $selected = $value == $item->getSelectOptionValue() ? ' selected = "true" ' : "";
                    $buf .= '<option value="' . htmlspecialchars($item->getSelectOptionValue() ?? '') . '"' . $selected . '>' . htmlentities($item->getSelectOptionTitle() ?? '') . '</option>';
                } elseif (is_array($item)) {
                    $selected = $value == $item[1] ? ' selected = "true" ' : "";
                    $buf .= '<option value="' . htmlspecialchars($item[1] ?? '') . '"' . $selected . '>' . htmlentities($item[0] ?? '') . '</option>';
                }
            }
        }
        $buf .= '</select>';
        return $buf;
    }

    /**
     * Create a grouped select input widget to be used
     * in a custom form.
     *
     * @param <type> $name: name of the select box, group name is pre-defined as $name.'_group';
     * @param <type> $items: associative array including group=>groupitems pairs;
     * @param <type> $value: current value of option item;
     * @param <type> $groupvalue: current group value of optgroup item;
     */
    public static function groupSelect($name, $items, $value = null, $groupvalue = null, $class = null, $style = null, $allmsg = "-- Select --")
    {
        $buf = '<select id="' . $name . '"  name="' . $name . '" class="' . $class . '" style="' . $style . '">';
        if ($items) {
            $buf .= $allmsg ? "<option value=''>" . $allmsg . "</option>" : '';
            foreach ($items as $groupname => $groupitems) {
                $buf .= '<optgroup label="' . $groupname . '">';
                foreach ($groupitems as $item) {
                    if (is_array($item)) {
                        $selected = ($groupvalue == $groupname && $value == $item[1]) ? ' selected = "true" ' : "";
                        $buf .= '<option value="' . htmlspecialchars($item[1]) . '"' . $selected . '>' . htmlentities($item[0]) . '</option>';
                    } elseif ($item instanceof DbObject) {
                        $selected = ($groupvalue == $groupname && $value == $item->getSelectOptionValue()) ? ' selected = "true" ' : "";
                        $buf .= '<option value="' . htmlspecialchars($item->getSelectOptionValue()) . '"' . $selected . '>' . htmlentities($item->getSelectOptionTitle()) . '</option>';
                    } elseif (is_scalar($item)) {
                        $selected = ($groupvalue == $groupname && $value == $item) ? ' selected = "true" ' : "";
                        $buf .= '<option value="' . htmlspecialchars($item) . '"' . $selected . '>' . htmlentities($item) . '</option>';
                    }
                }
                $buf .= '</optgroup>';
            }
        }
        $buf .= '</select><input type="hidden" value="' . $groupvalue . '" name="' . $name . '_group">';

        $buf .= '<script type="text/javascript">$("#' . $name . ' > optgroup").click(function(){$("[name=' . $name . '_group]").attr("value", $(this).attr("label"));});</script>';

        return $buf;
    }

    /**
     * Create a multi select field using jQuery
     *
     * @param <type> $name
     * @param <type> $items
     * @param <type> $values
     * @param <type> $class
     * @param <type> $style
     * @param <type> $allmsg
     * @return <type>
     */
    public static function multiSelect($name, $items, $values = null, $class = null, $style = null, $allmsg = null)
    {
        $buf = '<select  multiple="multiple" id="' . $name . '"  name="' . $name . '[]" class="' . $class . '" style="' . $style . '">';
        if ($items) {
            foreach ($items as $item) {
                if (is_array($item)) {
                    $selected = $values && in_array($item[1], $values) ? ' selected = "true" ' : "";
                    $buf .= '<option value="' . htmlspecialchars($item[1]) . '"' . $selected . '>' . htmlentities($item[0]) . '</option>';
                } elseif ($item instanceof DbObject) {
                    $selected = $values && in_multiarray($item->getSelectOptionValue(), $values) ? ' selected = "true" ' : "";
                    $buf .= '<option value="' . htmlspecialchars($item->getSelectOptionValue()) . '"' . $selected . '>' . htmlentities($item->getSelectOptionTitle()) . '</option>';
                } elseif (is_scalar($item)) {
                    $selected = $values && in_array($item, $values) ? ' selected = "true" ' : "";
                    $buf .= '<option value="' . htmlspecialchars($item) . '"' . $selected . '>' . htmlentities($item) . '</option>';
                }
            }
        }
        $buf .= '</select>';

        $buf .= "<script>
                jQuery(\"#{$name}\").asmSelect({addItemTarget: 'bottom', removeLabel: '<img src=\"'" . WEBROOT . "'/img/bin_closed.png\" border=\"0\"/>'});
                jQuery(\"#{$name}\").change(function(e, data) { $.fn.colorbox.resize(); });
                </script>";

        return $buf;
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
        if ($minLength == null) {
            $minLength = 1;
        }
        $acp_value = $value;
        if (is_array($options)) {
            $source = "[";
            foreach ($options as $option) {
                if (is_array($option)) {
                    $array_id = (!empty($option[1]) ? $option[1] : $option[0]);
                    $array_value = $option[0];
                    $source .= '{"id":"' . $array_id . '","value":"' . $array_value . '"},';
                    if ($value == $array_id) {
                        $acp_value = $array_value;
                    }
                } elseif (is_a($option, "DbObject")) {
                    $source .= '{"id":"' . htmlentities($option->getSelectOptionValue()) . '","value":"' . htmlentities($option->getSelectOptionTitle()) . '"},';
                    if ($value == $option->getSelectOptionValue()) {
                        $acp_value = $option->getSelectOptionTitle();
                    }
                } elseif (is_object($option)) {
                    // Ima go ahead and assume that option will have id and value parameters
                    $source .= json_encode($option) . ", ";
                } elseif (is_scalar($option)) {
                    $source .= '{"id":"' . $option . '","value":"' . $option . '"},';
                }
            }
            // Remove trailing comma
            $source = substr(($source ?? ""), 0, -1);
            $source .= "]";
        } else {
            $source = "'" . $options . "'";
        }

        $buf = '<input type="hidden" id="' . $name . '"  name="' . $name . '" value="' . $value . '"/>';
        $buf .= '<input type="text" id="acp_' . $name . '"  name="acp_' . $name . '" value="' . $acp_value . '" class="' . $class . '" style="' . $style . '" ' . $required . ' />';
        $buf .= "<script type='text/javascript'>";
        $buf .= '$(function(){
                    $("#acp_' . $name . '").keyup(function(e){
                        if (e.which != 13) {
                            $("#' . $name . '").val("");
                        }
                    });
                    $("#acp_' . $name . '").autocomplete({
                        minLength:' . $minLength . ',
                        source: ' . $source . ',
                        select: function(event,ui){
                            $("#' . $name . '").val(ui.item.id); //acp_' . $name . '(event,ui);
                            selectAutocompleteCallback(event, ui);
                        }
                    });
                });';
        $buf .= "</script>";
        return $buf;
    }

    public static function img($src, $alt = "")
    {
        $buf = '<img border="0" src="' . $src . '" alt="' . $alt . '"/>';
        return $buf;
    }

    /**
     * validates the request parameters according to
     * the rules passed in $valarray. It must be of the
     * following form:
     *
     * array(
     *   "<param-name>" => array("<regexp>","<error message>"),
     *   "<param-name>" => array("<regexp>","<error message>"),
     *   ...
     * )
     *
     * returns an array which contains all produced error
     * messages
     */
    public static function validate($valarray, $values = null)
    {
        if (!$valarray || !sizeof($valarray)) {
            return null;
        }
        $error = [];
        if ($values == null) {
            $values = $_REQUEST;
        }
        foreach ($valarray as $param => $rule) {
            $regex = $rule[0];
            $message = $rule[1];
            $val = trim($values[$param]);
            if (!preg_match("/" . $regex . "/", $val)) {
                $error[] = $message;
            }
        }
        return count($error) > 0 ? $error : null;
    }

    public static function pagination($currentpage, $numpages, $pagesize, $totalresults, $baseurl, $pageparam = "p", $pagesizeparam = "ps", $totalresultsparam = "tr")
    {
        // See functions.php for implementation of isNumber
        // Prepare buffer
        $buf = '';
        if (isNumber($currentpage) && isNumber($numpages) && isNumber($pagesize) && isNumber($totalresults)) {
            // Check that we're within range
            if ($currentpage > 0 && $currentpage <= $numpages && $numpages > 1) {
                $buf = "<ul class='pagination'>";

                // Build pagination links
                for ($page = 1; $page <= $numpages; $page++) {
                    // Check if the current page
                    if ($currentpage == $page) {
                        $buf .= "<li class='current'>";
                    } else {
                        $buf .= "<li>";
                    }

                    $url_parsed = parse_url($baseurl);

                    $url_string = $url_parsed['path'];
                    $url_string .= (empty($url_parsed['query']) ? '?' : '?' . $url_parsed['query'] . '&') . $pageparam . '=' . $page . '&' . $pagesizeparam . '=' . $pagesize . '&' . $totalresultsparam . '=' . $totalresults;
                    $url_string .= (!empty($url_parsed['fragment']) ? '#' . $url_parsed['fragment'] : '');

                    $buf .= '<a href=\'' . $url_string . '\'>' . $page . '</a></li>';
                }

                $buf .= "</ul>";
            }
        }

        return $buf;
    }

    /**
     * Paginated list will build the required HTML to create a paginated list. $list_items is an array
     * of strings containing the HTML to be wrapped into the <li></li> tags.
     *
     * @param array $list_items
     * @param integer $page
     * @param integer $page_size
     * @param integer $total_results
     * @param string $base_url
     * @param string|null $sort
     * @param string $sort_direction
     * @param string $page_query_param
     * @param string $pagesize_query_param
     * @param string $total_results_query_param
     * @param string $sort_query_param
     * @param string $sort_direction_param
     * @return string
     */
    public static function paginatedList(
        array $list_items,
        int $page,
        int $page_size,
        int $total_results,
        string $base_url,
        ?string $sort = null,
        string $sort_direction = "asc",
        string $page_query_param = "page",
        string $pagesize_query_param = "page_size",
        string $total_results_query_param = "total_results",
        string $sort_query_param = "sort",
        string $sort_direction_param = "sort_direction"
    ): string {
        // Build URL for pagination.
        $url_parsed = parse_url($base_url);
        $url_string = $url_parsed["path"];
        $url_string .= (empty($url_parsed["query"]) ? "?" : "?" . $url_parsed["query"] . "&") . $sort_query_param . "=" . $sort . "&" . $sort_direction_param . "=" . $sort_direction;
        $url_string .= (!empty($url_parsed["fragment"]) ? "#" . $url_parsed["fragment"] : "");

        // Generate the table.
        $num_results = $total_results;
        if ($page_size > 0) {
            $num_results = ceil($total_results / $page_size);
        }

        if ($total_results == 0) {
            return '<div class="row-fluid clearfix"><div class="small-12 medium-6 small-text-center medium-text-left columns" style="margin: 5px 0px;">No results found</div></div>';
        }

        $count_items = count($list_items);
        $starting_item = (($page - 1) * $page_size) + 1;
        $buffer = '<div class="row-fluid clearfix">'
            . '<div class="small-12 medium-6 small-text-center medium-text-left columns" style="margin-top: 5px;">Showing ' . $starting_item . ' - ' . ($starting_item + $count_items - 1) . ' of ' . $total_results . '</div>'
            . '<div class="small-12 medium-6 columns">';
        if ($num_results > 0) {
            $buffer .= '<div class="row-fluid clearfix"><span class="small-3 medium-6 columns small-text-center medium-text-right" style="margin-top: 5px;">Page:</span><select onchange="location = this.value;" class="small-9 medium-6 columns right">';
            // Build URL for dropdown pagination.
            $dropdown_url_string = $url_parsed['path'];
            $dropdown_url_string .= (empty($url_parsed['query']) ? "?" : '?' . $url_parsed['query'] . '&') . $sort_query_param . '=' . $sort . '&' . $sort_direction_param . '=' . $sort_direction;

            for ($i = 1; $i <= $num_results; $i++) {
                $buffer .= '<option' . ($i == $page ? ' selected="selected"' : '') . ' value="' . $dropdown_url_string . '&' . $page_query_param . '=' . $i . (!empty($url_parsed['fragment']) ? '#' . $url_parsed['fragment'] : '') . '">' . $i . '</option>';
            }
            $buffer .= '</select></div>';
        }
        $buffer .= "</div></div>"
            . "<ul class='small-block-grid-1 medium-block-grid-2 large-block-grid-6'>";

        foreach ($list_items as $list_item) {
            $buffer .= "<li>$list_item</li>";
        }
        $buffer .= "</ul>";
        $buffer .= '<div class="pagination-centered">' . Html::pagination($page, $num_results, $page_size, $total_results, $url_string, $page_query_param, $pagesize_query_param, $total_results_query_param) . '</div>';

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
            return '<div class="row-fluid clearfix"><div class="small-12 medium-6 small-text-center medium-text-left columns" style="margin: 5px 0px;">No results found</div></div>';
        }

        $count_items = count($data);
        $starting_item = (($page - 1) * $page_size) + 1;
        $buffer = '<div class="row-fluid clearfix">'
            . '<div class="small-12 medium-6 small-text-center medium-text-left columns" style="margin-top: 5px;">Showing ' . $starting_item . ' - ' . ($starting_item + $count_items - 1) . ' of ' . $total_results . '</div>'
            . '<div class="small-12 medium-6 columns">';
        if ($num_results > 0) {
            $buffer .= '<div class="row-fluid clearfix"><span class="small-3 medium-6 columns small-text-center medium-text-right" style="margin-top: 5px;">Page:</span><select onchange="location = this.value;" class="small-9 medium-6 columns right">';
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
            . "<div class='row-fluid clearfix table-responsive'>"
            . "<table class='small-12'>";
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
                    . (is_array($title) ? '<div class="right">'
                        . ($title[0] !== $sort || ($title[0] === $sort && $sort_direction !== 'asc') ? '<a class="sort-ascending" href="' . $sort_asc_string . '"><i class="fi-play sort-icons "></i></a>' : '')
                        . ($title[0] !== $sort || ($title[0] === $sort && $sort_direction !== 'desc') ? '<a class="sort-descending" href="' . $sort_desc_string . '"><i class="fi-play sort-icons"></i></a>' : '')
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
        $buffer .= "</table></div>";
        $buffer .= '<div class="pagination-centered">' . Html::pagination($page, $num_results, $page_size, $total_results, $url_string, $page_query_param, $pagesize_query_param, $total_results_query_param) . '</div>';
        return $buffer;
    }

    /**
     *  Filter function returns formatted form for declaring filters. Data is the same
     *  as how Html::form is used. Filter parameters can be retrieved with Request::string
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

        $form = new \Html\form();
        // If form tag is needed print it
        $form->id($id)->setClass($class)->method($method)->action($action);

        $buffer = "";
        $buffer .= $form->open();

        // Set up vars
        $hidden = "";
        $buffer .= "<fieldset style=\"padding: 0; padding-top: 10px; padding-left: 10px;\">\n";
        $buffer .= "<legend>" . $legend . "</legend>\n";
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
                    $buffer .= '<li><label class=\'small-12 columns\'>' . $row->label . '<div>' . $row->__toString() . '</div></label></li>';
                } else {
                    $buffer .= $row->__toString();
                }
                continue;
            }

            $buffer .= "<li>";

            // Get row parameters
            $title = !empty($row[0]) ? $row[0] : null;
            $type = !empty($row[1]) ? $row[1] : null;
            $name = !empty($row[2]) ? $row[2] : "";
            $value = !empty($row[3]) ? $row[3] : "";

            $readonly = "";

            $required = null;
            if (!empty($validation[$name])) {
                if (in_array("required", $validation[$name])) {
                    $required = "required";
                }
            }

            // handle disabled fields
            if (substr(($name ?? ""), 0, 1) == '-') {
                $name = substr(($name ?? ""), 1);
                $readonly = " readonly='true' ";
            }

            // span entry fields that have no title
            if (!empty($title)) {
                $mediumCols = 9;
                if ($type == "checkbox") {
                    $mediumCols = 6;
                }
                $buffer .= "<div class='small-12 columns'><label>{$title}";
            } else {
                $buffer .= "<div class='small-12'>";
            }

            $size = !empty($row[4]) ? $row[4] : null;

            // Get the input that we need
            switch ($type) {
                case "text":
                case "password":
                    $buffer .= '<input' . $readonly . ' style="width:100%;"  type="' . $type . '" name="' . $name . '" value="' . htmlspecialchars($value ?? '') . '" size="' . (!empty($row[4]) ? $row[4] : null) . '" id="' . $name . '"/>';
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

    public static function listGrid($data, $buttons = [], $perRow = 2)
    {
        if (!is_array($data)) {
            return;
        }

        $buffer = "";
        $mediumPerRow = ($perRow > 1 ? $perRow - 1 : 1);
        $buffer .= "<ul class='listGrid small-block-grid-1 medium-block-grid-$mediumPerRow large-block-grid-$perRow'>";

        // List data items
        foreach ($data as $id => $d) {
            $buffer .= "<li class='grid-list-panel'>";
            $buffer .= "<div class='panel clearfix'><div class='small-12'>";

            if (!empty($d)) {
                // Loop through each line
                foreach ($d as $line) {
                    $buffer .= "<div class='row'><div class='small-12'>";
                    if (!empty($line)) {
                        // If data is an array loop through and print
                        if (is_array($line)) {
                            $row_width = floor(12 / count($line));
                            foreach ($line as $item) {
                                // Make the last item in a line text align right
                                $buffer .= "<div class='small-12 medium-{$row_width} small-text-left " . ($item === end($line) ? "medium-text-right " : "") . "columns'>{$item}</div>";
                            }
                        } else {
                            $buffer .= "<div class='small-12 columns'>{$line}</div>";
                        }
                    }
                    $buffer .= "</div></div>";
                }
            }
            $buffer .= "</div></div>";

            // Add buttons
            if (!empty($buttons[$id])) {
                $buffer .= "<div class='row'>";
                $button_width = floor(12 / count($buttons[$id]));
                $last_increment = 12 - (count($buttons[$id]) * $button_width);
                $button_counter = 0;

                // The code just above and below will perfectly fit any amount of buttons (up to 12) in a row
                // I.e. if there are 7 buttons, the first two will be one column wide but the last 5 will be 2 columns (cool huh?)
                foreach ($buttons[$id] as $b) {
                    $buffer .= "<div class='small-12 left medium-";
                    $buffer .= (++$button_counter > (count($buttons[$id]) - $last_increment) ? ($button_width + 1) : $button_width);
                    $buffer .= ("'>" . $b . "</div>");
                }
                $buffer .= "</div>";
            }

            $buffer .= "</li>";
        }

        $buffer .= "</ul>";

        return $buffer;
    }

    public static function breadcrumbs($data = [], $w = null)
    {
        if (!empty($data)) {
            $buffer = "<ul class='breadcrumbs'>";
            foreach ($data as $entry) {
                $buffer .= "<li" . ($entry !== end($data) ? "><a href='" . $entry['link'] . "'>" . $entry['name'] . "</a>" : " class='current'>" . $entry['name']) . "</li>";
            }
            $buffer .= "</ul>";
            return $buffer;
        } else {
            // Try and make breadcrumbs from the history class
            if (class_exists("History")) {
                $breadcrumbs = History::get();
                $buffer = "<ul class='cmfive_breadcrumbs'>";
                // $buffer .= "<li><i class='fi-clock'></i></li>";
                if (!empty($breadcrumbs)) {
                    $isFirst = true && ($_SERVER['REQUEST_URI'] === key($breadcrumbs));
                    foreach ($breadcrumbs as $path => $value) {
                        if (!empty($w) && $w instanceof Web) {
                            if (!AuthService::getInstance($w)->allowed($path)) {
                                continue;
                            }
                        }
                        $buffer .= "<li" . (!$isFirst ? "><a href='" . $path . "'><span data-tooltip aria-haspopup='true' title='" . $value['name'] . "' ><div class='breadcrumb-content'>" . $value['name'] . "</div></span></a>" : " class='current'><span data-tooltip aria-haspopup='true' title='" . $value['name'] . "' ><div class='breadcrumb-content'>" . $value['name'] . "</div></span>") . "</li>";
                        $isFirst = false;
                    }
                } else {
                    $buffer .= "<li>Your history will appear here</li>";
                }

                $buffer .= "</ul>";
                return $buffer;
            }
        }
    }

    /**
     * Creates a view for uploading multiple files at the same time
     * Uses the $name parameter to distinguish between multiple
     * instances on one page.
     *
     * @param <String> $name
     * @return <String> buffer
     */
    public static function multiFileUpload($name)
    {
        $buffer = <<<UPLOAD
            <div id='multiFileUpload_{$name}'>
                <div id='{$name}_file_0' class='row-fluid clearfix multiFileUploadRow'>
                    <div class="medium-4 columns">
                        <label>
                            <input type='file' name='{$name}[0][file]' id='{$name}[0][file]' />
                        </label>
                    </div>
                    <div class="medium-6 columns">
                        <label>
                            <input type='text' name='{$name}[0][description]' id='{$name}[0][description]' placeholder='Description' />
                        </label>
                    </div>
                    <div class="medium-2 columns">
                        <label>
                            <button class='button tiny' onclick='$(this).parent().remove(); return false;' type='button'>Remove</button>
                        </label>
                    </div>
                </div>
            </div>
            <button type='button' class='button tiny' style='margin-top: 10px;' id='{$name}_addNewFile'>Add another file</button>
            <script type='text/javascript'>
                var {$name}_total_files = 1;
                $("#{$name}_addNewFile").click(function() {
                    $("#multiFileUpload_{$name}").append("<div id='{$name}_file_" + {$name}_total_files + "' class='row-fluid clearfix multiFileUploadRow'><div class='medium-4 columns'><label><input type='file' name='{$name}[" + {$name}_total_files + "][file]' id='{$name}[" + {$name}_total_files + "][file]' /></label></div><div class='medium-6 columns'><label><input type='text' name='{$name}[" + {$name}_total_files + "][description]' id='{$name}[" + {$name}_total_files + "][description]' placeholder='Description' /></label></div><div class='medium-2 columns'><label><button onclick='$(this).parent().remove(); return false;' type='button' class='button tiny'>Remove</button></label></div></div>");
                    {$name}_total_files++;
                });
            </script>
UPLOAD;
        return $buffer;
    }

    /**
     * Returns embed iframe for displaying pdfs and other documents in the browser
     *
     * @param string $link
     * @param string $width
     * @param string $height
     * @return string
     */
    public static function embedDocument(string $link, $width = '1024', $height = '724', $zoom = 'page-width', $is_docx = false)
    {
        return "<object style='min-height: 600px float: none; display: inline;' data='{$link}#view=FitH&navpanes=0' width='100%' height='$height' loading='lazy' allow='fullscreen'></object>";
    }

    /**
     * Display a message inside an alert box.
     *
     * @param string $msg
     * @param string $class
     * @return string
     */
    public static function alertBox($msg, $type = "info"): string
    {
        if ($type !== "info" && $type !== "warning" && $type !== "alert" && $type !== "success") {
            $type = "info";
        }

        return "<div data-alert class='alert alert-box {$type}'>{$msg}<a href='#' class='close'>&times;</a></div>";
    }

    /**
     * Strips all HTML tags that aren't in the allow list.
     *
     * @param string $s
     * @return string
     */
    public static function sanitise(string $string): string
    {
        return strip_tags($string, "<a><blockquote><em><div><h1><h2><h3><h4><h5><h6><li><ol><p><strong><s><u><ul>");
    }
}
