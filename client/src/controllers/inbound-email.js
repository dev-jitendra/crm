

import RecordController from 'controllers/record';

class InboundEmailController extends RecordController {

    checkAccess(action) {
        if (this.getUser().isAdmin()) {
            return true;
        }

        return false;
    }
}

export default InboundEmailController;
