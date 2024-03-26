

import Controller from 'controller';

class PasswordChangeRequestController extends Controller {

    
    actionPasswordChange(options) {
        options = options || {};

        if (!options.id) {
            throw new Error();
        }

        this.entire('views/user/password-change-request', {
            requestId: options.id,
            strengthParams: options.strengthParams,
            notFound: options.notFound,
        }, view => {
            view.render();
        });
    }
}

export default PasswordChangeRequestController;
