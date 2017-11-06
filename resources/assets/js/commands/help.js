import { Command } from './command';

export class Help extends Command {
    constructor(shell, options) {
        super('help', shell, options);
    }

    is(name) {
        return ['list', 'help'].includes(name);
    }

    handle() {
        this.line();
        this.line(this.options.helpInfo);
        this.serverInfo();
    }
}
