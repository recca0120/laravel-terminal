import { Command } from '../command';
export declare class Artisan extends Command {
    is(command: string): boolean;
    run(command: string): Promise<any>;
    comfirmable(command: string): boolean;
    getComfirm(command: string): any;
    getComfirmCommand(command: string): string;
    private removePHP;
}
