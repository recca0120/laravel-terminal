import { Command } from '../command';
import { HttpClient } from '../httpclient';
import { OutputFormatter } from '../output-formatter';
import { basicSetup, EditorView } from 'codemirror';
import { vim, Vim as _Vim } from '@replit/codemirror-vim'
import { javascript } from '@codemirror/lang-javascript';
import { css } from '@codemirror/lang-css';
import { php } from '@codemirror/lang-php';
import { json } from '@codemirror/lang-json';
import { vue } from '@codemirror/lang-vue';
import { html } from '@codemirror/lang-html';
import { markdown } from '@codemirror/lang-markdown';
import { xml } from '@codemirror/lang-xml';
import { materialDark } from 'cm6-theme-material-dark'
import { EditorState, Compartment } from '@codemirror/state';

const languageConf = new Compartment

export class Editor {
    private section: HTMLElement;
    private cm: EditorView;

    constructor() {
        this.section = document.createElement('div');
        this.section.classList.add('CodeMirror-fullscreen');
        this.section.style.display = 'none';

        document.body.appendChild(this.section);

        this.cm = new EditorView({
            state: this.createState(),
            parent: this.section,
        });

        _Vim.defineEx('q', 'q', () => {
            this.cm.dom.dispatchEvent(new Event('q'));
            this.quit();
        });

        _Vim.defineEx('w', 'w', () => {
            this.cm.dom.dispatchEvent(new Event('w'));
        });

        _Vim.defineEx('wq', 'wq', () => {
            this.cm.dom.dispatchEvent(new Event('wq'));
            this.quit();
        });
    }

    on(type: string, cb: any): Editor {
        this.cm.dom.addEventListener(type, cb);

        return this;
    }

    show(): Editor {
        this.section.style.display = 'block';

        // setTimeout(() => {
        //     this.cm.focus();
        // }, 200);

        return this;
    }

    hide(): Editor {
        this.section.style.display = 'none';

        return this;
    }

    setText(text: string): Editor {
        // this.cm.dispatch({
        //     changes: { from: 0, to: this.cm.state.doc.length, insert: text },
        //     sequential: true
        // });
        this.cm.setState(this.createState({ doc: text }));

        return this;
    }

    getText(): string {
        return this.cm.state.doc.toString();
    }

    setCursor(pos: any): Editor {
        this.cm.dispatch({ selection: { anchor: pos } })

        return this;
    }

    setModeByFile(file: string): Editor {
        this.cm.dispatch({
            effects: languageConf.reconfigure(this.getLanguageByFile(file)),
        })

        return this;
    }

    private quit(): Editor {
        this.setText('');
        this.hide();

        return this;
    }

    private getLanguageByFile(file: string): any {
        const lookup: any = {
            'html': html(),
            'htm': html(),
            'js': javascript(),
            'css': css(),
            'json': json(),
            'vue': vue(),
            'md': markdown(),
            'xml': xml(),
            'php': php(),
        };
        const matches: any = file.match(/.+\.([^.]+)$/);

        return lookup[(matches[1] ?? 'php').toLowerCase()];
    }

    private createState(params = {}) {
        return EditorState.create({
            extensions: [vim(), languageConf.of(php()), basicSetup, materialDark],
            ...params
        });
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
                const text: string = await this.client.jsonrpc(cmd.method, [this.file]);

                this.editor
                    .setCursor(0)
                    .setText(text)
                    .setModeByFile(this.file)
                    .show();
            } catch (e) {
                reject(e);
            }
        });
    }

    private async write(): Promise<void> {
        try {
            const text = JSON.stringify(this.editor.getText().replace(/\n$/, ''));
            await this.client.jsonrpc('vi', [this.file, `--text=${text}`]);
        } catch (error) {
            this.reject(error);
        }
    }
}
