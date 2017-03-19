'use babel';

import $ from '../bootstrap';
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
import Command from './command';

export default class Vi extends Command {
    constructor(api, options) {
        super(api, options);
        const textarea = $('<textarea style="display: none;"></textarea>').appendTo(document.body).get(0);
        this.editor = CodeMirror.fromTextArea(textarea, {
            lineNumbers: true,
            matchBrackets: true,
            keyMap: 'vim',
            showCursorWhenSelecting: true,
            theme: 'monokai',
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
            const path = cmd.rest;
            const editor = this.editor;
            const matches = path.match(/.+\.([^.]+)$/);
            let info;
            let mode;
            let spec;
            if (matches.length > 0) {
                info = CodeMirror.findModeByExtension(matches[1]);
                if (info) {
                    mode = info.mode;
                    spec = info.mime;
                }
            } else if (/\//.test(path)) {
                info = CodeMirror.findModeByMIME(path);
                if (info) {
                    mode = info.mode;
                    spec = info.mime;
                }
            }

            if (['htmlmixed', 'css', 'javascript', 'php'].includes(mode) === false) {
                mode = 'php';
                spec = 'application/x-httpd-php';
            }

            if (mode) {
                editor.setOption('mode', spec);
                CodeMirror.autoLoadMode(editor, mode);
            }
            $(editor.getWrapperElement()).show();
            const doc = editor.getDoc();
            const cm = doc.cm;
            doc.setValue(response.result);
            doc.setCursor(0);
            editor.focus();
            cm.focus();
            const save = () => {
                const value = JSON.stringify(doc.getValue());
                cmd.command += ` --text=${value}`;
                this.makeRequest(cmd.command).then(() => {}, () => {});
            };
            const quit = () => {
                $(editor.getWrapperElement()).hide();
                this.api.$term.resume();
                this.api.$term.focus();
            };
            CodeMirror.Vim.defineEx('q', 'q', () => {
                quit();
            });
            CodeMirror.Vim.defineEx('w', 'w', () => {
                save();
            });
            CodeMirror.Vim.defineEx('wq', 'wq', () => {
                save();
                quit();
            });
        }, (response) => {
            this.api.loading.hide();
            this.api.echo(response.result);
            this.api.serverInfo();
        });
    }
}
