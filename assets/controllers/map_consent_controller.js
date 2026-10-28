import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static values = { src: String, title: String };

    load() {
        const iframe = document.createElement('iframe');
        iframe.src = this.srcValue;
        iframe.title = this.titleValue;
        iframe.className = 'h-full w-full';
        iframe.referrerPolicy = 'no-referrer-when-downgrade';
        this.element.replaceChildren(iframe);
        // le bouton a disparu : on garde le focus clavier dans la zone carte
        iframe.focus();
    }
}
