

import Acl from 'acl';

class MassEmailAcl extends Acl {

    checkIsOwner(model) {
        if (model.has('campaignId')) {
            return true;
        }

        return super.checkIsOwner(model);
    }

    checkInTeam(model) {
        if (model.has('campaignId')) {
            return true;
        }

        return super.checkInTeam(model);
    }
}

export default MassEmailAcl;
