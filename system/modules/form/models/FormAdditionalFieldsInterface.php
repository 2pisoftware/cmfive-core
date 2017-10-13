<?php

class FormAdditionalFieldsInterface extends FormFieldInterface {

	protected static $_respondsTo = [
		["LatLong", "latlong"],
		["Unique ID", "unique_id"],
		["Attachment", "attachment"],
		["Subform", "subform"]
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
				return [['Associated Form', 'select', 'associated_form', null, $w->Form->getForms()]];
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
						$output .= Html::a($attachment->getViewUrl(), 'View ' . $attachment->title, null, null, null, "_blank");
					}
				}

				return $output;
			case "subform":
				// $form = $w->Form->getForm($form_value->value);

				// if (!empty($form->id)) {
				// 	$num_instances = $form->countFormInstancesForObject($form_value);
				// 	return $num_instances . ' ' . $form_value->title . '(s)';
				// }

				return Html::box('/form-field/manage_subform/' . $form_value->id . '?display_only=1', 'View ' . $field->name) . '<br/>' .
						Html::a('/form-field/manage_subform/' . $form_value->id, 'Manage ' . $field->name, null, null, null, "_blank");

				break;
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
			default:
				return $form_value->value;
		}

		return $form_value->value;	
	}

}