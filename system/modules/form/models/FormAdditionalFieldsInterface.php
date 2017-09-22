<?php

class FormAdditionalFieldsInterface extends FormFieldInterface {

	protected static $_respondsTo = [
		["LatLong", "latlong"]
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
			case "latlong":
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
		return [];
	}

	/**
	 * Provide form row definition array for metadata associated with 
	 * this type
	 * 
	 * @return [[$name,$type,$field]]
	 */
	public static function metadataForm($type) {
		// if (!static::doesRespondTo($type)) {
		// 	return null;
		// }

		return null;
	}

	/**
	 * Transform a value into a format useful for presentation based on its type.
	 * 
	 * Decimal types are rounded.
	 * Date types are presented in Australian date format.
	 * 
	 * @return string
	 */
	public static function modifyForDisplay($type, $value, $metadata = null, $w) {
		// if (!static::doesRespondTo($type)) {
		// 	return $value;
		// }

		return $value;
	}

	/**
	 * Transform date values into a format useful for DbObject based
	 * persistence.
	 * 
	 * @return string
	 */
	public static function modifyForPersistance($type, $value) {
		// if (!static::doesRespondTo($type)) {
		// 	return $value;
		// }

		return $value;	
	}

}