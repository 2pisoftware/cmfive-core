<form action='/timelog/edit/<?php echo !empty($timelog->id) ? $timelog->id : ''; ?><?php echo $redirect ? '?redirect=' . $redirect : ''; ?>' method='POST' name='timelog_edit_form' target='_self' id='timelog_edit_form' class=' small-12 columns'  >
	<div class="row-fluid clearfix small-12 multicolform">
		
		<div class="panel clearfix">
			<div class="small-12 columns section-header">
				<h4><?php echo (!empty($timelog->id)) ? "Update" : "Create"; ?> timelog</h4>
			</div>
			<ul class="small-block-grid-1 medium-block-grid-1 section-body">
				<li>
					<label class="small-12 columns">Assigned User
						<?php if ($w->Auth->user()->is_admin) {
							echo (new \Html\Form\Autocomplete([
								"id|name"	=> "user_id",
								"title"		=> empty($timelog->id) ? $w->Auth->user()->getFullName() : (!empty($timelog->user_id) ? $w->Auth->getUser($timelog->user_id)->getFullName() : null),
								"value"		=> empty($timelog->id) ? $w->Auth->user()->id : (!empty($timelog->user_id) ? $timelog->user_id : null)
							]))->setOptions($w->Auth->getUsers());
						} else {
							echo (new \Html\Form\InputField\Hidden([
								"name"		=> "user_id",
								"value"		=> empty($timelog->id) ? $w->Auth->user()->id : $timelog->user_id
							]));
						} ?>
					</label>
				</li>
			</ul>
			<ul class="small-block-grid-1 medium-block-grid-2 section-body">
				<li>
					<label class="small-12 columns">Module
						<?php echo (new \Html\Form\Select([
							"id|name"			=> "object_class",
							"selected_option"	=> $timelog->object_class ? : $tracking_class ?: key(reset($select_indexes)),
							"options"			=> $select_indexes
						])); ?>
					</label>
				</li>
				<li>
					<label class="small-12 columns">Search
						<?php 
						$usable_class = !empty($timelog->object_class) ? $timelog->object_class : (!empty($tracking_class) ? $tracking_class : (key(reset($select_indexes))));
						$where_clause = [];
						if (!empty($usable_class)) {
							if (in_array('is_deleted', (new $usable_class($w))->getDbTableColumnNames())) {
								$where['is_deleted'] = 0;
							}
						}
						
						echo (new \Html\Form\Autocomplete([
							"id|name"		=> "search",
							"title"			=> !empty($object) ? $object->getSelectOptionTitle() : null,
							"value"			=> !empty($timelog->object_id) ? $timelog->object_id : $tracking_id,
							"required"		=> "true"
						]))->setOptions(!empty($usable_class) ? $w->Timelog->getObjects($usable_class, $where) : ''); ?>
					</label>
				</li>
				<?php echo (new \Html\Form\InputField(["type" => "hidden", "id|name" => "object_id", "value" => $timelog->object_id ? : $tracking_id])); ?>
			</ul>
			<ul class="small-block-grid-1 medium-block-grid-2 section-body">
				<li>
					<label class="small-12 columns">Date
						<?php echo (new \Html\Form\InputField\Date([
							"id|name"		=> "date_start",
							"value"			=> $timelog->getDateStart(),
							"required"		=> "true"
						])); ?>
					</label>
				</li>
				<li>
					<label class="small-12 columns">Time Started
						<?php echo(new \Html\Form\InputField([
							"id|name"		=> "time_start",
							"value"			=> $timelog->getTimeStart(),
							"pattern"		=> "^(0?[0-9]|1[0-9]|2[0-3]):[0-5][0-9](\s+)?(AM|PM|am|pm)?$",
							"placeholder"	=> "12hr format: 11:30pm or 24hr format: 23:30",
							"required"		=> "true"
						])); ?>
					</label>
				</li>
			</ul>
			<?php if (!$timelog->isRunning()) : ?>
				<ul class="small-block-grid-1 medium-block-grid-2 section-body">
					<li>
						<div class="row-fluid clearfix">
							<div class="small-2 medium-1 columns">
								<?php echo (new \Html\Form\InputField\Radio([
									"name"		=> "select_end_method",
									"value"		=> "time",
									"class"		=> "right",
									"style"		=> "margin-top: 20px;",
									"checked"	=> "true",
									"tabindex"	=> -1
								])); ?>
							</div>
							<div class="small-10 medium-11 columns">
								<label>End time
									<?php echo(new \Html\Form\InputField([
										"id|name"		=> "time_end",
										"value"			=> $timelog->getTimeEnd(),
										"pattern"		=> "^(0?[0-9]|1[0-9]|2[0-3]):[0-5][0-9](\s+)?(AM|PM|am|pm)?$",
										"placeholder"	=> "12hr format: 11:30pm or 24hr format: 23:30",
										"required"		=> "true"
									])); ?>
								</label>
								<small id="timelog__end-time-error" class="error" style="display: none;">End time must be after the start time</small>
							</div>
						</div>
					</li>
					<li>
						<div class="row-fluid">
							<div class="small-2 medium-1 columns">
								<?php echo (new \Html\Form\InputField\Radio([
									"name"		=> "select_end_method",
									"value"		=> "hours",
									"class"		=> "right",
									"style"		=> "margin-top: 20px;",
									"tabindex"	=> -1
								])); ?>
							</div>
							<div class="small-10 medium-11 columns">
								<label>Hours/mins worked
									<div class="row-fluid">
										<div class="small-12 medium-6 columns" style="padding: 0px;">
											<?php echo (new \Html\Form\InputField\Number([
												"id|name"		=> "hours_worked",
												"value"			=> $timelog->getHoursWorked(),
												"min"			=> 0,
												"max"			=> 23,
												"step"			=> 1,
												"placeholder"	=> "Hours: 0-23",
												"disabled"		=> "true"
											])); ?>
										</div>
										<div class="small-12 medium-6 columns" style="padding: 0px;">
											<?php echo (new \Html\Form\InputField\Number([
												"id|name"		=> "minutes_worked",
												"value"			=> $timelog->getMinutesWorked(),
												"min"			=> 0,
												"max"			=> 59,
												"step"			=> 1,
												"placeholder"	=> "Mins: 0-59",
												"disabled"		=> "true"
											])); ?>
										</div>
										<small id="timelog__hours-mins-error" class="error" style="display: none;">Either hours or minutes must be set</small>
									</div>
								</label>
							</div>
						</div>
					</li>
				</ul>
			<?php endif; ?>
			<ul class="small-block-grid-1 medium-block-grid-1 section-body">
				<li>
					<label class="small-12 columns">Description
						<?php echo (new \Html\Form\Textarea([
							"id|name"		=> "description",
							"value"			=> !empty($timelog->id) ? $timelog->getComment()->comment : null,
							"rows"			=> 8
						])); ?>	
					</label>
				</li>
			</ul>
			<?php if (!empty($form)) : ?>
				<?php foreach($form as $form_section_heading => $form_array) : ?>
					<?php foreach($form_array as $form_element_key => $form_elements) : ?>
						<?php foreach($form_elements as $form_element) : ?>
							<ul class="small-block-grid-1 medium-block-grid-1 section-body">
								<li>
									<label class="small-12 columns"><?php echo $form_element->label; ?>
										<?php echo $form_element; ?>
									</label>
								</li>
							</ul>
						<?php endforeach; ?>
					<?php endforeach; ?>
				<?php endforeach; ?>
			<?php endif; ?>
			<ul class="small-block-grid-1 medium-block-grid-1 section-body">
				<li>
					<div class="small-12 columns">
						<button class="button small">Save</button>
					</div>
				</li>
			</ul>
		</div>
	</div>
