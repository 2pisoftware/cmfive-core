
//import CodeMirror from 'codemirror';
import { basicSetup, EditorView } from 'codemirror'
import {EditorState} from "@codemirror/state"
//import {syntaxHighlighting, defaultHighlightStyle} from "@codemirror/language"
import {html} from "@codemirror/lang-html"


export class CodeMirror {
    private static SELECT_TARGET = '.code-mirror-target';

    static bindCodeMirrorEditor() {

        const cmEditors = document.querySelectorAll(CodeMirror.SELECT_TARGET);

        if (cmEditors) {
            cmEditors.forEach((cm) => {
                const content = cm.getAttribute('cm-value');
                //let str: string = "hello " + content;
                //console.log(str);
                
                let editor = new EditorView({
                    doc: content,
                    extensions: [
                        basicSetup,
                        //html(),
                    ],
                    parent: cm as HTMLInputElement,
                    });
                
                const textarea = document.getElementById(cm.id);
                cm.closest('form').removeEventListener('submit', () => textarea.innerText = cm.querySelector('.cm-content').innerHTML);
                cm.closest('form').addEventListener('submit', () => textarea.innerText = Array.from(cm.querySelectorAll(".cm-line")).map(e => e.textContent).join("\n"));

                let str: string = "hello 2 " + Array.from(cm.querySelectorAll('.cm-line')).map(e => e.textContent).join("\n");
                    //Array.from(document.querySelectorAll(".cm-line")).map(x => x.textContent).join("\n");
                
                //let str: string = "hello " + Array.from(cm.querySelectorAll(".cm-line")).map(e => e.textContent).join("\n");
                console.log(str);

            })
        }
    }
}