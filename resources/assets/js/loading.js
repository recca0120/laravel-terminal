import cliSpinners from 'cli-spinners';

export class Loading {
    constructor(options) {
        this.options = Object.assign(
            {
                text: '',
                color: 'cyan',
            },
            options
        );

        const sp = this.options.spinner;
        this.spinner = typeof sp === 'object' ? sp : cliSpinners.dots;
        this.text = this.options.text;
        this.interval = this.options.interval || this.spinner.interval || 100;
        this.stream = this.options.stream;
        this.id = null;
        this.frameIndex = 0;
        this.enabled = true;
    }

    frame() {
        const frames = this.spinner.frames;
        let frame = frames[this.frameIndex];

        this.frameIndex = ++this.frameIndex % frames.length;

        return frame + ' ' + this.text;
    }

    clear() {
        if (!this.enabled) {
            return this;
        }

        this.stream.clearLine();
        this.stream.cursorTo(0);

        return this;
    }

    render() {
        this.clear();
        this.stream.write(this.frame());

        return this;
    }

    start(text) {
        if (text) {
            this.text = text;
        }

        if (!this.enabled || this.id) {
            return this;
        }

        this.stream.hideCursor();
        this.render();
        this.id = setInterval(this.render.bind(this), this.interval);

        return this;
    }

    stop() {
        if (!this.enabled) {
            return this;
        }

        clearInterval(this.id);
        this.id = null;
        this.frameIndex = 0;
        this.clear();
        this.stream.showCursor();

        return this;
    }

    succeed(text) {
        return this.stopAndPersist({ symbol: logSymbols.success, text });
    }

    fail(text) {
        return this.stopAndPersist({ symbol: logSymbols.error, text });
    }

    warn(text) {
        return this.stopAndPersist({ symbol: logSymbols.warning, text });
    }

    info(text) {
        return this.stopAndPersist({ symbol: logSymbols.info, text });
    }

    stopAndPersist(options) {
        // Legacy argument
        // TODO: Deprecate sometime in the future
        if (typeof options === 'string') {
            options = {
                symbol: options,
            };
        }

        options = options || {};

        this.stop();
        this.stream.write(`${options.symbol || ' '} ${options.text || this.text}\n`);

        return this;
    }
}
