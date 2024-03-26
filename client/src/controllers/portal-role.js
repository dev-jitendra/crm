

import RecordController from 'controllers/record';

class PortalRoleController extends RecordController {

    checkAccess(action) {
        if (this.getUser().isAdmin()) {
            return true;
        }

        return false;
    }
}

export default PortalRoleController;
