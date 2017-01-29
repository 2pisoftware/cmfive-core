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
	public static function formConfig($type,$metaData,$w) {
		//print_r([$type,$metaData]);
		$options=[];
		if ($type=="autocomplete" || $type=="select")  {
			if (!empty($metaData['object_type'])) {
				try {
					$service = new DbService($w);
					$filter='';
					// eg {"login like ?": "%e%"}
					if (!empty($metaData['object_filter'])) {
						try {
							$filter=json_decode($metaData['object_filter'],true);
						} catch (Exception $e) {
							// fallback to following test
						}
						if (!is_array($filter)) {
							$filter=$metaData['object_filter'];
						}						
					}
					$options=$service->getObjects($metaData['object_type'],$filter);
					//foreach ($options as $option) {
					//	$options[]=$option->getSelectOptionTitle();
					//}
				} catch (Exception $e) {
					//silently fail no options
				}
			} else if (!empty($metaData['options'])) {
				$options=explode(",",$metaData['options']);
				foreach ($options as $k=>$option) {
					if (is_int($k)) {
						$options[$k]=[$option,$k+1];
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
	public static function metadataForm($type) {
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
	public static function modifyForDisplay($type, $value, $metadata = null,$w) {
		if (!static::doesRespondTo($type)) {
			return $value;
		}
		
		// Alter value based on type
		switch (strtolower($type)) {
			case "decimal":
				$decimal_places = self::getMetadataForKey($metadata, "decimal_places");
				if (!empty($decimal_places->id)) {
					return round($value, $decimal_places->meta_value);
				} else {
					return $value * 1.0;
				}
			case "autocomplete":
			case "select":
				return static::modifyAutocompleteForDisplay($value,$metadata,$w);
			case "date":
				return date("d/m/Y", $value);
			case "datetime":
				return date("d/m/Y H:i:s", $value);
			default:
				return $value;
		}
	}
	
	public static function modifyAutocompleteForDisplay($value,$metadataObjects,$w) {
		if (is_array($metadataObjects)) {
			$metaData=[];
			foreach ($metadataObjects as $meta) {
				$metaData[$meta->meta_key] = $meta->meta_value;
			}
			// DB LOOKUP
			if (!empty($metaData['object_type'])) {
				try {
					$service = new DbService($w);
					$filter='';
					// eg {"login like ?": "%e%"}
					if (!empty($metaData['object_filter'])) {
						try {
							$filter = json_decode($metaData['object_filter'],true);
						} catch (Exception $e) {
							// silent fallback to following test
							//echo $e->getMessage();
						}
						if (!is_array($filter)) {
							$filter = $metaData['object_filter'];
						}	
					}
					$options = $service->getObjects($metaData['object_type'],$filter);
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
				$options=explode(",",$metaData['options']);
				foreach ($options as $k=>$option) {
					if ($k+1==$value) {
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
	public static function modifyForPersistance($type, $value) {
		if (!static::doesRespondTo($type)) {
			return $value;
		}
		
		// Alter value based on type
		switch (strtolower($type)) {
			case "date":
			case "datetime":
				return strtotime(str_replace("/", "-", $value));
			default:
				return $value;
		}
	}

}
