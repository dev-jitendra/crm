

import View from 'view';
import Base64 from 'js-base64';
import $ from 'jquery';

class LoginSecondStepView extends View {

    
    template = 'login-second-step'

    
    views =  {
        footer: {
            fullSelector: 'body > footer',
            view: 'views/site/footer',
        },
    }

    
    anotherUser = null

    
    loginData =  null

    
    headers =  null

    
    isPopoverDestroyed =  false

    
    events = {
        
        'submit #login-form': function (e) {
            e.preventDefault();

            this.send();
        },
        
        'click [data-action="backToLogin"]': function () {
            this.trigger('back');
        },
        
        'keydown': function (e) {
            if (Espo.Utils.getKeyFromKeyEvent(e) === 'Control+Enter') {
                e.preventDefault();

                this.send();
            }
        },
    }

    
    data() {
        return {
            message: this.message,
        };
    }

    
    setup() {
        this.message = this.translate(this.options.loginData.message, 'messages', 'User');
        this.anotherUser = this.options.anotherUser || null;
        this.headers = this.options.headers || {};
        this.loginData = this.options.loginData;
    }

    
    afterRender() {
        this.$code = $('[data-name="field-code"]');
        this.$submit = this.$el.find('#btn-send');

        this.$code.focus();
    }

    
    send() {
        const code = this.$code.val().trim().replace(/\s/g, '');

        const userName = this.options.userName;
        const token = this.loginData.token;
        const headers = Espo.Utils.clone(this.headers);

        if (code === '') {
            this.processEmptyCode();

            return;
        }

        this.disableForm();

        if (userName && token) {
            const authString = Base64.encode(userName + ':' + token);

            headers['Authorization'] = 'Basic ' + authString;
            headers['Espo-Authorization'] = authString;
        }

        headers['Espo-Authorization-Code'] = code;
        headers['Espo-Authorization-Create-Token-Secret'] = 'true';

        if (this.anotherUser !== null) {
            headers['X-Another-User'] = this.anotherUser;
        }

        this.notifyLoading();

        Espo.Ajax
            .getRequest('App/user', null, {
                login: true,
                headers: headers,
            })
            .then(data => {
                Espo.Ui.notify(false);

                this.triggerLogin(userName, data);
            })
            .catch(xhr => {
                this.undisableForm();


                if (xhr.status === 401) {
                    const statusReason = xhr.getResponseHeader('X-Status-Reason');

                    if (statusReason === 'error') {
                        this.onError();

                        return;
                    }

                    this.onWrongCredentials();
                }
            });
    }

    
    triggerLogin(userName, data) {
        if (this.anotherUser) {
            data.anotherUser = this.anotherUser;
        }

        if (!userName) {
            userName = (data.user || {}).userName;
        }

        this.trigger('login', userName, data);
    }

    
    processEmptyCode() {
        this.isPopoverDestroyed = false;

        const message = this.getLanguage().translate('codeIsRequired', 'messages', 'User');

        const $el = this.$code;

        $el
            .popover({
                placement: 'bottom',
                container: 'body',
                content: message,
                trigger: 'manual',
            })
            .popover('show');

        const $cell = $el.closest('.form-group');

        $cell.addClass('has-error');

        $el.one('mousedown click', () => {
            $cell.removeClass('has-error');

            if (this.isPopoverDestroyed) {
                return;
            }

            $el.popover('destroy');

            this.isPopoverDestroyed = true;
        });
    }

    
    onFail(msg) {
        const $cell = $('#login .form-group');

        $cell.addClass('has-error');

        this.$el.one('mousedown click', () => {
            $cell.removeClass('has-error');
        });

        Espo.Ui.error(this.translate(msg, 'messages', 'User'));
    }

    
    onError() {
        this.onFail('loginError');
    }

    
    onWrongCredentials() {
        this.onFail('wrongCode');
    }

    
    notifyLoading() {
        Espo.Ui.notify(' ... ');
    }

    
    disableForm() {
        this.$submit.addClass('disabled').attr('disabled', 'disabled');
    }

    
    undisableForm() {
        this.$submit.removeClass('disabled').removeAttr('disabled');
    }
}


export default LoginSecondStepView;
