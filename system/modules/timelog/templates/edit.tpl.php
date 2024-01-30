<?php echo $form ?>
<script type="text/javascript">
    function parseTime(time, date) {
        const isTwelveHour = time[time.length - 1] === 'm';
        console.log('time -1', time[time.length - 1])
        const parts = time.split(':');
        let hours = parseInt(parts[0]);
        const minutes = parseInt(parts[1]);
        if (isTwelveHour) {
            console.log('its in twelve hour form mate')
            if (hours === 12) {
                hours = 0;
            }
            if (time[time.length - 2] === 'p') {
                hours += 12;
            }
        }
        //guard statement
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
        const userSelect = document.getElementById('user_id')

        const search = document.getElementById('acp_search')
        const moduleSelect = document.getElementById('object_class')

        const startDate = document.getElementById('date_start')
        const startTime = document.getElementById('time_start')

        const endDate = document.getElementById('date_end')
        const endTime = document.getElementById('time_end') ?? false
        const hoursWorked = document.getElementById('hours_worked')
        const minutesWorked = document.getElementById('minutes_worked')

        const description = document.getElementById('description')

        const saveButton = document.getElementsByClassName('savebutton')[0]

        const isTimelogRunning = (startTime.value && !endTime) ? true : false
        let selectEndMethod = ''
        if (!isTimelogRunning) {
            if (endTime.value) {
                selectEndMethod = 'time'
            } else if (hoursWorked.value || minutesWorked.value) {
                selectEndMethod = 'hours'
            }
        }


        let radios = document.querySelectorAll("input[type=radio][name=select_end_method]");
        radios.forEach(function(radio) {
            radio.addEventListener('change', function() {
                // document.getElementById("timelog__end-time-error").style.display = 'none';
                // document.getElementById("timelog__end-time-error").parentNode.classList.remove('error');

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
        saveButton.disabled = true //ensure save button is disabled on page load

        //validation functions
        function validateEndTime() {
            if (selectEndMethod === 'time') {
                if (parseTime(endTime.value) <= parseTime(startTime.value)) {
                    alert('End time is before start time')
                    saveButton.disabled = true
                    return false
                } else if (parseTime(endTime.value) >= parseTime(startTime.value)) {
                    saveButton.disabled = false
                    return true
                }
            } else if (selectEndMethod === 'hours') {
                console.log('hours validate')
                if ((!hoursWorked && !minutesWorked) || (hoursWorked.value <= 0 || minutesWorked.value <= 0)) {

                    alert('Hours and minutes must be greater than 0')
                    saveButton.disabled = true
                    return false
                } else {
                    saveButton.disabled = false
                    return true
                }
            }
        }

        function validateTime() {
            const currentTime = Date.now()
            const formTime = parseTime(startTime.value, startDate.value)

            if (formTime > currentTime) {
                alert('Timelog cannot start in the future')
                saveButton.disabled = true
                return false
            } else {
                if (!isTimelogRunning) {
                    validateEndTime()
                } else {
                    return true
                }
            }
        }

        //validation event listeners
        //TODO make these red or otherwise obvious when invalid
        if (!isTimelogRunning) {
            endTime.addEventListener('change', function() {
                validateTime() ? this.classList.remove('input-error') : this.classList.add('input-error')
            })
            hoursWorked.addEventListener('change', function() {
                if (validateTime()) {
                    console.log('return true')
                    this.classList.remove('input-error')
                } else {
                    this.classList.add('input-error')
                    console.log(this);
                }
            })
            minutesWorked.addEventListener('change', function() {
                validateTime() ? this.classList.remove('input-error') : this.classList.add('input-error')
            })
        }

        startTime.addEventListener('change', () => {
            validateTime()
        })

        startDate.addEventListener('change', () => {
            validateTime()
        })

        moduleSelect.addEventListener('change', () => {
            search.value = ''
            updateFields();
            //updateFields is able to enable the submit button so must also validate time here
            validateTime()
        })

        search.addEventListener('change', () => {
            document.getElementById('object_id').value = search.value
        })

    }
    initialiseEditModal();

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