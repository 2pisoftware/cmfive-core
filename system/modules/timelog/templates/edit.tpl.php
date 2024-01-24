<?php echo $form ?>
<script type="text/javascript">
    // Variables for each component
    const userSelect = document.getElementById('user_id')

    const search = document.getElementById('acp_search')
    const moduleSelect = document.getElementById('object_class')

    const startDate = document.getElementById('date_start')
    const startTime = document.getElementById('time_start')

    const endDate = document.getElementById('date_end')
    const endTime = document.getElementById('time_end')
    const hoursWorked = document.getElementById('hours_worked')
    const minutesWorked = document.getElementById('minutes_worked')

    const description = document.getElementById('description')

    const saveButton = document.getElementsByClassName('savebutton')[0]


    var radios = document.querySelectorAll("input[type=radio][name=select_end_method]");
    radios.forEach(function(radio) {
        radio.addEventListener('change', function() {
            // document.getElementById("timelog__end-time-error").style.display = 'none';
            // document.getElementById("timelog__end-time-error").parentNode.classList.remove('error');

            if (this.value === "time") {
                endTime.removeAttribute("disabled");

                hoursWorked.setAttribute("disabled", "disabled");
                minutesWorked.setAttribute("disabled", "disabled");

                hoursWorked.value = "";
                minutesWorked.value = "";

                document.getElementById("time_end").focus();
            } else if (this.value === "hours") {
                hoursWorked.removeAttribute("disabled");
                minutesWorked.removeAttribute("disabled");

                endTime.setAttribute("disabled", "disabled");
                endTime.value = "";

                hoursWorked.focus();
            }
        });
    });

    // Check if there is a object_class selected, if not, disable submit and search, if so update search url
    const searchUrlBase = 'timelog/ajaxSearch';

    const updateFields = () => {
        if (moduleSelect.value == "") {
            //if there is no module selected, disable the search bar and save button
            if (search.tomselect) {
                search.tomselect.disable();
            } else {
                search.setAttribute('readonly', true);
            }
            saveButton.disabled = true
        } else {
            //if there is a module selected, enable the search bar and save button
            if (search.tomselect) {
                search.tomselect.enable();
            } else {
                search.removeAttribute('readonly');
            }
            search.setAttribute('data-url', searchUrlBase + "?index=" + moduleSelect.value)

            saveButton.disabled = false
        }
    }

    updateFields();

    moduleSelect.addEventListener('change', () => {
        search.value = ''
        updateFields();
    })

    // If the start time changes and there is no end time then set end time
    // to start time, and vice versa
    startTime.addEventListener('focusout', function() {
        if (endTime.value == "") {
            endTime.value = endTime.value;
        }
    });
    endTime.addEventListener('focusout', function() {
        if (startTime.value == "") {
            startTime.value = endTime.value;
        }
    });

    // TODO implement autocomplete functionality

    // TODO implement error messages

    // document.getElementById("time_end").addEventListener('keyup', function() {
    //     document.getElementById("timelog__end-time-error").style.display = 'none';
    //     document.getElementById("timelog__end-time-error").parentNode.classList.remove('error');
    // });

    // document.getElementById("hours_worked").addEventListener('keyup', function() {
    //     document.getElementById("timelog__hours-mins-error").style.display = 'none';
    //     document.getElementById("timelog__hours-mins-error").parentNode.classList.remove('error');
    // });

    // document.getElementById("minutes_worked").addEventListener('keyup', function() {
    //     document.getElementById("timelog__hours-mins-error").style.display = 'none';
    //     document.getElementById("timelog__hours-mins-error").parentNode.classList.remove('error');
    // });

    // Input values are module, search and description

    // OLD CODE
    // $(document).ready(function() {
    //     $("input[type=radio][name=select_end_method]").change(function() {
    //         $("#timelog__end-time-error").hide();
    //         $("#timelog__end-time-error").parent().removeClass('error');

    //         if (this.value === "time") {
    //             $("#time_end").removeAttr("disabled");

    //             $("#hours_worked").attr("disabled", "disabled");
    //             $("#minutes_worked").attr("disabled", "disabled");

    //             $("#hours_worked").val("");
    //             $("#minutes_worked").val("");

    //             $("#time_end").focus();
    //         } else if (this.value === "hours") {
    //             $("#hours_worked").removeAttr("disabled");
    //             $("#minutes_worked").removeAttr("disabled");

    //             $("#time_end").attr("disabled", "disabled");
    //             $("#time_end").val("");

    //             $("#hours_worked").focus();
    //         }
    //     });

    //     // If there is no task group selected, we disable submit
    //     if ($("#object_id").val() == '') {
    //         $(".savebutton").prop("disabled", true);
    //         $("#acp_search").attr("readonly", "true");
    //     }
    //     var searchBaseUrl = '/timelog/ajaxSearch';

    //     // If the start time changes and there is no end time then set end time
    //     // to start time, and vice versa
    //     $("#dt_start").focusout(function() {
    //         if ($("#dt_end").val() == "") {
    //             $('#dt_end').val($("#dt_start").val());
    //         }
    //         //console.log("Start has lost focus");
    //     });
    //     $("#dt_end").focusout(function() {
    //         if ($("#dt_start").val() == "") {
    //             $('#dt_start').val($("#dt_end").val());
    //         }
    //     });

    //     // If there is already a value in #object_class, that is, we are
    //     // editing, then set the searchURL
    //     var searchUrl = '';
    //     if ($("#object_class").val !== '') {
    //         $("#acp_search").removeAttr("readonly");
    //         searchUrl = searchBaseUrl + "?index=" + $("#object_class").val();
    //     }
    //     $("#object_class").change(function() {
    //         console.log('object class changed');
    //         $("#acp_search").val('');
    //         $("#timelog_edit_form .panel + .panel").remove();
    //         if ($(this).val() !== "") {
    //             $("#acp_search").removeAttr("readonly");
    //             searchUrl = searchBaseUrl + "?index=" + $(this).val();
    //         } else {
    //             // This fails with unknown page...
    //             $("#acp_search").attr("readonly", "true");
    //             searchUrl = searchBaseUrl;
    //         }
    //     });

    //     $("#acp_search").autocomplete({
    //         source: function(request, response) {
    //             $.ajax({
    //                 url: searchUrl + "&term=" + request.term,
    //                 success: function(result) {
    //                     response(JSON.parse(result));
    //                 }
    //             });
    //         },
    //         // When the have selected a search value then do the ajax call
    //         select: function(event, ui) {
    //             $("#object_id").val(ui.item.id);
    //             // Task is chosen, allow submit
    //             $(".savebutton").prop("disabled", false);
    //             $("#timelog_edit_form .panel + .panel").remove();
    //             $.get('/timelog/ajaxGetExtraData/' + $("#object_class").val() + '/' + $("#object_id").val())
    //                 .done(function(response) {
    //                     if (response != '') {
    //                         var append_panel = "<div class='panel'><div class='row-fluid section-header'><h4>Additional Fields" + $("#object_class").val() +
    //                             "</h4></div><ul class='small-block-grid-1 medium-block-grid-1 section-body'><li>" + response + "</li></ul></div>";
    //                         $("#timelog_edit_form .panel").after(append_panel);
    //                     }
    //                 });

    //         },
    //         minLength: 3
    //     });

    //     $("#time_end").on('keyup', function() {
    //         $("#timelog__end-time-error").hide();
    //         $("#timelog__end-time-error").parent().removeClass('error');
    //     });

    //     $("#hours_worked").on('keyup', function() {
    //         $("#timelog__hours-mins-error").hide();
    //         $("#timelog__hours-mins-error").parent().removeClass('error');
    //     });

    //     $("#minutes_worked").on('keyup', function() {
    //         $("#timelog__hours-mins-error").hide();
    //         $("#timelog__hours-mins-error").parent().removeClass('error');
    //     });

    //     $("#timelog_edit_form").on('submit', function() {
    //         // Validate start/finish times
    //         <?php if (!$timelog->isRunning()) : ?>
    //             if ($("input[name='select_end_method']:checked").val() === 'time') {
    //                 var startDate = parseTime($("#time_start").val());
    //                 var endDate = parseTime($("#time_end").val());

    //                 if (endDate <= startDate) {
    //                     $("#timelog__end-time-error").show();
    //                     $("#timelog__end-time-error").parent().addClass('error');
    //                     return false;
    //                 }
    //             } else {
    //                 var hours_worked = $("#hours_worked").val();
    //                 var minutes_worked = $("#minutes_worked").val();

    //                 if ((!hours_worked && !minutes_worked) || (hours_worked <= 0 && minutes_worked <= 0)) {
    //                     $("#timelog__hours-mins-error").show();
    //                     $("#timelog__hours-mins-error").parent().addClass('error');
    //                     return false;
    //                 }
    //             }
    //         <?php else : ?>
    //             if ($("#date_start").val() != "" && $("#time_start").val() != '') {
    //                 var moment_start = moment($("#date_start").val() + ' ' + $("#time_start").val(), ['DD/MM/YYYY HH:mm ', 'DD/MM/YYYY hh:mm a']);
    //                 if (!moment_start.isValid()) {
    //                     alert('An invalid time format was provided');
    //                     return false;
    //                 } else {
    //                     if (moment_start.isAfter(new Date())) {
    //                         alert('Start date/time cannot be in the future');
    //                         return false;
    //                     }
    //                 }
    //             } else {
    //                 alert("A start date and time are required");
    //                 return false;
    //             }
    //         <?php endif; ?>
    //     });

    //     $("#timelogForm").on("submit", function() {
    //         $.ajax({
    //             url: '/timelog/ajaxStart',
    //             method: 'POST',
    //             data: {
    //                 'object': $("#object_class").val(),
    //                 'object_id': $("#object_id").val(),
    //                 'description': $("#description").val()
    //             },
    //             success: function(result) {
    //                 alert(result);
    //             }
    //         });
    //         return false;
    //     });

    //     // Need to simulate change to module type to set url
</script>