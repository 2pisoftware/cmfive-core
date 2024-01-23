<?php echo $form ?>
<script type="text/javascript">
    window.addEventListener('cmfive-modal-loaded', () => {
        // Input values are module and search
        const search = document.getElementById('acp_search')
        const moduleSelect = document.getElementById('object_class')
        const saveButton = document.getElementsByClassName('savebutton')[0]

        let searchUrlBase = "/ajaxSearch"


        if (moduleSelect.value == "") {
            //if there is no module selected, disable the search bar and save button
            search.setAttribute('readonly', 'true')
            saveButton.disabled = true
        } else {
            search.setAttribute('data-url', searchUrlBase + "&index=" + moduleSelect.value)
        }

        moduleSelect.addEventListener('change', () => {
            console.log("Object class changed")
            // change the autocomplete URL here
            search.setAttribute('data-url', searchUrlBase + "&index=" + moduleSelect.value)

            //clear the search bar
            search.value = ''
            //actviate the search bar and button
            search.setAttribute('readonly', 'false')
            saveButton.disabled = false
        })
    });
</script>