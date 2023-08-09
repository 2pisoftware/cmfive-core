
//import CodeMirror from 'codemirror';
import { basicSetup, EditorView } from 'codemirror'
import {EditorState} from "@codemirror/state"
//import {syntaxHighlighting, defaultHighlightStyle} from "@codemirror/language"
//import {html} from "@codemirror/lang-html"


export class CodeMirror {
    private static SELECT_TARGET = '.code-mirror-target';

    static bindCodeMirrorEditor() {

        const cmEditors = document.querySelectorAll(CodeMirror.SELECT_TARGET);

        if (cmEditors) {
            cmEditors.forEach((q) => {
                const content = q.getAttribute('cm-value');
                //let str: string = "hello " + content;
                //console.log(str);
                
                let editor = new EditorView({
                    doc: content,
                    extensions: [
                        basicSetup,
                    ],
                    parent: q as HTMLInputElement,
                    });
                
                const textarea = document.getElementById(q.id);
                q.closest('form').removeEventListener('submit', () => textarea.innerText = q.querySelector('.cm-content').innerHTML);
                q.closest('form').addEventListener('submit', () => textarea.innerText = q.querySelector('.cm-content').innerHTML);
/*
                let str: string = "hello " + editor.state.doc.toString();
                console.log(str);
                let transaction = editor.state.update({changes: {from: 0, insert: "0"}})
                let str1: string = "hello " + transaction.state.doc.toString();
                console.log(str1)
                editor.dispatch(transaction);
                */

            })
        }
        /*

        document.querySelectorAll(CodeMirror.SELECT_TARGET)?.forEach(s => {
            const config = s.getAttribute('data-config');

            new EditorView({
                extensions: [
                    basicSetup,
                ],
                parent: s.parentElement as HTMLInputElement,
                });
        })
    }

        let s: HTMLInputElement = document.querySelector(CodeMirror.SELECT_TARGET);
        let str: string = "hello " + s.parentElement;
        console.log(str);
        new EditorView({
            extensions: [
                basicSetup,
            ],
            parent: s.parentElement as HTMLInputElement,
        });
    }
        */
    }
}