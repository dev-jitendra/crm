

import Acl from 'acl';

class ImportAcl extends Acl {

    checkScope(data, action, precise, entityAccessData) {
        return !!data;
    }

    
    checkModelRead(model, data, precise) {
        return true;
    }

    checkIsOwner(model) {
        if (this.getUser().id === model.get('createdById')) {
            return true;
        }

        return false;
    }

    checkModelDelete(model, data, precise) {
        return true;
    }
}

export default ImportAcl;
