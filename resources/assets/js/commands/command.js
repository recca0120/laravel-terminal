export class Shell {
    constructor(shell, options) {
        this.shell = shell;
        this.options = options;
    }

    environment() {
        return this.shell.environment();
    }

    isProduction() {
        return this.shell.isProduction();
    }

    serverInfo() {
        return this.shell.serverInfo();
    }

    pause() {
        this.shell.pause();
    }

    resume() {
        this.shell.resume();
    }

    focus() {
        this.shell.focus();
    }

    scrollToBottom() {
        this.shell.scrollToBottom();
    }

    style(text, style = '') {
        return this.shell.style(text, style);
    }

    echo(text) {
        this.shell.echo(text);
    }

    line(text) {
        this.shell.line(text);
    }

    error(text) {
        this.shell.error(text);
    }

    ask(message, title = '') {
        return this.shell.ask(message, title);
    }
}

export class Command extends Shell {
    constructor(name, shell, options) {
        super(shell, options);
        this.name = name;
    }

    is(command) {
        return command === this.name;
    }

    async handle(parameters) {
        try {
            const response = await this.sendRequest([`--command="${parameters.join(' ')}"`]);
            this.line(response);
            this.serverInfo();
        } catch (error) {
            this.error(error.message);
            this.serverInfo();
        }
    }

    confirmToProceed(command) {
        return this.shell.confirmToProceed(this.name).includes(command);
    }

    async sendRequest(parameters) {
        return this.shell.sendRequest(this.name, parameters);
    }
}
