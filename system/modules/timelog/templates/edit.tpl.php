<?php echo $form ?>
<script type="text/javascript">
    function initialiseEditModal() {
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
        const searchBaseUrl = '/timelog/ajaxSearch';

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
                search.setAttribute('data-url', searchBaseUrl + "?index=" + moduleSelect.value)

                saveButton.disabled = false
            }
        }

        updateFields();

        moduleSelect.addEventListener('change', () => {
            search.value = ''
            updateFields();
        })

        search.addEventListener('change', () => {
            document.getElementById('object_id').value = search.value
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
    }
    initialiseEditModal();
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
</script>