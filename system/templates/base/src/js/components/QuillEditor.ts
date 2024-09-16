// js/components/QuillEditor.ts

import * as quill from 'quill';
const Quill = quill.default;

export class QuillEditor {
    private static quillTarget = '.quill-editor';

    static bindQuillEditor()
    {
        const quillEditors = document.querySelectorAll(QuillEditor.quillTarget);

        if (quillEditors) {
            quillEditors.forEach((q) => {
                const options = q.getAttribute('data-quill-options');
                let editor = new Quill('#' + q.id, JSON.parse(options));

                const textarea = document.getElementById(q.id.substring(6));
                q.closest('form').removeEventListener('submit', () => textarea.innerText = q.querySelector('.ql-editor').innerHTML);
                q.closest('form').addEventListener('submit', () => textarea.innerText = q.querySelector('.ql-editor').innerHTML);
            })
        }
    }

}
