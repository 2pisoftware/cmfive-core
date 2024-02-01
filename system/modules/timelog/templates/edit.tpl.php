<?php echo $form ?>
<script type="text/javascript">
    function parseTime(time, date) {
        const isTwelveHour = time[time.length - 1] === 'm';
        const parts = time.split(':');
        let hours = parseInt(parts[0]);
        const minutes = parseInt(parts[1]);
        if (isTwelveHour) {
            if (hours === 12) {
                hours = 0;
            }
            if (time[time.length - 2] === 'p') {
                hours += 12;
            }
        }

        if (date === undefined) {
            return new Date(0, 0, 0, hours, minutes, 0, 0).getTime();
        }

        const dateParts = date.split('-');
        const year = date ? parseInt(dateParts[0]) : 0
        const month = date ? parseInt(dateParts[1]) - 1 : 0
        const day = date ? parseInt(dateParts[2]) : 0

        return new Date(year, month, day, hours, minutes, 0, 0).getTime();
    }

    function initialiseEditModal() {
        // Variables for each component
        let userSelect = document.getElementById('user_id')

        let search = document.getElementById('acp_search')
        let moduleSelect = document.getElementById('object_class')

        let startDate = document.getElementById('date_start')
        let startTime = document.getElementById('time_start')

        let endDate = document.getElementById('date_end')
        let endTime = document.getElementById('time_end') ?? false
        let hoursWorked = document.getElementById('hours_worked')
        let minutesWorked = document.getElementById('minutes_worked')

        let description = document.getElementById('description')

        let saveButton = document.getElementsByClassName('savebutton')[0]

        // Defaults to time, can be changed with radio buttons
        let selectEndMethod = 'time'

        let radios = document.querySelectorAll("input[type=radio][name=select_end_method]");
        radios.forEach(function(radio) {
            radio.addEventListener('change', function() {

                if (this.value === "time") {
                    selectEndMethod = 'time'
                    endTime.removeAttribute("disabled");

                    hoursWorked.setAttribute("disabled", "disabled");
                    minutesWorked.setAttribute("disabled", "disabled");

                    hoursWorked.value = "";
                    minutesWorked.value = "";

                    document.getElementById("time_end").focus();

                } else if (this.value === "hours") {
                    selectEndMethod = 'hours'
                    hoursWorked.removeAttribute("disabled");
                    minutesWorked.removeAttribute("disabled");

                    endTime.setAttribute("disabled", "disabled");
                    endTime.value = "";

                    hoursWorked.focus();
                }
            });
        });

        const searchBaseUrl = '/timelog/ajaxSearch';
        // Check if there is a object_class selected, if not, disable submit and search, if so update search url
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
        saveButton.disabled = true //ensure save button is disabled on page load

        // validation functions
        function validateEndTime() {
            if (selectEndMethod === 'time') {
                if (parseTime(endTime.value, startDate.value) < parseTime(startTime.value, startDate.value)) {
                    throw new Error('End time is before start time')
                } else if (parseTime(endTime.value, startDate.value) == parseTime(startTime.value, startDate.value)) {
                    throw new Error('End time is the same as start time')
                }
            } else if (selectEndMethod === 'hours') {
                if ((!hoursWorked && !minutesWorked) || (hoursWorked.value < 0 || minutesWorked.value < 0) || (hoursWorked.value == 0 && minutesWorked.value == 0)) {
                    throw new Error('Hours and minutes must be greater than 0')
                }
            }
        }

        function validateTime() {
            const currentTime = Date.now()
            const formTime = parseTime(startTime.value, startDate.value)
            if (formTime > currentTime) {
                throw new Error('Timelog cannot start in the future')
            }
            if (endTime) {
                validateEndTime()
            }
        }

        /**
         * Fixes case where error caused by altering one field is corrected by changing another field
         * without would lead to css style still being applied to original violating field
         */
        function removeClass(className) {
            //This class ensures 
            let dirtyObjects = document.querySelectorAll('.' + className)
            dirtyObjects.forEach(dirtyObject => {
                dirtyObject.classList.remove(className)
            })
        }

        // validation event listeners
        let validatingElements = [startTime, startDate];
        if (endTime) {
            // if end time exists, so will hours worked and minutes worked
            validatingElements = validatingElements.concat([endTime, hoursWorked, minutesWorked])
        }

        validatingElements.forEach(element => {
            element.addEventListener('change', function() {
                try {
                    validateTime()
                    removeClass('input-error');
                    saveButton.disabled = false
                } catch (error) {
                    alert(error.message)
                    this.classList.add('input-error')
                    saveButton.disabled = true
                }
            })
        })

        moduleSelect.addEventListener('change', () => {
            search.value = ''
            updateFields();
            //updateFields is able to enable the submit button so must also validate time here
            validateTime() ? saveButton.disabled = false : saveButton.disabled = true
        })

        search.addEventListener('change', () => {
            document.getElementById('object_id').value = search.value
        })

    }
    initialiseEditModal();
</script>