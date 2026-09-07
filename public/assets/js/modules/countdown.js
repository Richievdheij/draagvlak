/**
 * A countdown that runs inside the page.
 *
 * The server prints a correct value first, so a browser without JavaScript
 * shows a timer that stands still instead of one that is wrong. This module
 * only keeps it moving.
 *
 * Inside the element it looks for, all optional:
 *   [data-countdown-label]    gets the remaining time as mm:ss
 *   [data-countdown-fill]     gets a width from 100% down to 0%
 *   [data-countdown-active]   shown while there is time left
 *   [data-countdown-expired]  shown once the time is up
 *
 * When it reaches zero the element also gets the class is-expired, so the
 * styling of that state stays in CSS.
 *
 * @param {HTMLElement} element Element with data-seconds, and optionally
 *                              data-window with the size of the full bar.
 * @returns {void}
 */
export function init(element) {
    let secondsLeft = Number(element.dataset.seconds ?? 0);

    if (!Number.isFinite(secondsLeft) || secondsLeft <= 0) {
        expire(element);

        return;
    }

    const total = Number(element.dataset.window ?? 0) || secondsLeft;
    const label = element.querySelector('[data-countdown-label]');
    const fill = element.querySelector('[data-countdown-fill]');

    const timer = setInterval(() => {
        secondsLeft -= 1;

        if (secondsLeft <= 0) {
            clearInterval(timer);
            paint(label, fill, 0, total);
            expire(element);

            return;
        }

        paint(label, fill, secondsLeft, total);
    }, 1000);

    paint(label, fill, secondsLeft, total);
}

/** Write the remaining time into the label and the bar. */
function paint(label, fill, secondsLeft, total) {
    if (label) {
        label.textContent = formatCountdown(secondsLeft);
    }

    if (fill) {
        fill.style.width = `${Math.max(0, Math.min(100, (secondsLeft / total) * 100))}%`;
    }
}

/** Switch the element to its finished state. */
function expire(element) {
    element.classList.add('is-expired');

    element.querySelectorAll('[data-countdown-active]').forEach((node) => {
        node.hidden = true;
    });

    element.querySelectorAll('[data-countdown-expired]').forEach((node) => {
        node.hidden = false;
    });
}

/** Seconds as mm:ss, the same notation the server prints. */
function formatCountdown(seconds) {
    const safe = Math.max(0, seconds);
    const minutes = String(Math.floor(safe / 60)).padStart(2, '0');

    return `${minutes}:${String(safe % 60).padStart(2, '0')}`;
}
