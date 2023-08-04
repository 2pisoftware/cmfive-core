
//import CodeMirror from 'codemirror';
import { basicSetup, EditorView } from 'codemirror'


export class CodeMirror {
    private static SELECT_TARGET = '.code-mirror-target';

    static bindCodeMirrorEditor() {
        document.querySelectorAll(CodeMirror.SELECT_TARGET)?.forEach(s => {
            const config = s.getAttribute('data-config');

            new EditorView({
                extensions: [
                    basicSetup,
                ],
                parent: s as HTMLInputElement,
                });
        })
    }
}