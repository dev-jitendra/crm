

import Acl from 'acl';

class CampaignTrackingUrlAcl extends Acl {

    checkIsOwner(model) {
        if (model.has('campaignId')) {
            return true;
        }

        return false;
    }

    checkInTeam(model) {
        if (model.has('campaignId')) {
            return true;
        }

        return false;
    }
}

export default CampaignTrackingUrlAcl;
