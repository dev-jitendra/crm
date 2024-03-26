

import RecordController from 'controllers/record';

class TeamController extends RecordController {

    checkAccess(action) {
        if (action === 'read') {
            return true;
        }

        if (this.getUser().isAdmin()) {
            return true;
        }

        return false;
    }
}

export default TeamController;
