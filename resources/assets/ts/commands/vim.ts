import { Command } from '../command';
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
import * as CodeMirror from 'codemirror';
import { HttpClient } from '../httpclient';
import { OutputFormatter } from '../output-formatter';

export class Editor {
    private textarea: HTMLTextAreaElement;
    private editor: CodeMirror.EditorFromTextArea;
    private wapperElement: HTMLElement;
    private doc: CodeMirror.Doc;

    constructor() {
        this.textarea = document.createElement('textarea');
        document.body.appendChild(this.textarea);

        this.editor = CodeMirror.fromTextArea(this.textarea, {
            lineNumbers: true,
            keyMap: 'vim',
            showCursorWhenSelecting: true,
            theme: 'monokai',
        });

        this.wapperElement = this.editor.getWrapperElement();
        this.wapperElement.className += ' CodeMirror-fullscreen';

        this.doc = this.editor.getDoc();

        CodeMirror['Vim']['defineEx']('q', 'q', () => {
            this.textarea.dispatchEvent(new Event('q'));
            this.quit();
        });

        CodeMirror['Vim']['defineEx']('w', 'w', () => {
            this.textarea.dispatchEvent(new Event('w'));
        });

        CodeMirror['Vim']['defineEx']('wq', 'wq', () => {
            this.textarea.dispatchEvent(new Event('wq'));
            this.quit();
        });
    }

    on(type: string, cb: any): Editor {
        this.textarea.addEventListener(type, cb);

        return this;
    }

    show(): Editor {
        this.editor.getWrapperElement().style.display = 'block';
        setTimeout(() => {
            this.editor.focus();
        }, 200);

        return this;
    }

    hide(): Editor {
        this.wapperElement.style.display = 'none';

        return this;
    }

    setText(text: string): Editor {
        this.doc.setValue(text);

        return this;
    }

    getText(): string {
        return this.doc.getValue();
    }

    setOption(key: string, value: any): Editor {
        this.editor.setOption(key, value);

        return this;
    }

    setCursor(pos: any): Editor {
        this.doc.setCursor(pos);

        return this;
    }

    setModeByFile(file: string): Editor {
        this.setOption('mode', this.getModeByFile(file).mode);

        return this;
    }

    private quit(): Editor {
        this.setText('');
        this.hide();

        return this;
    }

    private getModeByFile(file: string): any {
        const matches = file.match(/.+\.([^.]+)$/);

        if (matches.length > 0) {
            return CodeMirror['findModeByExtension'](matches[1]);
        }

        if (/\//.test(file)) {
            return CodeMirror['findModeByMIME'](file);
        }

        return {
            mode: 'php',
            mime: 'application/x-httpd-php',
        };
    }
}

export class Vim extends Command {
    private editor: Editor;
    private file: string = '';
    private resolve: any;
    private reject: any;

    constructor(client: HttpClient, outputFormatter: OutputFormatter, options: any) {
        super(client, outputFormatter, options);
        this.editor = new Editor();
        this.editor.hide();

        this.editor.on('q', () => {
            if (!this.resolve) {
                return;
            }

            this.resolve('');
        });

        this.editor.on('w', () => {
            this.write();
        });

        this.editor.on('wq', async () => {
            if (!this.resolve) {
                return;
            }

            await this.write();

            this.resolve('');
        });
    }

    is(command: string): boolean {
        return /^(\.\/)?vi(m)?/.test(command);
    }

    private async write(): Promise<void> {
        try {
            const text = JSON.stringify(this.editor.getText().replace(/\n$/, ''));
            await this.client.jsonrpc('vi', [this.file, `--text=${text}`]);
        } catch (error) {
            this.reject(error);
        }
    }

    async run(command: string): Promise<any> {
        return new Promise(async (resolve, reject) => {
            this.resolve = resolve;
            this.reject = reject;

            const cmd = this.parseSentence(command);
            this.file = cmd.params[0] || '';

            if (!this.file) {
                return reject('');
            }

            try {
                const text: string = await this.client.jsonrpc(cmd.name, [this.file]);

                this.editor
                    .setModeByFile(this.file)
                    .show()
                    .setText(text)
                    .setCursor(0);
            } catch (e) {
                reject(e);
            }
        });
    }
}
