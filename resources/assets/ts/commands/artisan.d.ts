import { Command } from '../command';
export declare class Artisan extends Command {
    is(command: any): boolean;
    comfirmable(command: string): boolean;
    getComfirm(command: string): any;
    getComfirmCommand(command: string): string;
}
