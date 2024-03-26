

import Acl from 'acl';

class UserAcl extends Acl {

    
    checkModelRead(model, data, precise) {
        if (model.isPortal()) {
            if (this.get('portalPermission') === 'yes') {
                return true;
            }
        }

        return this.checkModel(model, data, 'read', precise);
    }

    checkIsOwner(model) {
        return this.getUser().id === model.id;
    }
}

export default UserAcl;
