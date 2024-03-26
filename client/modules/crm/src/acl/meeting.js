

import Acl from 'acl';

class MeetingAcl extends Acl {

    
    checkModelRead(model, data, precise) {
        return this._checkModelCustom('read', model, data, precise);
    }

    
    checkModelStream(model, data, precise) {
        return this._checkModelCustom('stream', model, data, precise);
    }

    _checkModelCustom(action, model, data, precise) {
        let result = this.checkModel(model, data, action, precise);

        if (result) {
            return true;
        }

        if (data === false) {
            return false;
        }

        let d = data || {};

        if (d[action] === 'no') {
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

export default MeetingAcl;

