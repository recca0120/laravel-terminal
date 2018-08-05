import { Command } from '../command';

export class Artisan extends Command {
    is(command: string): boolean {
        console.log(this.removePHP(command))
        return /^(\.\/)?artisan/.test(this.removePHP(command));
    }

    async run(command: string): Promise<any> {
        const cmd: any = this.parseSentence(this.removePHP(command));

        return await this.client.jsonrpc(cmd.cmd, [`--command="${cmd.parameters.join(' ')}"`]);
    }

    comfirmable(command: string): boolean {
        command = this.removePHP(command);

        if (this.isProduction() === false || /--force/.test(command) === true) {
            return false;
        }

        return [
            'migrate',
            'migrate:fresh',
            'migrate:install',
            'migrate:refresh',
            'migrate:reset',
            'migrate:rollback',
            'db:seed',
        ].some(pattern => {
            return new RegExp(`\\s${pattern}\\s`).test(` ${command.trim()} `);
        });
    }

    getComfirm(command: string): any {
        const title = this.outputFormatter.comment(
            [
                '',
                '**************************************',
                '*     Application In Production!     *',
                '**************************************',
                '',
            ].join('\n')
        );

        const message = [
            this.outputFormatter.info('Do you really wish to run this command? [y/N] (yes/no)'),
            this.outputFormatter.comment('[no]'),
        ].join(' ');

        const cancel = ['', this.outputFormatter.comment('Command Cancelled!'), ''].join('\n');

        return { title, message, cancel };
    }

    getComfirmCommand(command: string): string {
        return `${command} --force`;
    }

    private removePHP(command: string) {
        return command.replace(/^php\s+/, '');
    }
}
