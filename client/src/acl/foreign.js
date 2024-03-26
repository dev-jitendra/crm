

import Acl from 'acl';


class ForeignAcl extends Acl {

    checkIsOwner(model) {
        return true;
    }

    checkInTeam(model) {
        return true;
    }
}

export default ForeignAcl;
