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
import { HttpClient } from '../httpclient';
import { OutputFormatter } from '../output-formatter';
export declare class Editor {
    private textarea;
    private editor;
    private wapperElement;
    private doc;
    constructor();
    on(type: string, cb: any): Editor;
    show(): Editor;
    hide(): Editor;
    setText(text: string): Editor;
    getText(): string;
    setOption(key: string, value: any): Editor;
    setCursor(pos: any): Editor;
    setModeByFile(file: string): Editor;
    private quit;
    private getModeByFile;
}
export declare class Vim extends Command {
    private editor;
    private file;
    private resolve;
    private reject;
    constructor(client: HttpClient, outputFormatter: OutputFormatter, options: any);
    is(command: string): boolean;
    private write;
    run(command: string): Promise<any>;
}
