import 'codemirror/keymap/vim';
import 'codemirror/addon/dialog/dialog';
import 'codemirror/addon/search/searchcursor';
import 'codemirror/addon/edit/matchbrackets';
import 'codemirror/addon/display/fullscreen';
import 'codemirror/mode/clike/clike';
import 'codemirror/mode/meta';
import 'codemirror/mode/php/php';
import 'codemirror/mode/css/css';
import 'codemirror/mode/javascript/javascript';
import 'codemirror/mode/htmlmixed/htmlmixed';
import 'codemirror/mode/xml/xml';

import CodeMirror from 'codemirror';
import { Command } from './command';
import tap from 'lodash/tap';

export class Vi extends Command {
    constructor(shell, options) {
        super('vi', shell, options);
        this.textarea = document.createElement('textarea');
        this.textarea = tap(document.createElement('textarea'), textarea => {
            document.body.appendChild(textarea);
        });

        this.editor = tap(
            CodeMirror.fromTextArea(this.textarea, {
                lineNumbers: true,
                matchBrackets: true,
                styleActiveLine: true,
                keyMap: 'vim',
                showCursorWhenSelecting: true,
                theme: 'monokai',
            }),
            editor => {
                editor.getWrapperElement().className += ' CodeMirror-fullscreen';
            }
        );
        this.doc = this.editor.getDoc();
        this.cm = this.doc.cm;
        this.hide();

        CodeMirror.Vim.defineEx('q', 'q', () => {
            this.quit();
        });

        CodeMirror.Vim.defineEx('w', 'w', () => {
            this.save();
        });

        CodeMirror.Vim.defineEx('wq', 'wq', () => {
            this.save()
                .then(() => {
                    this.quit();
                })
                .catch(error => this.error(error));
        });
    }

    show() {
        this.editor.getWrapperElement().style.display = 'block';
        setTimeout(() => {
            this.cm.focus();
        }, 200);
    }

    hide() {
        this.editor.getWrapperElement().style.display = 'none';
    }

    setOption(key, value) {
        this.editor.setOption(key, value);
    }

    setValue(text) {
        this.doc.setValue(text);
    }

    setCursor(pos) {
        this.doc.setCursor(pos);
    }

    save() {
        const value = JSON.stringify(this.doc.getValue().replace(/\n$/, ''));

        return this.sendRequest([this.file, `--text=${value}`]);
    }

    quit() {
        this.setValue('');
        this.hide();
        setTimeout(() => {
            this.resume();
            this.focus();
        }, 200);
    }

    info() {
        const matches = this.file.match(/.+\.([^.]+)$/);

        if (matches.length > 0) {
            return CodeMirror.findModeByExtension(matches[1]);
        }

        if (/\//.test(this.file)) {
            return CodeMirror.findModeByMIME(this.file);
        }

        return {
            mode: 'php',
            mime: 'application/x-httpd-php',
        };
    }

    async open(parameters) {
        const response = await this.sendRequest([this.file]);
        const info = this.info();
        this.setOption('mode', info.mode);
        this.pause();
        this.show();
        this.setValue(response);
        this.setCursor(0);
    }

    handle(parameters) {
        this.file = parameters[0] ? parameters[0] : '';
        this.open();
    }
}
