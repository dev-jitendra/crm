

import RecordController from 'controllers/record';

class LastViewedController extends RecordController {

    entityType = 'ActionHistoryRecord'

    checkAccess(action) {
        return this.getAcl().check(this.entityType, action);
    }
}


export default LastViewedController;
