import { Command } from './command';

export class Artisan extends Command {
    constructor(shell, options) {
        super('artisan', shell, options);
    }

    async handle(parameters) {
        const firstParameter = parameters[0] ? parameters[0] : '';
        if (
            parameters.includes('--force') === false &&
            this.isProduction() === true &&
            this.confirmToProceed(firstParameter) === true
        ) {
            const title = this.style(
                [
                    '**************************************',
                    '*     Application In Production!     *',
                    '**************************************',
                    '\n',
                ].join('\n'),
                'comment'
            );

            const result = await this.ask(
                [
                    this.style('Do you really wish to run this command? [y/N] (yes/no)', 'info'),
                    this.style('[no]', 'comment'),
                ].join(' '),
                title
            );

            if (result === false) {
                this.line();
                this.echo(this.style('Command Cancelled!', 'comment'));
                this.line();

                return;
            }
        }

        return super.handle(parameters);
    }
}
