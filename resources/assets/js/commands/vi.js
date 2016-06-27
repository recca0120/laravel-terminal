'use strict';

import Command from './command';
import CodeMirror from 'codemirror';
import 'codemirror/addon/dialog/dialog';
import 'codemirror/addon/search/searchcursor';
import 'codemirror/addon/mode/loadmode';
import 'codemirror/addon/edit/matchbrackets';
import 'codemirror/addon/display/fullscreen';
import 'codemirror/mode/clike/clike';
import 'codemirror/mode/meta';
import 'codemirror/keymap/vim';
import 'codemirror/mode/php/php';
import 'codemirror/mode/css/css';
import 'codemirror/mode/javascript/javascript';
import 'codemirror/mode/htmlmixed/htmlmixed';
import 'codemirror/mode/xml/xml';

export default class Vi extends Command {
    constructor(options) {
        super(options);
        let textarea = $('<textarea style="display: none;"></textarea>').appendTo(document.body).get(0);
        this.editor = CodeMirror.fromTextArea(textarea, {
            lineNumbers: true,
            matchBrackets: true,
            keyMap: 'vim',
            showCursorWhenSelecting: true,
            theme: 'monokai'
        });
        this.editor.getWrapperElement().className += ' CodeMirror-fullscreen';
        $(this.editor.getWrapperElement()).hide();
    }

    match(name) {
        return name === 'vi';
    }

    call(cmd) {
        this.api.$term.pause();
        this.makeRequest(cmd.command).then((response) => {
            let path = cmd.rest;
            let editor = this.editor;
            let m, info, mode, spec;
            if (m = path.match(/.+\.([^.]+)$/)) {
                info = CodeMirror.findModeByExtension(m[1]);
                if (info) {
                    mode = info.mode;
                    spec = info.mime;
                }
            } else if (/\//.test(path)) {
                info = info = CodeMirror.findModeByMIME(path);
                if (info) {
                    mode = info.mode;
                    spec = info.mime;
                }
            };

            if (['htmlmixed', 'css', 'javascript', 'php'].includes(mode) === false) {
                mode = 'php';
                spec = 'application/x-httpd-php';
            }

            if (mode) {
                editor.setOption("mode", spec);
                CodeMirror.autoLoadMode(editor, mode);
            }
            $(editor.getWrapperElement()).show();
            let doc = editor.getDoc();
            let cm = doc.cm;
            doc.setValue(response.result);
            doc.setCursor(0);
            editor.focus();
            cm.focus();
            let save = () => {
                let value = JSON.stringify(doc.getValue());
                cmd.command += ` --text=${value}`;
                this.makeRequest(cmd.command).then(() => {}, () => {});
            }
            let quit = () => {
                $(editor.getWrapperElement()).hide();
                this.api.$term.resume();
                this.api.$term.focus();
            }
            CodeMirror.Vim.defineEx('q', 'q', function() {
                quit();
            });
            CodeMirror.Vim.defineEx('w', 'w', function() {
                save();
            });
            CodeMirror.Vim.defineEx('wq', 'wq', function() {
                save();
                quit();
            });
        });
    }
}
