// js/components/QuillEditor.ts

import * as quill from 'quill';
const Quill = quill.default;

export class QuillEditor {
    private static quillTarget = '.quill-editor';
	private static quillExistsTarget = 'ql-container';

    static bindQuillEditor()
    {
        const quillEditors: NodeListOf<HTMLElement> = document.querySelectorAll(QuillEditor.quillTarget);

        if (quillEditors) {
            quillEditors.forEach((q) => {
				if (q.classList.contains(QuillEditor.quillExistsTarget)) return;

                const options = q.getAttribute('data-quill-options');
                let editor = new Quill(q, JSON.parse(options));

                const textarea = document.getElementById(q.id.substring(6));
                q.closest('form').removeEventListener('submit', () => textarea.innerText = q.querySelector('.ql-editor').innerHTML);
                q.closest('form').addEventListener('submit', () => textarea.innerText = q.querySelector('.ql-editor').innerHTML);
            })
        }
    }

}
