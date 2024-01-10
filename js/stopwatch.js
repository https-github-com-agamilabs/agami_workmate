class StopWatch {
    hr = 0;
    min = 0;
    sec = 0;
    isTimeStopped = true;

    constructor(target, timerTarget) {
        this.target = target;
        this.timerTarget = timerTarget;
    }

    set target(value) {
        // if (!$(value).length) {
        //     throw new Error(`Target "${value}" is not exist.`);
        //     return;
        // }
        this._target = value;
    }

    set timerTarget(value) {
        // if (!$(value).length) {
        //     throw new Error(`Start time target "${value}" is not exist.`);
        //     return;
        // }
        this._timerTarget = value;
    }

    getCurrentMenuTitle() {
        let filename = window.location.pathname.split("/").pop();
        // filename = (filename == `channels.php`) ? filename + window.location.search : filename;
        filename = (filename == `story.php`) ? filename + window.location.search : filename;
        return $(`.scrollbar-sidebar a[href=\"${filename}\"]`).text().trim();
    }

    startTimer() {
        if (this.isTimeStopped) {
            this.isTimeStopped = false;
            this.timerCycle();
            $(this._timerTarget, document).removeClass("fa-play-circle text-success").addClass("fa-stop-circle text-danger");
        }

        return this;
    }

    stopTimer() {
        if (!this.isTimeStopped) {
            this.isTimeStopped = true;
            $(this._timerTarget, document).removeClass("fa-stop-circle text-danger").addClass("fa-play-circle text-success");
            document.title = `${this.getCurrentMenuTitle()} | Employee | ${ORGNAME}`;
        }

        return this;
    }

    timerCycle() {
        if (!this.isTimeStopped) {
            this.sec = this.sec + 1;

            if (this.sec == 60) {
                this.min = this.min + 1;
                this.sec = 0;
            }

            if (this.min == 60) {
                this.hr = this.hr + 1;
                this.min = 0;
                this.sec = 0;
            }

            $(this._target, document).html(`${padZero(this.hr)}:${padZero(this.min)}:${padZero(this.sec)}`);
            document.title = `${this.getCurrentMenuTitle()} | Employee | ${ORGNAME} (${padZero(this.hr)}:${padZero(this.min)}:${padZero(this.sec)})`;
            this.timeoutID = setTimeout(() => this.timerCycle(), 1000);
        }

        return this;
    }

    resetTimer() {
        clearTimeout(this.timeoutID);
        this.hr = 0;
        this.sec = 0;
        this.min = 0;
        $(this._target, document).html(`${padZero(this.hr)}:${padZero(this.min)}:${padZero(this.sec)}`);

        return this;
    }

    setTimer(value = 0) {
        this.hr = parseInt(value / 3600, 10);
        this.min = parseInt((value % 3600) / 60, 10);
        this.sec = parseInt(value % 60, 10);
        this.isTimeStopped = value >= 0;
        $(this._target, document).html(`${padZero(this.hr)}:${padZero(this.min)}:${padZero(this.sec)}`);

        return this;
    }
}