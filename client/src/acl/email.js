

import Acl from 'acl';

class EmailAcl extends Acl {

    
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

    checkIsOwner(model) {
        if (
            this.getUser().id === model.get('assignedUserId') ||
            this.getUser().id === model.get('createdById')
        ) {
            return true;
        }

        if (!model.has('assignedUsersIds')) {
            return null;
        }

        if (~(model.get('assignedUsersIds') || []).indexOf(this.getUser().id)) {
            return true;
        }

        return false;
    }

    
    checkModelEdit(model, data, precise) {
        if (
            model.get('status') === 'Draft' &&
            model.get('createdById') === this.getUser().id
        ) {
            return true;
        }

        return this.checkModel(model, data, 'edit', precise);
    }

    checkModelDelete(model, data, precise) {
        const result = this.checkModel(model, data, 'delete', precise);

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

        if (model.get('createdById') === this.getUser().id) {
            if (model.get('status') !== 'Sent' && model.get('status') !== 'Archived') {
                return true;
            }
        }

        return result;
    }
}

export default EmailAcl;
