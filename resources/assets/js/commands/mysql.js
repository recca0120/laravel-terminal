'use babel';

import Command from './command';

export default class Mysql extends Command {
    match(name) {
        return name === 'mysql';
    }

    call(cmd) {
        cmd.command = `mysql --command="${this.addslashes(cmd.rest)}"`;
        super.call(cmd);
    }
}
