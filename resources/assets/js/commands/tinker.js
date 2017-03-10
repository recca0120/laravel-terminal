'use babel';

import Command from './command';

export default class Tinker extends Command {
    match(name) {
        return name === 'tinker';
    }

    call(cmd) {
        cmd.command = `tinker --command="${this.addslashes(cmd.rest)}"`;
        super.call(cmd);
    }
}
