import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['current', 'preview', 'previewWrap', 'newLabel', 'input'];

    preview() {
        const input = this.hasInputTarget ? this.inputTarget.querySelector('input[type="file"]') : null;

        if (!input || !input.files || input.files.length === 0) {
            this.hidePreview();

            return;
        }

        const file = input.files[0];

        if (!file.type.startsWith('image/')) {
            this.hidePreview();

            return;
        }

        const reader = new FileReader();

        reader.onload = (event) => {
            this.previewTarget.src = event.target.result;
            this.previewTarget.classList.remove('hidden');
            this.previewWrapTarget.classList.remove('hidden');
            this.newLabelTarget.classList.remove('hidden');
        };

        reader.readAsDataURL(file);
    }

    hidePreview() {
        if (this.hasPreviewTarget) {
            this.previewTarget.src = '';
            this.previewTarget.classList.add('hidden');
        }

        if (this.hasPreviewWrapTarget) {
            this.previewWrapTarget.classList.add('hidden');
        }

        if (this.hasNewLabelTarget) {
            this.newLabelTarget.classList.add('hidden');
        }
    }
}
