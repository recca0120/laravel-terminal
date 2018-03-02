import { Command } from './command';

export class MySQL extends Command {
    constructor(shell, options) {
        super('mysql', shell, options);

        this.connection = null;
    }

    async handle(parameters) {
        if (parameters[0] === 'use') {
            this.connection = parameters[1].replace(/;/g, '').trim();
            this.line(`current: ${this.connection}`);

            return;
        }

        super.handle(parameters);
    }

    async sendRequest(parameters) {
        if (this.connection !== null) {
            parameters.push(`--connection=${this.connection}`);
        }

        return this.shell.sendRequest(this.name, parameters);
    }
}
