// src/js/components/MultiFileUpload.ts

export class MultiFileUpload {
    private static containerTarget = 'multi-upload-file-container';
    private static buttonTarget = 'multi-upload-button';
    private static fileTarget = 'multi-upload-file-element';
    private static filesContainerTarget = 'multi-upload-files'
    private static fileListTarget = 'multi-upload-files-display';

    // private static uploadFormData = [];

    static bindInteractions() {
        document.querySelectorAll('.' + MultiFileUpload.buttonTarget)?.forEach(b => {
            b.removeEventListener('click', MultiFileUpload.buttonClickHandler);
            b.addEventListener('click', MultiFileUpload.buttonClickHandler);
        });

        document.querySelectorAll('.' + MultiFileUpload.fileTarget)?.forEach(f => {
            f.removeEventListener('change', () => MultiFileUpload.fileChangeHandler(f));
            f.addEventListener('change', () => MultiFileUpload.fileChangeHandler(f));
        });

        document.querySelectorAll('.' + MultiFileUpload.containerTarget + ' .remove')?.forEach(r => {
            r.removeEventListener('click', MultiFileUpload.removeExistingFile);
            r.addEventListener('click', MultiFileUpload.removeExistingFile);
        })
    }

    static buttonClickHandler = function() {
        const file_element = this.closest('.' + MultiFileUpload.containerTarget)?.querySelector('.' + MultiFileUpload.fileTarget);
        if (file_element) {
            file_element.dispatchEvent(new MouseEvent('click', {
                bubbles: true,
                cancelable: true,
                view: window
            }));
        } else {
            console.error('Could not find file element');
        }
    }

    static fileChangeHandler = function(file_object) {
        const parent = file_object.closest('.' + MultiFileUpload.containerTarget);
        if (!parent) {
            console.error('Could not find file container');
            return;
        }

        var files = file_object.files;
        const file_container = parent.querySelector('.' + MultiFileUpload.filesContainerTarget);
        if (!file_container) {
            console.error('Could not find file list container');
            return;
        }
        const dt = new DataTransfer();
        // Add existing items
        if (file_container.files && file_container.files.length) {
            for (let i = 0; i < file_container.files.length; i++) {
                dt.items.add(file_container.files.item(i));
            }
        }

        // Add new items
        for (let file of files) {
            dt.items.add(file)
        }

        // Set in our "container"
        file_container.files =  dt.files;//.append(files);
        

        this.files = (new DataTransfer()).files;

        MultiFileUpload.loadList(parent);
    }

    static removeExistingFile = function() {
        const parent_p = this.closest('p');
        const container_parent = this.closest('.' + MultiFileUpload.containerTarget)

        if (parent_p && container_parent) {
            let hidden_remove = document.getElementById(container_parent.id + '_remove') as HTMLInputElement;
            if (hidden_remove) {
                let values = hidden_remove.value?.split(',');
                values.push(parent_p.getAttribute('data-file-id'));
                hidden_remove.value = values.join(',');

                parent_p.classList.add('d-none');
            }
        }
    }

    private static loadList(parent) {
        const file_display = parent.querySelector('.' + MultiFileUpload.fileListTarget);

        const file_container = parent.querySelector('.' + MultiFileUpload.filesContainerTarget);
        if (!file_container) {
            console.error('Could not find file list container');
            return;
        }

        if (file_container.files.length > 0) {
            file_display.classList.remove('d-none');
        } else {
            if (!file_display.classList.contains('d-none')) {
                file_display.classList.add('d-none');
            }
        }
        file_display.innerHTML = '';

        for (let i = 0; i < file_container.files.length; i++) {
            const display_el = document.createElement('div');
            display_el.classList.add('multi-upload-item');
            display_el.innerText = file_container.files.item(i).name;
            const remove_button = document.createElement('button');
            // remove_button.classList.add('btn', 'btn-sm', 'btn-outline-info');
            remove_button.innerHTML = '<i class="bi bi-x"></i>';
            remove_button.addEventListener('click', (e) => {
                e.preventDefault();
                (file_display as HTMLElement).removeChild(display_el);

                // @todo: remove file from file_container object
                if (file_container.files && file_container.files.length) {
                    let _dt = new DataTransfer();
                    for (let fc = 0; fc < file_container.files.length; fc++) {
                        console.log(i, fc);
                        if (fc !== i) {
                            _dt.items.add(file_container.files.item(fc));
                        }
                    }
                    file_container.files = _dt.files
                }

                if (file_display.querySelectorAll('.multi-upload-item').length == 0 && !file_display.classList.contains('d-none')) {
                    file_display.classList.add('d-none');
                }

                MultiFileUpload.loadList(parent);
            })
            remove_button.onclick = MultiFileUpload.removeButtonHandler;
            display_el.appendChild(remove_button);
            file_display.appendChild(display_el);
        }
    }

    static removeButtonHandler = function() {

    }
}
