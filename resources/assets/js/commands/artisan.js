'use babel';

import $ from '../bootstrap';
import Command from './command';

export default class Artisan extends Command {
    match(name) {
        return name === 'artisan';
    }

    call(cmd) {
        const rest = $.terminal.parse_command(cmd.rest.trim());
        if (this.api.options.environment === 'production' &&
            rest.args.includes('--force') === false &&
            this.api.options.confirmToProceed[cmd.name].includes(rest.name) === true
        ) {
            this.api.$term.echo(this.api.comment('**************************************'));
            this.api.$term.echo(this.api.comment('*     Application In Production!     *'));
            this.api.$term.echo(this.api.comment('**************************************'));
            this.api.$term.echo(' ');

            const promise = this.api.confirm(`${this.api.info('Do you really wish to run this command? [y/N] (yes/no)')} ${this.api.comment('[no]')}: `);
            promise.then(() => {
                cmd.command = `artisan --command="${this.addslashes(cmd.rest)}"`;
                super.call(cmd);
            }, () => {
                this.$term.echo(' ');
                this.$term.echo(`${this.comment('Command Cancelled!')}`);
                this.$term.echo(' ');
            });
        } else {
            cmd.command = `artisan --command="${this.addslashes(cmd.rest)}"`;
            super.call(cmd);
        }
    }
}
