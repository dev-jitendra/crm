

import RecordController from 'controllers/record';

class RoleController extends RecordController {

    checkAccess(action) {
        if (this.getUser().isAdmin()) {
            return true;
        }

        return false;
    }
}

export default RoleController;
