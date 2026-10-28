import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['menu', 'button'];

    toggle() {
        this.#setOpen(this.menuTarget.classList.contains('hidden'));
    }

    close() {
        this.#setOpen(false);
    }

    #setOpen(open) {
        this.menuTarget.classList.toggle('hidden', !open);
        this.buttonTarget.setAttribute('aria-expanded', String(open));
    }
}
