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

				const form = q.closest("form");

                const textarea = form.querySelector(`#${q.id.replace("quill_", "")}`) as HTMLTextAreaElement;

				const cb = () => textarea.value = editor.getSemanticHTML();

                form.removeEventListener('submit', cb);
                form.addEventListener('submit', cb);
            })
        }
    }

}
