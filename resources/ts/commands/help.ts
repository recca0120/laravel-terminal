import {Command} from '../command';

export class Help extends Command {
    is(command: string): boolean {
        return /^(\.\/)?(list|help)/.test(command);
    }

    run(): Promise<any> {
        return new Promise(resolve => {
            resolve(['', this.options.helpInfo].join('\n'));
        });
    }
}
