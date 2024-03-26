



import View from 'view';
import Base64 from 'js-base64';
import $ from 'jquery';

class LoginView extends View {

    
    template = 'login'

    
    views = {
        footer: {
            fullSelector: 'body > footer',
            view: 'views/site/footer',
        },
    }

    
    anotherUser = null

    
    isPopoverDestroyed = false

    
    handler = null

    
    fallback = false

    
    method = null

    
    events = {
        
        'submit #login-form': function (e) {
            e.preventDefault();

            this.login();
        },
        
        'click #sign-in': function () {
            this.signIn();
        },
        
        'click a[data-action="passwordChangeRequest"]': function () {
            this.showPasswordChangeRequest();
        },
        
        'click a[data-action="showFallback"]': function () {
            this.showFallback();
        },
        
        'keydown': function (e) {
            if (Espo.Utils.getKeyFromKeyEvent(e) === 'Control+Enter') {
                e.preventDefault();

                if (
                    this.handler &&
                    (!this.fallback || !this.$username.val())
                ) {
                    this.signIn();

                    return;
                }

                this.login();
            }
        },
    }

    
    data() {
        return {
            logoSrc: this.getLogoSrc(),
            showForgotPassword: this.getConfig().get('passwordRecoveryEnabled'),
            anotherUser: this.anotherUser,
            hasSignIn: !!this.handler,
            hasFallback: !!this.handler && this.fallback,
            method: this.method,
            signInText: this.signInText,
            logInText: this.logInText,
        };
    }

    
    setup() {
        this.anotherUser = this.options.anotherUser || null;

        const loginData = this.getConfig().get('loginData') || {};

        this.fallback = !!loginData.fallback;
        this.method = loginData.method;

        if (loginData.handler) {
            this.wait(
                Espo.loader
                    .requirePromise(loginData.handler)
                    .then(Handler => {
                        this.handler = new Handler(this, loginData.data || {});
                    })
            );

            this.signInText = this.getLanguage().has(this.method, 'signInLabels', 'Global') ?
                this.translate(this.method, 'signInLabels') :
                this.translate('Sign in');
        }

        if (this.getLanguage().has('Log in', 'labels', 'Global')) {
            this.logInText = this.translate('Log in');
        }

        this.logInText = this.getLanguage().has('Log in', 'labels', 'Global') ?
            this.translate('Log in') :
            this.translate('Login');
    }

    
    getLogoSrc() {
        const companyLogoId = this.getConfig().get('companyLogoId');

        if (!companyLogoId) {
            return this.getBasePath() +
                (this.getConfig().get('logoSrc') || 'client/img/logo.svg');
        }

        return this.getBasePath() + '?entryPoint=LogoImage&id=' + companyLogoId;
    }

    
    afterRender() {
        this.$submit = this.$el.find('#btn-login');
        this.$signIn = this.$el.find('#sign-in');
        this.$username = this.$el.find('#field-userName');
        this.$password = this.$el.find('#field-password');

        if (this.options.prefilledUsername) {
            this.$username.val(this.options.prefilledUsername);
        }

        if (this.handler) {
            this.$username.closest('.cell').addClass('hidden');
            this.$password.closest('.cell').addClass('hidden');
            this.$submit.closest('.cell').addClass('hidden');
        }
    }

    
    signIn() {
        this.disableForm();

        this.handler
            .process()
            .then(headers => {
                this.proceed(headers);
            })
            .catch(() => {
                this.undisableForm();
            })
    }

    
    login() {
        let authString;
        let userName = this.$username.val();
        const password = this.$password.val();

        const trimmedUserName = userName.trim();

        if (trimmedUserName !== userName) {
            this.$username.val(trimmedUserName);

            userName = trimmedUserName;
        }

        if (userName === '') {
            this.processEmptyUsername();

            return;
        }

        this.disableForm();

        try {
            authString = Base64.encode(userName  + ':' + password);
        }
        catch (e) {
            Espo.Ui.error(this.translate('Error') + ': ' + e.message, true);

            this.undisableForm();

            throw e;
        }

        const headers = {
            'Authorization': 'Basic ' + authString,
            'Espo-Authorization': authString,
        };

        this.proceed(headers, userName, password);
    }

    
    proceed(headers, userName, password) {
        headers = Espo.Utils.clone(headers);

        const initialHeaders = Espo.Utils.clone(headers);

        headers['Espo-Authorization-By-Token'] = 'false';
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
                    const data = xhr.responseJSON || {};
                    const statusReason = xhr.getResponseHeader('X-Status-Reason');

                    if (statusReason === 'second-step-required') {
                        xhr.errorIsHandled = true;
                        this.onSecondStepRequired(initialHeaders, userName, password, data);

                        return;
                    }

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

    
    processEmptyUsername() {
        this.isPopoverDestroyed = false;

        const $el = this.$username;

        const message = this.getLanguage().translate('userCantBeEmpty', 'messages', 'User');

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

    
    disableForm() {
        this.$submit.addClass('disabled').attr('disabled', 'disabled');
        this.$signIn.addClass('disabled').attr('disabled', 'disabled');
    }

    
    undisableForm() {
        this.$submit.removeClass('disabled').removeAttr('disabled');
        this.$signIn.removeClass('disabled').removeAttr('disabled');
    }

    
    onSecondStepRequired(headers, userName, password, data) {
        const view = data.view || 'views/login-second-step';

        this.trigger('redirect', view, headers, userName, password, data);
    }

    
    onError() {
        this.onFail('loginError');
    }

    
    onWrongCredentials() {
        const msg = this.handler ?
            'failedToLogIn' :
            'wrongUsernamePassword';

        this.onFail(msg);
    }

    
    onFail(msg) {
        const $cell = $('#login .form-group');

        $cell.addClass('has-error');

        this.$el.one('mousedown click', () => {
            $cell.removeClass('has-error');
        });

        Espo.Ui.error(this.translate(msg, 'messages', 'User'));
    }

    
    showFallback() {
        this.$el.find('[data-action="showFallback"]').addClass('hidden');

        this.$el.find('.panel-body').addClass('fallback-shown');

        this.$username.closest('.cell').removeClass('hidden');
        this.$password.closest('.cell').removeClass('hidden');
        this.$submit.closest('.cell').removeClass('hidden');
    }

    
    notifyLoading() {
        Espo.Ui.notify(' ... ');
    }

    
    showPasswordChangeRequest() {
        this.notifyLoading();

        this.createView('passwordChangeRequest', 'views/modals/password-change-request', {
            url: window.location.href,
        }, view => {
            view.render();

            Espo.Ui.notify(false);
        });
    }
}

export default LoginView;
