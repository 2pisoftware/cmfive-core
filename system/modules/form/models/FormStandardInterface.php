<?php

/**
 * This class provides a base implementation of FormFieldInterface.
 * The logic for rendering and processing of field types is handled here.
 * Currently only date, datetime, text and decimal field types are supported.
 */
class FormStandardInterface extends FormFieldInterface
{
    protected static $_respondsTo = [
        ["Text", "text"],
        ["Text Area", "textarea"],
        ["Decimal", "decimal"],
        ["Email", "email"],
        ["Phone Number", "tel"],
        ["Date", "date"],
        ["Date & Time", "datetime"],
        ["Time", "time"],
        ["Select", "select"],
        ["Autocomplete", "autocomplete"]
    ];

    /**
     * Map FormField type to HtmlBootstrap5::multiColForm() type
     *
     * @return string
     */
    public static function formType($type)
    {
        if (!static::doesRespondTo($type)) {
            return null;
        }

        switch (strtolower($type)) {
            case "date":
                return "date";
            case "datetime":
                return "datetime";
            case "time":
                return "time";
            case "autocomplete":
                return "autocomplete";
            case "select":
                return "select";
            case "textarea":
                return "textarea";
            case "email":
                return "email";
            case "tel":
                return "tel";
            case "decimal":
            case "text":
            default:
                return "text";
        }
    }

    /**
     * Map Form metadata to an array of extra parameters to HtmlBootstrap5::multiColForm()
     *
     * @return []
     */
    public static function formConfig($type, $metadata, $w)
    {
        $options = [];
        if ($type == "autocomplete" || $type == "select") {
            if (!empty($metadata['object_type'])) {
                try {
                    $service = new DbService($w);
                    $filter = '';
                    // eg {"login like ?": "%e%"}
                    if (!empty($metadata['object_filter'])) {
                        try {
                            $filter = json_decode($metadata['object_filter'], true);
                        } catch (Exception $e) {
                            // fallback to following test
                        }
                        if (!is_array($filter)) {
                            $filter = $metadata['object_filter'];
                        }
                    }
                    $options = $service->getObjects($metadata['object_type'], $filter);
                } catch (Throwable $e) {
                    //silently fail no options
                }
            } elseif (!empty($metadata['options'])) {
                $options = explode(",", $metadata['options']);
                foreach ($options as $k => $option) {
                    if (is_int($k)) {
                        $options[$k] = [$option, $k + 1];
                    }
                }
            } elseif (!empty($metadata['user_rows'])) {
                foreach ($metadata['user_rows'] as $index => $user_row) {
                    $options[$index] = [$user_row['value'], $user_row['key']];
                }
            }
        }
        return [$options];
    }

    /**
     * Provide form row definition array for metadata associated with
     * this type
     *
     * @return [[$name,$type,$field]]
     */
    public static function metadataForm($type, Web $w)
    {
        if (!static::doesRespondTo($type)) {
            return null;
        }

        switch (strtolower($type)) {
            case "decimal":
                return [["Decimal Places", "text", "decimal_places"]];
            case "autocomplete":
                return [["Object", "text", "object_type"], ["Filter", "text", "object_filter"], ["Options", "text", "options"]];
            case "select":
                return VueComponentRegister::getComponent('metadata-select');
                // return [["Object", "text", "object_type"],["Filter", "text", "object_filter"],["Options", "text", "options"]];
            default:
                return null;
        }
    }

    /**
     * Transform a value into a format useful for presentation based on its type.
     *
     * Decimal types are rounded.
     * Date types are presented in Australian date format.
     *
     * @return string
     */
    public static function modifyForDisplay(FormValue $form_value, $w, $metadata = null)
    {
        $field = $form_value->getFormField();

        if (!static::doesRespondTo($field->type)) {
            return $form_value->value;
        }

        // Alter value based on type
        switch (strtolower($field->type)) {
            case "decimal":
                $decimal_places = self::getMetadataForKey($metadata, "decimal_places");
                if (!empty($decimal_places->id)) {
                    return round($form_value->value, intval($decimal_places->meta_value));
                } else {
                    return $form_value->value * 1.0;
                }
            case "autocomplete":
            case "select":
                return static::modifyAutocompleteForDisplay($form_value->value, $metadata, $w);
            case "date":
                return !empty($form_value->value) ? formatDate($form_value->value, "d/m/Y") : $form_value->value;
            case "datetime":
                return !empty($form_value->value) ? formatDateTime($form_value->value, "d/m/Y H:i:s") : $form_value->value;
            case "time":
                return !empty($form_value->value) ? formatTime($form_value->value) : $form_value->value;
            default:
                return $form_value->value;
        }
    }

    public static function modifyAutocompleteForDisplay($value, $metadataObjects, $w)
    {
        if (is_array($metadataObjects)) {
            $metadata = [];
            foreach ($metadataObjects as $meta) {
                $metadata[$meta->meta_key] = $meta->meta_value;
            }
            // DB LOOKUP
            if (!empty($metadata['object_type'])) {
                try {
                    $filter = '';

                    // eg {"login like ?": "%e%"}
                    if (!empty($metadata['object_filter'])) {
                        try {
                            $filter = json_decode($metadata['object_filter'], true);
                        } catch (Exception $e) {
                            // silent fallback to following test
                            //echo $e->getMessage();
                        }
                        if (!is_array($filter)) {
                            $filter = $metadata['object_filter'];
                        }
                    }

                    // Check if defined class exists and extends DbObject
                    if (!class_exists($metadata['object_type']) || !is_subclass_of($metadata['object_type'], 'DbObject')) {
                        return null;
                    }
                    $options = FormService::getInstance($w)->getObjects($metadata['object_type'], $filter);
                    foreach ($options as $option) {
                        if ($option->id == $value) {
                            return $option->getSelectOptionTitle();
                        }
                    }
                } catch (Exception $e) {
                    //silently fail no options
                    //echo $e->getMessage();
                }
                // CSV OPTIONS
            } elseif (!empty($metadata['options'])) {
                $options = explode(",", $metadata['options']);
                foreach ($options as $k => $option) {
                    if ($k + 1 == $value) {
                        return $option;
                    }
                }
            } elseif (!empty($metadata['user_rows'])) {
                foreach ($metadata['user_rows'] as $user_row) {
                    if ($value == $user_row['key']) {
                        return $user_row['value'];
                    }
                }
            } else {
                // missing metadata no object_type OR options
            }
        } else {
            // missing metadata
        }
    }


    /**
     * Transform date values into a format useful for DbObject based
     * persistence.
     *
     * @return string
     */
    public static function modifyForPersistance(FormValue $form_value)
    {
        $field = $form_value->getFormField();

        if (!static::doesRespondTo($field->type)) {
            return $form_value->value;
        }

        // Alter value based on type
        switch (strtolower($field->type)) {
            case "select":
                $meta_data_array = [];
                $meta_data = $field->getMetaData() ?? [];

                foreach ($meta_data as $m) {
                    if ($m->meta_key === "user_rows") {
                        $meta_data_array = $m->meta_value;
                    }
                }

                foreach ($meta_data_array as $md) {
                    if ($md["value"] === $form_value->value) {
                        return $md["key"];
                    } elseif ($md["key"] === $form_value->value) {
                        return $md["key"];
                    }
                }
                break;
            case "date":
            case "datetime":
            case "time":
                return strtotime(str_replace("/", "-", $form_value->value));
            default:
                return $form_value->value;
        }
    }
}
