'use babel';

export default class Loading {
    constructor(term) {
        this.$term = term;
        Object.assign(this, {
            anim: ['/', '|', '\\', '-'],
            prompt: null,
            delay: 50,
            intervalId: null,
            counter: 0,
        });
    }

    show() {
        this.counter++;
        this.$term.disable();
        this.prompt = this.$term.get_prompt();
        let i = 0;
        this.intervalId = setInterval(() => {
            this.$term.set_prompt(this.anim[i++]);
            if (i > this.anim.length - 1) {
                i = 0;
            }
        }, this.delay);
    }

    hide() {
        this.counter--;
        if (this.counter <= 0) {
            clearInterval(this.intervalId);
            this.$term.enable();
            this.$term.set_prompt(this.prompt);
            this.counter = 0;
        }
    }
}
