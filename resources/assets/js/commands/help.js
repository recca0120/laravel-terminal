'use babel';

import Command from './command';

export default class Help extends Command {
    match(name) {
        return ['list', 'help'].includes(name);
    }

    call() {
        this.api.echo(this.api.options.helpInfo);
        this.api.serverInfo();
    }
}
