

import ModalView from 'views/modal';

class Auth2faRequiredModalView extends ModalView {

    noCloseButton = true
    escapeDisabled = true

    events = {
        'click [data-action="proceed"]': 'actionProceed',
        'click [data-action="logout"]': 'actionLogout',
    }

    
    templateContent = `
        <div class="complex-text">{{complexText viewObject.messageText}}</div>
        <div class="button-container btn-group" style="margin-top: 30px">
        <button class="btn btn-primary" data-action="proceed">{{translate 'Proceed'}}</button>
        <button class="btn btn-default" data-action="logout">{{translate 'Log Out'}}</button></div>
    `

    setup() {
        this.buttonList = [];

        this.headerText = this.translate('auth2FARequiredHeader', 'messages', 'User');
        
        this.messageText = this.translate('auth2FARequired', 'messages', 'User');
    }

    actionProceed() {
        this.createView('dialog', 'views/user/modals/security', {
            userModel: this.getUser(),
        }, view => {
            view.render();

            this.listenToOnce(view, 'done', () => {
                this.clearView('dialog');
                this.close();
            });
        });
    }

    actionLogout() {
        this.getRouter().logout();
    }
}

export default Auth2faRequiredModalView;
