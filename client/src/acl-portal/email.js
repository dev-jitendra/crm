

import AclPortal from 'acl-portal';

class EmailAclPortal extends AclPortal {

    
    checkModelRead(model, data, precise) {
        const result = this.checkModel(model, data, 'read', precise);

        if (result) {
            return true;
        }

        if (data === false) {
            return false;
        }

        const d = data || {};

        if (d.read === 'no') {
            return false;
        }

        if (model.has('usersIds')) {
            if (~(model.get('usersIds') || []).indexOf(this.getUser().id)) {
                return true;
            }
        }
        else if (precise) {
            return null;
        }

        return result;
    }
}

export default EmailAclPortal;
