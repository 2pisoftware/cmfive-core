
//import CodeMirror from 'codemirror';
import { basicSetup, EditorView } from 'codemirror'
import {EditorState} from "@codemirror/state"
//import {syntaxHighlighting, defaultHighlightStyle} from "@codemirror/language"
import {html} from "@codemirror/lang-html"


export class CodeMirror {
    private static SELECT_TARGET = '.code-mirror-target';
    private static views: EditorView[];
    
    static bindCodeMirrorEditor() {

        const cmEditors = document.querySelectorAll(CodeMirror.SELECT_TARGET);

        if (cmEditors) {
            cmEditors.forEach((cm) => {
                const content = cm.getAttribute('cm-value');
                
                const editorView = new EditorView({
                    doc: content,
                    extensions: [
                        basicSetup,
                        //html(),
                    ],
                    parent: cm as HTMLInputElement,
                });
                
                cm.removeEventListener('update', (event) => CodeMirror.updateCallback(editorView, content, event));
                cm.addEventListener('update', (event) => CodeMirror.updateCallback(editorView, content, event));

                // CodeMirror.views.push(editorView);

                const textarea = document.getElementById(cm.id);
                cm.closest('form').removeEventListener('submit', () => textarea.innerText = cm.querySelector('.cm-content').innerHTML);
                cm.closest('form').addEventListener('submit', () => textarea.innerText = Array.from(cm.querySelectorAll(".cm-line")).map(e => e.textContent).join("\n"));
            })
        }
    }

    static updateCallback(editorView: EditorView, content, event) {
        let state = editorView.state
        let transaction = state.update({changes: {from: 0, to: state.doc.length, insert: event.detail}})
        editorView.update([transaction])
    }
}