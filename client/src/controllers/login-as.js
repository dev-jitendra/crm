

import Controller from 'controller';

class LoginAsController extends Controller {

    
    
    actionLogin(options) {
        const anotherUser = options.anotherUser;
        const username = options.username;

        if (!anotherUser) {
            throw new Error("No anotherUser.");
        }

        this.baseController.login({
            anotherUser: anotherUser,
            username: username,
        });

        this.listenToOnce(this.baseController, 'login', () => {
            this.baseController.once('router-set', () => {
                const url = window.location.href.split('?')[0];

                window.location.replace(url);
            })
        });
    }
}

export default LoginAsController;
