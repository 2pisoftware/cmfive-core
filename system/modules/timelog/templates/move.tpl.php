<?php echo $form ?>
<script type="text/javascript">
    function initialiseMoveModal() {

        // Input values are module and search
        const search = document.getElementById('acp_search')
        const moduleSelect = document.getElementById('object_class')
        const saveButton = document.getElementsByClassName('savebutton')[0]

        const searchUrlBase = "/timelog/ajaxSearch"

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
    }
    initialiseMoveModal();
</script>