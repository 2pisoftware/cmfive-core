
import { basicSetup, EditorView } from 'codemirror';

import { html } from "@codemirror/lang-html";
import { defaultHighlightStyle, syntaxHighlighting } from "@codemirror/language";

export class CodeMirror {
    private static SELECT_TARGET = '.code-mirror-target';
    private static views: EditorView[];
    
    static bindCodeMirrorEditor() {
        const cmEditors = document.querySelectorAll(CodeMirror.SELECT_TARGET);

        if (cmEditors) {
            cmEditors.forEach((cm) => {
                if (cm.querySelector(".cm-editor")) {
                    return; //already bound
                }

                const textarea = document.getElementById(cm.getAttribute("data-id")) as HTMLTextAreaElement;
                const content = textarea.value;

                const editorView = new EditorView({
                    doc: content,
                    extensions: [
                        basicSetup,
                        html(),
                        syntaxHighlighting(defaultHighlightStyle),
                    ],
                    parent: cm as HTMLInputElement,
                });

                cm.removeEventListener('update', (event) => CodeMirror.updateCallback(editorView, event));
                cm.addEventListener('update', (event) => CodeMirror.updateCallback(editorView, event));

                cm.closest('form').removeEventListener('submit', () => textarea.innerText = CodeMirror.getContentAsJSONString(editorView));
                cm.closest('form').addEventListener('submit', () => textarea.innerText = CodeMirror.getContentAsJSONString(editorView));
            })
        }
    }

    static getContentAsJSONString(editorView: EditorView) {
        return JSON.stringify(editorView.state.doc.toString(), (_, char) => {
            // Replace whitespace characters with escape sequences
            if (char === '\n') return '\\n'; // newline
            else if (char === '\t') return '\\t'; // tab
            else return char; // for non-whitespace characters
        });
    }

    static updateCallback(editorView: EditorView, event) {
        let state = editorView.state
        let transaction = state.update({changes: {from: 0, to: state.doc.length, insert: event.detail}})
        editorView.update([transaction])
    }
}