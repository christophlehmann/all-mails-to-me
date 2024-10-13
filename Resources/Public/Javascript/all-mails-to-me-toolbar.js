import AjaxRequest from "@typo3/core/ajax/ajax-request.js";
import "@typo3/backend/element/spinner-element.js";
import Icons from "@typo3/backend/icons.js";
import Notification from "@typo3/backend/notification.js";

class AllMailsToMeToolbar {

    constructor() {
        this.toggleElement = document.querySelector('#all-mails-to-me');
        this.toggleElement.onclick = () => {
            this.toggle()
        }
        this.updateTitle();
    }

    toggle() {
        if (this.isEnabled()) {
            new AjaxRequest(TYPO3.settings.ajaxUrls.allmailstome_disable).get().then((async e => {
                this.updateState(0);
            }),function (error) {
                Notification.error('All mails to me', `The request failed with ${error.response.status}: ${error.response.statusText}`, 5);
            });
        } else {
            new AjaxRequest(TYPO3.settings.ajaxUrls.allmailstome_enable).get().then((async e => {
                const responseText = await e.resolve();
                Notification.success('All mails to me', responseText, 5);
                this.updateState(1);
            }),function (error) {
                error.response.text().then(errorMessage => Notification.error('All mails to me', errorMessage, 5));
            });
        }
    }

    updateState(newState) {
        this.toggleElement.dataset.isEnabled = newState;
        Icons.getIcon('actions-envelope', Icons.sizes.small, null, (newState ? 'default' : 'disabled')).then((icon) => {
            this.toggleElement.innerHTML = icon;
            this.updateTitle();
        });
    }

    updateTitle() {
        let newTitleSuffix = (this.isEnabled() ? this.toggleElement.dataset.stateEnabled : this.toggleElement.dataset.stateDisabled);
        this.toggleElement.setAttribute('title', this.toggleElement.dataset.title + " (" + newTitleSuffix + ")");
    }

    isEnabled() {
        return parseInt(this.toggleElement.dataset.isEnabled) === 1;
    }
}

export default new AllMailsToMeToolbar;