</form>
<script type="text/javascript">
	// Input values are module, search and description
	$(document).ready(function () {
		$("input[type=radio][name=select_end_method]").change(function() {
			$("#timelog__end-time-error").hide();
			$("#timelog__end-time-error").parent().removeClass('error');
			
			if (this.value === "time") {
				$("#time_end").removeAttr("disabled");
				
				$("#hours_worked").attr("disabled", "disabled");
				$("#minutes_worked").attr("disabled", "disabled");
				
				$("#hours_worked").val("");
				$("#minutes_worked").val("");
				
				$("#time_end").focus();
			} else if (this.value === "hours") {
				$("#hours_worked").removeAttr("disabled");
				$("#minutes_worked").removeAttr("disabled");
				
				$("#time_end").attr("disabled", "disabled");
				$("#time_end").val("");
				
				$("#hours_worked").focus();
			}
		});
		
		// If there is no task group selected, we disable submit
		if ($("#object_id").val() == '') {
			$(".savebutton").prop("disabled", true);
			$("#acp_search").attr("readonly", "true");
		}
		var searchBaseUrl = '/timelog/ajaxSearch';

		// If the start time changes and there is no end time then set end time
		// to start time, and vice versa
		$("#dt_start").focusout(function () {
			if ($("#dt_end").val() == "") {
				$('#dt_end').val($("#dt_start").val());
			}
			//console.log("Start has lost focus");
		});
		$("#dt_end").focusout(function () {
			if ($("#dt_start").val() == "") {
				$('#dt_start').val($("#dt_end").val());
			}
		});

		// If there is already a value in #object_class, that is, we are 
		// editing, then set the searchURL
		var searchUrl = '';
		if ($("#object_class").val !== '') {
                    $("#acp_search").removeAttr("readonly");
                    searchUrl = searchBaseUrl + "?index=" + $("#object_class").val();
		}
		$("#object_class").change(function () {
                    console.log('object class changed');
			$("#acp_search").val('');
			$("#timelog_edit_form .panel + .panel").remove();
			if ($(this).val() !== "") {
				$("#acp_search").removeAttr("readonly");
				searchUrl = searchBaseUrl + "?index=" + $(this).val();
			} else {
				// This fails with unknown page...
				$("#acp_search").attr("readonly", "true");
				searchUrl = searchBaseUrl;
			}
		});

		$("#acp_search").autocomplete({
			source: function (request, response) {
				$.ajax({
					url: searchUrl + "&term=" + request.term,
					success: function (result) {
						response(JSON.parse(result));
					}
				});
			},
			// When the have selected a search value then do the ajax call  
			select: function (event, ui) {
				$("#object_id").val(ui.item.id);
				// Task is chosen, allow submit
				$(".savebutton").prop("disabled", false);
				$("#timelog_edit_form .panel + .panel").remove();
				$.get('/timelog/ajaxGetExtraData/' + $("#object_class").val() + '/' + $("#object_id").val())
						.done(function (response) {
							if (response != '') {
								var append_panel = "<div class='panel'><div class='row-fluid section-header'><h4>Additional Fields" + $("#object_class").val() + "</h4></div><ul class='small-block-grid-1 medium-block-grid-1 section-body'><li>" + response + "</li></ul></div>";
								$("#timelog_edit_form .panel").after(append_panel);
							}
						});

			},
			minLength: 3
		});

		$("#time_end").on('keyup', function() {
			$("#timelog__end-time-error").hide();
			$("#timelog__end-time-error").parent().removeClass('error');
		});
		
		$("#hours_worked").on('keyup', function() {
			$("#timelog__hours-mins-error").hide();
			$("#timelog__hours-mins-error").parent().removeClass('error');
		});
		
		$("#minutes_worked").on('keyup', function() {
			$("#timelog__hours-mins-error").hide();
			$("#timelog__hours-mins-error").parent().removeClass('error');
		});
				
		$("#timelog_edit_form").on('submit', function() {
			// Validate start/finish times
			<?php if (!$timelog->isRunning()) : ?>
				if ($("input[name='select_end_method']:checked").val() === 'time') {
					var startDate = parseTime($("#time_start").val());
					var endDate = parseTime($("#time_end").val());
				
					if (endDate <= startDate) {
						$("#timelog__end-time-error").show();
						$("#timelog__end-time-error").parent().addClass('error');
						return false;
					}
				} else {
					var hours_worked = $("#hours_worked").val();
					var minutes_worked = $("#minutes_worked").val();
					
					if ((!hours_worked && !minutes_worked) || (hours_worked <= 0 && minutes_worked <= 0)) {
						$("#timelog__hours-mins-error").show();
						$("#timelog__hours-mins-error").parent().addClass('error');
						return false;
					}
				}
			<?php else : ?>
				if ($("#date_start").val() != "" && $("#time_start").val() != '') {                
	                var moment_start = moment($("#date_start").val() + ' ' + $("#time_start").val(), ['DD/MM/YYYY HH:mm ', 'DD/MM/YYYY hh:mm a']);
	                if (!moment_start.isValid()) {
	                	alert('An invalid time format was provided');
	                	return false;
	                } else {
	                	if (moment_start.isAfter(new Date())) {
	                		alert('Start date/time cannot be in the future');
	                		return false;
	                	}
	                }
	            } else {
	            	alert("A start date and time are required");
	            	return false;
	            }
			<?php endif; ?>
		});

		$("#timelogForm").on("submit", function () {
			$.ajax({
				url: '/timelog/ajaxStart',
				method: 'POST',
				data: {
					'object': $("#object_class").val(),
					'object_id': $("#object_id").val(),
					'description': $("#description").val()
				},
				success: function (result) {
					alert(result);
				}
			});
			return false;
		});

		// Need to simulate change to module type to set url

	});

</script>
