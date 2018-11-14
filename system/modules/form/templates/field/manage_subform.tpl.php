<?php if (!empty($error_message)) : ?>
	<div data-alert class="alert-box warning radius"><?php echo $error_message; ?></div>
<?php else:
	echo $w->partial("listform", ["form" => $subform, "redirect_url" => '/form-field/manage_subform/' . $form_value->id, 'object' => $form_value, 'display_only' => $display_only], "form"); 
endif;