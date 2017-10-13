<?php
/**
 * This class provides a base implementation of FormFieldInterface.
 * The logic for rendering and processing of field types is handled here.
 * Currently only date, datetime, text and decimal field types are supported.
 */
class FormStandardInterface extends FormFieldInterface {
	
	protected static $_respondsTo = [
		["Text", "text"],
		["Decimal", "decimal"],
		["Date", "date"],
		["Date & Time", "datetime"],
		["Time", "time"],
		["Select", "select"],
		["Autocomplete", "autocomplete"]
	];
	
	/**
	 * Map FormField type to Html::multiColForm() type
	 * 
	 * @return string
	 */
	public static function formType($type) {
		if (!static::doesRespondTo($type)) {
			return null;
		}
		
		switch(strtolower($type)) {
			case "date": 
				return "date"; 
			case "datetime":
			case "time":
				return "datetime";
			case "autocomplete":
				return "autocomplete";
			case "select":
				return "select";
			case "decimal":
			case "text":
			default:
				return "text";
		}
		return null;
	}
	
	/**
	 * Map Form metadata to an array of extra parameters to Html::multiColForm() 
	 * 
	 * @return []
	 */
	public static function formConfig($type, $metaData, $w) {
		$options = [];
		if ($type == "autocomplete" || $type == "select")  {
			if (!empty($metaData['object_type'])) {
				try {
					$service = new DbService($w);
					$filter = '';
					// eg {"login like ?": "%e%"}
					if (!empty($metaData['object_filter'])) {
						try {
							$filter = json_decode($metaData['object_filter'], true);
						} catch (Exception $e) {
							// fallback to following test
						}
						if (!is_array($filter)) {
							$filter = $metaData['object_filter'];
						}						
					}
					$options = $service->getObjects($metaData['object_type'], $filter);
				} catch (Exception $e) {
					//silently fail no options
				}
			} else if (!empty($metaData['options'])) {
				$options = explode(",", $metaData['options']);
				foreach ($options as $k => $option) {
					if (is_int($k)) {
						$options[$k] = [$option, $k + 1];
					}
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
	public static function metadataForm($type, Web $w) {
		if (!static::doesRespondTo($type)) {
			return null;
		}
		
		switch(strtolower($type)) {
			case "decimal":
				return [["Decimal Places", "text", "decimal_places"]];
			case "autocomplete":
				return [["Object", "text", "object_type"],["Filter", "text", "object_filter"],["Options", "text", "options"]];
			case "select":
				return [["Object", "text", "object_type"],["Filter", "text", "object_filter"],["Options", "text", "options"]];
			
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
	public static function modifyForDisplay(FormValue $form_value, $w, $metadata = null) {
		$field = $form_value->getFormField(); 
		
		if (!static::doesRespondTo($field->type)) {
			return $form_value->value;
		}
		
		// Alter value based on type
		switch (strtolower($field->type)) {
			case "decimal":
				$decimal_places = self::getMetadataForKey($metadata, "decimal_places");
				if (!empty($decimal_places->id)) {
					return round($form_value->value, $decimal_places->meta_value);
				} else {
					return $form_value->value * 1.0;
				}
			case "autocomplete":
			case "select":
				return static::modifyAutocompleteForDisplay($form_value->value, $metadata, $w);
			case "date":
				return (!empty($form_value->value) ? formatDate($form_value->value, "d/m/Y") : $form_value->value);
			case "datetime":
				return (!empty($form_value->value) ? formatDateTime($form_value->value, "d/m/Y H:i:s") : $form_value->value);
			case "time":
				return (!empty($form_value->value) ? formatTime($form_value->value) : $form_value->value);
			default:
				return $form_value->value;
		}
	}
	
	public static function modifyAutocompleteForDisplay($value, $metadataObjects, $w) {
		if (is_array($metadataObjects)) {
			$metaData = [];
			foreach ($metadataObjects as $meta) {
				$metaData[$meta->meta_key] = $meta->meta_value;
			}
			// DB LOOKUP
			if (!empty($metaData['object_type'])) {
				try {
					$filter = '';
					
					// eg {"login like ?": "%e%"}
					if (!empty($metaData['object_filter'])) {
						try {
							$filter = json_decode($metaData['object_filter'], true);
						} catch (Exception $e) {
							// silent fallback to following test
							//echo $e->getMessage();
						}
						if (!is_array($filter)) {
							$filter = $metaData['object_filter'];
						}	
					}
					$options = $w->Form->getObjects($metaData['object_type'], $filter);
					foreach ($options as $option) {
						if ($option->id == $value)  {
							return $option->getSelectOptionTitle();
						}
					}
				} catch (Exception $e) {
					//silently fail no options
					//echo $e->getMessage();
				}
			// CSV OPTIONS
			} else if (!empty($metaData['options'])) {
				$options=explode(",", $metaData['options']);
				foreach ($options as $k => $option) {
					if ($k + 1 == $value) {
						return $option;
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
	public static function modifyForPersistance(FormValue $form_value) {
		$field = $form_value->getFormField(); 
		
		if (!static::doesRespondTo($field->type)) {
			return $form_value->value;
		}
		
		// Alter value based on type
		switch (strtolower($field->type)) {
			case "date":
			case "datetime":
			case "time":
				return strtotime(str_replace("/", "-", $form_value->value));
			default:
				return $form_value->value;
		}
	}

}
