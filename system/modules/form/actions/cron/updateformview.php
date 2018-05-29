<?php

function updateformview_ALL(Web $w) {

	$forms = $w->Form->getForms();

	if (!empty($forms)) {
		foreach ($forms as $form) {
			$settings = 'SET SESSION group_concat_max_len = 1000000';
			$set = $w->db->query($settings)->execute();

			$form_id = $form->id;
			$query = 'SELECT CONCAT("SELECT form_instance.id as instance_id, form_instance.form_id as form_id, form_instance.object_class as object_class, form_instance.object_id as object_id, CASE WHEN form_instance.object_class = ""FormValue"" THEN (SELECT fv.form_instance_id FROM form_value fv WHERE fv.id = form_instance.object_id) ELSE NULL END as parent_instance_id, ", GROUP_CONCAT("t_", form_field.technical_name, ".value AS ", form_field.technical_name), " FROM form LEFT JOIN form_instance ON (form_instance.form_id = form.id AND form_instance.is_deleted = 0) ", GROUP_CONCAT("LEFT JOIN form_value AS t_", form_field.technical_name, " ON (form_instance.id = t_", form_field.technical_name, ".form_instance_id AND t_", form_field.technical_name, ".form_field_id = ", QUOTE(form_field.id), ")" SEPARATOR " "  ), " WHERE form.id = ' . $form_id .' ") as "view_query" FROM form_field WHERE form_id = ' . $form_id;

			$result = $w->db->query($query)->fetch(PDO::FETCH_ASSOC);

			$view_name = str_replace(' ', '_', $form->title) . '_view';
			$query2 = 'CREATE OR REPLACE VIEW ' . $view_name . ' AS ' . $result['view_query'];

			$view = $w->db->query($query2)->execute();
		}
	}
}
