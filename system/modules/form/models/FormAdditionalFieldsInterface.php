<?php

class FormAdditionalFieldsInterface extends FormFieldInterface {

	protected static $_respondsTo = [
		["LatLong", "latlong"],
		["Unique ID", "unique_id"],
		["Attachment", "attachment"]
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
		
		switch (strtolower($type)) {
			case "attachment":
				return "file";
			case "unique_id":
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
	public static function modifyForDisplay(FormValue $form_value, $w, $metadata = null) {
		$field = $form_value->getFormField(); 

		if (!static::doesRespondTo($field->type)) {
			return $form_value->value;
		}

		switch (strtolower($field->type)) {
			case "attachment":
				$output = '';

				// Get attachments - value should be the actual FormValue object
				$attachments = $w->File->getAttachments($form_value);
				if (!empty($attachments)) {
					foreach($attachments as $attachment) {
						$output .= Html::a($attachment->getViewUrl(), 'View ' . $attachment->title, null, null, null, "_blank");
					}
				}

				return $output;
			default:
				return $form_value->value;
		}

		return $form_value->value;
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

		// switch (strtolower($field->type)) {
		// 	case "attachment":
		// 		return $form_value->value;
		// 	default:
		// 		return $form_value->value;
		// }

		return $form_value->value;	
	}

}