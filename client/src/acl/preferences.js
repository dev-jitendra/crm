

import Acl from 'acl';

class PreferencesAcl extends Acl {

    checkIsOwner(model) {
        if (this.getUser().id === model.id) {
            return true;
        }

        return false;
    }
}

export default PreferencesAcl;
