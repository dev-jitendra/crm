



import Controller from 'controller';
import BaseView from 'views/base';


class BaseController extends Controller {

    
    login(options) {
        const viewName = this.getConfig().get('loginView') || 'views/login';

        const anotherUser = (options || {}).anotherUser;
        const prefilledUsername = (options || {}).username;

        const viewOptions = {
            anotherUser: anotherUser,
            prefilledUsername: prefilledUsername,
        };

        this.entire(viewName, viewOptions, loginView => {
            loginView.render();

            loginView.on('login', (userName, data) => {
                this.trigger('login', this.normalizeLoginData(userName, data));
            });

            loginView.once('redirect', (viewName, headers, userName, password, data) => {
                loginView.remove();

                this.entire(viewName, {
                    loginData: data,
                    userName: userName,
                    password: password,
                    anotherUser: anotherUser,
                    headers: headers,
                }, secondStepView => {
                    secondStepView.render();

                    secondStepView.once('login', (userName, data) => {
                        this.trigger('login', this.normalizeLoginData(userName, data));
                    });

                    secondStepView.once('back', () => {
                        secondStepView.remove();

                        this.login();
                    });
                });
            });
        });
    }

    
    normalizeLoginData(userName, data) {
        return {
            auth: {
                userName: userName,
                token: data.token,
                anotherUser: data.anotherUser,
            },
            user: data.user,
            preferences: data.preferences,
            acl: data.acl,
            settings: data.settings,
            appParams: data.appParams,
            language: data.language,
        };
    }

    
    logout() {
        const title = this.getConfig().get('applicationName') || 'EspoCRM';

        $('head title').text(title);

        this.trigger('logout');
    }

    
    clearCache() {
        this.entire('views/clear-cache', {
            cache: this.getCache(),
        }, view => {
            view.render();
        });
    }

    
    actionLogin() {
        this.login();
    }

    
    actionLogout() {
        this.logout();
    }

    
    actionLogoutWait() {
        this.entire('views/base', {template: 'logout-wait'}, view => {
            view.render()
                .then(() => Espo.Ui.notify(' ... '))
        });
    }

    
    actionClearCache() {
        this.clearCache();
    }

    
    error404() {
        const view = new BaseView({template: 'errors/404'});

        this.entire(view);
    }

    
    error403() {
        const view = new BaseView({template: 'errors/403'});

        this.entire(view);
    }
}

export default BaseController;
