<?php

class FormAdditionalFieldsInterface extends FormFieldInterface {

	protected static $_respondsTo = [
		["LatLong", "latlong"],
		["Unique ID", "unique_id"],
		["Attachment", "attachment"],
		["Subform", "subform"],
		["Yes/No", 'boolean'],
		["Multiple Value", "multivalue"]
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
			case "subform":
				return "hidden";
			case "boolean":
				return "checkbox";
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
		// if (!static::doesRespondTo($type)) {
		// 	return null;
		// }

		// switch($type) {
		//  default:
		// 		return [];
		// }

		return [];
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

		switch($type) {
			case "subform":
				return VueComponentRegister::getComponent('metadata-subform');
			case "multivalue":
				return [['Delimiter', 'text', 'delimiter']];
			default:
				return null;
		}

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
						$output .= (new \Html\a())->href($attachment->getViewUrl())->text('View ' . $attachment->title)->target('_blank')->setClass('block-link abbreviated-link')->setTitle($attachment->title);
					}
				}

				return $output;
			case "subform":
				return Html::box('/form-field/manage_subform/' . $form_value->id . '?display_only=1', 'View ' . $field->name, false, false, null, null, null, null, 'block-link') . 
					   Html::a('/form-field/manage_subform/' . $form_value->id, 'Manage ' . $field->name, null, 'block-link', null, "_blank");

				break;
			case "boolean":
				return $form_value->value == 1 ? "Yes" : ($form_value->value !== null ? "No" : "");
			case "multivalue":
				$delimiter = ',';
				if (!empty($metadata)) {
					foreach($metadata as $_meta) {
						if ($_meta->meta_key == 'delimiter') {
							$delimiter = $_meta->meta_value;
							break;
						}
					}
				}

				return str_replace($delimiter, '<br/>', $form_value->value);
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

		switch (strtolower($field->type)) {
			case "attachment":
				// Upload attachment if FILES superglobal has an entry for the field
				if (array_key_exists($field->technical_name, $_FILES)) {
					// Upload attachment to form value object
					$attachment_id = $form_value->w->File->uploadAttachment($field->technical_name, $form_value);
					if (!empty($attachment_id)) {
						// Append attachment ID so a link to the attachment will be displayed
						$form_value->value .= (!empty($form_value->value) ? ',' : '') . $attachment_id;
					}
				}
				return $form_value->value;
			case "boolean":
				if ($_SERVER['REQUEST_METHOD'] == 'POST') {
					if (array_key_exists($field->name, $_POST))	{
						$form_value->value = 1;
					} else {
						$form_value->value = 0;
					}
				}
				if ($form_value->value != 1 || $form_value->value != 0) {
					$form_value->value = !!$form_value->value;
				} 
				return $form_value->value;
			default:
				return $form_value->value;
		}

		return $form_value->value;	
	}

}