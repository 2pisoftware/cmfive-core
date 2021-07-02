// js/components/QuillEditor.ts

import Quill from 'quill';

export class QuillEditor {
    private static quillTarget = '.quill-editor';

    static bindQuillEditor()
    {
        const quillEditors = document.querySelectorAll(QuillEditor.quillTarget);

        if (quillEditors) {
            quillEditors.forEach((q) => {
                const options = q.getAttribute('data-quill-options');
                let editor = new Quill('#' + q.id, JSON.parse(options));
            })
        }
    }
}
