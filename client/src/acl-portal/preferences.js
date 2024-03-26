

import AclPortal from 'acl-portal';

class PreferencesAclPortal extends AclPortal {

    checkIsOwner(model) {
        if (this.getUser().id === model.id) {
            return true;
        }

        return false;
    }
}

export default PreferencesAclPortal;
