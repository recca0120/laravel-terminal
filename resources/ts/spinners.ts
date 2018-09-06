const cliSpinners = require('cli-spinners');

export class Spinner {
    private spinner: any = cliSpinners.dots;
    private frameIndex = 0;
    private interval: any = null;

    constructor(style: string = 'dots') {
        this.setStyle(style);
    }

    setStyle(style: string): Spinner {
        this.spinner = cliSpinners[style];

        return this;
    }

    start(cb: Function): Spinner {
        this.interval = setInterval(() => {
            cb(this.frame());
        }, this.wait());

        return this;
    }

    stop(): Spinner {
        if (this.interval) {
            clearInterval(this.interval);
        }

        return this;
    }

    private wait(): number {
        return this.spinner.interval;
    }

    private frame(): string {
        const frames = this.spinner.frames;
        let frame = frames[this.frameIndex];

        this.frameIndex = ++this.frameIndex % frames.length;

        return `${frame} `;
    }
}
