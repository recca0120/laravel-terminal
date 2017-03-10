'use babel';

import Command from './command';

export default class Default extends Command {
    match(name) {
        return ['list', 'help'].includes(name) === false;
    }
}
