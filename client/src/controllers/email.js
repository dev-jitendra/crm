

import RecordController from 'controllers/record';

class EmailController extends RecordController {

    prepareModelView(model, options) {
        super.prepareModelView(model, options);

        this.listenToOnce(model, 'after:send', () => {
            const key = this.name + 'List';
            const stored = this.getStoredMainView(key);

            if (stored) {
                this.clearStoredMainView(key);
            }
        });
    }
}

export default EmailController;
