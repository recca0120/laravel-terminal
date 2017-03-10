'use babel';

import Command from './command';

export default class Composer extends Command {
    match(name) {
        return name === 'composer';
    }

    call(cmd) {
        cmd.command = `composer --command="${this.addslashes(cmd.rest)}"`;
        super.call(cmd);
    }
}
